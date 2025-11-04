@extends('layouts.dukcapil', ['title' => 'Grafik Data'])

@section('content')
    <div class="dk-card mb-4">
        <div class="card-body p-4">
            <div class="row g-3 align-items-lg-end">
                <div class="col-xl-3 col-lg-4">
                    <h6 class="dk-card__title mb-1">Filter Wilayah & Periode</h6>
                    <p class="text-xs text-muted mb-0">
                        Pilih tahun, semester, kecamatan, atau desa/kelurahan untuk menampilkan grafik spesifik.
                    </p>
                </div>
                <form method="GET" class="col-xl-9 col-lg-8">
                    <div class="row g-3 align-items-md-end">
                        <div class="col-xl-2 col-lg-3 col-md-6">
                            <label class="form-label text-uppercase text-xs text-muted">Tahun</label>
                            <select class="form-select" name="year" onchange="this.form.submit()">
                                <option value="">Terbaru</option>
                                @foreach ($years as $year)
                                    <option value="{{ $year }}" {{ (int) ($selectedYear ?? 0) === $year ? 'selected' : '' }}>
                                        {{ $year }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-xl-2 col-lg-3 col-md-6">
                            <label class="form-label text-uppercase text-xs text-muted">Semester</label>
                            <select class="form-select" name="semester" onchange="this.form.submit()">
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
                        <div class="col-xl-4 col-lg-3 col-md-6">
                            <label class="form-label text-uppercase text-xs text-muted">Kecamatan</label>
                            <select class="form-select" name="district_id" onchange="this.form.submit()">
                                <option value="">Seluruh Kecamatan</option>
                                @foreach ($districts as $district)
                                    <option value="{{ $district->id }}" {{ (int) $selectedDistrict === $district->id ? 'selected' : '' }}>
                                        {{ $district->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-xl-4 col-lg-3 col-md-6 dk-filter-village">
                            <label class="form-label text-uppercase text-xs text-muted">Desa/Kelurahan</label>
                            <div class="d-flex flex-column flex-sm-row gap-2 align-items-sm-end">
                                <select class="form-select flex-fill" name="village_id" onchange="this.form.submit()" {{ $villages->isEmpty() ? 'disabled' : '' }}>
                                    <option value="">Semua Desa/Kel</option>
                                    @foreach ($villages as $village)
                                        <option value="{{ $village->id }}" {{ (int) $selectedVillage === $village->id ? 'selected' : '' }}>
                                            {{ $village->name }}
                                        </option>
                                    @endforeach
                                </select>
                                <a href="{{ route('public.charts') }}" class="btn btn-outline-secondary flex-shrink-0 px-3">
                                    Reset
                                </a>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @if (!$period)
        <div class="alert alert-warning border-0 dk-card">
            <strong>Data belum tersedia.</strong> Unggah dataset terlebih dahulu untuk menampilkan grafik agregat.
        </div>
    @else
        @php
            $kabupatenName = config('app.region_name', 'Kabupaten Madiun');
            $districtName = $selectedDistrict ? optional($districts->firstWhere('id', (int) $selectedDistrict))->name : null;
            $villageName = $selectedVillage ? optional($villages->firstWhere('id', (int) $selectedVillage))->name : null;
            $areaSegments = [$kabupatenName];
            if ($districtName) {
                $areaSegments[] = 'Kecamatan ' . $districtName;
                $areaSegments[] = $villageName ? ('Desa/Kelurahan ' . $villageName) : 'Seluruh Desa/Kelurahan';
            } else {
                $areaSegments[] = 'Seluruh Kecamatan';
            }
            $areaDescriptor = implode(' • ', array_filter($areaSegments));
            $periodParts = [];
            if (!empty($period['semester'])) {
                $periodParts[] = 'Semester ' . $period['semester'];
            }
            if (!empty($period['year'])) {
                $periodParts[] = 'Tahun ' . $period['year'];
            }
            $periodLabel = !empty($periodParts) ? implode(' ', $periodParts) : null;
        @endphp

        <div class="row g-4">
            @foreach ($charts as $key => $chart)
                <div class="col-12">
                    <div class="dk-card h-100">
                        <div class="card-body">
                            @include('public.partials.table-heading', [
                                'title' => $chart['title'] ?? ucfirst($key),
                                'areaDescriptor' => $areaDescriptor,
                                'periodLabel' => $periodLabel,
                            ])

                            @if (empty($chart['labels']))
                                <p class="text-center text-muted mb-0">
                                    Data {{ strtolower($chart['title'] ?? $key) }} belum tersedia untuk filter yang dipilih.
                                </p>
                            @else
                                @php
                                    $chartHeight = match ($key) {
                                        'occupation' => '540px',
                                        'single-age' => '420px',
                                        default => '360px'
                                    };
                                @endphp
                                <div class="chart-container" style="height: {{ $chartHeight }};">
                                    <canvas id="chart-{{ $key }}"></canvas>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const charts = @json($charts);
            const chartsNeedingTags = @json($chartsNeedingTags);
            const chartsAngledTags = @json($chartsAngledTags);

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

                    const {ctx, chartArea, scales} = chart;
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

            Object.entries(charts).forEach(([key, config]) => {
                if (!config.labels || config.labels.length === 0 || !Array.isArray(config.datasets) || config.datasets.length === 0) {
                    return;
                }

                const canvas = document.getElementById(`chart-${key}`);
                if (!canvas) {
                    return;
                }

                const ctx = canvas.getContext('2d');
                canvas.dataset.chartKey = key;
                const needsTags = chartsNeedingTags.includes(key);
                const angledTags = chartsAngledTags.includes(key);
                const longestLabel = config.labels.reduce((max, label) => Math.max(max, (label || '').length), 0);
                const bottomPadding = angledTags
                    ? Math.min(260, Math.max(160, longestLabel * 6 + 32))
                    : (needsTags ? 70 : 16);
                new Chart(ctx, {
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
                                    callback: function(value) {
                                        return new Intl.NumberFormat('id-ID').format(value);
                                    }
                                }
                            },
                            x: {
                                ticks: {
                                    autoSkip: false,
                                    maxRotation: 45,
                                    minRotation: 0,
                                    callback: function(value, index, ticks) {
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
                                    label: function(context) {
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
            });
        });
    </script>
@endpush


