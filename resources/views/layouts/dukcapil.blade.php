<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Serdadu') - Sistem Rekap Data Terpadu</title>
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')
    
    <style>
        /* Icon color styling untuk sidebar navigation */
        .sidebar-nav-icon {
            filter: brightness(0) saturate(100%);
            opacity: 0.7;
            transition: all 0.2s ease;
        }
        
        /* Icon hijau saat hover */
        .sidebar-nav-link:hover .sidebar-nav-icon {
            filter: brightness(0) saturate(100%) invert(27%) sepia(95%) saturate(1352%) hue-rotate(120deg) brightness(0.4);
            opacity: 1;
        }
        
        /* Icon putih saat active (dipilih) */
        .sidebar-nav-link.active .sidebar-nav-icon {
            filter: brightness(0) invert(1);
            opacity: 1;
        }
    </style>
</head>

<body class="bg-gray-50 antialiased" data-sidebar-state>
    <!-- Sidebar Desktop -->
    <aside 
        id="desktop-sidebar"
        class="fixed top-0 left-0 z-40 h-screen transition-all duration-300 ease-in-out bg-white border-r border-gray-200 shadow-sm overflow-hidden flex-col hidden lg:flex w-64"
        data-sidebar
    >
        <!-- Brand Section -->
        <div class="flex items-center justify-center h-16 border-b border-gray-200 px-4" data-sidebar-brand>
            <div class="flex items-center gap-3 min-w-0 flex-1 justify-center" data-sidebar-brand-content>
                <img 
                    src="{{ asset('img/kabupaten-madiun.png') }}" 
                    alt="Logo" 
                    class="w-10 h-10 flex-shrink-0 object-contain cursor-pointer hover:opacity-80 transition-opacity"
                    data-sidebar-logo
                    title="Klik untuk collapse/expand sidebar"
                >
                <div class="min-w-0" data-sidebar-text>
                    <div class="font-semibold text-gray-900 truncate">Serdadu</div>
                    <div class="text-xs text-gray-500 truncate">Sistem Rekap Data Terpadu</div>
            </div>
            </div>
        </div>

        <!-- Navigation -->
        <nav class="flex-1 overflow-y-auto px-4 py-4" data-sidebar-nav>
            <div class="space-y-1">
                <a 
                    href="{{ route('public.landing') }}"
                    class="sidebar-nav-link flex items-center gap-3 px-3 py-2.5 text-sm font-medium rounded-lg transition-colors {{ request()->routeIs('public.landing') ? 'bg-[#007151] text-white active' : 'text-gray-700 hover:bg-gray-100' }}"
                    title="Home"
                    data-sidebar-nav-item
                >
                    <img src="{{ asset('img/home.png') }}" alt="" class="sidebar-nav-icon w-5 h-5 flex-shrink-0">
                    <span class="whitespace-nowrap" data-sidebar-nav-text>Home</span>
                </a>
                
                <a 
                    href="{{ route('public.data') }}"
                    class="sidebar-nav-link flex items-center gap-3 px-3 py-2.5 text-sm font-medium rounded-lg transition-colors {{ request()->routeIs('public.data') ? 'bg-[#007151] text-white active' : 'text-gray-700 hover:bg-gray-100' }}"
                    title="Tabel"
                    data-sidebar-nav-item
                >
                    <img src="{{ asset('img/table.png') }}" alt="" class="sidebar-nav-icon w-5 h-5 flex-shrink-0">
                    <span class="whitespace-nowrap" data-sidebar-nav-text>Tabel</span>
                </a>
                
                <a 
                    href="{{ route('public.charts') }}"
                    class="sidebar-nav-link flex items-center gap-3 px-3 py-2.5 text-sm font-medium rounded-lg transition-colors {{ request()->routeIs('public.charts') ? 'bg-[#007151] text-white active' : 'text-gray-700 hover:bg-gray-100' }}"
                    title="Grafik"
                    data-sidebar-nav-item
                >
                    <img src="{{ asset('img/bar-stats.png') }}" alt="" class="sidebar-nav-icon w-5 h-5 flex-shrink-0">
                    <span class="whitespace-nowrap" data-sidebar-nav-text>Grafik</span>
                </a>
                
                <a 
                    href="{{ url('/compare') }}"
                    class="sidebar-nav-link flex items-center gap-3 px-3 py-2.5 text-sm font-medium rounded-lg transition-colors {{ request()->is('compare') ? 'bg-[#007151] text-white active' : 'text-gray-700 hover:bg-gray-100' }}"
                    title="Compare"
                    data-sidebar-nav-item
                >
                    <img src="{{ asset('img/compare.png') }}" alt="" class="sidebar-nav-icon w-5 h-5 flex-shrink-0">
                    <span class="whitespace-nowrap" data-sidebar-nav-text>Compare</span>
                </a>
                
                <a 
                    href="{{ url('/terms') }}"
                    class="sidebar-nav-link flex items-center gap-3 px-3 py-2.5 text-sm font-medium rounded-lg transition-colors {{ request()->is('terms') ? 'bg-[#007151] text-white active' : 'text-gray-700 hover:bg-gray-100' }}"
                    title="Terms"
                    data-sidebar-nav-item
                >
                    <img src="{{ asset('img/terms.png') }}" alt="" class="sidebar-nav-icon w-5 h-5 flex-shrink-0">
                    <span class="whitespace-nowrap" data-sidebar-nav-text>Terms</span>
                </a>
            </div>
        </nav>

        <!-- Footer -->
        <div 
            class="border-t border-gray-200 space-y-1 overflow-hidden p-4"
            data-sidebar-footer
        >
            <div class="text-xs text-gray-500 text-center">
                Copyright © 2025 
                <a href="{{ url('/') }}" class="text-[#007151] hover:underline" target="_blank" rel="noopener">Serdadu</a>
                <a href="https://dukcapil.madiunkab.go.id" class="text-[#007151] hover:underline" target="_blank" rel="noopener">Dukcapil Kab. Madiun</a>
            </div>
            <div class="text-xs text-gray-500 text-center">Versi 0.1.2</div>
        </div>
</aside>

    <!-- Mobile Header -->
    <header class="lg:hidden fixed top-0 left-0 right-0 z-50 h-16 bg-white border-b border-gray-200 shadow-sm">
        <div class="flex items-center justify-between h-full px-4">
            <div class="flex items-center gap-3">
                <img 
                    src="{{ asset('img/kabupaten-madiun.png') }}" 
                    alt="Logo" 
                    class="w-10 h-10 object-contain"
                >
                <div>
                    <div class="font-semibold text-gray-900 text-sm">Serdadu</div>
                    <div class="text-xs text-gray-500">Sistem Rekap Data Terpadu</div>
                </div>
            </div>
            <button 
                id="mobile-menu-toggle"
                class="p-2 text-gray-600 hover:text-gray-900 hover:bg-gray-100 rounded-lg transition-colors"
                aria-label="Toggle menu"
            >
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                </svg>
            </button>
        </div>
    </header>

    <!-- Mobile Sidebar Overlay -->
    <div 
        id="mobile-sidebar-overlay"
        class="lg:hidden fixed inset-0 z-30 bg-black bg-opacity-50 hidden transition-opacity duration-300"
    ></div>

    <!-- Mobile Sidebar -->
    <aside 
        id="mobile-sidebar"
        class="lg:hidden fixed top-0 left-0 z-40 h-full w-64 bg-white shadow-xl transform -translate-x-full transition-transform duration-300 flex flex-col"
    >
        <div class="flex items-center justify-between h-16 px-4 border-b border-gray-200 flex-shrink-0">
            <div class="flex items-center gap-3">
                <img src="{{ asset('img/kabupaten-madiun.png') }}" alt="Logo" class="w-10 h-10 object-contain">
                <div>
                    <div class="font-semibold text-gray-900 text-sm">Serdadu</div>
                    <div class="text-xs text-gray-500">Sistem Rekap Data Terpadu</div>
                </div>
            </div>
            <button 
                id="mobile-menu-close"
                class="p-2 text-gray-600 hover:text-gray-900 hover:bg-gray-100 rounded-lg transition-colors"
                aria-label="Close menu"
            >
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>
        
        <nav class="flex-1 px-4 py-4 space-y-1 overflow-y-auto">
            <a 
                href="{{ route('public.landing') }}"
                class="mobile-menu-link sidebar-nav-link flex items-center gap-3 px-3 py-2.5 text-sm font-medium rounded-lg transition-colors {{ request()->routeIs('public.landing') ? 'bg-[#007151] text-white active' : 'text-gray-700 hover:bg-gray-100' }}"
            >
                <img src="{{ asset('img/home.png') }}" alt="" class="sidebar-nav-icon w-5 h-5 flex-shrink-0">
                <span>Home</span>
            </a>
            
            <a 
                href="{{ route('public.data') }}"
                class="mobile-menu-link sidebar-nav-link flex items-center gap-3 px-3 py-2.5 text-sm font-medium rounded-lg transition-colors {{ request()->routeIs('public.data') ? 'bg-[#007151] text-white active' : 'text-gray-700 hover:bg-gray-100' }}"
            >
                <img src="{{ asset('img/table.png') }}" alt="" class="sidebar-nav-icon w-5 h-5 flex-shrink-0">
                <span>Tabel</span>
            </a>
            
            <a 
                href="{{ route('public.charts') }}"
                class="mobile-menu-link sidebar-nav-link flex items-center gap-3 px-3 py-2.5 text-sm font-medium rounded-lg transition-colors {{ request()->routeIs('public.charts') ? 'bg-[#007151] text-white active' : 'text-gray-700 hover:bg-gray-100' }}"
            >
                <img src="{{ asset('img/bar-stats.png') }}" alt="" class="sidebar-nav-icon w-5 h-5 flex-shrink-0">
                <span>Grafik</span>
            </a>
            
            <a 
                href="{{ url('/compare') }}"
                class="mobile-menu-link sidebar-nav-link flex items-center gap-3 px-3 py-2.5 text-sm font-medium rounded-lg transition-colors {{ request()->is('compare') ? 'bg-[#007151] text-white active' : 'text-gray-700 hover:bg-gray-100' }}"
            >
                <img src="{{ asset('img/compare.png') }}" alt="" class="sidebar-nav-icon w-5 h-5 flex-shrink-0">
                <span>Compare</span>
            </a>
            
            <a 
                href="{{ url('/terms') }}"
                class="mobile-menu-link sidebar-nav-link flex items-center gap-3 px-3 py-2.5 text-sm font-medium rounded-lg transition-colors {{ request()->is('terms') ? 'bg-[#007151] text-white active' : 'text-gray-700 hover:bg-gray-100' }}"
            >
                <img src="{{ asset('img/terms.png') }}" alt="" class="sidebar-nav-icon w-5 h-5 flex-shrink-0">
                <span>Terms</span>
            </a>
        </nav>
        
        <div class="p-4 border-t border-gray-200 space-y-1 mt-auto flex-shrink-0">
            <div class="text-xs text-gray-500 text-center">
                Copyright © 2025 
                <a href="{{ url('/') }}" class="text-[#007151] hover:underline" target="_blank" rel="noopener">Serdadu</a>
                <a href="https://dukcapil.madiunkab.go.id" class="text-[#007151] hover:underline" target="_blank" rel="noopener">Dukcapil Kab. Madiun</a>
            </div>
            <div class="text-xs text-gray-500 text-center">Versi 0.1.2</div>
        </div>
    </aside>

    <!-- Main Content -->
    <main 
        id="main-content"
        class="transition-all duration-300 ease-in-out min-h-screen lg:pt-0 pt-16 lg:ml-64"
        style="will-change: margin-left;"
    >
        <!-- Breadcrumb Navigation -->
        <div class="border-b border-gray-200 bg-white px-4 lg:px-6 py-3">
            <nav class="flex items-center gap-2 text-sm" aria-label="Breadcrumb">
                @php
                    $breadcrumbs = [];
                    
                    // Home/Beranda
                    if (request()->routeIs('public.landing')) {
                        $breadcrumbs[] = [
                            'label' => 'Beranda',
                            'route' => 'public.landing',
                            'icon' => 'home',
                            'active' => true
                        ];
                    } else {
                        $breadcrumbs[] = [
                            'label' => 'Beranda',
                            'route' => 'public.landing',
                            'icon' => 'home',
                            'active' => false
                        ];
                        
                        // Tabel/Data
                        if (request()->routeIs('public.data') || request()->routeIs('public.data.fullscreen')) {
                            $breadcrumbs[] = [
                                'label' => 'Tabel',
                                'route' => 'public.data',
                                'icon' => 'table',
                                'active' => false
                            ];
                            
                            // Tambahkan kategori tab aktif jika ada
                            $category = request()->query('category', 'gender');
                            $categoryLabels = [
                                'gender' => 'Jenis Kelamin',
                                'age' => 'Kelompok Umur',
                                'single-age' => 'Umur Tunggal',
                                'education' => 'Pendidikan',
                                'occupation' => 'Pekerjaan',
                                'marital' => 'Status Perkawinan',
                                'household' => 'Kepala Keluarga',
                                'religion' => 'Agama',
                                'wajib-ktp' => 'Wajib KTP',
                                'kk' => 'Kartu Keluarga',
                            ];
                            $categoryLabel = $categoryLabels[$category] ?? 'Jenis Kelamin';
                            
                            if (request()->routeIs('public.data.fullscreen')) {
                                $breadcrumbs[] = [
                                    'label' => $categoryLabel,
                                    'route' => null,
                                    'icon' => 'table',
                                    'active' => false
                                ];
                                $breadcrumbs[] = [
                                    'label' => 'Fullscreen',
                                    'route' => null,
                                    'icon' => 'maximize',
                                    'active' => true
                                ];
                            } else {
                                $breadcrumbs[] = [
                                    'label' => $categoryLabel,
                                    'route' => null,
                                    'icon' => 'table',
                                    'active' => true
                                ];
                            }
                        }
                        
                        // Grafik
                        if (request()->routeIs('public.charts')) {
                            $breadcrumbs[] = [
                                'label' => 'Grafik',
                                'route' => 'public.charts',
                                'icon' => 'chart',
                                'active' => false
                            ];
                            
                            // Tambahkan kategori tab aktif jika ada
                            $category = request()->query('category', 'gender');
                            $categoryLabels = [
                                'gender' => 'Jenis Kelamin',
                                'age' => 'Kelompok Umur',
                                'single-age' => 'Umur Tunggal',
                                'education' => 'Pendidikan',
                                'occupation' => 'Pekerjaan',
                                'marital' => 'Status Perkawinan',
                                'household' => 'Kepala Keluarga',
                                'religion' => 'Agama',
                                'wajib-ktp' => 'Wajib KTP',
                                'kk' => 'Kartu Keluarga',
                            ];
                            $categoryLabel = $categoryLabels[$category] ?? 'Jenis Kelamin';
                            
                            $breadcrumbs[] = [
                                'label' => $categoryLabel,
                                'route' => null,
                                'icon' => 'chart',
                                'active' => true
                            ];
                        }
                    }
                @endphp
                
                @if(count($breadcrumbs) > 1)
                    <button 
                        onclick="window.history.back()" 
                        class="flex items-center justify-center w-8 h-8 text-gray-500 hover:text-gray-700 hover:bg-gray-100 rounded-lg transition-colors focus:outline-none focus:ring-2 focus:ring-[#007151] focus:ring-offset-2"
                        title="Kembali"
                        aria-label="Kembali"
                    >
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                        </svg>
                    </button>
                    <span class="text-gray-300">|</span>
                @endif
                
                <ol class="flex items-center gap-2">
                    @foreach($breadcrumbs as $index => $breadcrumb)
                        <li class="flex items-center gap-2">
                            @if($index > 0)
                                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                </svg>
                            @endif
                            
                            @if($breadcrumb['route'] && !$breadcrumb['active'])
                                <a 
                                    href="{{ route($breadcrumb['route']) }}" 
                                    class="flex items-center gap-1.5 text-gray-600 hover:text-gray-900 transition-colors {{ (isset($categoryLabel) && $breadcrumb['label'] === $categoryLabel) ? 'breadcrumb-category' : '' }}"
                                >
                                    @if($breadcrumb['icon'] === 'home')
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                                        </svg>
                                    @elseif($breadcrumb['icon'] === 'table')
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M3 14h18m-9-4v8m-7 0h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                                        </svg>
                                    @elseif($breadcrumb['icon'] === 'chart')
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                                        </svg>
                                    @elseif($breadcrumb['icon'] === 'maximize')
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4"></path>
                                        </svg>
                                    @endif
                                    <span>{{ $breadcrumb['label'] }}</span>
                                </a>
                            @else
                                <span class="flex items-center gap-1.5 text-gray-900 font-medium {{ isset($categoryLabel) && $breadcrumb['label'] === $categoryLabel ? 'breadcrumb-category' : '' }}">
                                    @if($breadcrumb['icon'] === 'home')
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                                        </svg>
                                    @elseif($breadcrumb['icon'] === 'table')
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M3 14h18m-9-4v8m-7 0h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                                        </svg>
                                    @elseif($breadcrumb['icon'] === 'chart')
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                                        </svg>
                                    @elseif($breadcrumb['icon'] === 'maximize')
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4"></path>
                                        </svg>
                                    @endif
                                    <span class="breadcrumb-category-text">{{ $breadcrumb['label'] }}</span>
                                </span>
                            @endif
                        </li>
                    @endforeach
                </ol>
            </nav>
        </div>
        
        <div class="p-4 lg:p-6 max-w-full">
            @yield('content')
        </div>
    </main>

    @stack('scripts')
    
    <script>
        // Vanilla JavaScript untuk sidebar collapse (CSP-friendly)
        (function() {
            'use strict';
            
            // Desktop Sidebar
            const desktopSidebar = document.getElementById('desktop-sidebar');
            const mainContent = document.getElementById('main-content');
            
            const sidebarText = document.querySelector('[data-sidebar-text]');
            const sidebarNav = document.querySelector('[data-sidebar-nav]');
            const sidebarNavTexts = document.querySelectorAll('[data-sidebar-nav-text]');
            const sidebarNavItems = document.querySelectorAll('[data-sidebar-nav-item]');
            const sidebarBrand = document.querySelector('[data-sidebar-brand]');
            const sidebarBrandContent = document.querySelector('[data-sidebar-brand-content]');
            const sidebarLogo = document.querySelector('[data-sidebar-logo]');
            const sidebarFooter = document.querySelector('[data-sidebar-footer]');
            
            // Mobile Sidebar
            const mobileSidebar = document.getElementById('mobile-sidebar');
            const mobileOverlay = document.getElementById('mobile-sidebar-overlay');
            const mobileToggle = document.getElementById('mobile-menu-toggle');
            const mobileClose = document.getElementById('mobile-menu-close');
            const mobileMenuLinks = document.querySelectorAll('.mobile-menu-link');
            
            let collapsed = false;
            let hoverExpanded = false;
            let hoverTimeout = null;
            
            // Load state from localStorage
            try {
                const stored = localStorage.getItem('sidebarCollapsed');
                if (stored === 'true') {
                    collapsed = true;
                }
            } catch (e) {
                console.warn('localStorage not available');
            }
            
            // Update desktop sidebar state
            function updateDesktopSidebar() {
                if (!desktopSidebar || !mainContent) {
                    return;
                }
                
                const isExpanded = !collapsed || hoverExpanded;
                
                if (!isExpanded) {
                    // Collapsed state
                    desktopSidebar.classList.remove('w-64');
                    desktopSidebar.classList.add('w-20');
                    mainContent.classList.remove('lg:ml-64');
                    mainContent.classList.add('lg:ml-20');
                    
                    // Hide text elements
                    if (sidebarText) sidebarText.style.display = 'none';
                    sidebarNavTexts.forEach(el => el.style.display = 'none');
                    if (sidebarFooter) sidebarFooter.style.display = 'none';
                    
                    // Adjust padding and center alignment
                    if (sidebarBrand) {
                        sidebarBrand.classList.remove('px-4');
                        sidebarBrand.classList.add('px-2', 'justify-center');
                    }
                    if (sidebarBrandContent) {
                        sidebarBrandContent.classList.remove('flex-1');
                        sidebarBrandContent.classList.add('justify-center');
                    }
                    if (sidebarLogo) {
                        sidebarLogo.classList.remove('w-10', 'h-10');
                        sidebarLogo.classList.add('w-8', 'h-8');
                    }
                    if (sidebarNav) {
                        sidebarNav.classList.remove('px-4');
                        sidebarNav.classList.add('px-2');
                    }
                    // Center navigation items
                    sidebarNavItems.forEach(item => {
                        item.classList.remove('gap-3', 'px-3');
                        item.classList.add('justify-center', 'px-2');
                    });
                } else {
                    // Expanded state (either manually expanded or hover expanded)
                    desktopSidebar.classList.remove('w-20');
                    desktopSidebar.classList.add('w-64');
                    mainContent.classList.remove('lg:ml-20');
                    mainContent.classList.add('lg:ml-64');
                    
                    // Show text elements
                    if (sidebarText) sidebarText.style.display = '';
                    sidebarNavTexts.forEach(el => el.style.display = '');
                    if (sidebarFooter) sidebarFooter.style.display = '';
                    
                    // Adjust padding and restore alignment
                    if (sidebarBrand) {
                        sidebarBrand.classList.remove('px-2', 'justify-center');
                        sidebarBrand.classList.add('px-4');
                    }
                    if (sidebarBrandContent) {
                        sidebarBrandContent.classList.add('flex-1');
                        sidebarBrandContent.classList.remove('justify-center');
                    }
                    if (sidebarLogo) {
                        sidebarLogo.classList.remove('w-8', 'h-8');
                        sidebarLogo.classList.add('w-10', 'h-10');
                    }
                    if (sidebarNav) {
                        sidebarNav.classList.remove('px-2');
                        sidebarNav.classList.add('px-4');
                    }
                    // Restore navigation items alignment
                    sidebarNavItems.forEach(item => {
                        item.classList.remove('justify-center', 'px-2');
                        item.classList.add('gap-3', 'px-3');
                    });
                }
                
                // Save to localStorage (only when not hover expanded)
                if (!hoverExpanded) {
                    try {
                        localStorage.setItem('sidebarCollapsed', String(collapsed));
                    } catch (e) {
                        console.warn('Could not save to localStorage');
                    }
                }
            }
            
            // Click on logo to toggle collapse
            if (sidebarLogo) {
                sidebarLogo.addEventListener('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    collapsed = !collapsed;
                    hoverExpanded = false;
                    updateDesktopSidebar();
                });
            }
            
            // Hover to expand functionality
            if (desktopSidebar) {
                desktopSidebar.addEventListener('mouseenter', function() {
                    if (collapsed) {
                        // Clear any existing timeout
                        if (hoverTimeout) {
                            clearTimeout(hoverTimeout);
                        }
                        
                        // Small delay before expanding to avoid accidental triggers
                        hoverTimeout = setTimeout(function() {
                            if (collapsed && !hoverExpanded) {
                                hoverExpanded = true;
                                updateDesktopSidebar();
                            }
                        }, 100);
                    }
                });
                
                desktopSidebar.addEventListener('mouseleave', function() {
                    // Clear timeout if mouse leaves before delay
                    if (hoverTimeout) {
                        clearTimeout(hoverTimeout);
                        hoverTimeout = null;
                    }
                    
                    // Collapse back if it was hover-expanded
                    if (hoverExpanded) {
                        hoverExpanded = false;
                        updateDesktopSidebar();
                    }
                });
            }
            
            // Prevent logo click from triggering hover
            if (sidebarLogo) {
                sidebarLogo.addEventListener('mouseenter', function(e) {
                    e.stopPropagation();
                });
            }
            
            // Mobile sidebar functions
            function openMobileSidebar() {
                if (mobileSidebar && mobileOverlay) {
                    mobileSidebar.classList.remove('-translate-x-full');
                    mobileOverlay.classList.remove('hidden');
                    document.body.style.overflow = 'hidden';
                }
            }
            
            function closeMobileSidebar() {
                if (mobileSidebar && mobileOverlay) {
                    mobileSidebar.classList.add('-translate-x-full');
                    mobileOverlay.classList.add('hidden');
                    document.body.style.overflow = '';
                }
            }
            
            if (mobileToggle) {
                mobileToggle.addEventListener('click', openMobileSidebar);
            }
            
            if (mobileClose) {
                mobileClose.addEventListener('click', closeMobileSidebar);
            }
            
            if (mobileOverlay) {
                mobileOverlay.addEventListener('click', closeMobileSidebar);
            }
            
            mobileMenuLinks.forEach(link => {
                link.addEventListener('click', closeMobileSidebar);
            });
            
            // Initialize sidebar state
            updateDesktopSidebar();
            
            // Debug: Log initial state
            console.log('Sidebar initialized:', {
                collapsed: collapsed,
                hoverExpanded: hoverExpanded,
                sidebar: desktopSidebar ? 'found' : 'not found',
                mainContent: mainContent ? 'found' : 'not found'
            });
        })();
    </script>
</body>
</html>
