{{-- resources/views/filament/petugas/pages/profil-petugas.blade.php --}}
<x-filament-panels::page>
    <form wire:submit.prevent="submit">
        {{ $this->form }}

<br>
<br>
        <div class="mt-4">
            <button type="submit" class="inline-flex items-center justify-center px-4 py-2 text-sm font-medium text-white bg-primary-600 border border-transparent rounded-lg hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                Simpan Perubahan
            </button>
        </div>
    </form>
</x-filament-panels::page>