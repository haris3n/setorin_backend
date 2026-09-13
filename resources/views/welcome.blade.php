<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>SetorIn — Smart Waste Bank & Eco-Rewards Ecosystem</title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700;800&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Alpine.js for interactive components -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <!-- Vite Styles & Scripts -->
    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @else
        <script src="https://cdn.tailwindcss.com"></script>
        <script>
            tailwind.config = {
                darkMode: 'class',
                theme: {
                    extend: {
                        colors: {
                            eco: {
                                50: '#ecfdf5',
                                100: '#d1fae5',
                                400: '#34d399',
                                500: '#10b981',
                                600: '#059669',
                                700: '#047857',
                                800: '#065f46',
                                900: '#064e3b',
                                950: '#022c22',
                            },
                            darkbg: '#070F0D',
                            darkcard: '#0E1A16',
                            darkborder: '#172E27',
                        },
                        fontFamily: {
                            sans: ['Plus Jakarta Sans', 'sans-serif'],
                            display: ['Outfit', 'sans-serif'],
                        }
                    }
                }
            }
        </script>
    @endif

    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: #070F0D;
            color: #E2E8F0;
            overflow-x: hidden;
        }

        h1, h2, h3, h4, .font-display {
            font-family: 'Outfit', sans-serif;
        }

        /* Custom Glassmorphism */
        .glass-panel {
            background: rgba(14, 26, 22, 0.7);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: 1px solid rgba(16, 185, 129, 0.15);
        }

        .glass-panel-hover {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .glass-panel-hover:hover {
            background: rgba(18, 34, 29, 0.85);
            border-color: rgba(16, 185, 129, 0.35);
            transform: translateY(-4px);
            box-shadow: 0 12px 30px -10px rgba(16, 185, 129, 0.2);
        }

        .glow-emerald {
            box-shadow: 0 0 40px -5px rgba(16, 185, 129, 0.3);
        }

        .glow-emerald-sm {
            box-shadow: 0 0 15px 0px rgba(16, 185, 129, 0.25);
        }

        /* Ambient Glow Backgrounds */
        .ambient-glow-1 {
            position: absolute;
            top: -10%;
            left: 20%;
            width: 500px;
            height: 500px;
            background: radial-gradient(circle, rgba(16, 185, 129, 0.18) 0%, rgba(5, 150, 105, 0) 70%);
            filter: blur(60px);
            pointer-events: none;
            z-index: 0;
        }

        .ambient-glow-2 {
            position: absolute;
            top: 40%;
            right: 10%;
            width: 600px;
            height: 600px;
            background: radial-gradient(circle, rgba(52, 211, 153, 0.12) 0%, rgba(6, 95, 70, 0) 70%);
            filter: blur(80px);
            pointer-events: none;
            z-index: 0;
        }

        /* Custom Scrollbar */
        ::-webkit-scrollbar {
            width: 8px;
        }
        ::-webkit-scrollbar-track {
            background: #070F0D;
        }
        ::-webkit-scrollbar-thumb {
            background: #172E27;
            border-radius: 4px;
        }
        ::-webkit-scrollbar-thumb:hover {
            background: #10b981;
        }
    </style>
</head>
<body class="antialiased min-h-screen flex flex-col selection:bg-emerald-500 selection:text-white relative">

    <!-- Ambient Background Glows -->
    <div class="ambient-glow-1"></div>
    <div class="ambient-glow-2"></div>

    <!-- Navigation Header -->
    <header class="sticky top-0 z-50 backdrop-blur-xl bg-[#070F0D]/80 border-b border-emerald-900/30 transition-all">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-20">
                
                <!-- Logo -->
                <a href="#" class="flex items-center gap-3 group">
                    <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-emerald-400 to-emerald-700 flex items-center justify-center shadow-lg shadow-emerald-900/40 group-hover:scale-105 transition-transform">
                        <svg class="w-6 h-6 text-emerald-950" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
                        </svg>
                    </div>
                    <div class="flex flex-col">
                        <span class="text-2xl font-extrabold font-display tracking-tight text-white flex items-center gap-1.5">
                            Setor<span class="text-emerald-400">In</span>
                            <span class="text-[10px] uppercase font-bold tracking-widest px-2 py-0.5 rounded-full bg-emerald-500/10 text-emerald-400 border border-emerald-500/20">v2.0</span>
                        </span>
                        <span class="text-[11px] text-emerald-400/70 font-medium -mt-1 tracking-wide">Smart Waste Ecosystem</span>
                    </div>
                </a>

                <!-- Desktop Navigation Links -->
                <nav class="hidden md:flex items-center gap-8 text-sm font-medium text-slate-300">
                    <a href="#fitur" class="hover:text-emerald-400 transition-colors">Fitur Utama</a>
                    <a href="#simulator" class="hover:text-emerald-400 transition-colors">Kalkulator Sampah</a>
                    <a href="#harga" class="hover:text-emerald-400 transition-colors">Harga Sampah</a>
                    <a href="#alursistem" class="hover:text-emerald-400 transition-colors">Cara Kerja</a>
                    <a href="#ekosistem" class="hover:text-emerald-400 transition-colors">Portal Role</a>
                    <a href="#faq" class="hover:text-emerald-400 transition-colors">FAQ</a>
                </nav>

                <!-- Action Buttons / Login Links -->
                <div class="flex items-center gap-3">
                    <a href="{{ url('/petugas') }}" class="hidden sm:inline-flex items-center gap-2 px-4 py-2 rounded-xl text-xs font-semibold text-emerald-300 bg-emerald-950/60 border border-emerald-800/50 hover:bg-emerald-900/60 hover:border-emerald-700 transition-all">
                        <svg class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                        Portal Petugas
                    </a>
                    
                    <a href="{{ url('/admin') }}" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl text-xs font-bold text-emerald-950 bg-gradient-to-r from-emerald-400 via-emerald-300 to-teal-300 hover:brightness-110 shadow-lg shadow-emerald-500/25 transition-all transform active:scale-95">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                        Portal Admin
                    </a>
                </div>

            </div>
        </div>
    </header>

    <!-- Main Content Body -->
    <main class="flex-grow relative z-10">
        
        <!-- HERO SECTION -->
        <section class="pt-12 pb-20 md:pt-20 md:pb-28 relative overflow-hidden">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                
                <div class="text-center max-w-4xl mx-auto space-y-6">
                    
                    <!-- Top Eco Tag -->
                    <div class="inline-flex items-center gap-2 px-4 py-2 rounded-full glass-panel border border-emerald-500/30 text-emerald-400 text-xs font-semibold tracking-wide shadow-inner">
                        <span class="flex h-2 w-2 relative">
                          <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                          <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-500"></span>
                        </span>
                        🌱 Platform Bank Sampah Smart & Eco-Rewards #1 Indonesia
                    </div>

                    <!-- Main Headline -->
                    <h1 class="text-4xl sm:text-6xl lg:text-7xl font-extrabold text-white tracking-tight leading-[1.15]">
                        Ubah Sampah Jadi <br class="hidden sm:inline" />
                        <span class="bg-clip-text text-transparent bg-gradient-to-r from-emerald-400 via-teal-300 to-emerald-200">
                            Saldo E-Wallet & Koin Hijau
                        </span>
                    </h1>

                    <!-- Subtitle Description -->
                    <p class="text-base sm:text-lg text-slate-300 max-w-2xl mx-auto leading-relaxed font-normal">
                        Solusi daur ulang modern terintegrasi. Setor sampahmu ke Bank Sampah terdekat, dapatkan penimbangan digital presisi dari petugas, dan cairkan cuan langsung ke <span class="text-emerald-400 font-semibold">DANA, OVO, GoPay</span> atau Rekening Bank.
                    </p>

                    <!-- CTAs -->
                    <div class="flex flex-col sm:flex-row items-center justify-center gap-4 pt-4">
                        <a href="#simulator" class="w-full sm:w-auto px-8 py-4 rounded-2xl bg-gradient-to-r from-emerald-500 to-teal-600 hover:from-emerald-400 hover:to-teal-500 text-emerald-950 font-bold text-sm shadow-xl shadow-emerald-600/30 hover:shadow-emerald-500/50 transition-all flex items-center justify-center gap-3 group">
                            <span>Simulasi Hitung Cuan</span>
                            <svg class="w-5 h-5 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M17 8l4 4m0 0l-4 4m4-4H3" />
                            </svg>
                        </a>

                        <a href="#alursistem" class="w-full sm:w-auto px-8 py-4 rounded-2xl glass-panel text-slate-200 hover:text-white font-semibold text-sm hover:bg-emerald-950/40 border-emerald-800/40 transition-all flex items-center justify-center gap-2">
                            <svg class="w-5 h-5 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            Pelajari Cara Kerja
                        </a>
                    </div>

                </div>

                <!-- DYNAMIC STATS GRID -->
                <div class="mt-16 sm:mt-24 grid grid-cols-2 md:grid-cols-4 gap-4 sm:gap-6">
                    
                    <div class="glass-panel p-6 rounded-2xl border border-emerald-500/20 text-center relative overflow-hidden group hover:border-emerald-500/40 transition-all">
                        <div class="absolute top-0 right-0 w-16 h-16 bg-emerald-500/10 rounded-bl-full pointer-events-none"></div>
                        <div class="text-3xl sm:text-4xl font-black text-emerald-400 font-display tracking-tight">
                            {{ number_format($stats['total_sampah_kg'], 0, ',', '.') }}<span class="text-emerald-500 text-xl">+</span>
                        </div>
                        <div class="text-xs sm:text-sm font-medium text-slate-300 mt-1">Kg Sampah Terdaur Ulang</div>
                    </div>

                    <div class="glass-panel p-6 rounded-2xl border border-emerald-500/20 text-center relative overflow-hidden group hover:border-emerald-500/40 transition-all">
                        <div class="absolute top-0 right-0 w-16 h-16 bg-teal-500/10 rounded-bl-full pointer-events-none"></div>
                        <div class="text-3xl sm:text-4xl font-black text-teal-300 font-display tracking-tight">
                            Rp {{ $stats['total_saldo_dicairkan'] }}
                        </div>
                        <div class="text-xs sm:text-sm font-medium text-slate-300 mt-1">Total Saldo Terdistribusi</div>
                    </div>

                    <div class="glass-panel p-6 rounded-2xl border border-emerald-500/20 text-center relative overflow-hidden group hover:border-emerald-500/40 transition-all">
                        <div class="absolute top-0 right-0 w-16 h-16 bg-emerald-500/10 rounded-bl-full pointer-events-none"></div>
                        <div class="text-3xl sm:text-4xl font-black text-emerald-400 font-display tracking-tight">
                            {{ number_format($stats['total_bank_sampah'], 0, ',', '.') }}
                        </div>
                        <div class="text-xs sm:text-sm font-medium text-slate-300 mt-1">Mitra Bank Sampah Aktif</div>
                    </div>

                    <div class="glass-panel p-6 rounded-2xl border border-emerald-500/20 text-center relative overflow-hidden group hover:border-emerald-500/40 transition-all">
                        <div class="absolute top-0 right-0 w-16 h-16 bg-emerald-500/10 rounded-bl-full pointer-events-none"></div>
                        <div class="text-3xl sm:text-4xl font-black text-emerald-300 font-display tracking-tight">
                            {{ number_format($stats['total_nasabah'], 0, ',', '.') }}+
                        </div>
                        <div class="text-xs sm:text-sm font-medium text-slate-300 mt-1">Nasabah Terdaftar</div>
                    </div>

                </div>

            </div>
        </section>

        <!-- INTERACTIVE CALCULATOR SIMULATOR SECTION -->
        <section id="simulator" class="py-16 md:py-24 relative">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                
                <div class="glass-panel p-8 sm:p-12 rounded-3xl border border-emerald-500/25 glow-emerald relative overflow-hidden">
                    <div class="grid grid-cols-1 lg:grid-cols-12 gap-10 items-center">
                        
                        <!-- Left Info -->
                        <div class="lg:col-span-5 space-y-5">
                            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-emerald-950 border border-emerald-700/50 text-emerald-400 text-xs font-semibold">
                                🧮 Simulator Interaktif
                            </div>
                            <h2 class="text-3xl sm:text-4xl font-bold text-white leading-tight">
                                Hitung Estimasi <br class="hidden sm:inline"/>
                                <span class="text-emerald-400">Pendapatan Setoranmu</span>
                            </h2>
                            <p class="text-slate-300 text-sm leading-relaxed">
                                Pilih jenis sampah yang kamu miliki di rumah dan geser jumlah beratnya (Kg) untuk melihat estimasi rupiah dan bonus koin imbalan secara *real-time*.
                            </p>
                            
                            <div class="space-y-3 pt-2">
                                <div class="flex items-center gap-3 text-xs text-slate-300">
                                    <span class="w-5 h-5 rounded-full bg-emerald-500/20 text-emerald-400 flex items-center justify-center font-bold">✓</span>
                                    <span>Penimbangan presisi menggunakan timbangan digital resmi petugas</span>
                                </div>
                                <div class="flex items-center gap-3 text-xs text-slate-300">
                                    <span class="w-5 h-5 rounded-full bg-emerald-500/20 text-emerald-400 flex items-center justify-center font-bold">✓</span>
                                    <span>Bonus Koin Hijau yang dapat ditukar dengan voucher menarik</span>
                                </div>
                            </div>
                        </div>

                        <!-- Right Calculator Widget (Alpine.js) -->
                        <div class="lg:col-span-7" x-data="{
                            selectedType: 'plastik',
                            weight: 5,
                            rates: {
                                'plastik': { name: 'Botol Plastik PET / HDPE', price: 4500, coin: 10, icon: '🥤', co2: 1.2 },
                                'kertas': { name: 'Kardus & Kertas Bekas', price: 2800, coin: 5, icon: '📦', co2: 0.9 },
                                'logam': { name: 'Kaleng Aluminium / Logam', price: 12000, coin: 25, icon: '🥫', co2: 2.5 },
                                'kaca': { name: 'Botol Kaca Utuh', price: 1500, coin: 3, icon: '🍾', co2: 0.4 },
                                'elektronik': { name: 'E-Waste / Sampah Elektronik', price: 18000, coin: 40, icon: '💻', co2: 3.8 }
                            },
                            get currentRate() { return this.rates[this.selectedType] },
                            get totalRupiah() { return this.weight * this.currentRate.price },
                            get totalCoin() { return this.weight * this.currentRate.coin },
                            get totalCo2() { return (this.weight * this.currentRate.co2).toFixed(1) }
                        }">
                            <div class="bg-[#091511] p-6 sm:p-8 rounded-2xl border border-emerald-800/40 space-y-6">
                                
                                <!-- Waste Type Selection Grid -->
                                <div>
                                    <label class="block text-xs font-semibold text-emerald-400 uppercase tracking-wider mb-3">
                                        1. Pilih Kategori Sampah
                                    </label>
                                    <div class="grid grid-cols-2 sm:grid-cols-3 gap-2.5">
                                        <template x-for="(item, key) in rates" :key="key">
                                            <button @click="selectedType = key"
                                                :class="selectedType === key ? 'bg-emerald-600 text-emerald-950 border-emerald-400 shadow-md font-bold' : 'bg-emerald-950/40 text-slate-300 border-emerald-900/60 hover:bg-emerald-900/30'"
                                                class="p-3 rounded-xl border text-xs text-left transition-all flex items-center gap-2">
                                                <span x-text="item.icon" class="text-lg"></span>
                                                <span x-text="item.name" class="truncate"></span>
                                            </button>
                                        </template>
                                    </div>
                                </div>

                                <!-- Weight Slider -->
                                <div>
                                    <div class="flex justify-between items-center mb-2">
                                        <label class="text-xs font-semibold text-emerald-400 uppercase tracking-wider">
                                            2. Tentukan Berat Sampah (Kg)
                                        </label>
                                        <span class="text-sm font-bold text-white bg-emerald-900/60 px-3 py-1 rounded-lg border border-emerald-700/50">
                                            <span x-text="weight"></span> Kg
                                        </span>
                                    </div>
                                    <input type="range" min="1" max="100" x-model="weight"
                                        class="w-full h-2.5 bg-emerald-950 rounded-lg appearance-none cursor-pointer accent-emerald-400">
                                    <div class="flex justify-between text-[11px] text-slate-400 mt-1">
                                        <span>1 Kg</span>
                                        <span>50 Kg</span>
                                        <span>100 Kg</span>
                                    </div>
                                </div>

                                <!-- Result Output Box -->
                                <div class="p-5 rounded-xl bg-gradient-to-br from-emerald-950 to-teal-950 border border-emerald-500/30 space-y-4">
                                    <div class="grid grid-cols-2 gap-4">
                                        <div>
                                            <div class="text-[11px] text-emerald-400/80 font-medium">Estimasi Saldo Terima</div>
                                            <div class="text-2xl sm:text-3xl font-extrabold text-white font-display">
                                                Rp <span x-text="totalRupiah.toLocaleString('id-ID')"></span>
                                            </div>
                                        </div>
                                        <div>
                                            <div class="text-[11px] text-amber-400/80 font-medium">Bonus Koin Hijau</div>
                                            <div class="text-2xl sm:text-3xl font-extrabold text-amber-400 font-display flex items-center gap-1">
                                                +<span x-text="totalCoin.toLocaleString('id-ID')"></span> 🪙
                                            </div>
                                        </div>
                                    </div>

                                    <div class="pt-3 border-t border-emerald-900/50 flex justify-between items-center text-xs">
                                        <span class="text-slate-300">Dampak Lingkungan Ditimbulkan:</span>
                                        <span class="text-emerald-400 font-bold flex items-center gap-1">
                                            🌿 Reduksi <span x-text="totalCo2"></span> Kg emisi CO₂
                                        </span>
                                    </div>
                                </div>

                            </div>
                        </div>

                    </div>
                </div>

            </div>
        </section>

        <!-- CARA KERJA (STEPS WORKFLOW) -->
        <section id="alursistem" class="py-16 md:py-24 relative">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                
                <div class="text-center max-w-3xl mx-auto space-y-4 mb-16">
                    <h2 class="text-xs font-bold text-emerald-400 uppercase tracking-widest">Alur Penyetoran Sampah</h2>
                    <h3 class="text-3xl sm:text-5xl font-bold text-white">4 Langkah Mudah Hasilkan Cuan Hijau</h3>
                    <p class="text-slate-300 text-sm">Proses cepat, transparan, dan dapat dipantau secara real-time langsung melalui perangkat Anda.</p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                    
                    <!-- Step 1 -->
                    <div class="glass-panel p-6 rounded-2xl border border-emerald-900/40 relative group hover:border-emerald-500/40 transition-all">
                        <div class="w-12 h-12 rounded-xl bg-emerald-950 text-emerald-400 border border-emerald-700/50 flex items-center justify-center font-extrabold text-lg mb-5 group-hover:scale-110 transition-transform">
                            01
                        </div>
                        <h4 class="text-lg font-bold text-white mb-2">Pilah Sampah</h4>
                        <p class="text-slate-300 text-xs leading-relaxed">Pisahkan sampah anorganik rumah tangga sesuai jenisnya (plastik, kertas, logam, botol kaca, atau elektronik).</p>
                    </div>

                    <!-- Step 2 -->
                    <div class="glass-panel p-6 rounded-2xl border border-emerald-900/40 relative group hover:border-emerald-500/40 transition-all">
                        <div class="w-12 h-12 rounded-xl bg-emerald-950 text-emerald-400 border border-emerald-700/50 flex items-center justify-center font-extrabold text-lg mb-5 group-hover:scale-110 transition-transform">
                            02
                        </div>
                        <h4 class="text-lg font-bold text-white mb-2">Pilih Bank Sampah</h4>
                        <p class="text-slate-300 text-xs leading-relaxed">Cari lokasi Bank Sampah terdekat melalui aplikasi Nasabah atau ajukan jadwal penjemputan oleh Petugas.</p>
                    </div>

                    <!-- Step 3 -->
                    <div class="glass-panel p-6 rounded-2xl border border-emerald-900/40 relative group hover:border-emerald-500/40 transition-all">
                        <div class="w-12 h-12 rounded-xl bg-emerald-950 text-emerald-400 border border-emerald-700/50 flex items-center justify-center font-extrabold text-lg mb-5 group-hover:scale-110 transition-transform">
                            03
                        </div>
                        <h4 class="text-lg font-bold text-white mb-2">Penimbangan Digital</h4>
                        <p class="text-slate-300 text-xs leading-relaxed">Petugas menginput hasil penimbangan ke sistem via Portal Petugas dengan konfirmasi harga transparan.</p>
                    </div>

                    <!-- Step 4 -->
                    <div class="glass-panel p-6 rounded-2xl border border-emerald-900/40 relative group hover:border-emerald-500/40 transition-all">
                        <div class="w-12 h-12 rounded-xl bg-emerald-950 text-amber-400 border border-amber-700/50 flex items-center justify-center font-extrabold text-lg mb-5 group-hover:scale-110 transition-transform">
                            04
                        </div>
                        <h4 class="text-lg font-bold text-white mb-2">Cairkan Saldo</h4>
                        <p class="text-slate-300 text-xs leading-relaxed">Saldo otomatis bertambah di dompet digital aplikasi Anda dan siap dicairkan ke DANA, OVO, GoPay, atau Bank.</p>
                    </div>

                </div>

            </div>
        </section>

        <!-- HARGA SAMPAH DIRECTORY -->
        <section id="harga" class="py-16 md:py-24 relative bg-emerald-950/20">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                
                <div class="flex flex-col md:flex-row md:items-end justify-between mb-12 gap-4">
                    <div>
                        <h2 class="text-xs font-bold text-emerald-400 uppercase tracking-widest mb-2">Transparansi Harga</h2>
                        <h3 class="text-3xl sm:text-4xl font-bold text-white">Daftar Harga Sampah Terbaru</h3>
                    </div>
                    <span class="text-xs text-slate-400 flex items-center gap-1.5">
                        <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                        Diperbarui secara real-time dari mitra Bank Sampah
                    </span>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                    
                    @if(count($hargaSampah) > 0)
                        @foreach($hargaSampah as $item)
                            <div class="glass-panel glass-panel-hover p-6 rounded-2xl border border-emerald-900/50">
                                <div class="flex items-center justify-between mb-4">
                                    <div class="w-10 h-10 rounded-xl bg-emerald-950 border border-emerald-800 flex items-center justify-center text-emerald-400 font-bold">
                                        ♻️
                                    </div>
                                    <span class="text-[10px] uppercase font-bold tracking-wider px-2.5 py-1 rounded-full bg-emerald-500/10 text-emerald-400 border border-emerald-500/20">
                                        {{ $item->status ?? 'Aktif' }}
                                    </span>
                                </div>
                                <h4 class="text-lg font-bold text-white mb-1">{{ $item->jenis_sampah }}</h4>
                                <div class="flex items-baseline gap-1 mt-3">
                                    <span class="text-2xl font-black text-emerald-400 font-display">Rp {{ number_format($item->harga_per_kg, 0, ',', '.') }}</span>
                                    <span class="text-xs text-slate-300">/ kg</span>
                                </div>
                            </div>
                        @endforeach
                    @else
                        <!-- Preset Sample Directory Cards if DB is empty -->
                        <div class="glass-panel glass-panel-hover p-6 rounded-2xl border border-emerald-900/50">
                            <div class="flex items-center justify-between mb-3">
                                <span class="text-2xl">🥤</span>
                                <span class="text-[10px] uppercase font-bold px-2.5 py-0.5 rounded-full bg-emerald-500/10 text-emerald-400 border border-emerald-500/20">Populer</span>
                            </div>
                            <h4 class="text-lg font-bold text-white">Botol Plastik PET (Bening)</h4>
                            <p class="text-xs text-slate-300 mt-1 mb-4">Botol air mineral bersih tanpa label & tutup</p>
                            <div class="flex items-baseline gap-1 pt-2 border-t border-emerald-900/40">
                                <span class="text-2xl font-extrabold text-emerald-400 font-display">Rp 4.500</span>
                                <span class="text-xs text-slate-300">/ Kg</span>
                            </div>
                        </div>

                        <div class="glass-panel glass-panel-hover p-6 rounded-2xl border border-emerald-900/50">
                            <div class="flex items-center justify-between mb-3">
                                <span class="text-2xl">📦</span>
                                <span class="text-[10px] uppercase font-bold px-2.5 py-0.5 rounded-full bg-emerald-500/10 text-emerald-400 border border-emerald-500/20">Tinggi Stok</span>
                            </div>
                            <h4 class="text-lg font-bold text-white">Kardus & Paperbox Bekas</h4>
                            <p class="text-xs text-slate-300 mt-1 mb-4">Kardus kemasan kering dan terlipat rapi</p>
                            <div class="flex items-baseline gap-1 pt-2 border-t border-emerald-900/40">
                                <span class="text-2xl font-extrabold text-emerald-400 font-display">Rp 2.800</span>
                                <span class="text-xs text-slate-300">/ Kg</span>
                            </div>
                        </div>

                        <div class="glass-panel glass-panel-hover p-6 rounded-2xl border border-emerald-900/50">
                            <div class="flex items-center justify-between mb-3">
                                <span class="text-2xl">🥫</span>
                                <span class="text-[10px] uppercase font-bold px-2.5 py-0.5 rounded-full bg-amber-500/10 text-amber-400 border border-amber-500/20">Nilai Tinggi</span>
                            </div>
                            <h4 class="text-lg font-bold text-white">Kaleng Aluminium & Minuman</h4>
                            <p class="text-xs text-slate-300 mt-1 mb-4">Kaleng minuman kemasan dipipihkan</p>
                            <div class="flex items-baseline gap-1 pt-2 border-t border-emerald-900/40">
                                <span class="text-2xl font-extrabold text-emerald-400 font-display">Rp 12.000</span>
                                <span class="text-xs text-slate-300">/ Kg</span>
                            </div>
                        </div>

                        <div class="glass-panel glass-panel-hover p-6 rounded-2xl border border-emerald-900/50">
                            <div class="flex items-center justify-between mb-3">
                                <span class="text-2xl">🍾</span>
                                <span class="text-[10px] uppercase font-bold px-2.5 py-0.5 rounded-full bg-emerald-500/10 text-emerald-400 border border-emerald-500/20">Aktif</span>
                            </div>
                            <h4 class="text-lg font-bold text-white">Botol Kaca Utuh & Sirop</h4>
                            <p class="text-xs text-slate-300 mt-1 mb-4">Kaca utuh tanpa retak/pecah</p>
                            <div class="flex items-baseline gap-1 pt-2 border-t border-emerald-900/40">
                                <span class="text-2xl font-extrabold text-emerald-400 font-display">Rp 1.500</span>
                                <span class="text-xs text-slate-300">/ Kg</span>
                            </div>
                        </div>

                        <div class="glass-panel glass-panel-hover p-6 rounded-2xl border border-emerald-900/50">
                            <div class="flex items-center justify-between mb-3">
                                <span class="text-2xl">💻</span>
                                <span class="text-[10px] uppercase font-bold px-2.5 py-0.5 rounded-full bg-amber-500/10 text-amber-400 border border-amber-500/20">Special E-Waste</span>
                            </div>
                            <h4 class="text-lg font-bold text-white">Komponen Elektronik Bekas</h4>
                            <p class="text-xs text-slate-300 mt-1 mb-4">Motherboard, kabel, HP lama, gadget rusak</p>
                            <div class="flex items-baseline gap-1 pt-2 border-t border-emerald-900/40">
                                <span class="text-2xl font-extrabold text-emerald-400 font-display">Rp 18.000</span>
                                <span class="text-xs text-slate-300">/ Kg</span>
                            </div>
                        </div>

                        <div class="glass-panel glass-panel-hover p-6 rounded-2xl border border-emerald-900/50">
                            <div class="flex items-center justify-between mb-3">
                                <span class="text-2xl">🛢️</span>
                                <span class="text-[10px] uppercase font-bold px-2.5 py-0.5 rounded-full bg-emerald-500/10 text-emerald-400 border border-emerald-500/20">Bio Energy</span>
                            </div>
                            <h4 class="text-lg font-bold text-white">Minyak Jelantah Dapur</h4>
                            <p class="text-xs text-slate-300 mt-1 mb-4">Minyak goreng bekas pakai dalam jerigen</p>
                            <div class="flex items-baseline gap-1 pt-2 border-t border-emerald-900/40">
                                <span class="text-2xl font-extrabold text-emerald-400 font-display">Rp 6.500</span>
                                <span class="text-xs text-slate-300">/ Liter</span>
                            </div>
                        </div>
                    @endif

                </div>

            </div>
        </section>

        <!-- SYSTEM & ROLE PORTALS SHOWCASE -->
        <section id="ekosistem" class="py-16 md:py-24 relative">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                
                <div class="text-center max-w-3xl mx-auto space-y-4 mb-16">
                    <h2 class="text-xs font-bold text-emerald-400 uppercase tracking-widest">Akses Ekosistem SetorIn</h2>
                    <h3 class="text-3xl sm:text-5xl font-bold text-white">Terhubung untuk Semua Peran</h3>
                    <p class="text-slate-300 text-sm">Platform terpadu yang memfasilitasi Nasabah, Petugas Lapangan, dan Administrator dalam satu infrastruktur.</p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                    
                    <!-- Nasabah Card -->
                    <div class="glass-panel p-8 rounded-3xl border border-emerald-500/20 flex flex-col justify-between hover:border-emerald-500/50 transition-all">
                        <div class="space-y-4">
                            <div class="w-14 h-14 rounded-2xl bg-emerald-500/10 border border-emerald-500/30 flex items-center justify-center text-3xl">
                                📱
                            </div>
                            <h4 class="text-xl font-bold text-white">Aplikasi Mobile Nasabah</h4>
                            <p class="text-slate-300 text-xs leading-relaxed">
                                Aplikasi seluler untuk masyarakat menyetor sampah, memantau saldo, mengklaim koin misi hijau, dan melakukan penarikan saldo ke e-wallet.
                            </p>
                            <ul class="space-y-2 text-xs text-slate-300 pt-2">
                                <li class="flex items-center gap-2">🟢 Registrasi & OTP Verifikasi HP</li>
                                <li class="flex items-center gap-2">🟢 Peta Lokasi Bank Sampah</li>
                                <li class="flex items-center gap-2">🟢 Penarikan DANA, OVO, GoPay</li>
                            </ul>
                        </div>
                        <div class="pt-6 mt-6 border-t border-emerald-900/40">
                            <span class="text-xs text-emerald-400 font-semibold flex items-center gap-2">
                                🔗 REST API Endpoint: <code class="text-[11px] bg-emerald-950 px-2 py-1 rounded text-emerald-300">/api/nasabah/*</code>
                            </span>
                        </div>
                    </div>

                    <!-- Petugas Card -->
                    <div class="glass-panel p-8 rounded-3xl border border-emerald-500/30 flex flex-col justify-between hover:border-emerald-500/60 transition-all shadow-lg glow-emerald-sm">
                        <div class="space-y-4">
                            <div class="w-14 h-14 rounded-2xl bg-teal-500/10 border border-teal-500/30 flex items-center justify-center text-3xl">
                                🛵
                            </div>
                            <h4 class="text-xl font-bold text-white">Portal Petugas Lapangan</h4>
                            <p class="text-slate-300 text-xs leading-relaxed">
                                Panel khusus petugas untuk mengonfirmasi transaksi penyerahan, melakukan penimbangan digital, dan mengelola jadwal penjemputan sampah.
                            </p>
                            <ul class="space-y-2 text-xs text-slate-300 pt-2">
                                <li class="flex items-center gap-2">🟢 Verifikasi & Input Timbangan</li>
                                <li class="flex items-center gap-2">🟢 Manajemen Penjemputan Sampah</li>
                                <li class="flex items-center gap-2">🟢 Rekap Laporan Harian Petugas</li>
                            </ul>
                        </div>
                        <div class="pt-6 mt-6 border-t border-emerald-900/40">
                            <a href="{{ url('/petugas') }}" class="w-full py-3 rounded-xl bg-emerald-950 hover:bg-emerald-900 border border-emerald-700 text-emerald-300 font-bold text-xs flex items-center justify-center gap-2 transition-all">
                                Masuk Portal Petugas (/petugas) →
                            </a>
                        </div>
                    </div>

                    <!-- Admin Card -->
                    <div class="glass-panel p-8 rounded-3xl border border-emerald-500/20 flex flex-col justify-between hover:border-emerald-500/50 transition-all">
                        <div class="space-y-4">
                            <div class="w-14 h-14 rounded-2xl bg-emerald-500/10 border border-emerald-500/30 flex items-center justify-center text-3xl">
                                ⚙️
                            </div>
                            <h4 class="text-xl font-bold text-white">Panel Utama Administrator</h4>
                            <p class="text-slate-300 text-xs leading-relaxed">
                                Dashboard Filament Admin modern untuk verifikasi penarikan dana, manajemen harga sampah, pemantauan pengguna, dan ekspor laporan transaksi.
                            </p>
                            <ul class="space-y-2 text-xs text-slate-300 pt-2">
                                <li class="flex items-center gap-2">🟢 Persetujuan Penarikan Saldo</li>
                                <li class="flex items-center gap-2">🟢 Pengaturan Harga & Koin Sampah</li>
                                <li class="flex items-center gap-2">🟢 Ekspor Laporan CSV & Excel</li>
                            </ul>
                        </div>
                        <div class="pt-6 mt-6 border-t border-emerald-900/40">
                            <a href="{{ url('/admin') }}" class="w-full py-3 rounded-xl bg-gradient-to-r from-emerald-500 to-teal-500 hover:from-emerald-400 hover:to-teal-400 text-emerald-950 font-extrabold text-xs flex items-center justify-center gap-2 shadow-md transition-all">
                                Masuk Dashboard Admin (/admin) →
                            </a>
                        </div>
                    </div>

                </div>

            </div>
        </section>

        <!-- FAQ ACCORDION SECTION -->
        <section id="faq" class="py-16 md:py-24 relative bg-emerald-950/10">
            <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
                
                <div class="text-center space-y-4 mb-12">
                    <h2 class="text-xs font-bold text-emerald-400 uppercase tracking-widest">FAQ</h2>
                    <h3 class="text-3xl font-bold text-white">Pertanyaan Sering Diajukan</h3>
                </div>

                <div class="space-y-4">
                    
                    <details class="glass-panel p-6 rounded-2xl border border-emerald-900/50 group cursor-pointer">
                        <summary class="font-bold text-white text-base flex justify-between items-center list-none">
                            <span>Bagaimana cara kerja pencairan saldo hasil penyetoran sampah?</span>
                            <span class="text-emerald-400 group-open:rotate-180 transition-transform font-bold">↓</span>
                        </summary>
                        <p class="text-slate-300 text-xs leading-relaxed mt-4 pt-3 border-t border-emerald-900/40">
                            Setelah sampah Anda ditimbang dan dikonfirmasi oleh Petugas via aplikasi, saldo akan langsung masuk ke dompet digital akun SetorIn Anda. Anda dapat mengajukan penarikan kapan saja ke rekening bank atau dompet digital (DANA, OVO, GoPay) dengan proses persetujuan cepat dari Admin.
                        </p>
                    </details>

                    <details class="glass-panel p-6 rounded-2xl border border-emerald-900/50 group cursor-pointer">
                        <summary class="font-bold text-white text-base flex justify-between items-center list-none">
                            <span>Apakah ada batasan minimal berat sampah yang bisa disetor?</span>
                            <span class="text-emerald-400 group-open:rotate-180 transition-transform font-bold">↓</span>
                        </summary>
                        <p class="text-slate-300 text-xs leading-relaxed mt-4 pt-3 border-t border-emerald-900/40">
                            Tidak ada batasan minimal jika Anda mengantar langsung ke Bank Sampah mitra. Namun untuk layanan penjemputan oleh Petugas Lapangan, disarankan minimal total berat sampah anorganik adalah 3-5 Kg.
                        </p>
                    </details>

                    <details class="glass-panel p-6 rounded-2xl border border-emerald-900/50 group cursor-pointer">
                        <summary class="font-bold text-white text-base flex justify-between items-center list-none">
                            <span>Apa perbedaan Saldo Rupiah dan Koin Hijau SetorIn?</span>
                            <span class="text-emerald-400 group-open:rotate-180 transition-transform font-bold">↓</span>
                        </summary>
                        <p class="text-slate-300 text-xs leading-relaxed mt-4 pt-3 border-t border-emerald-900/40">
                            Saldo Rupiah berasal dari harga timbangan sampah yang Anda setor dan dapat dicairkan langsung menjadi uang tunai/e-wallet. Koin Hijau adalah poin bonus gamifikasi yang didapatkan dari penyelesaian misi daur ulang harian/mingguan dan dapat ditukarkan dengan hadiah khusus.
                        </p>
                    </details>

                    <details class="glass-panel p-6 rounded-2xl border border-emerald-900/50 group cursor-pointer">
                        <summary class="font-bold text-white text-base flex justify-between items-center list-none">
                            <span>Bagaimana cara mendaftar sebagai Mitra Bank Sampah atau Petugas?</span>
                            <span class="text-emerald-400 group-open:rotate-180 transition-transform font-bold">↓</span>
                        </summary>
                        <p class="text-slate-300 text-xs leading-relaxed mt-4 pt-3 border-t border-emerald-900/40">
                            Pendaftaran Petugas dan Bank Sampah dikelola langsung oleh Administrator melalui Panel Admin (`/admin`). Pengurus Bank Sampah lokal dapat mengajukan permohonan ke admin pusat untuk pembuatan akun resmi petugas.
                        </p>
                    </details>

                </div>

            </div>
        </section>

    </main>

    <!-- FOOTER -->
    <footer class="border-t border-emerald-900/30 bg-[#040A08] text-slate-400 text-xs relative z-10">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8 mb-10">
                
                <!-- Col 1 -->
                <div class="space-y-4 md:col-span-2">
                    <div class="flex items-center gap-2">
                        <div class="w-8 h-8 rounded-lg bg-emerald-500 flex items-center justify-center text-emerald-950 font-bold">🌱</div>
                        <span class="text-xl font-bold font-display text-white">SetorIn</span>
                    </div>
                    <p class="text-slate-400 max-w-sm text-xs leading-relaxed">
                        Ekosistem pengolahan dan daur ulang sampah pintar berbasis teknologi web & mobile. Mendorong masyarakat hidup bersih dan berkelanjutan melalui sistem eco-rewards digital.
                    </p>
                    <div class="flex items-center gap-2 text-emerald-400 text-xs pt-1">
                        <span class="w-2.5 h-2.5 rounded-full bg-emerald-400 animate-pulse"></span>
                        <span class="font-mono">System API v2.0 & Backend Status: Operational</span>
                    </div>
                </div>

                <!-- Col 2 -->
                <div class="space-y-3">
                    <h5 class="text-white font-bold text-sm">Tautan Portal</h5>
                    <ul class="space-y-2">
                        <li><a href="{{ url('/admin') }}" class="hover:text-emerald-400 transition-colors">Portal Admin Filament</a></li>
                        <li><a href="{{ url('/petugas') }}" class="hover:text-emerald-400 transition-colors">Portal Petugas Lapangan</a></li>
                        <li><a href="{{ url('/api/documentation') }}" class="hover:text-emerald-400 transition-colors">Dokumentasi REST API</a></li>
                        <li><a href="#simulator" class="hover:text-emerald-400 transition-colors">Kalkulator Setor Sampah</a></li>
                    </ul>
                </div>

                <!-- Col 3 -->
                <div class="space-y-3">
                    <h5 class="text-white font-bold text-sm">Teknologi</h5>
                    <ul class="space-y-2">
                        <li class="flex items-center gap-1.5">⚡ Laravel Framework</li>
                        <li class="flex items-center gap-1.5">🎨 Tailwind CSS v4</li>
                        <li class="flex items-center gap-1.5">🛡️ Laravel Sanctum API</li>
                        <li class="flex items-center gap-1.5">🖥️ Filament Admin Panel</li>
                    </ul>
                </div>

            </div>

            <div class="pt-8 border-t border-emerald-900/40 flex flex-col sm:flex-row justify-between items-center gap-4">
                <p>© {{ date('Y') }} SetorIn Platform. Seluruh Hak Cipta Dilindungi.</p>
                <div class="flex gap-6">
                    <a href="#" class="hover:text-emerald-400">Privasi</a>
                    <a href="#" class="hover:text-emerald-400">Syarat & Ketentuan</a>
                    <a href="#" class="hover:text-emerald-400">Kontak Support</a>
                </div>
            </div>
        </div>
    </footer>

</body>
</html>
