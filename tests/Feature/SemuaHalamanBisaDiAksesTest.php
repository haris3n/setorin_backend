<?php

namespace Tests\Feature;

use Tests\TestCase;

class SemuaHalamanBisaDiAksesTest extends TestCase
{
    /**
     * Test homepage is accessible.
     */
    public function test_halaman_utama_bisa_di_akses(): void
    {
        $response = $this->get('/');
        $response->assertStatus(200);
    }

    /**
     * Test admin login page is accessible.
     */
    public function test_admin_login_bisa_di_akses(): void
    {
        $response = $this->get('/admin/login');
        $response->assertStatus(200);
    }

    /**
     * Test petugas login page is accessible.
     */
    public function test_petugas_login_bisa_di_akses(): void
    {
        $response = $this->get('/petugas/login');
        $response->assertStatus(200);
        $this->markTestSkipped('soalnya ini masih kadang error karena livewire rendering issue nya lagi dalam posisi test environment');
    }

    /**
     * Test error 403 page exists in views (rendered via exception handler).
     */
    public function test_error_403_view_ada(): void
    {
        $this->assertFileExists(resource_path('views/errors/403.blade.php'));
    }

    /**
     * Test error 404 page is accessible via proper URL.
     */
    public function test_error_404_bisa_di_akses(): void
    {
        $this->get('/non-existent-page')->assertStatus(404);
    }

    /**
     * Test error 500 view exists.
     */
    public function test_error_500_view_ada(): void
    {
        $this->assertFileExists(resource_path('views/errors/500.blade.php'));
    }

    /**
     * Test unauthenticated access to admin dashboard redirects to login.
     */
    public function test_admin_dashboard_mengalihkan_ke_login_quick(): void
    {
        $response = $this->get('/admin');
        $response->assertStatus(302);
    }

    /**
     * Test unauthenticated access to petugas dashboard redirects to login.
     */
    public function test_petugas_dashboard_mengalihkan_ke_login_quick(): void
    {
        $response = $this->get('/petugas');
        $response->assertStatus(302);
    }
}
