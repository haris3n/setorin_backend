import { createRequire } from 'module'
import { resolve, dirname } from 'path'
import { fileURLToPath } from 'url'

const __dirname = dirname(fileURLToPath(import.meta.url))
const require = createRequire(import.meta.url)

// ✅ Conditional import: akan gagal secara graceful jika vendor/ belum ada
// (misalnya sebelum composer install dijalankan)
let preset = {}
try {
    preset = require(resolve(__dirname, './vendor/filament/support/tailwind.config.preset'))
} catch (e) {
    console.warn('[tailwind] ⚠️  Filament preset tidak ditemukan, pastikan composer install sudah dijalankan.')
}

export default {
    presets: [preset],
    content: [
        './app/Filament/**/*.php',
        './resources/views/filament/**/*.blade.php',
        './vendor/filament/**/*.blade.php',
    ],
}
