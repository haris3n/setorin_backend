<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Setor.in — Sistem Bank Sampah Digital | Dinas Lingkungan Hidup</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
            --green-50:  #f0fdf4;
            --green-100: #dcfce7;
            --green-200: #bbf7d0;
            --green-500: #22c55e;
            --green-600: #16a34a;
            --green-700: #15803d;
            --green-800: #166534;
            --green-900: #14532d;
            --slate-50:  #f8fafc;
            --slate-100: #f1f5f9;
            --slate-200: #e2e8f0;
            --slate-400: #94a3b8;
            --slate-500: #64748b;
            --slate-600: #475569;
            --slate-700: #334155;
            --slate-800: #1e293b;
            --slate-900: #0f172a;
            --amber-100: #fef3c7;
            --amber-600: #d97706;
            --amber-800: #92400e;
        }

        html { scroll-behavior: smooth; }

        body {
            font-family: 'Plus Jakarta Sans', system-ui, sans-serif;
            background: var(--green-50);
            color: var(--slate-800);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            line-height: 1.5;
        }

        /* ── Navbar ── */
        .navbar {
            position: sticky;
            top: 0;
            z-index: 100;
            background: #fff;
            border-bottom: 1px solid var(--slate-200);
            box-shadow: 0 1px 3px rgba(0,0,0,.04);
        }

        .navbar-inner {
            max-width: 1100px;
            margin: 0 auto;
            padding: 0 24px;
            height: 64px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
        }

        .brand {
            display: flex;
            align-items: center;
            gap: 10px;
            text-decoration: none;
        }

        .brand-icon {
            width: 36px;
            height: 36px;
            background: linear-gradient(135deg, var(--green-600), var(--green-800));
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .brand-icon svg { width: 20px; height: 20px; color: #fff; }

        .brand-name {
            font-size: 1.25rem;
            font-weight: 800;
            color: var(--green-900);
            letter-spacing: -0.02em;
        }

        .navbar-meta {
            font-size: 0.75rem;
            color: var(--slate-400);
            text-align: right;
            line-height: 1.4;
        }

        .navbar-meta strong {
            display: block;
            color: var(--slate-600);
            font-weight: 600;
        }

        /* ── Hero ── */
        .hero {
            position: relative;
            overflow: hidden;
            background: linear-gradient(160deg, #ecfdf5 0%, #f0fdf4 40%, #f8fafc 100%);
            border-bottom: 1px solid var(--green-200);
        }

        .hero::before {
            content: '';
            position: absolute;
            top: -120px;
            right: -80px;
            width: 360px;
            height: 360px;
            background: var(--green-500);
            border-radius: 50%;
            opacity: 0.06;
            pointer-events: none;
        }

        .hero-inner {
            position: relative;
            max-width: 1100px;
            margin: 0 auto;
            padding: 48px 24px 40px;
            text-align: center;
        }

        .badge-dlh {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: #fff;
            border: 1px solid var(--green-200);
            color: var(--green-800);
            font-size: 0.8125rem;
            font-weight: 600;
            padding: 8px 18px;
            border-radius: 999px;
            margin-bottom: 20px;
            box-shadow: 0 1px 4px rgba(22,163,74,.08);
        }

        .badge-dlh svg { width: 16px; height: 16px; flex-shrink: 0; }

        .hero h1 {
            font-size: clamp(1.75rem, 4vw, 2.5rem);
            font-weight: 800;
            color: var(--slate-900);
            letter-spacing: -0.03em;
            line-height: 1.2;
            margin-bottom: 14px;
        }

        .hero h1 span { color: var(--green-600); }

        .hero-desc {
            font-size: 1.0625rem;
            color: var(--slate-500);
            max-width: 560px;
            margin: 0 auto 28px;
        }

        .hero-stats {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 12px;
        }

        .hero-stat {
            background: #fff;
            border: 1px solid var(--slate-200);
            border-radius: 10px;
            padding: 10px 18px;
            font-size: 0.8125rem;
            color: var(--slate-600);
        }

        .hero-stat strong {
            color: var(--green-700);
            font-weight: 700;
        }

        /* ── Portal cards ── */
        .portals {
            flex: 1;
            max-width: 1100px;
            margin: 0 auto;
            padding: 40px 24px 48px;
            width: 100%;
        }

        .portals-label {
            text-align: center;
            font-size: 0.875rem;
            font-weight: 600;
            color: var(--slate-500);
            text-transform: uppercase;
            letter-spacing: 0.06em;
            margin-bottom: 24px;
        }

        .portals-grid {
            display: grid;
            grid-template-columns: 1fr;
            gap: 20px;
        }

        @media (min-width: 640px) {
            .portals-grid { grid-template-columns: 1fr 1fr; }
        }

        .portal-card {
            background: #fff;
            border: 1px solid var(--slate-200);
            border-radius: 16px;
            padding: 28px;
            display: flex;
            flex-direction: column;
            box-shadow: 0 2px 12px rgba(0,0,0,.05);
            transition: transform .2s ease, box-shadow .2s ease;
        }

        .portal-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 28px rgba(0,0,0,.1);
        }

        .portal-card-icon {
            width: 52px;
            height: 52px;
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 18px;
            flex-shrink: 0;
        }

        .portal-card-icon svg {
            width: 26px;
            height: 26px;
        }

        .portal-card-icon--admin {
            background: var(--green-100);
            color: var(--green-700);
        }

        .portal-card-icon--petugas {
            background: #e0f2fe;
            color: #0369a1;
        }

        .portal-card h2 {
            font-size: 1.25rem;
            font-weight: 700;
            color: var(--slate-900);
            margin-bottom: 8px;
        }

        .portal-card p {
            font-size: 0.875rem;
            color: var(--slate-500);
            flex: 1;
            margin-bottom: 18px;
        }

        .portal-tag {
            display: inline-block;
            font-size: 0.6875rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.04em;
            padding: 4px 10px;
            border-radius: 6px;
            margin-bottom: 20px;
            width: fit-content;
        }

        .portal-tag--admin {
            background: var(--amber-100);
            color: var(--amber-800);
            border: 1px solid #fcd34d;
        }

        .portal-tag--petugas {
            background: var(--green-100);
            color: var(--green-800);
            border: 1px solid var(--green-200);
        }

        .btn {
            display: block;
            width: 100%;
            text-align: center;
            font-size: 0.875rem;
            font-weight: 600;
            padding: 13px 20px;
            border-radius: 10px;
            text-decoration: none;
            transition: background .15s, color .15s, border-color .15s;
        }

        .btn-primary {
            background: var(--green-600);
            color: #fff;
        }

        .btn-primary:hover { background: var(--green-700); }

        .btn-outline {
            background: #fff;
            color: var(--green-700);
            border: 2px solid var(--green-600);
        }

        .btn-outline:hover { background: var(--green-50); }

        /* ── Info strip ── */
        .info-strip {
            background: #fff;
            border-top: 1px solid var(--slate-200);
            border-bottom: 1px solid var(--slate-200);
        }

        .info-strip-inner {
            max-width: 1100px;
            margin: 0 auto;
            padding: 28px 24px;
            display: grid;
            grid-template-columns: 1fr;
            gap: 24px;
            text-align: center;
        }

        @media (min-width: 768px) {
            .info-strip-inner {
                grid-template-columns: repeat(3, 1fr);
                gap: 0;
            }
            .info-item + .info-item {
                border-left: 1px solid var(--slate-200);
            }
        }

        .info-item-icon {
            width: 40px;
            height: 40px;
            margin: 0 auto 10px;
            background: var(--green-100);
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--green-700);
        }

        .info-item-icon svg { width: 20px; height: 20px; }

        .info-item h3 {
            font-size: 0.875rem;
            font-weight: 700;
            color: var(--slate-800);
            margin-bottom: 4px;
        }

        .info-item p {
            font-size: 0.8125rem;
            color: var(--slate-500);
        }

        /* ── Footer ── */
        .footer {
            background: var(--slate-900);
            color: var(--slate-400);
            margin-top: auto;
        }

        .footer-inner {
            max-width: 1100px;
            margin: 0 auto;
            padding: 24px;
            display: flex;
            flex-direction: column;
            gap: 12px;
            font-size: 0.8125rem;
        }

        @media (min-width: 640px) {
            .footer-inner {
                flex-direction: row;
                justify-content: space-between;
                align-items: center;
            }
        }

        .footer-brand {
            color: #fff;
            font-weight: 700;
            font-size: 0.9375rem;
        }

        .footer-brand span { color: var(--green-500); }

        .footer-credit { text-align: left; }

        @media (min-width: 640px) {
            .footer-credit { text-align: right; }
        }

        /* ── Animations ── */
        @keyframes fadeUp {
            from { opacity: 0; transform: translateY(16px); }
            to   { opacity: 1; transform: translateY(0); }
        }

        .anim { animation: fadeUp .45s ease-out both; }
        .anim-d1 { animation-delay: .08s; }
        .anim-d2 { animation-delay: .16s; }
        .anim-d3 { animation-delay: .24s; }
    </style>
</head>
<body>

    <nav class="navbar">
        <div class="navbar-inner">
            <a href="/" class="brand">
                <div class="brand-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                    </svg>
                </div>
                <span class="brand-name">Setor.in</span>
            </a>
            <div class="navbar-meta">
                <strong>Dinas Lingkungan Hidup</strong>
                v1.0 &middot; Sistem Bank Sampah Digital
            </div>
        </div>
    </nav>

    <section class="hero">
        <div class="hero-inner anim">
            <div class="badge-dlh">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                Platform Pengelolaan Bank Sampah Digital
            </div>

            <h1>Selamat Datang di <span>Setor.in</span></h1>

            <p class="hero-desc">
                Sistem terintegrasi untuk pengelolaan transaksi penyetoran sampah,
                pencatatan nasabah, dan pelaporan bank sampah — dikembangkan untuk
                <strong>Dinas Lingkungan Hidup</strong>.
            </p>

            <div class="hero-stats">
                <div class="hero-stat"><strong>Digital</strong> &middot; Pencatatan real-time</div>
                <div class="hero-stat"><strong>3 Peran</strong> &middot; Admin, Petugas, Nasabah</div>
                <div class="hero-stat"><strong>Terintegrasi</strong> &middot; Web &amp; aplikasi mobile</div>
            </div>
        </div>
    </section>

    <section class="portals">
        <p class="portals-label anim anim-d1">Pilih portal masuk</p>

        <div class="portals-grid">
            <article class="portal-card anim anim-d2">
                <div class="portal-card-icon portal-card-icon--admin">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                    </svg>
                </div>
                <h2>Portal Admin</h2>
                <p>
                    Kelola data pengguna, harga sampah, misi, konten edukasi,
                    laporan sistem, dan persetujuan penarikan saldo nasabah.
                </p>
                <span class="portal-tag portal-tag--admin">Akses Penuh Sistem</span>
                <a href="{{ url('/admin/login') }}" class="btn btn-primary">
                    Masuk sebagai Admin &rarr;
                </a>
            </article>

            <article class="portal-card anim anim-d3">
                <div class="portal-card-icon portal-card-icon--petugas">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/>
                    </svg>
                </div>
                <h2>Portal Petugas</h2>
                <p>
                    Proses transaksi penyetoran sampah nasabah, kelola jadwal
                    operasional bank sampah, dan buat laporan harian.
                </p>
                <span class="portal-tag portal-tag--petugas">Akses Operasional</span>
                <a href="{{ url('/petugas/login') }}" class="btn btn-outline">
                    Masuk sebagai Petugas &rarr;
                </a>
            </article>
        </div>
    </section>

    <section class="info-strip">
        <div class="info-strip-inner">
            <div class="info-item">
                <div class="info-item-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                    </svg>
                </div>
                <h3>Bank Sampah Digital</h3>
                <p>Platform modern untuk pengelolaan sampah terpilah</p>
            </div>
            <div class="info-item">
                <div class="info-item-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                </div>
                <h3>Multi Pengguna</h3>
                <p>Admin, Petugas Bank Sampah, dan Nasabah (aplikasi mobile)</p>
            </div>
            <div class="info-item">
                <div class="info-item-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                    </svg>
                </div>
                <h3>Transaksi Real-time</h3>
                <p>Proses otomatis dan pencatatan digital yang akurat</p>
            </div>
        </div>
    </section>

    <footer class="footer">
        <div class="footer-inner">
            <div>
                <div class="footer-brand">&copy; 2026 <span>Setor.in</span></div>
                <div>Sistem Bank Sampah Digital &middot; Dinas Lingkungan Hidup</div>
            </div>
            <div class="footer-credit">
                Kelompok 8 &middot; D4 RPL &middot; POLINDRA
            </div>
        </div>
    </footer>

</body>
</html>
