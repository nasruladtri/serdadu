@extends('layouts.dukcapil', ['title' => 'Perbandingan Data'])

@push('styles')
    <style>
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

        .chart-container {
            min-height: 400px;
            width: 100%;
            position: relative;
        }
        
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

        .chart-legend {
            display: flex;
            flex-wrap: wrap;
            gap: 1rem;
            justify-content: center;
            align-items: center;
        }
        
        .chart-legend-item {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-size: 0.875rem;
            color: #374151;
        }
        
        .chart-legend-color {
            width: 16px;
            height: 16px;
            border-radius: 4px;
            flex-shrink: 0;
        }

        .compare-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.25rem 0.75rem;
            border-radius: 0.5rem;
            font-size: 0.75rem;
            font-weight: 500;
        }

        .badge-primary {
            background-color: #dbeafe;
            color: #1e40af;
        }

        .badge-compare {
            background-color: #fef3c7;
            color: #92400e;
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

        // Build labels untuk primary dan compare - gunakan input user yang sebenarnya
        $primaryLabel = 'Data Utama';
        if ($primaryYear && $primarySemester) {
            $primaryLabel = 'S' . $primarySemester . ' ' . $primaryYear;
            if ($primaryDistrict) {
                $primaryLabel .= ' - ' . \Illuminate\Support\Str::title($districts->firstWhere('id', $primaryDistrict)->name ?? '');
            }
        } elseif ($primaryPeriod) {
            // Fallback jika tidak ada input, gunakan period yang di-resolve
            $primaryLabel = 'S' . $primaryPeriod['semester'] . ' ' . $primaryPeriod['year'];
            if ($primaryDistrict) {
                $primaryLabel .= ' - ' . \Illuminate\Support\Str::title($districts->firstWhere('id', $primaryDistrict)->name ?? '');
            }
        }

        $compareLabel = 'Data Pembanding';
        if ($compareYear && $compareSemester) {
            $compareLabel = 'S' . $compareSemester . ' ' . $compareYear;
            if ($compareDistrict) {
                $compareLabel .= ' - ' . \Illuminate\Support\Str::title($districts->firstWhere('id', $compareDistrict)->name ?? '');
            }
        } elseif ($comparePeriod) {
            // Fallback jika tidak ada input, gunakan period yang di-resolve
            $compareLabel = 'S' . $comparePeriod['semester'] . ' ' . $comparePeriod['year'];
            if ($compareDistrict) {
                $compareLabel .= ' - ' . \Illuminate\Support\Str::title($districts->firstWhere('id', $compareDistrict)->name ?? '');
            }
        }
    @endphp

    {{-- Filter Section --}}
    <div class="dk-card mb-4">
        <div class="p-4">
            <h6 class="dk-card__title mb-4">Pengaturan Perbandingan</h6>
            
            <form method="GET" action="{{ route('public.compare') }}">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    {{-- Primary Data Filters --}}
                    <div class="space-y-4">
                        <div class="flex items-center gap-2 mb-3">
                            <span class="compare-badge badge-primary">Data Utama</span>
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <div>
                                <label class="block text-xs font-medium text-gray-500 uppercase mb-1">Tahun</label>
                                <select class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500" name="year">
                                    <option value="">Pilih Tahun</option>
                                    @foreach ($years as $year)
                                        <option value="{{ $year }}" {{ (int) ($primaryYear ?? 0) === $year ? 'selected' : '' }}>
                                            {{ $year }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-500 uppercase mb-1">Semester</label>
                                <select class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500" name="semester" id="primary-semester">
                                    <option value="">Pilih Semester</option>
                                    @if (!empty($allAvailableSemesters))
                                        @foreach ($allAvailableSemesters as $option)
                                            <option value="{{ $option }}" {{ (int) ($primarySemester ?? 0) === $option ? 'selected' : '' }}>
                                                Semester {{ $option }}
                                            </option>
                                        @endforeach
                                    @elseif (!empty($primaryAvailableSemesters))
                                        @foreach ($primaryAvailableSemesters as $option)
                                            <option value="{{ $option }}" {{ (int) ($primarySemester ?? 0) === $option ? 'selected' : '' }}>
                                                Semester {{ $option }}
                                            </option>
                                        @endforeach
                                    @else
                                        <option value="" disabled>Belum tersedia</option>
                                    @endif
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-500 uppercase mb-1">Kecamatan</label>
                                <select class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500" name="district_id">
                                    <option value="">Semua Kecamatan</option>
                                    @foreach ($districts as $district)
                                        <option value="{{ $district->id }}" {{ (int) ($primaryDistrict ?? 0) === $district->id ? 'selected' : '' }}>
                                            {{ \Illuminate\Support\Str::title($district->name) }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-500 uppercase mb-1">Desa/Kelurahan</label>
                                <select class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 disabled:bg-gray-100 disabled:cursor-not-allowed" name="village_id" {{ $primaryVillages->isEmpty() ? 'disabled' : '' }}>
                                    <option value="">Semua Desa/Kelurahan</option>
                                    @foreach ($primaryVillages as $village)
                                        <option value="{{ $village->id }}" {{ (int) ($primaryVillage ?? 0) === $village->id ? 'selected' : '' }}>
                                            {{ \Illuminate\Support\Str::title($village->name) }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                    {{-- Compare Data Filters --}}
                    <div class="space-y-4">
                        <div class="flex items-center gap-2 mb-3">
                            <span class="compare-badge badge-compare">Data Pembanding</span>
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <div>
                                <label class="block text-xs font-medium text-gray-500 uppercase mb-1">Tahun</label>
                                <select class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500" name="compare_year" id="compare-year">
                                    <option value="">Pilih Tahun</option>
                                    @foreach ($years as $year)
                                        <option value="{{ $year }}" {{ (int) ($compareYear ?? 0) === $year ? 'selected' : '' }}>
                                            {{ $year }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-500 uppercase mb-1">Semester</label>
                                <select class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500" name="compare_semester" id="compare-semester">
                                    <option value="">Pilih Semester</option>
                                    @if (!empty($compareAvailableSemesters))
                                        {{-- Prioritaskan semester yang tersedia untuk tahun yang dipilih --}}
                                        @foreach ($compareAvailableSemesters as $option)
                                            <option value="{{ $option }}" {{ (int) ($compareSemester ?? 0) === $option ? 'selected' : '' }}>
                                                Semester {{ $option }}
                                            </option>
                                        @endforeach
                                    @elseif (!empty($allAvailableSemesters))
                                        {{-- Fallback ke semua semester jika tahun belum dipilih --}}
                                        @foreach ($allAvailableSemesters as $option)
                                            <option value="{{ $option }}" {{ (int) ($compareSemester ?? 0) === $option ? 'selected' : '' }}>
                                                Semester {{ $option }}
                                            </option>
                                        @endforeach
                                    @else
                                        <option value="" disabled>Belum tersedia</option>
                                    @endif
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-500 uppercase mb-1">Kecamatan</label>
                                <select class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500" name="compare_district_id">
                                    <option value="">Semua Kecamatan</option>
                                    @foreach ($districts as $district)
                                        <option value="{{ $district->id }}" {{ (int) ($compareDistrict ?? 0) === $district->id ? 'selected' : '' }}>
                                            {{ \Illuminate\Support\Str::title($district->name) }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-500 uppercase mb-1">Desa/Kelurahan</label>
                                <select class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 disabled:bg-gray-100 disabled:cursor-not-allowed" name="compare_village_id" {{ $compareVillages->isEmpty() ? 'disabled' : '' }}>
                                    <option value="">Semua Desa/Kelurahan</option>
                                    @foreach ($compareVillages as $village)
                                        <option value="{{ $village->id }}" {{ (int) ($compareVillage ?? 0) === $village->id ? 'selected' : '' }}>
                                            {{ \Illuminate\Support\Str::title($village->name) }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mt-4 flex justify-end">
                    <button type="submit" class="px-6 py-2 bg-[#007151] text-white rounded-lg hover:bg-[#005a3f] transition-colors font-medium">
                        Bandingkan
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- Tab Navigation - Always Visible --}}
    <div class="dk-card mt-4">
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
            @foreach ($tabs as $key => $label)
                <div class="dk-tab-pane {{ $loop->first ? 'show active' : 'hidden' }}" id="tab-{{ $key }}" role="tabpanel"
                    aria-labelledby="tab-{{ $key }}-tab">
                    
                    @if (!$primaryPeriod || !$comparePeriod)
                        {{-- Placeholder when data not selected --}}
                        <div class="flex flex-col items-center justify-center py-16 px-4 text-center">
                            <div class="w-16 h-16 mx-auto mb-4 text-gray-400 opacity-50">
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" class="w-full h-full">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                                </svg>
                            </div>
                            <h3 class="text-lg font-semibold text-gray-900 mb-2">Pilih Data untuk Dibandingkan</h3>
                            <p class="text-sm text-gray-500">Silakan pilih periode dan wilayah untuk Data Utama dan Data Pembanding, lalu klik tombol "Bandingkan" di atas.</p>
                        </div>
                        @else
                            {{-- Side-by-side Charts --}}
                            @php
                                $primaryChart = $primaryCharts[$key] ?? null;
                                $compareChart = $compareCharts[$key] ?? null;
                                $chartHeight = in_array($key, ['single-age', 'occupation']) ? '700px' : '600px';
                            @endphp

                            <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 lg:gap-6">
                                {{-- Primary Chart (Left) --}}
                                <div class="chart-wrapper-container">
                                    <div class="chart-wrapper bg-gradient-to-br from-white via-gray-50 to-white rounded-2xl sm:rounded-3xl p-3 sm:p-4 md:p-6 shadow-sm border border-gray-100">
                                        <div class="mb-4 flex items-center justify-center">
                                            <span class="compare-badge badge-primary">{{ $primaryLabel }}</span>
                                        </div>
                                        @if (!$primaryChart || empty($primaryChart['labels']))
                                            <div class="flex flex-col items-center justify-center py-12 px-4 text-center">
                                                <div class="w-12 h-12 mb-3 text-gray-400 opacity-50">
                                                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" class="w-full h-full">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                                                    </svg>
                                                </div>
                                                <p class="text-sm text-gray-500 font-medium">Data belum tersedia</p>
                                            </div>
                                        @else
                                            <div class="relative chart-container w-full" style="height: {{ $chartHeight }}; min-height: {{ $chartHeight }};">
                                                <canvas id="chart-primary-{{ $key }}" data-chart-key="primary-{{ $key }}" class="w-full h-full"></canvas>
                                            </div>
                                            <div class="chart-legend mt-4 pt-4 border-t border-gray-200" id="legend-primary-{{ $key }}"></div>
                                        @endif
                                    </div>
                                </div>

                                {{-- Compare Chart (Right) --}}
                                <div class="chart-wrapper-container">
                                    <div class="chart-wrapper bg-gradient-to-br from-white via-gray-50 to-white rounded-2xl sm:rounded-3xl p-3 sm:p-4 md:p-6 shadow-sm border border-gray-100">
                                        <div class="mb-4 flex items-center justify-center">
                                            <span class="compare-badge badge-compare">{{ $compareLabel }}</span>
                                        </div>
                                        @if (!$compareChart || empty($compareChart['labels']))
                                            <div class="flex flex-col items-center justify-center py-12 px-4 text-center">
                                                <div class="w-12 h-12 mb-3 text-gray-400 opacity-50">
                                                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" class="w-full h-full">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                                                    </svg>
                                                </div>
                                                <p class="text-sm text-gray-500 font-medium">Data belum tersedia</p>
                                            </div>
                                        @else
                                            <div class="relative chart-container w-full" style="height: {{ $chartHeight }}; min-height: {{ $chartHeight }};">
                                                <canvas id="chart-compare-{{ $key }}" data-chart-key="compare-{{ $key }}" class="w-full h-full"></canvas>
                                            </div>
                                            <div class="chart-legend mt-4 pt-4 border-t border-gray-200" id="legend-compare-{{ $key }}"></div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endif
                </div>
            @endforeach
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Tab navigation - always available
            function showTab(targetId) {
                document.querySelectorAll('.dk-tab-pane').forEach(function(pane) {
                    pane.classList.add('hidden');
                    pane.classList.remove('show', 'active');
                });
                
                var targetPane = document.querySelector(targetId);
                if (targetPane) {
                    targetPane.classList.remove('hidden');
                    targetPane.classList.add('show', 'active');
                }
                
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

            // Initialize tab navigation
            var urlParams = new URLSearchParams(window.location.search);
            var categoryParam = urlParams.get('category');
            var initialCategory = categoryParam || 'gender';
            
            if (categoryParam) {
                var targetTabId = '#tab-' + categoryParam;
                var targetTab = document.querySelector(targetTabId);
                if (targetTab) {
                    showTab(targetTabId);
                } else {
                    showTab('#tab-gender');
                    initialCategory = 'gender';
                }
            } else {
                showTab('#tab-gender');
            }

            // Tab button event listeners - setup once
            var tabButtons = document.querySelectorAll('#chartTabs button[data-tab-target]');
            var ensureChartFunction = null;

            @if ($primaryPeriod && $comparePeriod)
            // Chart rendering - only when data is available
            const primaryCharts = @json($primaryCharts);
            const compareCharts = @json($compareCharts);
            const chartsNeedingTags = @json($chartsNeedingTags);
            const chartsAngledTags = @json($chartsAngledTags);
            const chartInstances = {};

            const primaryLabel = @json($primaryLabel);
            const compareLabel = @json($compareLabel);

            // Category tag plugin
            const categoryTagPlugin = {
                id: 'categoryTagPlugin',
                afterDraw(chart, args, pluginOptions) {
                    const chartKey = chart.canvas.dataset.chartKey;
                    // Extract the base key (remove 'primary-' or 'compare-' prefix)
                    const key = chartKey.replace('primary-', '').replace('compare-', '');
                    if (!chartsNeedingTags.includes(key)) return;
                    
                    const labels = pluginOptions?.labels ?? chart.config.data.labels;
                    if (!labels || !labels.length) return;

                    const { ctx, scales } = chart;
                    const xScale = scales.x;
                    if (!xScale) return;

                    const fontSize = 10;
                    ctx.save();
                    ctx.font = `${fontSize}px "Inter", "Poppins", sans-serif`;
                    ctx.textAlign = 'center';
                    ctx.textBaseline = 'middle';

                    const isAngled = chartsAngledTags.includes(key);
                    const needsRotation = isAngled;

                    labels.forEach((label, index) => {
                        const x = xScale.getPixelForValue(index);
                        const y = chart.chartArea.bottom + (needsRotation ? 20 : 10);
                        
                        ctx.save();
                        ctx.translate(x, y);
                        if (needsRotation) {
                            ctx.rotate(-Math.PI / 2);
                        }
                        ctx.fillText(label || '', 0, 0);
                        ctx.restore();
                    });
                    ctx.restore();
                }
            };

            if (typeof Chart !== 'undefined') {
                Chart.register(categoryTagPlugin);
            }

            function ensureChart(key) {
                // Render primary chart (left)
                const primaryCanvas = document.getElementById('chart-primary-' + key);
                if (primaryCanvas && !chartInstances['primary-' + key]) {
                    const primaryConfig = primaryCharts[key];
                    if (primaryConfig && primaryConfig.labels && primaryConfig.labels.length > 0) {
                        renderChart('primary-' + key, primaryCanvas, primaryConfig, primaryLabel);
                    }
                }

                // Render compare chart (right)
                const compareCanvas = document.getElementById('chart-compare-' + key);
                if (compareCanvas && !chartInstances['compare-' + key]) {
                    const compareConfig = compareCharts[key];
                    if (compareConfig && compareConfig.labels && compareConfig.labels.length > 0) {
                        renderChart('compare-' + key, compareCanvas, compareConfig, compareLabel);
                    }
                }
            }

            function renderChart(chartKey, canvas, config, label) {
                if (chartInstances[chartKey]) return;

                setTimeout(() => {
                    if (typeof Chart === 'undefined') return;
                    
                    const key = chartKey.replace('primary-', '').replace('compare-', '');
                    const ctx = canvas.getContext('2d');
                    canvas.dataset.chartKey = chartKey;
                    const labels = config.labels || [];
                    const datasets = config.datasets || [];
                    const needsTags = chartsNeedingTags.includes(key);
                    const angledTags = chartsAngledTags.includes(key);
                    const longestLabel = labels.reduce((max, label) => Math.max(max, (label || '').length), 0);
                    const bottomPadding = angledTags
                        ? Math.min(260, Math.max(160, longestLabel * 6 + 32))
                        : (needsTags ? 70 : 16);

                    chartInstances[chartKey] = new Chart(ctx, {
                        type: 'bar',
                        data: {
                            labels: labels,
                            datasets: datasets
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
                                    labels: labels,
                                    angled: angledTags
                                }
                            }
                        }
                    });

                    // Buat legend
                    const legendElement = document.getElementById('legend-' + chartKey);
                    if (legendElement) {
                        legendElement.innerHTML = '';
                        datasets.forEach((dataset) => {
                            const color = Array.isArray(dataset.backgroundColor) 
                                ? dataset.backgroundColor[0] 
                                : dataset.backgroundColor;
                            const legendItem = document.createElement('div');
                            legendItem.className = 'chart-legend-item';
                            legendItem.innerHTML = `
                                <div class="chart-legend-color" style="background-color: ${color};"></div>
                                <span>${dataset.label || ''}</span>
                            `;
                            legendElement.appendChild(legendItem);
                        });
                    }
                }, 50);
            }

            ensureChartFunction = ensureChart;

            // Initialize first chart
            setTimeout(function() {
                if (typeof Chart !== 'undefined' && ensureChartFunction) {
                    ensureChartFunction(initialCategory);
                }
            }, 300);
            @endif

            // Tab button event listeners
            tabButtons.forEach(function (button) {
                button.addEventListener('click', function (event) {
                    event.preventDefault();
                    var targetSelector = this.getAttribute('data-tab-target');
                    if (!targetSelector) return;

                    showTab(targetSelector);
                    
                    var category = targetSelector.replace('#tab-', '');
                    var url = new URL(window.location.href);
                    url.searchParams.set('category', category);
                    window.history.pushState({}, '', url.toString());

                    // Initialize chart if data is available
                    @if ($primaryPeriod && $comparePeriod)
                    if (ensureChartFunction && typeof Chart !== 'undefined') {
                        setTimeout(function() {
                            ensureChartFunction(category);
                        }, 150);
                    }
                    @endif
                });
            });

        });
    </script>
@endpush

