<?php

namespace App\Filament\Admin\Pages\Auth;

use Filament\Pages\Auth\Login as BaseLogin;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\HtmlString;

class Login extends BaseLogin
{
    public function getHeading(): string|Htmlable
    {
        return new HtmlString('
            <div class="login-heading-stack">
                <div class="role-badge admin-badge">
                    <span class="pulse-dot"></span>
                    ADMINISTRATOR
                </div>
                <span class="login-heading-title">Masuk sebagai Admin</span>
            </div>
        ');
    }

    public function getSubHeading(): string|Htmlable|null
    {
        return 'Kelola sistem Setor.in dengan penuh tanggung jawab.';
    }
}
