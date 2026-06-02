import preset from '../../../../vendor/filament/filament/tailwind.config.preset'

export default {
    presets: [preset],
    content: [
        './app/Filament/Petugas/**/*.php',
        './resources/views/filament/petugas/**/*.blade.php',
        './vendor/filament/**/*.blade.php',
        './resources/css/filament/petugas/theme.css',
    ],
}
