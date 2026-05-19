{{-- resources/views/filament/petugas/pages/profil-petugas.blade.php --}}
<x-filament-panels::page>
    <form wire:submit="submit" class="space-y-6">
        {{ $this->form }}

        <div class="flex justify-end mt-6">
            <x-filament::button type="submit" color="primary" size="lg">
                Simpan Perubahan
            </x-filament::button>
        </div>
    </form>
</x-filament-panels::page>