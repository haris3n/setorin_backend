<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Setor.in — Platform Bank Sampah Digital</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    @if(file_exists(public_path('build/manifest.json')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @endif
    <style>
        body {
            font-family: 'Plus Jakarta Sans', system-ui, sans-serif;
            background-color: #F0FDF4;
            color: #111827;
            margin: 0;
            padding: 0;
            overflow-x: hidden;
        }

        /* Animations */
        @keyframes fadeUp {
            from { opacity: 0; transform: translateY(20px); }
            to   { opacity: 1; transform: translateY(0); }
        }

        .anim-fade-up {
            opacity: 0;
            animation: fadeUp 0.4s ease-out forwards;
        }

        .delay-100 { animation-delay: 0.1s; }
        .delay-200 { animation-delay: 0.2s; }
        .delay-300 { animation-delay: 0.3s; }
        .delay-400 { animation-delay: 0.4s; }

        /* Hero Background Decorations */
        .hero-bg {
            position: relative;
            overflow: hidden;
        }

        .hero-bg::before, .hero-bg::after {
            content: '';
            position: absolute;
            width: 400px;
            height: 400px;
            background-color: #16A34A;
            border-radius: 50%;
            opacity: 0.05;
            filter: blur(60px);
            z-index: 0;
            pointer-events: none;
        }

        .hero-bg::before {
            top: -100px;
            right: -100px;
        }

        .hero-bg::after {
            bottom: -100px;
            left: -100px;
        }

        .content-relative {
            position: relative;
            z-index: 10;
        }

        /* Card Hover */
        .portal-card {
            transition: all 0.2s ease;
        }
        
        .portal-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 32px rgba(0,0,0,0.12);
        }

        .btn-hover-primary { transition: background-color 0.15s ease; }
        .btn-hover-primary:hover { background-color: #15803D; }

        .btn-hover-outline { transition: background-color 0.15s ease; }
        .btn-hover-outline:hover { background-color: #F0FDF4; }
    </style>
</head>
<body class="flex flex-col min-h-screen">

    <!-- [A] NAVBAR -->
    <nav class="sticky top-0 z-50 bg-white border-b border-[#E5E7EB] h-[56px] flex items-center px-[5%] justify-between w-full">
        <div class="flex items-center gap-2 max-w-[1200px] mx-auto w-full justify-between">
            <div class="flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" aria-hidden="true" class="w-6 h-6 text-[#16A34A]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                </svg>
                <span class="text-[20px] font-bold text-[#14532D]">Setor.in</span>
            </div>
            <div class="hidden md:block text-[12px] text-[#9CA3AF]">
                v1.0 &middot; Sistem Bank Sampah Digital
            </div>
        </div>
    </nav>

    <main class="flex-grow hero-bg">
        <!-- [B] HERO SECTION -->
        <section class="content-relative pt-[40px] pb-[40px] md:pt-[64px] md:pb-[12px] px-[5%] text-center max-w-[680px] mx-auto">
            
            <div class="inline-block bg-[#DCFCE7] border border-[#86EFAC] text-[#14532D] text-[13px] font-medium rounded-full py-[6px] px-[16px] mb-[20px] anim-fade-up">
                &#x267B; Platform Bank Sampah Digital
            </div>

            <h1 class="text-[28px] md:text-[40px] font-bold text-[#111827] leading-tight mb-[16px] anim-fade-up delay-100">
                Selamat Datang di <br/>
                <span class="text-[#16A34A]">Setor.in</span>
            </h1>

            <p class="text-[17px] text-[#6B7280] max-w-[520px] mx-auto mb-[40px] anim-fade-up delay-200">
                Sistem digital pengelolaan transaksi penyetoran sampah untuk Admin dan Petugas Bank Sampah.
            </p>

        </section>

        <!-- [C] PORTAL SELECTION -->
        <section class="content-relative px-[5%] pb-[64px] max-w-[760px] mx-auto">
            <div class="flex flex-col md:flex-row gap-[20px] items-stretch justify-center">
                
                <!-- CARD ADMIN -->
                <div class="portal-card bg-white border border-[#E5E7EB] rounded-[16px] p-[28px] shadow-[0_2px_16px_rgba(0,0,0,0.07)] w-full md:w-1/2 flex flex-col anim-fade-up delay-300">
                    <div class="bg-[#DCFCE7] rounded-[12px] w-[48px] h-[48px] flex items-center justify-center mb-[16px]">
                        <svg xmlns="http://www.w3.org/2000/svg" aria-hidden="true" class="h-[22px] w-[22px] text-[#16A34A]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                        </svg>
                    </div>
                    
                    <h2 class="text-[18px] font-semibold text-[#111827]">Portal Admin</h2>
                    
                    <p class="text-[14px] text-[#6B7280] my-[8px] mb-[20px]">
                        Kelola data pengguna, harga sampah, misi, laporan sistem, dan persetujuan penarikan saldo.
                    </p>

                    <div class="mb-[20px]">
                        <span class="bg-[#FEF3C7] border border-[#D97706] text-[#92400E] text-[11px] font-semibold uppercase rounded-[6px] py-[3px] px-[10px]">
                            Akses Penuh Sistem
                        </span>
                    </div>

                    <div class="mt-auto">
                        <a href="{{ url('/admin/login') }}" class="btn-hover-primary inline-block w-full text-center bg-[#16A34A] text-white font-semibold text-[14px] rounded-[10px] py-[12px] px-[20px]">
                            Masuk sebagai Admin &rarr;
                        </a>
                    </div>
                </div>

                <!-- CARD PETUGAS -->
                <div class="portal-card bg-white border border-[#E5E7EB] rounded-[16px] p-[28px] shadow-[0_2px_16px_rgba(0,0,0,0.07)] w-full md:w-1/2 flex flex-col anim-fade-up delay-400">
                    <div class="bg-[#DCFCE7] rounded-[12px] w-[48px] h-[48px] flex items-center justify-center mb-[16px]">
                        <svg xmlns="http://www.w3.org/2000/svg" aria-hidden="true" class="h-[22px] w-[22px] text-[#16A34A]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                        </svg>
                    </div>
                    
                    <h2 class="text-[18px] font-semibold text-[#111827]">Portal Petugas</h2>
                    
                    <p class="text-[14px] text-[#6B7280] my-[8px] mb-[20px]">
                        Proses transaksi penyetoran sampah nasabah, kelola jadwal operasional, dan buat laporan bank sampah.
                    </p>

                    <div class="mb-[20px]">
                        <span class="bg-[#DCFCE7] border border-[#16A34A] text-[#14532D] text-[11px] font-semibold uppercase rounded-[6px] py-[3px] px-[10px]">
                            Akses Operasional
                        </span>
                    </div>

                    <div class="mt-auto">
                        <a href="{{ url('/petugas/login') }}" class="btn-hover-outline inline-block w-full text-center bg-white border-2 border-[#16A34A] text-[#16A34A] font-semibold text-[14px] rounded-[10px] py-[10px] px-[20px] box-border leading-[20px]">
                            Masuk sebagai Petugas &rarr;
                        </a>
                    </div>
                </div>

            </div>
        </section>
        
        <!-- [D] INFO STRIP -->
        <section class="content-relative bg-white border-t border-[#E5E7EB] py-[20px] px-[5%]">
            <div class="max-w-[1000px] mx-auto flex flex-col md:flex-row justify-center items-center gap-[20px] md:gap-[40px] text-center">
                
                <div>
                    <div class="text-[13px] font-semibold text-[#111827]">&#x267B; Bank Sampah Digital</div>
                    <div class="text-[12px] text-[#6B7280]">Platform modern dan terintegrasi</div>
                </div>
                
                <div class="hidden md:block w-px h-[30px] bg-[#E5E7EB]"></div>
                
                <div>
                    <div class="text-[13px] font-semibold text-[#111827]">3 Jenis Pengguna</div>
                    <div class="text-[12px] text-[#6B7280]">Admin, Petugas, dan Nasabah</div>
                </div>
                
                <div class="hidden md:block w-px h-[30px] bg-[#E5E7EB]"></div>
                
                <div>
                    <div class="text-[13px] font-semibold text-[#111827]">Real-time Transaksi</div>
                    <div class="text-[12px] text-[#6B7280]">Proses otomatis & pencatatan digital</div>
                </div>

            </div>
        </section>

    </main>

    <!-- [E] FOOTER -->
    <footer class="bg-[#F9FAFB] border-t border-[#E5E7EB] py-[16px] px-[5%]">
        <div class="max-w-[1200px] mx-auto flex flex-col md:flex-row justify-between items-center text-[13px] text-[#9CA3AF] text-center md:text-left gap-[8px]">
            <div>&copy; 2026 Setor.in &middot; Platform Digital Bank Sampah</div>
            <div>Kelompok 8 &middot; D3 TI / D4 RPL &middot; POLINDRA</div>
        </div>
    </footer>

</body>
</html>
