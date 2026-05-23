<!DOCTYPE html>
<html>
<head>
    <title>Kode OTP Verifikasi Setor.in</title>
</head>
<body style="font-family: Arial, sans-serif; background-color: #f3f4f6; margin: 0; padding: 20px;">
    <div style="max-width: 600px; margin: 0 auto; background-color: #ffffff; padding: 40px; border-radius: 12px; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);">
        <div style="text-align: center; margin-bottom: 30px;">
            <h1 style="color: #10b981; margin: 0; font-size: 28px;">Setor.in</h1>
            <p style="color: #6b7280; margin-top: 5px;">Bank Sampah Digital</p>
        </div>
        
        <p style="font-size: 16px; color: #374151; line-height: 1.5;">Halo,</p>
        <p style="font-size: 16px; color: #374151; line-height: 1.5;">
            Terima kasih telah mendaftar di aplikasi Setor.in! Untuk menyelesaikan proses pendaftaran dan mengaktifkan akun Anda, silakan gunakan kode OTP (One-Time Password) berikut:
        </p>
        
        <div style="text-align: center; margin: 35px 0;">
            <span style="display: inline-block; padding: 15px 35px; font-size: 28px; font-weight: bold; background-color: #ecfdf5; color: #059669; border-radius: 8px; letter-spacing: 8px; border: 1px dashed #34d399;">
                {{ $otpCode }}
            </span>
        </div>

        <p style="font-size: 14px; color: #ef4444; text-align: center; margin-bottom: 30px;">
            *Kode ini hanya berlaku selama 10 menit. Jangan berikan kode ini kepada siapapun.*
        </p>
        
        <hr style="border: none; border-top: 1px solid #e5e7eb; margin: 30px 0;">
        
        <p style="font-size: 14px; color: #9ca3af; text-align: center; line-height: 1.5;">
            Jika Anda tidak merasa melakukan pendaftaran di Setor.in, silakan abaikan pesan email ini.<br><br>
            Salam Hijau,<br><strong>Tim Setor.in</strong>
        </p>
    </div>
</body>
</html>
