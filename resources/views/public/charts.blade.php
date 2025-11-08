@extends('layouts.dukcapil', ['title' => 'Grafik Data'])

@push('styles')
    <style>
        /* Smooth tab transitions */
        .dk-tab-pane {
            animation: fadeIn 0.3s ease-in-out;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Chart container responsive heights - memenuhi section */
        .chart-container {
            min-height: 400px;
            width: 100%;
            position: relative;
        }
        
        /* Pastikan chart wrapper menggunakan ruang yang tersedia */
        .chart-wrapper-container {
            flex: 1;
            display: flex;
            flex-direction: column;
            min-height: 0;
            width: 100%;
        }
        
        .chart-wrapper {
            position: relative;
            flex: 1;
            display: flex;
            flex-direction: column;
            min-height: 0;
            width: 100%;
        }

        @media (max-width: 640px) {
            .chart-container {
                min-height: 350px !important;
            }
            .chart-container[style*="height: 500px"] {
                min-height: 350px !important;
            }
            .chart-container[style*="height: 600px"] {
                min-height: 400px !important;
            }
            .chart-container[style*="height: 700px"] {
                min-height: 450px !important;
            }
        }

        @media (min-width: 641px) and (max-width: 1024px) {
            .chart-container {
                min-height: 450px !important;
            }
            .chart-container[style*="height: 500px"] {
                min-height: 450px !important;
            }
            .chart-container[style*="height: 600px"] {
                min-height: 550px !important;
            }
            .chart-container[style*="height: 700px"] {
                min-height: 650px !important;
            }
        }

        @media (min-width: 1025px) {
            .chart-container {
                min-height: 500px !important;
            }
            .chart-container[style*="height: 500px"] {
                min-height: 500px !important;
            }
            .chart-container[style*="height: 600px"] {
                min-height: 600px !important;
            }
            .chart-container[style*="height: 700px"] {
                min-height: 700px !important;
            }
        }

        /* Pastikan tab pane menggunakan ruang maksimal */
        .dk-tab-pane {
            display: flex;
            flex-direction: column;
            gap: 1.5rem;
        }

        /* Pastikan table heading tidak mengambil terlalu banyak ruang */
        .dk-table-heading {
            flex-shrink: 0;
            margin-bottom: 0;
        }
    </style>
@endpush

@section('content')
    @php
        $tabs = [
            'gender' => 'Jenis Kelamin',
            'age' => 'Kelompok Umur',
            'single-age' => 'Umur Tunggal',
            'education' => 'Pendidikan',
            'occupation' => 'Pekerjaan',
            'marital' => 'Status Perkawinan',
            'kk' => 'Kartu Keluarga',
            'household' => 'Kepala Keluarga',
            'religion' => 'Agama',
            'wajib-ktp' => 'Wajib KTP',
        ];
    @endphp

    {{-- Kartu filter untuk memilih wilayah dan periode data agregat yang ditampilkan --}}
    <div class="dk-card mb-4">
        <div class="p-4">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-3 items-end">
                <div class="lg:col-span-3">
                    <h6 class="dk-card__title mb-1">Wilayah & Periode</h6>
                    <p class="text-xs text-gray-500 mb-0">
                        Pilih tahun, semester, kecamatan, atau desa/kelurahan untuk menampilkan grafik spesifik.
                    </p>
                </div>
                <form method="GET" class="lg:col-span-9">
                    {{-- Set filter wilayah dan periode; setiap perubahan auto submit agar data langsung diperbarui --}}
                    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 lg:grid-cols-5 gap-3 items-end">
                        <div class="sm:col-span-1 md:col-span-1 lg:col-span-1">
                            <label class="block text-xs font-medium text-gray-500 uppercase mb-1">Tahun</label>
                            <select class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500" name="year" onchange="this.form.submit()">
                                <option value="">Terbaru</option>
                                @foreach ($years as $year)
                                    <option value="{{ $year }}" {{ (int) ($selectedYear ?? 0) === $year ? 'selected' : '' }}>
                                    {{ $year }}
                                </option>
                            @endforeach
                            </select>
                        </div>
                        <div class="sm:col-span-1 md:col-span-1 lg:col-span-1">
                            <label class="block text-xs font-medium text-gray-500 uppercase mb-1">Semester</label>
                            <select class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500" name="semester" onchange="this.form.submit()">
                                <option value="">Terbaru</option>
                                @forelse ($semesterOptions as $option)
                                    <option value="{{ $option }}" {{ (int) ($selectedSemester ?? 0) === $option ? 'selected' : '' }}>
                                        Semester {{ $option }}
                                    </option>
                                @empty
                                    <option value="" disabled>Belum tersedia</option>
                                @endforelse
                            </select>
                        </div>
                        <div class="sm:col-span-1 md:col-span-1 lg:col-span-1">
                            <label class="block text-xs font-medium text-gray-500 uppercase mb-1">Kecamatan</label>
                            <select class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500" name="district_id" onchange="this.form.submit()">
                                <option value="">Semua Kecamatan</option>
                                @foreach ($districts as $district)
                                    <option value="{{ $district->id }}" {{ (int) $selectedDistrict === $district->id ? 'selected' : '' }}>
                                        {{ \Illuminate\Support\Str::title($district->name) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="sm:col-span-1 md:col-span-1 lg:col-span-1 dk-filter-village">
                            <label class="block text-xs font-medium text-gray-500 uppercase mb-1">Desa/Kelurahan</label>
                            <select class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 disabled:bg-gray-100 disabled:cursor-not-allowed" name="village_id" onchange="this.form.submit()" {{ $villages->isEmpty() ? 'disabled' : '' }}>
                                <option value="">Semua Desa/Kelurahan</option>
                                @foreach ($villages as $village)
                                    <option value="{{ $village->id }}" {{ (int) $selectedVillage === $village->id ? 'selected' : '' }}>
                                        {{ \Illuminate\Support\Str::title($village->name) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="sm:col-span-1 md:col-span-1 lg:col-span-1">
                            <a href="{{ route('public.charts') }}" class="inline-flex items-center justify-center w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                Reset
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Tampilkan pesan jika belum ada dataset yang dipilih atau diunggah --}}
    @if (!$period)
        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 dk-card">
            <strong class="text-yellow-800">Data belum tersedia.</strong> <span class="text-yellow-700">Unggah dataset terlebih dahulu untuk menampilkan grafik agregat.</span>
        </div>
    @else
        {{-- Inisialisasi variabel bantu untuk menyusun informasi tabel dan label tampilan --}}
        @php
            $kabupatenName = config('app.region_name', 'Kabupaten Madiun');
            $districtName = $selectedDistrict ? optional($districts->firstWhere('id', (int) $selectedDistrict))->name : null;
            $villageName = $selectedVillage ? optional($villages->firstWhere('id', (int) $selectedVillage))->name : null;
            $areaSegments = [$kabupatenName];
            if ($districtName) {
                $areaSegments[] = 'Kecamatan ' . \Illuminate\Support\Str::title($districtName);
                $areaSegments[] = $villageName ? ('Desa/Kelurahan ' . \Illuminate\Support\Str::title($villageName)) : 'Semua Desa/Kelurahan';
            } else {
                $areaSegments[] = 'Semua Kecamatan';
                $areaSegments[] = 'Semua Desa/Kelurahan';
            }
            $areaDescriptor = implode(' > ', array_filter($areaSegments));
            $periodLabelParts = [];
            if (!empty($period['semester'])) {
                $periodLabelParts[] = 'Semester ' . $period['semester'];
            }
            if (!empty($period['year'])) {
                $periodLabelParts[] = 'Tahun ' . $period['year'];
            }
            $periodLabel = !empty($periodLabelParts) ? implode(' ', $periodLabelParts) : null;
        @endphp

        <div class="dk-card mt-4">
            {{-- Navigasi tab pada layar desktop untuk berpindah antar kategori grafik --}}
            <ul class="dk-tabs" id="chartTabs" role="tablist">
                @foreach ($tabs as $key => $label)
                    <li role="presentation">
                        <button class="dk-tab-button {{ $loop->first ? 'active' : '' }}" id="tab-{{ $key }}-tab"
                            data-tab-target="#tab-{{ $key }}" type="button" role="tab"
                            aria-controls="tab-{{ $key }}" aria-selected="{{ $loop->first ? 'true' : 'false' }}">
                            <span class="dk-tab-button-text">{{ $label }}</span>
                            <span class="dk-tab-button-indicator"></span>
                        </button>
                    </li>
                @endforeach
            </ul>

            <div class="dk-tab-content mt-0 p-3 sm:p-4 lg:p-6" id="chartTabsContent">
                {{-- Tab grafik berdasarkan jenis kelamin --}}
                <div class="dk-tab-pane show active" id="tab-gender" role="tabpanel"
                    aria-labelledby="tab-gender-tab">
                    @include('public.partials.table-heading', [
                        'title' => $tabs['gender'],
                        'areaDescriptor' => $areaDescriptor,
                        'periodLabel' => $periodLabel,
                    ])
                    @php
                        $chart = $charts['gender'] ?? null;
                        $chartHeight = '600px';
                    @endphp
                    @if (!$chart || empty($chart['labels']))
                        <div class="flex flex-col items-center justify-center py-16 px-4 sm:py-20 md:py-24 text-center">
                            <div class="w-16 h-16 sm:w-20 sm:h-20 md:w-24 md:h-24 mb-4 sm:mb-6 text-gray-400 opacity-50">
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" class="w-full h-full">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                                </svg>
                            </div>
                            <p class="text-sm sm:text-base text-gray-500 font-medium">Data {{ strtolower($tabs['gender']) }} belum tersedia untuk filter yang dipilih.</p>
                        </div>
                    @else
                        <div class="chart-wrapper-container">
                            <div class="chart-wrapper bg-gradient-to-br from-white via-gray-50 to-white rounded-2xl sm:rounded-3xl p-3 sm:p-4 md:p-6 shadow-sm border border-gray-100 hover:shadow-lg hover:border-gray-200 transition-all duration-300 ease-out">
                                <div class="absolute inset-0 bg-gradient-to-br from-primary/5 via-transparent to-transparent rounded-2xl sm:rounded-3xl pointer-events-none"></div>
                                <div class="relative chart-container w-full" style="height: {{ $chartHeight }}; min-height: {{ $chartHeight }};">
                                    <canvas id="chart-gender" data-chart-key="gender" class="w-full h-full"></canvas>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>

                {{-- Tab grafik berdasarkan kelompok umur --}}
                <div class="dk-tab-pane hidden" id="tab-age" role="tabpanel"
                    aria-labelledby="tab-age-tab">
                    @include('public.partials.table-heading', [
                        'title' => $tabs['age'],
                        'areaDescriptor' => $areaDescriptor,
                        'periodLabel' => $periodLabel,
                    ])
                    @php
                        $chart = $charts['age'] ?? null;
                        $chartHeight = '600px';
                    @endphp
                    @if (!$chart || empty($chart['labels']))
                        <div class="flex flex-col items-center justify-center py-16 px-4 sm:py-20 md:py-24 text-center">
                            <div class="w-16 h-16 sm:w-20 sm:h-20 md:w-24 md:h-24 mb-4 sm:mb-6 text-gray-400 opacity-50">
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" class="w-full h-full">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                                </svg>
                            </div>
                            <p class="text-sm sm:text-base text-gray-500 font-medium">Data {{ strtolower($tabs['age']) }} belum tersedia untuk filter yang dipilih.</p>
                        </div>
                    @else
                        <div class="chart-wrapper-container">
                            <div class="chart-wrapper bg-gradient-to-br from-white via-gray-50 to-white rounded-2xl sm:rounded-3xl p-3 sm:p-4 md:p-6 shadow-sm border border-gray-100 hover:shadow-lg hover:border-gray-200 transition-all duration-300 ease-out">
                                <div class="absolute inset-0 bg-gradient-to-br from-primary/5 via-transparent to-transparent rounded-2xl sm:rounded-3xl pointer-events-none"></div>
                                <div class="relative chart-container w-full" style="height: {{ $chartHeight }}; min-height: {{ $chartHeight }};">
                                    <canvas id="chart-age" data-chart-key="age" class="w-full h-full"></canvas>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>

                {{-- Tab grafik umur tunggal --}}
                <div class="dk-tab-pane hidden" id="tab-single-age" role="tabpanel"
                    aria-labelledby="tab-single-age-tab">
                    @include('public.partials.table-heading', [
                        'title' => $tabs['single-age'],
                        'areaDescriptor' => $areaDescriptor,
                        'periodLabel' => $periodLabel,
                    ])
                    @php
                        $chart = $charts['single-age'] ?? null;
                        $chartHeight = '650px';
                    @endphp
                    @if (!$chart || empty($chart['labels']))
                        <div class="flex flex-col items-center justify-center py-16 px-4 sm:py-20 md:py-24 text-center">
                            <div class="w-16 h-16 sm:w-20 sm:h-20 md:w-24 md:h-24 mb-4 sm:mb-6 text-gray-400 opacity-50">
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" class="w-full h-full">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                                </svg>
                            </div>
                            <p class="text-sm sm:text-base text-gray-500 font-medium">Data {{ strtolower($tabs['single-age']) }} belum tersedia untuk filter yang dipilih.</p>
                        </div>
                    @else
                        <div class="chart-wrapper-container">
                            <div class="chart-wrapper bg-gradient-to-br from-white via-gray-50 to-white rounded-2xl sm:rounded-3xl p-3 sm:p-4 md:p-6 shadow-sm border border-gray-100 hover:shadow-lg hover:border-gray-200 transition-all duration-300 ease-out">
                                <div class="absolute inset-0 bg-gradient-to-br from-primary/5 via-transparent to-transparent rounded-2xl sm:rounded-3xl pointer-events-none"></div>
                                <div class="relative chart-container w-full" style="height: {{ $chartHeight }}; min-height: {{ $chartHeight }};">
                                    <canvas id="chart-single-age" data-chart-key="single-age" class="w-full h-full"></canvas>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>

                {{-- Tab grafik pendidikan --}}
                <div class="dk-tab-pane hidden" id="tab-education" role="tabpanel"
                    aria-labelledby="tab-education-tab">
                    @include('public.partials.table-heading', [
                        'title' => $tabs['education'],
                        'areaDescriptor' => $areaDescriptor,
                        'periodLabel' => $periodLabel,
                    ])
                    @php
                        $chart = $charts['education'] ?? null;
                        $chartHeight = '600px';
                    @endphp
                    @if (!$chart || empty($chart['labels']))
                        <div class="flex flex-col items-center justify-center py-16 px-4 sm:py-20 md:py-24 text-center">
                            <div class="w-16 h-16 sm:w-20 sm:h-20 md:w-24 md:h-24 mb-4 sm:mb-6 text-gray-400 opacity-50">
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" class="w-full h-full">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                                </svg>
                            </div>
                            <p class="text-sm sm:text-base text-gray-500 font-medium">Data {{ strtolower($tabs['education']) }} belum tersedia untuk filter yang dipilih.</p>
                        </div>
                    @else
                        <div class="chart-wrapper-container">
                            <div class="chart-wrapper bg-gradient-to-br from-white via-gray-50 to-white rounded-2xl sm:rounded-3xl p-3 sm:p-4 md:p-6 shadow-sm border border-gray-100 hover:shadow-lg hover:border-gray-200 transition-all duration-300 ease-out">
                                <div class="absolute inset-0 bg-gradient-to-br from-primary/5 via-transparent to-transparent rounded-2xl sm:rounded-3xl pointer-events-none"></div>
                                <div class="relative chart-container w-full" style="height: {{ $chartHeight }}; min-height: {{ $chartHeight }};">
                                    <canvas id="chart-education" data-chart-key="education" class="w-full h-full"></canvas>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>

                {{-- Tab grafik pekerjaan --}}
                <div class="dk-tab-pane hidden" id="tab-occupation" role="tabpanel"
                    aria-labelledby="tab-occupation-tab">
                    @include('public.partials.table-heading', [
                        'title' => $tabs['occupation'],
                        'areaDescriptor' => $areaDescriptor,
                        'periodLabel' => $periodLabel,
                    ])
                    @php
                        $chart = $charts['occupation'] ?? null;
                        $chartHeight = '700px';
                    @endphp
                    @if (!$chart || empty($chart['labels']))
                        <div class="flex flex-col items-center justify-center py-16 px-4 sm:py-20 md:py-24 text-center">
                            <div class="w-16 h-16 sm:w-20 sm:h-20 md:w-24 md:h-24 mb-4 sm:mb-6 text-gray-400 opacity-50">
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" class="w-full h-full">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                                </svg>
                            </div>
                            <p class="text-sm sm:text-base text-gray-500 font-medium">Data {{ strtolower($tabs['occupation']) }} belum tersedia untuk filter yang dipilih.</p>
                        </div>
                    @else
                        <div class="chart-wrapper-container">
                            <div class="chart-wrapper bg-gradient-to-br from-white via-gray-50 to-white rounded-2xl sm:rounded-3xl p-3 sm:p-4 md:p-6 shadow-sm border border-gray-100 hover:shadow-lg hover:border-gray-200 transition-all duration-300 ease-out">
                                <div class="absolute inset-0 bg-gradient-to-br from-primary/5 via-transparent to-transparent rounded-2xl sm:rounded-3xl pointer-events-none"></div>
                                <div class="relative chart-container w-full" style="height: {{ $chartHeight }}; min-height: {{ $chartHeight }};">
                                    <canvas id="chart-occupation" data-chart-key="occupation" class="w-full h-full"></canvas>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>

                {{-- Tab grafik status perkawinan --}}
                <div class="dk-tab-pane hidden" id="tab-marital" role="tabpanel"
                    aria-labelledby="tab-marital-tab">
                    @include('public.partials.table-heading', [
                        'title' => $tabs['marital'],
                        'areaDescriptor' => $areaDescriptor,
                        'periodLabel' => $periodLabel,
                    ])
                    @php
                        $chart = $charts['marital'] ?? null;
                        $chartHeight = '600px';
                    @endphp
                    @if (!$chart || empty($chart['labels']))
                        <div class="flex flex-col items-center justify-center py-16 px-4 sm:py-20 md:py-24 text-center">
                            <div class="w-16 h-16 sm:w-20 sm:h-20 md:w-24 md:h-24 mb-4 sm:mb-6 text-gray-400 opacity-50">
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" class="w-full h-full">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                                </svg>
                            </div>
                            <p class="text-sm sm:text-base text-gray-500 font-medium">Data {{ strtolower($tabs['marital']) }} belum tersedia untuk filter yang dipilih.</p>
                        </div>
                    @else
                        <div class="chart-wrapper-container">
                            <div class="chart-wrapper bg-gradient-to-br from-white via-gray-50 to-white rounded-2xl sm:rounded-3xl p-3 sm:p-4 md:p-6 shadow-sm border border-gray-100 hover:shadow-lg hover:border-gray-200 transition-all duration-300 ease-out">
                                <div class="absolute inset-0 bg-gradient-to-br from-primary/5 via-transparent to-transparent rounded-2xl sm:rounded-3xl pointer-events-none"></div>
                                <div class="relative chart-container w-full" style="height: {{ $chartHeight }}; min-height: {{ $chartHeight }};">
                                    <canvas id="chart-marital" data-chart-key="marital" class="w-full h-full"></canvas>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>

                {{-- Tab grafik kartu keluarga --}}
                <div class="dk-tab-pane hidden" id="tab-kk" role="tabpanel"
                    aria-labelledby="tab-kk-tab">
                    @include('public.partials.table-heading', [
                        'title' => $tabs['kk'],
                        'areaDescriptor' => $areaDescriptor,
                        'periodLabel' => $periodLabel,
                    ])
                    @php
                        $chart = $charts['kk'] ?? null;
                        $chartHeight = '600px';
                    @endphp
                    @if (!$chart || empty($chart['labels']))
                        <div class="flex flex-col items-center justify-center py-16 px-4 sm:py-20 md:py-24 text-center">
                            <div class="w-16 h-16 sm:w-20 sm:h-20 md:w-24 md:h-24 mb-4 sm:mb-6 text-gray-400 opacity-50">
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" class="w-full h-full">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                                </svg>
                            </div>
                            <p class="text-sm sm:text-base text-gray-500 font-medium">Data {{ strtolower($tabs['kk']) }} belum tersedia untuk filter yang dipilih.</p>
                        </div>
                    @else
                        <div class="chart-wrapper-container">
                            <div class="chart-wrapper bg-gradient-to-br from-white via-gray-50 to-white rounded-2xl sm:rounded-3xl p-3 sm:p-4 md:p-6 shadow-sm border border-gray-100 hover:shadow-lg hover:border-gray-200 transition-all duration-300 ease-out">
                                <div class="absolute inset-0 bg-gradient-to-br from-primary/5 via-transparent to-transparent rounded-2xl sm:rounded-3xl pointer-events-none"></div>
                                <div class="relative chart-container w-full" style="height: {{ $chartHeight }}; min-height: {{ $chartHeight }};">
                                    <canvas id="chart-kk" data-chart-key="kk" class="w-full h-full"></canvas>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>

                {{-- Tab grafik kepala keluarga --}}
                <div class="dk-tab-pane hidden" id="tab-household" role="tabpanel"
                    aria-labelledby="tab-household-tab">
                    @include('public.partials.table-heading', [
                        'title' => $tabs['household'],
                        'areaDescriptor' => $areaDescriptor,
                        'periodLabel' => $periodLabel,
                    ])
                    @php
                        $chart = $charts['household'] ?? null;
                        $chartHeight = '600px';
                    @endphp
                    @if (!$chart || empty($chart['labels']))
                        <div class="flex flex-col items-center justify-center py-16 px-4 sm:py-20 md:py-24 text-center">
                            <div class="w-16 h-16 sm:w-20 sm:h-20 md:w-24 md:h-24 mb-4 sm:mb-6 text-gray-400 opacity-50">
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" class="w-full h-full">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                                </svg>
                            </div>
                            <p class="text-sm sm:text-base text-gray-500 font-medium">Data {{ strtolower($tabs['household']) }} belum tersedia untuk filter yang dipilih.</p>
                        </div>
                    @else
                        <div class="chart-wrapper-container">
                            <div class="chart-wrapper bg-gradient-to-br from-white via-gray-50 to-white rounded-2xl sm:rounded-3xl p-3 sm:p-4 md:p-6 shadow-sm border border-gray-100 hover:shadow-lg hover:border-gray-200 transition-all duration-300 ease-out">
                                <div class="absolute inset-0 bg-gradient-to-br from-primary/5 via-transparent to-transparent rounded-2xl sm:rounded-3xl pointer-events-none"></div>
                                <div class="relative chart-container w-full" style="height: {{ $chartHeight }}; min-height: {{ $chartHeight }};">
                                    <canvas id="chart-household" data-chart-key="household" class="w-full h-full"></canvas>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>

                {{-- Tab grafik agama --}}
                <div class="dk-tab-pane hidden" id="tab-religion" role="tabpanel"
                    aria-labelledby="tab-religion-tab">
                    @include('public.partials.table-heading', [
                        'title' => $tabs['religion'],
                        'areaDescriptor' => $areaDescriptor,
                        'periodLabel' => $periodLabel,
                    ])
                    @php
                        $chart = $charts['religion'] ?? null;
                        $chartHeight = '600px';
                    @endphp
                    @if (!$chart || empty($chart['labels']))
                        <div class="flex flex-col items-center justify-center py-16 px-4 sm:py-20 md:py-24 text-center">
                            <div class="w-16 h-16 sm:w-20 sm:h-20 md:w-24 md:h-24 mb-4 sm:mb-6 text-gray-400 opacity-50">
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" class="w-full h-full">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                                </svg>
                            </div>
                            <p class="text-sm sm:text-base text-gray-500 font-medium">Data {{ strtolower($tabs['religion']) }} belum tersedia untuk filter yang dipilih.</p>
                        </div>
                    @else
                        <div class="chart-wrapper-container">
                            <div class="chart-wrapper bg-gradient-to-br from-white via-gray-50 to-white rounded-2xl sm:rounded-3xl p-3 sm:p-4 md:p-6 shadow-sm border border-gray-100 hover:shadow-lg hover:border-gray-200 transition-all duration-300 ease-out">
                                <div class="absolute inset-0 bg-gradient-to-br from-primary/5 via-transparent to-transparent rounded-2xl sm:rounded-3xl pointer-events-none"></div>
                                <div class="relative chart-container w-full" style="height: {{ $chartHeight }}; min-height: {{ $chartHeight }};">
                                    <canvas id="chart-religion" data-chart-key="religion" class="w-full h-full"></canvas>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>

                {{-- Tab grafik wajib KTP --}}
                <div class="dk-tab-pane hidden" id="tab-wajib-ktp" role="tabpanel"
                    aria-labelledby="tab-wajib-ktp-tab">
                    @include('public.partials.table-heading', [
                        'title' => $tabs['wajib-ktp'],
                        'areaDescriptor' => $areaDescriptor,
                        'periodLabel' => $periodLabel,
                    ])
                    @php
                        $chart = $charts['wajib-ktp'] ?? null;
                        $chartHeight = '600px';
                    @endphp
                    @if (!$chart || empty($chart['labels']))
                        <div class="flex flex-col items-center justify-center py-16 px-4 sm:py-20 md:py-24 text-center">
                            <div class="w-16 h-16 sm:w-20 sm:h-20 md:w-24 md:h-24 mb-4 sm:mb-6 text-gray-400 opacity-50">
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" class="w-full h-full">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                                </svg>
                            </div>
                            <p class="text-sm sm:text-base text-gray-500 font-medium">Data {{ strtolower($tabs['wajib-ktp']) }} belum tersedia untuk filter yang dipilih.</p>
                        </div>
                    @else
                        <div class="chart-wrapper-container">
                            <div class="chart-wrapper bg-gradient-to-br from-white via-gray-50 to-white rounded-2xl sm:rounded-3xl p-3 sm:p-4 md:p-6 shadow-sm border border-gray-100 hover:shadow-lg hover:border-gray-200 transition-all duration-300 ease-out">
                                <div class="absolute inset-0 bg-gradient-to-br from-primary/5 via-transparent to-transparent rounded-2xl sm:rounded-3xl pointer-events-none"></div>
                                <div class="relative chart-container w-full" style="height: {{ $chartHeight }}; min-height: {{ $chartHeight }};">
                                    <canvas id="chart-wajib-ktp" data-chart-key="wajib-ktp" class="w-full h-full"></canvas>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    @endif
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const chartConfigs = @json($charts);
            const chartsNeedingTags = @json($chartsNeedingTags);
            const chartsAngledTags = @json($chartsAngledTags);
            const chartInstances = {};

            // Chart.js custom plugin untuk category tags
            const categoryTagPlugin = {
                id: 'categoryTagPlugin',
                afterDraw(chart, args, pluginOptions) {
                    const key = chart.canvas.dataset.chartKey;
                    if (!chartsNeedingTags.includes(key)) {
                        return;
                    }
                    const labels = pluginOptions?.labels ?? chart.config.data.labels;
                    if (!labels || !labels.length) {
                        return;
                    }

                    const { ctx, chartArea, scales } = chart;
                    const xScale = scales.x;
                    if (!xScale) {
                        return;
                    }

                    const fontSize = 10;
                    ctx.save();
                    ctx.font = `${fontSize}px "Inter", "Poppins", sans-serif`;
                    ctx.textAlign = 'center';
                    ctx.textBaseline = 'middle';

                    const pluginAngled = !!pluginOptions?.angled;
                    const isAngled = pluginAngled || chartsAngledTags.includes(key);
                    const isFullVertical = chartsAngledTags.includes(key);
                    const needsRotation = isFullVertical;

                    labels.forEach((label, index) => {
                        const x = xScale.getPixelForValue(index);
                        const text = label.length > 24 ? label.slice(0, 24) + '…' : label;

                        if (needsRotation) {
                            ctx.save();
                            const yZero = chart.scales.y ? chart.scales.y.getPixelForValue(0) : chartArea.bottom;
                            ctx.translate(x, yZero + 6);
                            ctx.rotate(-Math.PI / 2);
                            ctx.fillStyle = '#1f3f7a';
                            ctx.textAlign = 'right';
                            ctx.textBaseline = 'middle';
                            ctx.fillText(text, 0, 0);
                            ctx.restore();
                        } else {
                            const metrics = ctx.measureText(text);
                            const paddingX = 6;
                            const paddingY = 4;
                            const boxWidth = metrics.width + paddingX * 2;
                            const boxHeight = fontSize + paddingY * 2;
                            const boxX = x - boxWidth / 2;
                            const boxY = chartArea.bottom + 6;

                            ctx.fillStyle = 'rgba(55, 125, 255, 0.12)';
                            ctx.beginPath();
                            if (ctx.roundRect) {
                                ctx.roundRect(boxX, boxY, boxWidth, boxHeight, 6);
                            } else {
                                ctx.rect(boxX, boxY, boxWidth, boxHeight);
                            }
                            ctx.fill();

                            ctx.fillStyle = '#1f3f7a';
                            ctx.textAlign = 'center';
                            ctx.fillText(text, x, boxY + boxHeight / 2);
                        }
                    });

                    ctx.restore();
                }
            };

            Chart.register(categoryTagPlugin);

            // Fungsi untuk memastikan chart di-initialize
            const ensureChart = (key) => {
                if (chartInstances[key]) {
                    // Chart sudah ada, resize jika perlu
                    setTimeout(() => {
                        chartInstances[key].resize();
                    }, 100);
                    return chartInstances[key];
                }

                const config = chartConfigs[key];
                if (!config || !Array.isArray(config.labels) || !config.labels.length || !Array.isArray(config.datasets) || !config.datasets.length) {
                    return null;
                }

                const canvas = document.getElementById(`chart-${key}`);
                if (!canvas) {
                    return null;
                }

                // Tunggu sedikit agar canvas sudah ter-render
                setTimeout(() => {
                    const ctx = canvas.getContext('2d');
                    canvas.dataset.chartKey = key;
                    const needsTags = chartsNeedingTags.includes(key);
                    const angledTags = chartsAngledTags.includes(key);
                    const longestLabel = config.labels.reduce((max, label) => Math.max(max, (label || '').length), 0);
                    const bottomPadding = angledTags
                        ? Math.min(260, Math.max(160, longestLabel * 6 + 32))
                        : (needsTags ? 70 : 16);

                    chartInstances[key] = new Chart(ctx, {
                        type: 'bar',
                        data: {
                            labels: config.labels,
                            datasets: config.datasets
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            layout: {
                                padding: {
                                    bottom: bottomPadding
                                }
                            },
                            scales: {
                                y: {
                                    beginAtZero: true,
                                    ticks: {
                                        callback(value) {
                                            return new Intl.NumberFormat('id-ID').format(value);
                                        }
                                    }
                                },
                                x: {
                                    ticks: {
                                        autoSkip: false,
                                        maxRotation: 45,
                                        minRotation: 0,
                                        callback(value, index, ticks) {
                                            const label = (ticks[index] && ticks[index].label) || '';
                                            return label.length > 20 ? label.substring(0, 20) + '…' : label;
                                        }
                                    }
                                }
                            },
                            plugins: {
                                legend: {
                                    display: false
                                },
                                tooltip: {
                                    callbacks: {
                                        label(context) {
                                            const label = context.dataset.label || '';
                                            const raw = context.parsed.y ?? context.parsed;
                                            return `${label}: ${new Intl.NumberFormat('id-ID').format(raw)}`;
                                        }
                                    }
                                },
                                categoryTagPlugin: {
                                    labels: config.labels,
                                    angled: angledTags
                                }
                            }
                        }
                    });
                }, 50);
            };

            // Helper function untuk show tab
            function showTab(targetId) {
                // Hide all tab panes
                document.querySelectorAll('.dk-tab-pane').forEach(function(pane) {
                    pane.classList.add('hidden');
                    pane.classList.remove('show', 'active');
                });
                
                // Show target tab pane
                var targetPane = document.querySelector(targetId);
                if (targetPane) {
                    targetPane.classList.remove('hidden');
                    targetPane.classList.add('show', 'active');
                }
                
                // Update tab buttons
                document.querySelectorAll('#chartTabs button').forEach(function(btn) {
                    btn.classList.remove('active');
                    btn.setAttribute('aria-selected', 'false');
                });
                
                var activeButton = document.querySelector('#chartTabs button[data-tab-target="' + targetId + '"]');
                if (activeButton) {
                    activeButton.classList.add('active');
                    activeButton.setAttribute('aria-selected', 'true');
                }
            }

            // Baca parameter category dari URL saat halaman dimuat
            var urlParams = new URLSearchParams(window.location.search);
            var categoryParam = urlParams.get('category');
            var initialCategory = categoryParam || 'gender';
            
            // Jika ada parameter category, aktifkan tab yang sesuai
            if (categoryParam) {
                var targetTabId = '#tab-' + categoryParam;
                var targetTab = document.querySelector(targetTabId);
                if (targetTab) {
                    showTab(targetTabId);
                } else {
                    // Jika tab tidak ditemukan, gunakan tab pertama
                    showTab('#tab-gender');
                    initialCategory = 'gender';
                }
            } else {
                // Jika tidak ada parameter, aktifkan tab pertama
                showTab('#tab-gender');
            }

            // Ambil elemen tombol tab
            var tabButtons = document.querySelectorAll('#chartTabs button[data-tab-target]');

            // Event listener untuk tombol tab
            tabButtons.forEach(function (button) {
                button.addEventListener('click', function (event) {
                    event.preventDefault();
                    var targetSelector = this.getAttribute('data-tab-target');
                    if (!targetSelector) {
                        return;
                    }

                    showTab(targetSelector);
                    
                    // Update URL dengan parameter category
                    var category = targetSelector.replace('#tab-', '');
                    var url = new URL(window.location.href);
                    url.searchParams.set('category', category);
                    window.history.pushState({}, '', url.toString());
                    
                    // Initialize chart untuk tab yang aktif
                    setTimeout(function() {
                        ensureChart(category);
                    }, 150);
                });
            });

            // Initialize chart untuk tab aktif saat halaman dimuat
            setTimeout(function() {
                ensureChart(initialCategory);
            }, 300);
        });
    </script>
@endpush


