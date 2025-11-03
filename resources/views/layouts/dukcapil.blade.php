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
        <div class="dk-sidebar__brand mb-3">
            <div class="dk-sidebar__logo">
                <img src="{{ asset('vendor/corporate-ui/img/kabupaten-madiun.png') }}" alt="Logo"
                    class="dk-logo-img">
            </div>
            <div class="dk-sidebar__brand-text">
                <span class="dk-brand-title">Serdadu</span>
            </div>
        </div>
        <div class="dk-sidebar__toggle">
            <button id="sidebarToggle" class="dk-sidebar-fab" type="button" aria-label="Sembunyikan sidebar">
                <span class="dk-sidebar-fab__icon">
                    <img src="{{ asset('img/arrow.png') }}" alt="Toggle sidebar">
                </span>
                <span class="dk-sidebar-fab__label text-xs" data-label>Sembunyikan</span>
            </button>
        </div>
        <nav class="dk-sidebar__nav">
            <a href="{{ route('public.landing') }}"
                class="dk-nav-link {{ request()->routeIs('public.landing') ? 'active' : '' }}">
                <span class="dk-nav-link__icon">
                    <img src="{{ asset('img/home.png') }}" alt="" class="dk-nav-link__image" loading="lazy" style="width: 24px; height: 24px;">
                </span>
                <span class="dk-nav-link__label">Home</span>
            </a>
            <a href="{{ route('public.data') }}"
                class="dk-nav-link {{ request()->routeIs('public.data') ? 'active' : '' }}">
                <span class="dk-nav-link__icon">
                    <img src="{{ asset('img/table.png') }}" alt="" class="dk-nav-link__image" loading="lazy" style="width: 24px; height: 24px;">
                </span>
                <span class="dk-nav-link__label">Tabel</span>
            </a>
            <a href="{{ route('public.charts') }}"
                class="dk-nav-link {{ request()->routeIs('public.charts') ? 'active' : '' }}">
                <span class="dk-nav-link__icon">
                    <img src="{{ asset('img/bar-stats.png') }}" alt="" class="dk-nav-link__image" loading="lazy" style="width: 24px; height: 24px;">
                </span>
                <span class="dk-nav-link__label">Grafik</span>
            </a>
        </nav>
        <div class="dk-sidebar__footer text-center">
            <span class="dk-sidebar__meta">&copy; Dinas Dukcapil Kabupaten Madiun</span>
            <span class="dk-sidebar__meta">Versi 0.1.2</span>
        </div>
    </aside>

    <div class="dk-main" data-main>
        <main class="dk-content">
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
                let manualCollapsed = sidebar.classList.contains('dk-sidebar--collapsed');
                let hoverExpanded = false;
                let hoverTimeoutId = null;
                let animationTimeoutId = null;
                let isAnimating = false;

                const applyLayoutState = () => {
                    const shouldCollapse = manualCollapsed && !hoverExpanded;
                    beginAnimation();
                    sidebar.classList.toggle('dk-sidebar--collapsed', shouldCollapse);
                    sidebar.classList.toggle('dk-sidebar--hovering', hoverExpanded);
                    main.classList.toggle('dk-main--expanded', shouldCollapse);
                };

                const syncFabState = () => {
                    toggleBtn.classList.toggle('is-collapsed', manualCollapsed);

                    if (labelElement) {
                        labelElement.textContent = manualCollapsed ? 'Tampilkan' : 'Sembunyikan';
                    }
                    toggleBtn.setAttribute('aria-label', manualCollapsed ? 'Tampilkan sidebar' : 'Sembunyikan sidebar');
                };

                const clearHoverTimeout = () => {
                    if (hoverTimeoutId) {
                        window.clearTimeout(hoverTimeoutId);
                        hoverTimeoutId = null;
                    }
                };

                const beginAnimation = () => {
                    isAnimating = true;
                    if (animationTimeoutId) {
                        window.clearTimeout(animationTimeoutId);
                    }
                    animationTimeoutId = window.setTimeout(() => {
                        isAnimating = false;
                        animationTimeoutId = null;
                    }, 260);
                };

                const handleTransitionEnd = (event) => {
                    if (event.target !== sidebar) {
                        return;
                    }
                    if (event.propertyName === 'width') {
                        isAnimating = false;
                        if (animationTimeoutId) {
                            window.clearTimeout(animationTimeoutId);
                            animationTimeoutId = null;
                        }
                    }
                };

                sidebar.addEventListener('transitionend', handleTransitionEnd);

                const setCollapsed = (collapsed) => {
                    manualCollapsed = collapsed;
                    if (!collapsed) {
                        hoverExpanded = false;
                    }
                    clearHoverTimeout();
                    applyLayoutState();
                    syncFabState();
                };

                applyLayoutState();
                syncFabState();

                toggleBtn.addEventListener('click', () => {
                    const isMobile = window.matchMedia('(max-width: 991.98px)').matches;
                    if (isMobile) {
                        if (offcanvasInstance) {
                            offcanvasInstance.show();
                        }
                        return;
                    }

                    setCollapsed(!manualCollapsed);
                });

                sidebar.addEventListener('mouseenter', () => {
                    if (!manualCollapsed || hoverExpanded || isAnimating) {
                        return;
                    }
                    clearHoverTimeout();
                    hoverTimeoutId = window.setTimeout(() => {
                        hoverExpanded = true;
                        applyLayoutState();
                        hoverTimeoutId = null;
                    }, 80);
                });

                sidebar.addEventListener('mouseleave', () => {
                    if (hoverTimeoutId) {
                        clearHoverTimeout();
                    }
                    if (!manualCollapsed || !hoverExpanded) {
                        return;
                    }
                    if (isAnimating) {
                        return;
                    }
                    hoverExpanded = false;
                    applyLayoutState();
                });
            }
        });
    </script>
    @stack('scripts')
</body>

</html>
