<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Models\{User, OtpVerifikasi, Saldo, Nasabah};
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\{Hash, Validator, DB, Cache};
use Carbon\Carbon;

class AuthController extends Controller
{
    // Konfigurasi login attempt limiting
    private const MAX_LOGIN_ATTEMPTS = 5;
    private const LOCKOUT_MINUTES = 15;

    /**
     * POST /api/register
     */
    public function register(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'nama'       => 'required|string|max:255',
            'email'      => 'required|email|unique:pengguna,email',
            'no_telepon' => 'required|string|unique:pengguna,no_telepon',
            'password'   => 'required|string|min:6|confirmed',
            'alamat'     => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        // Gunakan Database Transaction agar data tidak menggantung jika OTP gagal dibuat
        return DB::transaction(function () use ($request) {
            $user = User::create([
                'nama'        => $request->nama,
                'email'       => $request->email,
                'no_telepon'  => $request->no_telepon,
                'password'    => Hash::make($request->password),
                'alamat'      => $request->alamat,
                'role'        => 'nasabah',
                'status_akun' => 'pending',
            ]);

            $otpCode = rand(100000, 999999);

            OtpVerifikasi::create([
                'id_pengguna' => $user->id,
                'kode_otp'    => $otpCode,
                'tgl_expired' => Carbon::now()->addMinutes(10),
                'status_otp'  => 'aktif',
            ]);

            return response()->json([
                'status'  => true,
                'message' => 'Registrasi berhasil. Silakan verifikasi kode OTP Anda.',
                'otp_dev' => $otpCode, // TODO: Hapus pada environment production
            ], 201);
        });
    }

    /**
     * POST /api/verify-otp
     */
    public function verifyOtp(Request $request): JsonResponse
    {
        $request->validate([
            'email'    => 'required|email',
            'kode_otp' => 'required|string'
        ]);

        $user = User::where('email', $request->email)->firstOrFail();

        $otp = OtpVerifikasi::where('id_pengguna', $user->id)
            ->where('kode_otp', $request->kode_otp)
            ->where('status_otp', 'aktif')
            ->where('tgl_expired', '>=', Carbon::now())
            ->first();

        if (!$otp) {
            return response()->json([
                'status'  => false,
                'message' => 'Kode OTP tidak valid atau sudah kadaluwarsa.'
            ], 422);
        }

        return DB::transaction(function () use ($user, $otp) {
            // Update status user & OTP
            $user->update(['status_akun' => 'aktif']);
            $otp->update(['status_otp' => 'terpakai']);

            // Assign Role (Pastikan guard sudah sesuai di config/auth.php)
            $user->assignRole('nasabah');

            // Inisialisasi data Nasabah & Saldo awal
            Nasabah::firstOrCreate(['id_pengguna' => $user->id]);
            Saldo::firstOrCreate(
                ['id_pengguna' => $user->id],
                ['jumlah_saldo' => 0]
            );

            return response()->json([
                'status'  => true,
                'message' => 'Akun berhasil diverifikasi. Silakan login.'
            ]);
        });
    }

    /**
     * POST /api/login
     */
    public function login(Request $request): JsonResponse
    {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required|string'
        ]);

        $user = User::where('email', $request->email)->first();

        // Cek apakah akun terkunci karena terlalu banyak percobaan login
        $lockoutKey = 'login_attempts:' . $request->email;
        $lockedUntil = Cache::get($lockoutKey . ':locked');

        if ($lockedUntil && now()->lt(Carbon::parse($lockedUntil))) {
            $remainingMinutes = Carbon::parse($lockedUntil)->diffInMinutes(now()) + 1;
            return response()->json([
                'status'  => false,
                'message' => "Akun terkunci. Coba lagi dalam {$remainingMinutes} menit.",
                'locked'  => true,
                'locked_until' => $lockedUntil,
            ], 429);
        }

        // Verifikasi kredensial
        if (!$user || !Hash::check($request->password, $user->password)) {
            $this->incrementLoginAttempts($request->email);

            return response()->json([
                'status'  => false,
                'message' => 'Kredensial yang Anda berikan salah.'
            ], 401);
        }

        // Cek status akun
        if ($user->status_akun !== 'aktif') {
            return response()->json([
                'status'  => false,
                'message' => 'Akun Anda belum aktif. Silakan lakukan verifikasi OTP.'
            ], 403);
        }

        // Reset login attempts setelah berhasil login
        $this->resetLoginAttempts($request->email);

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'status'       => true,
            'message'      => 'Login berhasil.',
            'access_token' => $token,
            'token_type'   => 'Bearer',
            'user'         => [
                'id'          => $user->id,
                'nama'        => $user->nama,
                'email'       => $user->email,
                'role'        => $user->role,
                'status_akun' => $user->status_akun,
            ],
        ]);
    }

    /**
     * POST /api/logout
     */
    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'status'  => true,
            'message' => 'Berhasil keluar dari sistem.'
        ]);
    }

    /**
     * Tambah percobaan login gagal
     */
    private function incrementLoginAttempts(string $email): void
    {
        $key = 'login_attempts:' . $email;
        $lockedKey = $key . ':locked';

        $attempts = Cache::get($key, 0) + 1;
        Cache::put($key, $attempts, now()->addMinutes(self::LOCKOUT_MINUTES));

        // Jika mencapai batas max, kunci akun
        if ($attempts >= self::MAX_LOGIN_ATTEMPTS) {
            $lockedUntil = now()->addMinutes(self::LOCKOUT_MINUTES);
            Cache::put($lockedKey, $lockedUntil, now()->addMinutes(self::LOCKOUT_MINUTES));
        }
    }

    /**
     * Reset percobaan login
     */
    private function resetLoginAttempts(string $email): void
    {
        $key = 'login_attempts:' . $email;
        $lockedKey = $key . ':locked';

        Cache::forget($key);
        Cache::forget($lockedKey);
    }
}