<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? 'Data Agregat - Fullscreen' }}</title>
    <link rel="icon" type="image/png" href="{{ asset('vendor/corporate-ui/img/kabupaten-madiun.png') }}?v=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"
        integrity="sha512-31on1Uwx1PcT6zG17Q6C7GdYr387cMGX5CujjJVOk+3O8VjMBYPWaFzx5b9mzfFh1YgUo10xXMYN9bB+FsSjVg=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="{{ asset('css/dukcapil.css') . '?v=' . filemtime(public_path('css/dukcapil.css')) }}">
    <style>
        body {
            margin: 0;
            padding: 20px;
            background: #e2e8f0;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
        }
        .fullscreen-container {
            max-width: 100%;
            background: #ffffff;
            border-radius: 16px;
            box-shadow: 0 24px 48px rgba(15, 23, 42, 0.08);
            padding: 2rem;
        }
        .fullscreen-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.5rem;
            padding-bottom: 1rem;
            border-bottom: 1px solid #e2e8f0;
        }
        .fullscreen-header h4 {
            margin: 0;
            font-size: 1.25rem;
            font-weight: 600;
            color: #007151;
        }
        .fullscreen-close {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 1000;
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 0.5rem 1rem;
            color: #475569;
            text-decoration: none;
            font-size: 0.875rem;
            transition: all 0.2s ease;
            box-shadow: 0 2px 8px rgba(15, 23, 42, 0.1);
        }
        .fullscreen-close:hover {
            background: #f8fafc;
            color: #007151;
            border-color: #007151;
        }
        .dk-table-scroll {
            max-height: none !important;
            overflow: visible !important;
        }
        .fullscreen-table-container {
            width: 100%;
            overflow-x: auto;
            overflow-y: visible !important;
            max-height: none !important;
        }
        .fullscreen-table-container .dk-table {
            width: 100%;
        }
    </style>
</head>
<body>
    <a href="{{ route('public.data', request()->query()) }}" class="fullscreen-close">
        <i class="fas fa-times me-1"></i> Tutup
    </a>
    
    @php
        $tabs = [
            'gender' => 'Jenis Kelamin',
            'age' => 'Kelompok Umur',
            'single-age' => 'Umur Tunggal',
            'education' => 'Pendidikan',
            'occupation' => 'Pekerjaan',
            'marital' => 'Status Perkawinan',
            'household' => 'Kepala Keluarga',
            'religion' => 'Agama',
            'wajib-ktp' => 'Wajib KTP',
        ];
        $categoryLabel = $tabs[$category] ?? 'Data Agregat';
        $districtName = $selectedDistrict ? optional($districts->firstWhere('id', (int) $selectedDistrict))->name : null;
        $villageName = $selectedVillage ? optional($villages->firstWhere('id', (int) $selectedVillage))->name : null;
        $kabupatenName = config('app.region_name', 'Kabupaten Madiun');
        $areaSegments = [$kabupatenName];
        if ($districtName) {
            $areaSegments[] = 'Kecamatan ' . \Illuminate\Support\Str::title($districtName);
            $areaSegments[] = $villageName ? ('Desa/Kelurahan ' . \Illuminate\Support\Str::title($villageName)) : 'Semua Desa/Kelurahan';
        } else {
            $areaSegments[] = 'Semua Desa/Kelurahan';
        }
        $areaDescriptor = implode(' • ', array_filter($areaSegments));
        $periodLabelParts = [];
        if (!empty($period['semester'])) {
            $periodLabelParts[] = 'Semester ' . $period['semester'];
        }
        if (!empty($period['year'])) {
            $periodLabelParts[] = 'Tahun ' . $period['year'];
        }
        $periodLabel = !empty($periodLabelParts) ? implode(' ', $periodLabelParts) : null;
        $areaRows = $areaTable['rows'] ?? [];
        $areaTotals = $areaTable['totals'] ?? ['male' => 0, 'female' => 0, 'total' => 0];
        $areaColumn = $areaTable['column'] ?? 'Wilayah';
    @endphp
    
    <div class="fullscreen-container">
        <div class="fullscreen-header">
            <div>
                <h4 class="mb-2">{{ $categoryLabel }}</h4>
                @if (!empty($areaDescriptor))
                    <p class="text-xs text-muted mb-1">{{ $areaDescriptor }}</p>
                @endif
                @if ($periodLabel)
                    <span class="badge bg-primary">{{ $periodLabel }}</span>
                @endif
            </div>
        </div>

        @if (!$period)
            <div class="alert alert-warning border-0">
                <strong>Data belum tersedia.</strong> Unggah dataset terlebih dahulu untuk menampilkan ringkasan agregat.
            </div>
        @else
            <div class="table-responsive fullscreen-table-container">
                @if ($category === 'gender')
                    <table class="table table-sm dk-table mb-0">
                        <thead>
                            <tr>
                                <th style="width: 64px">No</th>
                                <th>{{ $areaColumn }}</th>
                                <th class="text-end">L</th>
                                <th class="text-end">P</th>
                                <th class="text-end">Jumlah</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($areaRows as $index => $row)
                                @php
                                    $isHighlighted = !empty($row['highlight']);
                                @endphp
                                <tr class="{{ $isHighlighted ? 'table-active' : '' }}">
                                    <td>{{ $index + 1 }}</td>
                                    <td>{{ \Illuminate\Support\Str::title($row['name']) }}</td>
                                    <td class="text-end">{{ number_format($row['male']) }}</td>
                                    <td class="text-end">{{ number_format($row['female']) }}</td>
                                    <td class="text-end fw-semibold">{{ number_format($row['total']) }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center text-muted">Data jenis kelamin belum tersedia.</td>
                                </tr>
                            @endforelse
                        </tbody>
                        @if (!empty($areaRows))
                            <tfoot>
                                <tr>
                                    <th colspan="2">Jumlah Keseluruhan</th>
                                    <th class="text-end">{{ number_format($areaTotals['male'] ?? 0) }}</th>
                                    <th class="text-end">{{ number_format($areaTotals['female'] ?? 0) }}</th>
                                    <th class="text-end">{{ number_format($areaTotals['total'] ?? 0) }}</th>
                                </tr>
                            </tfoot>
                        @endif
                    </table>
                @elseif ($category === 'age')
                    <table class="table table-sm dk-table mb-0">
                        <thead>
                            <tr>
                                <th style="width: 64px">No</th>
                                <th>Kelompok</th>
                                <th class="text-end">L</th>
                                <th class="text-end">P</th>
                                <th class="text-end">Jumlah</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($ageGroups as $index => $row)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $row['label'] }}</td>
                                    <td class="text-end">{{ number_format($row['male']) }}</td>
                                    <td class="text-end">{{ number_format($row['female']) }}</td>
                                    <td class="text-end fw-semibold">{{ number_format($row['total']) }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center text-muted">Data kelompok umur belum tersedia.</td>
                                </tr>
                            @endforelse
                        </tbody>
                        @if (!empty($ageGroups))
                            @php
                                $ageMale = array_sum(array_column($ageGroups, 'male'));
                                $ageFemale = array_sum(array_column($ageGroups, 'female'));
                                $ageTotal = array_sum(array_column($ageGroups, 'total'));
                            @endphp
                            <tfoot>
                                <tr>
                                    <th colspan="2">Jumlah Keseluruhan</th>
                                    <th class="text-end">{{ number_format($ageMale) }}</th>
                                    <th class="text-end">{{ number_format($ageFemale) }}</th>
                                    <th class="text-end">{{ number_format($ageTotal) }}</th>
                                </tr>
                            </tfoot>
                        @endif
                    </table>
                @elseif ($category === 'single-age')
                    <table class="table table-sm dk-table mb-0">
                        <thead>
                            <tr>
                                <th style="width: 64px">No</th>
                                <th>Usia</th>
                                <th class="text-end">L</th>
                                <th class="text-end">P</th>
                                <th class="text-end">Jumlah</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($singleAges as $row)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $row['label'] }}</td>
                                    <td class="text-end">{{ number_format($row['male']) }}</td>
                                    <td class="text-end">{{ number_format($row['female']) }}</td>
                                    <td class="text-end fw-semibold">{{ number_format($row['total']) }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center text-muted">Data umur tunggal belum tersedia.</td>
                                </tr>
                            @endforelse
                        </tbody>
                        @if (!empty($singleAges))
                            @php
                                $singleMale = array_sum(array_column($singleAges, 'male'));
                                $singleFemale = array_sum(array_column($singleAges, 'female'));
                                $singleTotal = array_sum(array_column($singleAges, 'total'));
                            @endphp
                            <tfoot>
                                <tr>
                                    <th colspan="2">Jumlah Keseluruhan</th>
                                    <th class="text-end">{{ number_format($singleMale) }}</th>
                                    <th class="text-end">{{ number_format($singleFemale) }}</th>
                                    <th class="text-end">{{ number_format($singleTotal) }}</th>
                                </tr>
                            </tfoot>
                        @endif
                    </table>
                @elseif ($category === 'occupation')
                    <table class="table table-sm dk-table mb-0">
                        <thead>
                            <tr>
                                <th style="width: 64px">No</th>
                                <th>Pekerjaan</th>
                                <th class="text-end">L</th>
                                <th class="text-end">P</th>
                                <th class="text-end">Jumlah</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($topOccupations as $item)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $item['label'] }}</td>
                                    <td class="text-end">{{ number_format($item['male']) }}</td>
                                    <td class="text-end">{{ number_format($item['female']) }}</td>
                                    <td class="text-end fw-semibold">{{ number_format($item['total']) }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center text-muted">Data pekerjaan belum tersedia.</td>
                                </tr>
                            @endforelse
                        </tbody>
                        @if (!empty($topOccupations))
                            @php
                                $jobMale = array_sum(array_column($topOccupations, 'male'));
                                $jobFemale = array_sum(array_column($topOccupations, 'female'));
                                $jobTotal = array_sum(array_column($topOccupations, 'total'));
                            @endphp
                            <tfoot>
                                <tr>
                                    <th colspan="2">Jumlah Keseluruhan</th>
                                    <th class="text-end">{{ number_format($jobMale) }}</th>
                                    <th class="text-end">{{ number_format($jobFemale) }}</th>
                                    <th class="text-end">{{ number_format($jobTotal) }}</th>
                                </tr>
                            </tfoot>
                        @endif
                    </table>
                @elseif ($category === 'education')
                    @include('public.partials.matrix-table', [
                        'matrix' => $educationMatrix,
                        'emptyMessage' => 'Data pendidikan belum tersedia.'
                    ])
                @elseif ($category === 'wajib-ktp')
                    @include('public.partials.matrix-table', [
                        'matrix' => $wajibKtpMatrix,
                        'emptyMessage' => 'Data wajib KTP belum tersedia.'
                    ])
                @elseif ($category === 'marital')
                    @include('public.partials.matrix-table', [
                        'matrix' => $maritalMatrix,
                        'emptyMessage' => 'Data status perkawinan belum tersedia.'
                    ])
                @elseif ($category === 'household')
                    @include('public.partials.matrix-table', [
                        'matrix' => $headHouseholdMatrix,
                        'emptyMessage' => 'Data kepala keluarga belum tersedia.'
                    ])
                @elseif ($category === 'religion')
                    @include('public.partials.matrix-table', [
                        'matrix' => $religionMatrix,
                        'emptyMessage' => 'Data agama belum tersedia.'
                    ])
                @endif
            </div>
        @endif
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL"
        crossorigin="anonymous"></script>
</body>
</html>

