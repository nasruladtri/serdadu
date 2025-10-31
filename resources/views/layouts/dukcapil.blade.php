<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ ($title ?? null) ? $title . ' · ' : '' }}{{ config('app.name', 'Dukcapil Madiun') }}</title>

    <link rel="icon" type="image/png" href="{{ asset('vendor/corporate-ui/img/kabupaten-madiun.png') }}?v=1">
    <link rel="shortcut icon" type="image/png" href="{{ asset('vendor/corporate-ui/img/kabupaten-madiun.png') }}?v=1">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&family=Inter:wght@400;500;600&display=swap"
        rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"
        integrity="sha512-31on1Uwx1PcT6zG17Q6C7GdYr387cMGX5CujjJVOk+3O8VjMBYPWaFzx5b9mzfFh1YgUo10xXMYN9bB+FsSjVg=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="{{ asset('css/dukcapil.css') }}">
    @stack('styles')
</head>

<body class="bg-body">
    <aside class="dk-sidebar" data-sidebar>
        <div class="dk-sidebar__brand">
            <div class="dk-sidebar__logo">
                <img src="{{ asset('vendor/corporate-ui/img/kabupaten-madiun.png') }}" alt="Kabupaten Madiun"
                    class="dk-logo-img">
            </div>
            <div class="dk-sidebar__brand-text">
                <span class="dk-brand-title">Serdadu</span>
                <small class="dk-brand-subtitle">Sistem Rekap Data Terpadu</small>
            </div>
        </div>

        <div class="dk-sidebar__toggle d-lg-flex">
            <button class="dk-sidebar-fab dk-nav-link" type="button" id="sidebarToggle" aria-label="Sembunyikan sidebar">
                <span class="dk-sidebar-fab__icon" data-icon aria-hidden="true">
                    <img src="{{ asset('img/menu.png') }}" alt="" class="dk-sidebar-fab__image" loading="lazy">
                </span>
                <span class="dk-nav-link__label">Menu</span>
                <span class="visually-hidden" data-label>Sembunyikan</span>
            </button>
        </div>

        <nav class="dk-sidebar__nav">
            <a href="{{ route('public.landing') }}"
                class="dk-nav-link {{ request()->routeIs('public.landing') ? 'active' : '' }}">
                <span class="dk-nav-link__icon">
                    <img src="{{ asset('img/home.png') }}" alt="" class="dk-nav-link__image" loading="lazy">
                </span>
                <span class="dk-nav-link__label">Beranda</span>
            </a>
            <a href="{{ route('public.data') }}"
                class="dk-nav-link {{ request()->routeIs('public.data') ? 'active' : '' }}">
                <span class="dk-nav-link__icon">
                    <img src="{{ asset('img/table.png') }}" alt="" class="dk-nav-link__image" loading="lazy">
                </span>
                <span class="dk-nav-link__label">Data Agregat</span>
            </a>
            <a href="{{ route('public.charts') }}"
                class="dk-nav-link {{ request()->routeIs('public.charts') ? 'active' : '' }}">
                <span class="dk-nav-link__icon">
                    <img src="{{ asset('img/bar-stats.png') }}" alt="" class="dk-nav-link__image" loading="lazy">
                </span>
                <span class="dk-nav-link__label">Grafik Data</span>
            </a>
        </nav>

        <div class="dk-sidebar__footer">
            <small class="dk-sidebar__meta">&copy; {{ date('Y') }} Dinas Dukcapil Kab. Madiun</small>
            <small class="dk-sidebar__meta">Versi publik</small>
            <small class="dk-sidebar__meta text-xs">Versi awal</small>
        </div>
    </aside>

    <div class="dk-main" data-main>
        <header class="dk-topbar shadow-sm">
            <div class="dk-title-group">
                <button class="dk-title-toggle d-lg-none" type="button" aria-label="Tampilkan menu"
                    data-offcanvas-toggle>
                    <span class="dk-title-toggle__icon">
                        <i class="fa-solid fa-bars-staggered"></i>
                    </span>
                    <span class="dk-title-toggle__text">Menu</span>
                </button>
                <div class="dk-topbar__tagline">
                    <span class="dk-topbar__tagline-label">Statistik Data Penduduk</span>
                    <small class="dk-topbar__tagline-note">Kabupaten Madiun</small>
                </div>
            </div>
            <div class="ms-auto d-none d-lg-flex align-items-center">
                <div class="text-end">
                    <span class="fw-semibold d-block">Dinas Kependudukan dan Pencatatan Sipil</span>
                    <small class="text-muted">Kab. Madiun</small>
                </div>
            </div>
        </header>

        <main class="dk-content py-4">
            @yield('content')
        </main>
    </div>

    {{-- Offcanvas sidebar for mobile --}}
    <div class="offcanvas offcanvas-start dk-offcanvas" tabindex="-1" id="sidebarOffcanvas">
        <div class="offcanvas-header">
            <div class="dk-offcanvas-brand">
                <div class="dk-offcanvas-brand__logo">
                    <img src="{{ asset('vendor/corporate-ui/img/kabupaten-madiun.png') }}" alt="Kabupaten Madiun"
                        class="dk-offcanvas-brand__img">
                </div>
                <div class="dk-offcanvas-brand__text">
                    <span class="dk-offcanvas-brand__title">Serdadu</span>
                    <small class="dk-offcanvas-brand__subtitle">Sistem Rekap Data Terpadu</small>
                </div>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>
        <div class="offcanvas-body">
            <nav class="dk-sidebar__nav">
                <a href="{{ route('public.landing') }}"
                    class="dk-nav-link {{ request()->routeIs('public.landing') ? 'active' : '' }}"
                    data-offcanvas-close>
                    <span class="dk-nav-link__icon">
                        <img src="{{ asset('img/home.png') }}" alt="" class="dk-nav-link__image" loading="lazy">
                    </span>
                    <span class="dk-nav-link__label">Beranda</span>
                </a>
                <a href="{{ route('public.data') }}"
                    class="dk-nav-link {{ request()->routeIs('public.data') ? 'active' : '' }}"
                    data-offcanvas-close>
                    <span class="dk-nav-link__icon">
                        <img src="{{ asset('img/table.png') }}" alt="" class="dk-nav-link__image" loading="lazy">
                    </span>
                    <span class="dk-nav-link__label">Data Agregat</span>
                </a>
                <a href="{{ route('public.charts') }}"
                    class="dk-nav-link {{ request()->routeIs('public.charts') ? 'active' : '' }}"
                    data-offcanvas-close>
                    <span class="dk-nav-link__icon">
                        <img src="{{ asset('img/bar-stats.png') }}" alt="" class="dk-nav-link__image" loading="lazy">
                    </span>
                    <span class="dk-nav-link__label">Grafik Data</span>
                </a>
            </nav>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL"
        crossorigin="anonymous"></script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const toggleBtn = document.getElementById('sidebarToggle');
            const sidebar = document.querySelector('[data-sidebar]');
            const main = document.querySelector('[data-main]');
            const labelElement = toggleBtn?.querySelector('[data-label]');
            const offcanvasEl = document.getElementById('sidebarOffcanvas');
            const fab = document.querySelector('.dk-sidebar-fab');
            const hasBootstrap = typeof bootstrap !== 'undefined';
            const offcanvasInstance = (offcanvasEl && hasBootstrap)
                ? bootstrap.Offcanvas.getOrCreateInstance(offcanvasEl)
                : null;

            // Hide floating FAB while offcanvas is open to prevent it blocking clicks
            if (offcanvasEl && fab) {
                offcanvasEl.addEventListener('show.bs.offcanvas', () => {
                    fab.style.display = 'none';
                });
                offcanvasEl.addEventListener('hidden.bs.offcanvas', () => {
                    // restore display for mobile; keep desktop visibility as earlier rules
                    fab.style.display = '';
                });
            }

            // Ensure offcanvas menu links close the drawer without blocking navigation
            if (offcanvasEl) {
                const offcanvasLinks = offcanvasEl.querySelectorAll('[data-offcanvas-close]');
                offcanvasLinks.forEach((link) => {
                    link.addEventListener('click', () => {
                        if (offcanvasInstance) {
                            // Hide the drawer on the next frame so the navigation can proceed normally
                            requestAnimationFrame(() => offcanvasInstance.hide());
                        }
                    });
                });
            }

            const offcanvasToggles = document.querySelectorAll('[data-offcanvas-toggle]');
            if (offcanvasToggles.length && offcanvasEl) {
                offcanvasToggles.forEach((btn) => {
                    btn.addEventListener('click', () => {
                        if (!offcanvasInstance) {
                            return;
                        }
                        if (offcanvasEl.classList.contains('show')) {
                            offcanvasInstance.hide();
                        } else {
                            offcanvasInstance.show();
                        }
                    });
                });
            }

            if (toggleBtn && sidebar && main) {
                const syncFabState = () => {
                    const isCollapsed = sidebar.classList.contains('dk-sidebar--collapsed');
                    toggleBtn.classList.toggle('is-collapsed', isCollapsed);

                    if (labelElement) {
                        labelElement.textContent = isCollapsed ? 'Tampilkan' : 'Sembunyikan';
                    }
                    toggleBtn.setAttribute('aria-label', isCollapsed ? 'Tampilkan sidebar' : 'Sembunyikan sidebar');
                };

                syncFabState();

                toggleBtn.addEventListener('click', () => {
                    const isMobile = window.matchMedia('(max-width: 991.98px)').matches;
                    if (isMobile) {
                        // On mobile/open small screens, open the offcanvas sidebar instead
                        if (offcanvasInstance) {
                            offcanvasInstance.show();
                            return;
                        }
                    }

                    // Desktop behaviour: collapse/expand sidebar
                    sidebar.classList.toggle('dk-sidebar--collapsed');
                    main.classList.toggle('dk-main--expanded');
                    syncFabState();
                });
            }
        });
    </script>
    @stack('scripts')
</body>

</html>

