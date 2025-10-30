@extends('layouts.dukcapil', ['title' => 'Data Agregat'])

@section('content')
    <div class="dk-card mb-4">
        <div class="card-body p-4">
            <div class="row g-3 align-items-lg-end">
                <div class="col-xl-3 col-lg-4">
                    <h6 class="dk-card__title mb-1">Filter Wilayah & Periode</h6>
                    <p class="text-xs text-muted mb-0">
                        Pilih tahun, semester, kecamatan, atau desa/kelurahan untuk menampilkan data spesifik.
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
                                <a href="{{ route('public.data') }}" class="btn btn-outline-secondary flex-shrink-0 px-3">
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
            <strong>Data belum tersedia.</strong> Unggah dataset terlebih dahulu untuk menampilkan ringkasan agregat.
        </div>
    @else
        @php
            $areaRows = $areaTable['rows'] ?? [];
            $areaTotals = $areaTable['totals'] ?? ['male' => 0, 'female' => 0, 'total' => 0];
            $areaColumn = $areaTable['column'] ?? 'Wilayah';
            $districtName = $selectedDistrict ? optional($districts->firstWhere('id', (int) $selectedDistrict))->name : null;
            $villageName = $selectedVillage ? optional($villages->firstWhere('id', (int) $selectedVillage))->name : null;
            $kabupatenName = config('app.region_name', 'Kabupaten Madiun');
            $areaSegments = [$kabupatenName];
            if ($districtName) {
                $areaSegments[] = 'Kecamatan ' . $districtName;
                $areaSegments[] = $villageName ? ('Desa/Kelurahan ' . $villageName) : 'Seluruh Desa/Kelurahan';
            } else {
                $areaSegments[] = 'Seluruh Kecamatan';
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
        @endphp

        <div class="dk-card mt-4">
            <div class="card-body p-4">
                <ul class="nav nav-pills dk-tabs" id="aggregateTabs" role="tablist">
                    @foreach ($tabs as $key => $label)
                        <li class="nav-item" role="presentation">
                            <button class="nav-link {{ $loop->first ? 'active' : '' }}" id="tab-{{ $key }}-tab"
                                data-bs-toggle="tab" data-bs-target="#tab-{{ $key }}" type="button" role="tab"
                                aria-controls="tab-{{ $key }}" aria-selected="{{ $loop->first ? 'true' : 'false' }}">
                                {{ $label }}
                            </button>
                        </li>
                    @endforeach
                </ul>

                <div class="tab-content dk-tab-content mt-4" id="aggregateTabsContent">
                    <div class="tab-pane fade show active dk-tab-pane" id="tab-gender" role="tabpanel"
                        aria-labelledby="tab-gender-tab">
                        @include('public.partials.table-heading', [
                            'title' => $tabs['gender'],
                            'areaDescriptor' => $areaDescriptor,
                            'periodLabel' => $periodLabel,
                        ])
                        <div class="table-responsive">
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
                                            <td>{{ $row['name'] }}</td>
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
                        </div>
                    </div>

                    <div class="tab-pane fade dk-tab-pane" id="tab-age" role="tabpanel"
                        aria-labelledby="tab-age-tab">
                        @include('public.partials.table-heading', [
                            'title' => $tabs['age'],
                            'areaDescriptor' => $areaDescriptor,
                            'periodLabel' => $periodLabel,
                        ])
                        <div class="table-responsive dk-table-scroll">
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
                        </div>
                    </div>

                    <div class="tab-pane fade dk-tab-pane" id="tab-education" role="tabpanel"
                        aria-labelledby="tab-education-tab">
                        @include('public.partials.table-heading', [
                            'title' => $tabs['education'],
                            'areaDescriptor' => $areaDescriptor,
                            'periodLabel' => $periodLabel,
                        ])
                        <div class="table-responsive dk-table-scroll">
                            @include('public.partials.matrix-table', [
                                'matrix' => $educationMatrix,
                                'emptyMessage' => 'Data pendidikan belum tersedia.'
                            ])
                        </div>
                    </div>

                    <div class="tab-pane fade dk-tab-pane" id="tab-occupation" role="tabpanel"
                        aria-labelledby="tab-occupation-tab">
                        @include('public.partials.table-heading', [
                            'title' => $tabs['occupation'],
                            'areaDescriptor' => $areaDescriptor,
                            'periodLabel' => $periodLabel,
                        ])
                        <div class="table-responsive dk-table-scroll">
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
                        </div>
                    </div>

                    <div class="tab-pane fade dk-tab-pane" id="tab-single-age" role="tabpanel"
                        aria-labelledby="tab-single-age-tab">
                        @include('public.partials.table-heading', [
                            'title' => $tabs['single-age'],
                            'areaDescriptor' => $areaDescriptor,
                            'periodLabel' => $periodLabel,
                        ])
                        <div class="table-responsive dk-table-scroll">
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
                        </div>
                    </div>

                    <div class="tab-pane fade dk-tab-pane" id="tab-wajib-ktp" role="tabpanel"
                        aria-labelledby="tab-wajib-ktp-tab">
                        @include('public.partials.table-heading', [
                            'title' => $tabs['wajib-ktp'],
                            'areaDescriptor' => $areaDescriptor,
                            'periodLabel' => $periodLabel,
                        ])
                        <div class="table-responsive dk-table-scroll">
                            @include('public.partials.matrix-table', [
                                'matrix' => $wajibKtpMatrix,
                                'emptyMessage' => 'Data wajib KTP belum tersedia.'
                            ])
                        </div>
                    </div>

                    <div class="tab-pane fade dk-tab-pane" id="tab-marital" role="tabpanel"
                        aria-labelledby="tab-marital-tab">
                        @include('public.partials.table-heading', [
                            'title' => $tabs['marital'],
                            'areaDescriptor' => $areaDescriptor,
                            'periodLabel' => $periodLabel,
                        ])
                        <div class="table-responsive dk-table-scroll">
                            @include('public.partials.matrix-table', [
                                'matrix' => $maritalMatrix,
                                'emptyMessage' => 'Data status perkawinan belum tersedia.'
                            ])
                        </div>
                    </div>

                    <div class="tab-pane fade dk-tab-pane" id="tab-household" role="tabpanel"
                        aria-labelledby="tab-household-tab">
                        @include('public.partials.table-heading', [
                            'title' => $tabs['household'],
                            'areaDescriptor' => $areaDescriptor,
                            'periodLabel' => $periodLabel,
                        ])
                        <div class="table-responsive dk-table-scroll">
                            @include('public.partials.matrix-table', [
                                'matrix' => $headHouseholdMatrix,
                                'emptyMessage' => 'Data kepala keluarga belum tersedia.'
                            ])
                        </div>
                    </div>

                    <div class="tab-pane fade dk-tab-pane" id="tab-religion" role="tabpanel"
                        aria-labelledby="tab-religion-tab">
                        @include('public.partials.table-heading', [
                            'title' => $tabs['religion'],
                            'areaDescriptor' => $areaDescriptor,
                            'periodLabel' => $periodLabel,
                        ])
                        <div class="table-responsive dk-table-scroll">
                            @include('public.partials.matrix-table', [
                                'matrix' => $religionMatrix,
                                'emptyMessage' => 'Data agama belum tersedia.'
                            ])
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
@endsection







