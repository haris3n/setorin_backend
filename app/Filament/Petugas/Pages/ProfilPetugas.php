<?php

namespace App\Filament\Petugas\Pages;

use App\Models\AktivitasPetugas;
use App\Models\User;
use Filament\Facades\Filament;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Form;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class ProfilPetugas extends Page
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-user-circle';

    protected static ?string $navigationLabel = 'Profil Saya';

    protected static ?string $title = 'Profil Saya';

    protected static string $view = 'filament.petugas.pages.profil-petugas';

    public array $data = [];

    public function mount(): void
    {
        $this->form->fill(Auth::user()->toArray());
    }

    protected function getFormSchema(): array
    {
        return [
            TextInput::make('nama')
                ->label('Nama Lengkap')
                ->required()
                ->maxLength(255),

            TextInput::make('email')
                ->label('Email')
                ->email()
                ->required()
                ->maxLength(255),

            TextInput::make('no_telepon')
                ->label('Nomor Telepon')
                ->tel()
                ->required()
                ->maxLength(20),

            TextInput::make('alamat')
                ->label('Alamat')
                ->maxLength(500)
                ->columnSpanFull(),
        ];
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema($this->getFormSchema())
            ->statePath('data')
            ->model(Auth::user());
    }

    public function submit(): void
    {
        $data = $this->form->getState();

        $user = Auth::user();

        // Validasi email unik
        $emailExists = User::where('email', $data['email'])
            ->where('id', '!=', $user->id)
            ->exists();

        if ($emailExists) {
            throw ValidationException::withMessages([
                'data.email' => 'Email sudah digunakan oleh pengguna lain.',
            ]);
        }

        $user->update([
            'nama' => $data['nama'],
            'email' => $data['email'],
            'no_telepon' => $data['no_telepon'],
            'alamat' => $data['alamat'] ?? $user->alamat,
        ]);

        // Log aktivitas
        if ($user->petugas) {
            AktivitasPetugas::log(
                $user->petugas->id,
                'profil_diubah',
                'Memperbarui profil pengguna'
            );
        }

        Filament::notify('success', 'Profil berhasil diperbarui.');

        // Refresh form with new data
        $this->form->fill($user->toArray());
    }
}