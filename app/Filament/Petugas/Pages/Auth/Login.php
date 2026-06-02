<?php

namespace App\Filament\Petugas\Pages\Auth;

use Filament\Pages\Auth\Login as BaseLogin;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\HtmlString;

class Login extends BaseLogin
{
    public function getHeading(): string|Htmlable
    {
        return new HtmlString('
            <div class="login-heading-stack">
                <div class="role-badge petugas-badge">
                    <span class="pulse-dot"></span>
                    PETUGAS OPERASIONAL
                </div>
                <span class="login-heading-title">Selamat Datang, Petugas</span>
            </div>
        ');
    }

    public function getSubHeading(): string|Htmlable|null
    {
        return 'Masuk untuk memproses transaksi penyetoran sampah.';
    }
}
