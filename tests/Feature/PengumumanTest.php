<?php

namespace Tests\Feature;

use App\Filament\Admin\Resources\PengumumanResource\Pages\CreatePengumuman;
use App\Filament\Admin\Resources\PengumumanResource\Pages\EditPengumuman;
use App\Models\Pengumuman;
use App\Models\User;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Livewire\Livewire;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class PengumumanTest extends TestCase
{
    use RefreshDatabase;

    private function user(string $role = 'admin'): User
    {
        $user = User::create([
            'nama' => 'Pengguna Pengujian',
            'email' => uniqid().'@example.com',
            'no_telepon' => uniqid(),
            'password' => 'password-test',
            'role' => $role,
            'status_akun' => 'aktif',
        ]);
        $user->assignRole(Role::findOrCreate($role, 'web'));

        return $user;
    }

    private function payload(array $overrides = []): array
    {
        return array_merge([
            'judul' => 'Jadwal Libur Bank Sampah',
            'isi' => 'Pelayanan kembali dibuka hari Senin.',
            'status' => 'published',
            'tanggal_mulai' => '2026-09-01',
            'tanggal_selesai' => '2026-09-30',
        ], $overrides);
    }

    public function test_admin_can_create_read_update_and_delete_pengumuman(): void
    {
        $admin = $this->user();
        Sanctum::actingAs($admin);

        $id = $this->postJson('/api/admin/pengumuman', $this->payload(['id_pengguna' => 999]))
            ->assertCreated()->assertJsonPath('data.id_pengguna', $admin->id)->json('data.id');
        $this->assertDatabaseHas('pengumuman', ['id' => $id, 'judul' => 'Jadwal Libur Bank Sampah']);
        $this->getJson('/api/admin/pengumuman?search=Libur&status=published')
            ->assertOk()->assertJsonPath('data.total', 1);
        $this->getJson('/api/admin/pengumuman/'.$id)->assertOk()->assertJsonPath('data.id', $id);
        $this->patchJson('/api/admin/pengumuman/'.$id, ['status' => 'archived'])
            ->assertOk()->assertJsonPath('data.status', 'archived');
        $this->deleteJson('/api/admin/pengumuman/'.$id)->assertOk();
        $this->assertDatabaseMissing('pengumuman', ['id' => $id]);
        $this->getJson('/api/admin/pengumuman/'.$id)->assertNotFound();
    }

    public function test_dates_and_required_fields_are_validated_including_partial_updates(): void
    {
        Sanctum::actingAs($this->user());
        $this->postJson('/api/admin/pengumuman', [])
            ->assertUnprocessable()->assertJsonValidationErrors(['judul', 'isi', 'tanggal_mulai']);
        $this->postJson('/api/admin/pengumuman', $this->payload([
            'status' => 'invalid', 'tanggal_selesai' => '2026-08-31',
        ]))->assertUnprocessable()->assertJsonValidationErrors(['status', 'tanggal_selesai']);

        $record = Pengumuman::create($this->payload());
        $this->patchJson('/api/admin/pengumuman/'.$record->id, ['tanggal_mulai' => '2026-10-01'])
            ->assertUnprocessable()->assertJsonValidationErrors('tanggal_selesai');
        $this->patchJson('/api/admin/pengumuman/'.$record->id, ['tanggal_selesai' => null])
            ->assertOk()->assertJsonPath('data.tanggal_selesai', null);
    }

    public function test_reader_only_sees_published_pengumuman_within_inclusive_period(): void
    {
        $this->travelTo(now()->setDate(2026, 9, 28)->startOfDay());
        Sanctum::actingAs($this->user('nasabah'));
        foreach ([
            ['judul' => 'Aktif'],
            ['judul' => 'Hari ini', 'tanggal_mulai' => '2026-09-28', 'tanggal_selesai' => '2026-09-28'],
            ['judul' => 'Tanpa batas', 'tanggal_selesai' => null],
            ['judul' => 'Draft', 'status' => 'draft'],
            ['judul' => 'Arsip', 'status' => 'archived'],
            ['judul' => 'Mendatang', 'tanggal_mulai' => '2026-09-29'],
            ['judul' => 'Kedaluwarsa', 'tanggal_selesai' => '2026-09-27'],
        ] as $data) {
            Pengumuman::create($this->payload($data));
        }

        $response = $this->getJson('/api/pengumuman')->assertOk()->assertJsonPath('data.total', 3);
        $this->assertEqualsCanonicalizing(['Aktif', 'Hari ini', 'Tanpa batas'], array_column($response->json('data.data'), 'judul'));
    }

    public function test_guests_and_non_admins_cannot_manage_pengumuman(): void
    {
        $this->getJson('/api/pengumuman')->assertUnauthorized();
        $this->postJson('/api/admin/pengumuman', $this->payload())->assertUnauthorized();
        $record = Pengumuman::create($this->payload());
        foreach (['nasabah', 'petugas'] as $role) {
            Sanctum::actingAs($this->user($role));
            $this->getJson('/api/admin/pengumuman')->assertForbidden();
            $this->postJson('/api/admin/pengumuman', $this->payload())->assertForbidden();
            $this->getJson('/api/admin/pengumuman/'.$record->id)->assertForbidden();
            $this->patchJson('/api/admin/pengumuman/'.$record->id, ['status' => 'draft'])->assertForbidden();
            $this->deleteJson('/api/admin/pengumuman/'.$record->id)->assertForbidden();
        }
    }

    public function test_filament_form_persists_author_and_validates_period(): void
    {
        $admin = $this->user();
        $this->actingAs($admin, 'web');
        Filament::setCurrentPanel(Filament::getPanel('admin'));

        $this->get('/admin/pengumuman')->assertOk();
        Livewire::test(CreatePengumuman::class)
            ->fillForm($this->payload(['tanggal_selesai' => '2026-08-31']))
            ->call('create')->assertHasFormErrors(['tanggal_selesai' => 'after_or_equal']);
        Livewire::test(CreatePengumuman::class)
            ->fillForm($this->payload())->call('create')->assertHasNoFormErrors();

        $record = Pengumuman::firstOrFail();
        $this->assertEquals($admin->id, $record->id_pengguna);
        Livewire::test(EditPengumuman::class, ['record' => $record->getRouteKey()])
            ->fillForm(['status' => 'archived'])->call('save')->assertHasNoFormErrors();
        $this->assertDatabaseHas('pengumuman', ['id' => $record->id, 'status' => 'archived']);
    }
}
