@extends('layouts.dukcapil', ['title' => 'Beranda'])

@push('styles')
    {{-- Berkas gaya dasar Leaflet untuk elemen kontrol peta --}}
    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
    {{-- Berkas CSS khusus halaman landing agar tidak lagi tercampur di dalam Blade --}}
    <link rel="stylesheet" href="{{ asset('css/landing.css') . '?v=' . filemtime(public_path('css/landing.css')) }}">
@endpush

@section('content')
    {{-- Kontainer utama halaman landing untuk pengunjung publik --}}
    <div class="landing-page">
        @if (!$period)
            {{-- Tampilkan pengingat apabila dataset belum pernah diunggah --}}
            <div class="alert alert-warning border-0 dk-card">
                <div class="d-flex align-items-center gap-2">
                    <i class="fas fa-exclamation-triangle"></i>
                    <div>
                        <strong>Data belum tersedia.</strong> Silakan unggah dataset terlebih dahulu melalui halaman admin.
                    </div>
                </div>
            </div>
        @else
            {{-- Konten statistik dan peta hanya dirender ketika data terbaru tersedia --}}
            <div class="row g-4 align-items-stretch landing-layout">
                <div class="col-12 col-lg-5 col-xl-4">
                    <div class="dk-card h-100 landing-overview">
                        <div class="card-body p-4">
                            <h6 class="dk-card__title mb-4">Data Agregat Kependudukan Terbaru</h6>

                            <div class="landing-wilayah-section">
                                <h6 class="text-uppercase text-xs text-muted mb-3">Wilayah</h6>
                                <div class="table-responsive">
                                    <table class="table table-sm dk-table">
                                        <tbody>
                                            <tr>
                                                <td class="fw-semibold">Nama Wilayah</td>
                                                <td class="text-end">Madiun</td>
                                            </tr>
                                            <tr>
                                                <td class="fw-semibold">Jumlah Daerah</td>
                                                <td class="text-end">{{ number_format($districtCount) }}</td>
                                            </tr>
                                            <tr>
                                                <td class="fw-semibold">Jumlah Desa/Kel</td>
                                                <td class="text-end">{{ number_format($villageCount) }}</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            {{-- Ringkasan jumlah penduduk berdasarkan kategori utama --}}
                            <div class="mb-4">
                                <h6 class="text-uppercase text-xs text-muted mb-3">Jumlah Penduduk</h6>
                                <div class="row g-3 landing-metric-grid">
                                    @php
                                        // Hitung nilai agregat serta persentase yang dipakai untuk visualisasi progress bar
                                        $totalPop = $totals['population'] ?? 0;
                                        $malePop = $totals['male'] ?? 0;
                                        $femalePop = $totals['female'] ?? 0;
                                        $wajibKtpTotal = $wajibKtp['total'] ?? 0;
                                        $malePercent = $totalPop > 0 ? ($malePop / $totalPop) * 100 : 0;
                                        $femalePercent = $totalPop > 0 ? ($femalePop / $totalPop) * 100 : 0;
                                        $wajibKtpPercent = $totalPop > 0 ? ($wajibKtpTotal / $totalPop) * 100 : 0;
                                    @endphp

                                    <div class="col-12 landing-metric-item">
                                        <div class="landing-metric-card landing-metric-card--primary">
                                            <div class="landing-metric-icon">
                                                <img src="{{ asset('img/penduduk.png') }}" alt="Total Penduduk" class="landing-metric-icon__img">
                                            </div>
                                            <div class="dk-metric__label text-white-50 mb-1">Total Penduduk</div>
                                            <div class="dk-metric text-white mb-2">{{ number_format($totalPop) }}</div>
                                            <div class="landing-metric-progress">
                                                <div class="landing-metric-progress-bar" style="--landing-progress: 100%;"></div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-6 landing-metric-item">
                                        <div class="landing-metric-card landing-metric-card--male">
                                            <div class="landing-metric-icon">
                                                <img src="{{ asset('img/l.png') }}" alt="Penduduk Laki-laki" class="landing-metric-icon__img">
                                            </div>
                                            <div class="dk-metric__label mb-1">Laki-laki</div>
                                            <div class="dk-metric dk-metric--sm mb-2">{{ number_format($malePop) }}</div>
                                            <div class="landing-metric-progress">
                                                <div class="landing-metric-progress-bar" style="--landing-progress: {{ $malePercent }}%;"></div>
                                            </div>
                                            <div class="landing-metric-percentage">
                                                <i class="fas fa-chart-line me-1"></i>{{ number_format($malePercent, 1) }}%
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-6 landing-metric-item">
                                        <div class="landing-metric-card landing-metric-card--female">
                                            <div class="landing-metric-icon">
                                                <img src="{{ asset('img/p.png') }}" alt="Penduduk Perempuan" class="landing-metric-icon__img">
                                            </div>
                                            <div class="dk-metric__label mb-1">Perempuan</div>
                                            <div class="dk-metric dk-metric--sm mb-2">{{ number_format($femalePop) }}</div>
                                            <div class="landing-metric-progress">
                                                <div class="landing-metric-progress-bar" style="--landing-progress: {{ $femalePercent }}%;"></div>
                                            </div>
                                            <div class="landing-metric-percentage">
                                                <i class="fas fa-chart-line me-1"></i>{{ number_format($femalePercent, 1) }}%
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-12 landing-metric-item">
                                        <div class="landing-metric-card landing-metric-card--ktp">
                                            <div class="landing-metric-icon">
                                                <img src="{{ asset('img/ktp.png') }}" alt="Wajib KTP" class="landing-metric-icon__img">
                                            </div>
                                            <div class="dk-metric__label mb-1">Wajib KTP (&ge; 17 tahun)</div>
                                            <div class="dk-metric dk-metric--sm mb-2">{{ number_format($wajibKtpTotal) }}</div>
                                            <div class="landing-metric-progress">
                                                <div class="landing-metric-progress-bar" style="--landing-progress: {{ $wajibKtpPercent }}%;"></div>
                                            </div>
                                            <div class="landing-metric-percentage">
                                                <i class="fas fa-chart-pie me-1"></i>{{ number_format($wajibKtpPercent, 1) }}%
                                            </div>
                                            <small class="text-muted d-block mt-2">
                                                <i class="fas fa-mars me-1"></i> L: {{ number_format($wajibKtp['male'] ?? 0) }} &bull;
                                                <i class="fas fa-venus ms-2 me-1"></i> P: {{ number_format($wajibKtp['female'] ?? 0) }}
                                            </small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-12 col-lg-7 col-xl-8">
                    <div class="dk-card h-100 landing-map-card">
                        <div class="card-body p-0 d-flex flex-column h-100">
                            <div class="landing-map-card__header d-flex flex-column flex-lg-row align-items-lg-start gap-3">
                                <h6 class="dk-card__title mb-0">Peta Persebaran Penduduk Kabupaten Madiun</h6>
                                @if(!empty($districtOptions) && $districtOptions->count())
                                    <div class="ms-lg-auto w-100 w-lg-auto landing-map-card__filter">
                                        <label for="landing-district-filter" class="form-label text-xs text-muted mb-1">Kecamatan</label>
                                        <select id="landing-district-filter" class="form-select form-select-sm">
                                            <option value="">SEMUA</option>
                                            @foreach($districtOptions as $district)
                                                <option value="{{ $district->code }}" data-slug="{{ \Illuminate\Support\Str::slug($district->name) }}">
                                                    {{ \Illuminate\Support\Str::title($district->name) }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                @endif
                            </div>
                            {{-- Area kanan berisi peta interaktif beserta loader bawaan --}}
                            <div class="landing-map-card__map d-flex flex-grow-1 position-relative">
                                <div id="landing-map-loader" class="landing-loading landing-map-loader position-absolute w-100 h-100">
                                    <div class="text-center">
                                        <div class="landing-loading-spinner mx-auto mb-3"></div>
                                        <div class="text-muted small">Memuat peta...</div>
                                    </div>
                                </div>
                                <div class="dk-map flex-grow-1">
                                    <div id="landing-map"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>
@endsection


@push('scripts')
    {{-- Skrip peta interaktif yang mengikat data demografis ke layer Leaflet --}}
    <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
    <script src="{{ asset('map/Peta Madiun/kab.js') }}"></script>
    <script src="{{ asset('map/Peta Madiun/kec.js') }}"></script>
    <script src="{{ asset('map/Peta Madiun/kel.js') }}"></script>

    @php
        $mapStats = $mapStats ?? [
            'districts' => ['by_code' => [], 'by_slug' => []],
            'villages' => ['by_code' => [], 'by_slug' => []],
        ];
    @endphp

    <script>
        (function () {
            // Ambil statistik yang sudah diproses di backend sebagai sumber data peta
            var mapStats = @json($mapStats);

            /**
             * Pastikan setiap bagian statistik mempunyai struktur default agar aman diakses.
             */
            function ensureStats(section) {
                section = section || {};
                return {
                    by_code: section.by_code || {},
                    by_slug: section.by_slug || {},
                };
            }

            // Bentuk indeks pencarian cepat untuk kecamatan dan desa/kelurahan
            var districtStatsIndex = ensureStats(mapStats.districts);
            var villageStatsIndex = ensureStats(mapStats.villages);

            // Siapkan beberapa opsi tile layer agar pengguna bisa memilih tampilan peta
            var carto = L.tileLayer('https://{s}.basemaps.cartocdn.com/light_all/{z}/{x}/{y}{r}.png', {
                maxZoom: 19,
                attribution: '&copy; OpenStreetMap contributors &copy; CARTO',
            });
            var cartoDark = L.tileLayer('https://{s}.basemaps.cartocdn.com/dark_all/{z}/{x}/{y}{r}.png', {
                maxZoom: 19,
                attribution: '&copy; OpenStreetMap contributors &copy; CARTO',
            });
            var cartoVoyager = L.tileLayer('https://{s}.basemaps.cartocdn.com/rastertiles/voyager/{z}/{x}/{y}{r}.png', {
                maxZoom: 19,
                attribution: '&copy; OpenStreetMap contributors &copy; CARTO',
            });
            var osm = L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                maxZoom: 19,
                attribution: '&copy; OpenStreetMap contributors',
            });
            var googleSat = L.tileLayer('https://{s}.google.com/vt/lyrs=s&x={x}&y={y}&z={z}',{
                maxZoom: 20,
                subdomains:['mt0','mt1','mt2','mt3'],
                attribution: 'Imagery &copy; Google'
            });

            // Inisialisasi peta dengan fokus ke Kabupaten Madiun
            var map = L.map('landing-map', {
                center: [-7.629, 111.515],
                zoom: 11,
                layers: [cartoVoyager],
            });
            var TARGET_VIEW_WIDTH_KM = 15;

            // Hilangkan indikator loading ketika peta siap digunakan
            map.whenReady(function() {
                var loader = document.getElementById('landing-map-loader');
                if (loader) {
                    loader.style.opacity = '0';
                    loader.style.transition = 'opacity 0.3s ease';
                    setTimeout(function() {
                        loader.style.display = 'none';
                    }, 300);
                }
            });

            // Buat pane khusus agar batas administratif memiliki urutan tumpukan yang konsisten
            map.createPane('kelPane');
            map.getPane('kelPane').style.zIndex = 470;

            map.createPane('kecPane');
            map.getPane('kecPane').style.zIndex = 460;

            map.createPane('kabPane');
            map.getPane('kabPane').style.zIndex = 480;
            map.getPane('kabPane').style.pointerEvents = 'none';

            map.createPane('hoverPane');
            map.getPane('hoverPane').style.zIndex = 600;
            map.getPane('hoverPane').style.pointerEvents = 'none';

            map.createPane('labelPane');
            map.getPane('labelPane').style.zIndex = 650;
            map.getPane('labelPane').style.pointerEvents = 'none';

            L.control.scale({ imperial: false, maxWidth: 160 }).addTo(map);

            /**
             * Gaya garis batas kabupaten.
             */
            function styleKab() {
                return { color: '#c0392b', weight: 2, fillOpacity: 0, fill: false };
            }

            /**
             * Gaya garis batas kecamatan.
             */
            function styleKec() {
                return { color: '#63d199', weight: 1.7, fillColor: '#63d199', fillOpacity: 0 };
            }

            /**
             * Gaya garis batas desa/kelurahan.
             */
            function styleKel() {
                return { color: '#00b4d8', weight: 1.3, fillColor: '#48cae4', fillOpacity: 0 };
            }

            /**
             * Format angka agar ramah dibaca dengan pemisah ribuan lokal.
             */
            function formatNumber(value) {
                var num = Number(value);
                return Number.isFinite(num) ? num.toLocaleString('id-ID') : '-';
            }

            /**
             * Escape nilai teks agar aman dimasukkan ke HTML popup.
             */
            function escapeHtml(value) {
                if (value === null || value === undefined) {
                    return '';
                }
                return String(value)
                    .replace(/&/g, '&amp;')
                    .replace(/</g, '&lt;')
                    .replace(/>/g, '&gt;')
                    .replace(/"/g, '&quot;')
                    .replace(/'/g, '&#39;');
            }

            /**
             * Susun isi popup Leaflet dari judul dan daftar data kunci.
             */
            function buildPopupContent(title, rows) {
                var html = '<div class="dk-popup">';
                if (title) {
                    html += '<strong>' + title + '</strong>';
                }
                if (rows && rows.length) {
                    html += '<table class="dk-popup__table">';
                    rows.forEach(function (row) {
                        html += '<tr><td><strong>'
                            + row.label + '</strong></td><td>'
                            + row.value + '</td></tr>';
                    });
                    html += '</table>';
                }
                html += '</div>';
                return html;
            }

            /**
             * Normalisasi nama kecamatan/desa menjadi slug sederhana.
             */
            function normalizeName(value) {
                if (value === null || value === undefined) {
                    return null;
                }
                var text = String(value).toLowerCase();
                if (text.normalize) {
                    text = text.normalize('NFD').replace(/[\u0300-\u036f]/g, '');
                }
                text = text.replace(/[^a-z0-9]+/g, '-').replace(/^-+|-+$/g, '');
                return text || null;
            }

            /**
             * Bentuk beberapa variasi slug untuk menoleransi perbedaan penulisan.
             */
            function slugVariants(slug) {
                if (!slug) {
                    return [];
                }
                var variants = [slug];
                var noDash = slug.replace(/-/g, '');
                if (noDash && variants.indexOf(noDash) === -1) {
                    variants.push(noDash);
                }
                return variants;
            }

            /**
             * Hasilkan kemungkinan variasi kode wilayah agar pencocokan lebih fleksibel.
             */
            function codeAliases(value) {
                var digits = value === undefined || value === null ? '' : String(value).replace(/\D+/g, '');
                if (!digits) {
                    return [];
                }
                var aliases = [digits];
                if (digits.length >= 3) {
                    aliases.push(digits.slice(-3).padStart(3, '0'));
                }
                if (digits.length >= 4) {
                    aliases.push(digits.slice(-4).padStart(4, '0'));
                }
                if (digits.length >= 5) {
                    aliases.push(digits.slice(-5).padStart(5, '0'));
                }
                return Array.from(new Set(aliases));
            }

            // Layer batas kabupaten selalu ada sebagai referensi utama
            var kabLayer = window.kab ? L.geoJSON(window.kab, { style: styleKab, pane: 'kabPane' }).addTo(map) : L.layerGroup().addTo(map);
            var kecLayer = null;
            var kelLayer = null;

            /**
             * Pastikan urutan layer mengikuti struktur administratif (desa di atas kecamatan, dst).
             */
            function ensureLayerOrder() {
                if (kelLayer && kelLayer.bringToFront) {
                    kelLayer.bringToFront();
                }
                if (kecLayer && kecLayer.bringToFront) {
                    kecLayer.bringToFront();
                }
                if (kabLayer && kabLayer.bringToFront) {
                    kabLayer.bringToFront();
                }
            }

            /**
             * Lepas layer dari peta bila sedang tidak digunakan.
             */
            function removeLayer(layer) {
                if (layer && map.hasLayer(layer)) {
                    map.removeLayer(layer);
                }
            }

            /**
             * Hitung centroid sederhana dari polygon agar label berada di posisi yang wajar.
             */
            function computeRingCentroid(ring) {
                if (!Array.isArray(ring) || ring.length < 3) {
                    return null;
                }
                var twiceArea = 0;
                var x = 0;
                var y = 0;
                for (var i = 0; i < ring.length - 1; i++) {
                    var p1 = ring[i];
                    var p2 = ring[i + 1];
                    if (!p1 || !p2) {
                        continue;
                    }
                    var f = (p1[0] * p2[1]) - (p2[0] * p1[1]);
                    twiceArea += f;
                    x += (p1[0] + p2[0]) * f;
                    y += (p1[1] + p2[1]) * f;
                }
                if (!twiceArea) {
                    return null;
                }
                var areaFactor = twiceArea * 3;
                return [x / areaFactor, y / areaFactor];
            }

            /**
             * Dapatkan titik representatif sebuah fitur geojson.
             */
            function computeFeatureCenter(feature) {
                if (!feature || !feature.geometry) {
                    return null;
                }
                var geom = feature.geometry;
                var type = geom.type;
                var coords = geom.coordinates;
                if (!coords) {
                    return null;
                }

                var result = null;
                var bestArea = 0;

                function accumulateCentroid(rings) {
                    if (!Array.isArray(rings) || !rings.length) {
                        return;
                    }
                    var outerRing = rings[0];
                    if (!Array.isArray(outerRing) || outerRing.length < 4) {
                        return;
                    }
                    var centroid = computeRingCentroid(outerRing);
                    if (!centroid) {
                        return;
                    }
                    var twiceArea = 0;
                    for (var i = 0; i < outerRing.length - 1; i++) {
                        var p1 = outerRing[i];
                        var p2 = outerRing[i + 1];
                        twiceArea += (p1[0] * p2[1]) - (p2[0] * p1[1]);
                    }
                    var area = Math.abs(twiceArea / 2);
                    if (!area) {
                        return;
                    }
                    if (area > bestArea) {
                        bestArea = area;
                        result = centroid;
                    }
                }

                if (type === 'Polygon') {
                    accumulateCentroid(coords);
                } else if (type === 'MultiPolygon') {
                    for (var i = 0; i < coords.length; i++) {
                        accumulateCentroid(coords[i]);
                    }
                }

                if (result && Number.isFinite(result[0]) && Number.isFinite(result[1])) {
                    return L.latLng(result[1], result[0]);
                }

                try {
                    var tempLayer = L.geoJSON(feature);
                    var bounds = tempLayer.getBounds();
                    if (bounds && bounds.isValid()) {
                        return bounds.getCenter();
                    }
                } catch (err) {
                    // abaikan kesalahan pengambilan batas sementara
                }
                return null;
            }

            /**
             * Jaga agar lebar tampilan peta tidak terlalu sempit ketika pengguna fokus ke satu wilayah kecil.
             */
            function adjustZoomToTargetWidth(targetKm) {
                if (!map || typeof map.getBounds !== 'function') {
                    return;
                }
                var bounds = map.getBounds();
                if (!bounds || !bounds.isValid()) {
                    return;
                }
                var mapSize = map.getSize();
                if (!mapSize || !mapSize.x) {
                    return;
                }
                var targetMeters = Number(targetKm) * 1000;
                if (!Number.isFinite(targetMeters) || targetMeters <= 0) {
                    return;
                }

                var currentMeters = map.distance(bounds.getNorthWest(), bounds.getNorthEast());
                if (!Number.isFinite(currentMeters) || currentMeters >= targetMeters) {
                    return;
                }

                var lat = map.getCenter() && Number.isFinite(map.getCenter().lat) ? map.getCenter().lat : 0;
                var cosLat = Math.cos(lat * Math.PI / 180);
                var calculatedZoom = Math.log2((156543.03392 * cosLat * mapSize.x) / targetMeters);
                if (!Number.isFinite(calculatedZoom)) {
                    return;
                }

                var targetZoom = Math.max(map.getMinZoom(), Math.min(map.getMaxZoom(), calculatedZoom));
                if (targetZoom < map.getZoom()) {
                    map.setZoom(targetZoom);
                }
            }

            // Layer-layer tambahan untuk highlight dan label numerik pada peta
            var hoverHighlightLayer = null;
            var districtLabelLayer = L.layerGroup().addTo(map);
            var villageLabelLayer = L.layerGroup().addTo(map);
            var districtLegendEl = null;
            var districtLegendTitleEl = null;
            var districtLabelData = [];

            /**
             * Hilangkan highlight sementara agar interaksi berikutnya konsisten.
             */
            function clearHoverHighlight() {
                if (hoverHighlightLayer && map.hasLayer(hoverHighlightLayer)) {
                    map.removeLayer(hoverHighlightLayer);
                }
                hoverHighlightLayer = null;
            }

            /**
             * Siapkan data statis label kecamatan (nomor urut dan titik koordinat untuk marker).
             */
            function buildDistrictLabelData() {
                if (!window.kec || !Array.isArray(window.kec.features)) {
                    return [];
                }
                return window.kec.features.map(function (feature, index) {
                    var props = feature && feature.properties ? feature.properties : {};
                    var name = props.nm_kecamatan || ('Kecamatan ' + (index + 1));
                    var center = computeFeatureCenter(feature);
                    return {
                        feature: feature,
                        number: index + 1,
                        name: name,
                        center: center
                    };
                });
            }

            /**
             * Render legenda kecamatan atau desa sesuai item yang diberikan.
             */
            function renderLegend(items, options) {
                if (!districtLegendEl || !districtLegendTitleEl) {
                    return;
                }

                options = options || {};
                var titleText = options.title || 'Keterangan';
                var prefix = typeof options.prefix === 'string' ? options.prefix.trim() : '';

                districtLegendTitleEl.textContent = titleText;

                if (!items || !items.length) {
                    districtLegendEl.innerHTML = '<li class="dk-map-legend__item text-muted">Data belum tersedia</li>';
                    districtLegendEl.scrollTop = 0;
                    return;
                }

                var legendHtml = items.map(function (item) {
                    var safeName = escapeHtml(item.name || '');
                    var labelText = safeName;
                    if (prefix) {
                        labelText = prefix + ' ' + labelText;
                    }
                    return '<li class="dk-map-legend__item">' +
                        '<span class="dk-map-legend__badge">' + escapeHtml(String(item.number)) + '</span>' +
                        '<span>' + labelText + '</span>' +
                        '</li>';
                }).join('');

                districtLegendEl.innerHTML = legendHtml;
                districtLegendEl.scrollTop = 0;
            }

            /**
             * Tempelkan label angka kecamatan sesuai filter yang aktif.
             */
            function renderDistrictLabels(selectionState) {
                if (!districtLabelLayer) {
                    return;
                }
                districtLabelLayer.clearLayers();

                var filterFn = selectionState && typeof selectionState.filterFn === 'function'
                    ? selectionState.filterFn
                    : null;

                districtLabelData.forEach(function (item) {
                    if (!item.center) {
                        return;
                    }
                    if (filterFn && !filterFn(item.feature)) {
                        return;
                    }
                    L.marker(item.center, {
                        icon: L.divIcon({
                            className: 'leaflet-div-icon dk-district-label',
                            html: '<span>' + item.number + '</span>',
                            iconSize: [15, 15],
                            iconAnchor: [20, 20]
                        }),
                        pane: 'labelPane',
                        interactive: false
                    }).addTo(districtLabelLayer);
                });
            }

            districtLabelData = buildDistrictLabelData();

            /**
             * Render label numerik desa/kelurahan setelah difilter.
             */
            function renderVillageLabels(entries) {
                if (!villageLabelLayer) {
                    return;
                }
                villageLabelLayer.clearLayers();
                if (!entries || !entries.length) {
                    return;
                }
                entries.forEach(function (item) {
                    if (!item.center) {
                        return;
                    }
                    L.marker(item.center, {
                        icon: L.divIcon({
                            className: 'leaflet-div-icon dk-village-label',
                            html: '<span>' + item.number + '</span>',
                            iconSize: [15, 15],
                            iconAnchor: [18, 18]
                        }),
                        pane: 'labelPane',
                        interactive: false
                    }).addTo(villageLabelLayer);
                });
            }

            /**
             * Kumpulkan daftar desa/kelurahan yang relevan dengan filter aktif.
             */
            function buildVillageLabelData(filterFn) {
                if (!window.kel || !Array.isArray(window.kel.features)) {
                    return [];
                }

                var entries = [];
                window.kel.features.forEach(function (feature) {
                    if (typeof filterFn === 'function' && !filterFn(feature)) {
                        return;
                    }
                    var props = feature && feature.properties ? feature.properties : {};
                    var name = props.nm_kelurahan || props.nm_desa || props.nama || props.nm_desa_kelurahan || 'Desa/Kelurahan';
                    var code = props.kd_kelurahan || props.kode_desa || props.kode || props.code || '';
                    var center = computeFeatureCenter(feature);
                    entries.push({
                        feature: feature,
                        code: code ? String(code) : '',
                        name: name,
                        center: center
                    });
                });

                entries.sort(function (a, b) {
                    var codeA = (a.code || '').replace(/\D+/g, '');
                    var codeB = (b.code || '').replace(/\D+/g, '');
                    if (codeA && codeB && codeA !== codeB) {
                        return codeA < codeB ? -1 : 1;
                    }
                    if (codeA && !codeB) {
                        return -1;
                    }
                    if (!codeA && codeB) {
                        return 1;
                    }
                    var nameA = (a.name || '').toLowerCase();
                    var nameB = (b.name || '').toLowerCase();
                    if (nameA < nameB) {
                        return -1;
                    }
                    if (nameA > nameB) {
                        return 1;
                    }
                    return 0;
                });

                entries.forEach(function (item, idx) {
                    item.number = idx + 1;
                });

                return entries;
            }

            /**
             * Buat layer geojson kecamatan dengan opsi filter opsional.
             */
            function createKecamatanLayer(filterFn) {
                if (!window.kec) {
                    return L.layerGroup();
                }

                var options = {
                    style: styleKec,
                    onEachFeature: bindDistrictFeature,
                    pane: 'kecPane'
                };

                if (typeof filterFn === 'function') {
                    options.filter = filterFn;
                }

                return L.geoJSON(window.kec, options);
            }

            /**
             * Buat layer geojson desa/kelurahan menyesuaikan kecamatan terpilih.
             */
            function createKelurahanLayer(districtState, filterFn) {
                if (!window.kel) {
                    return L.layerGroup();
                }

                var options = {
                    style: styleKel,
                    onEachFeature: bindVillageFeatureFactory(districtState),
                    pane: 'kelPane'
                };

                if (typeof filterFn === 'function') {
                    options.filter = filterFn;
                }

                return L.geoJSON(window.kel, options);
            }

            /**
             * Tambahkan layer interaktif baru ke peta sekaligus atur ulang urutannya.
             */
            function addInteractiveLayers(layers) {
                layers = Array.isArray(layers) ? layers : [layers];

                layers.forEach(function (layer) {
                    if (layer && layer.addTo) {
                        layer.addTo(map);
                    }
                });

                if (!map.hasLayer(kabLayer)) {
                    kabLayer.addTo(map);
                }

                ensureLayerOrder();
            }

            /**
             * Sesuaikan tampilan peta agar fokus ke layer utama yang sedang aktif.
             */
            function fitToLayers(primaryLayers) {
                var layers = Array.isArray(primaryLayers) ? primaryLayers : [primaryLayers];
                var bounds = null;

                layers.some(function (layer) {
                    if (!layer || !layer.getBounds) {
                        return false;
                    }
                    var layerBounds = layer.getBounds();
                    if (layerBounds && layerBounds.isValid()) {
                        bounds = layerBounds;
                        return true;
                    }
                    return false;
                });

                if (!bounds && kabLayer && kabLayer.getBounds) {
                    var kabBounds = kabLayer.getBounds();
                    if (kabBounds && kabBounds.isValid()) {
                        bounds = kabBounds;
                    }
                }

                if (bounds && bounds.isValid()) {
                    map.fitBounds(bounds.pad(0.05));
                    map.once('moveend', function () {
                        adjustZoomToTargetWidth(TARGET_VIEW_WIDTH_KM);
                    });
                }
            }

            /**
             * Beri efek highlight pada fitur yang disorot pengguna.
             */
            function highlightFeature(layer, color, weight, fillOpacity) {
                if (!layer) {
                    return;
                }

                clearHoverHighlight();

                if (layer.bringToFront) {
                    layer.bringToFront();
                }

                if (typeof layer.toGeoJSON === 'function') {
                    var geoJson = layer.toGeoJSON();
                    var strokeWeight = typeof weight === 'number' ? weight : 2;
                    var fillAlpha = typeof fillOpacity === 'number' ? fillOpacity : 0;

                    hoverHighlightLayer = L.geoJSON(geoJson, {
                        style: function () {
                            return {
                                color: color,
                                weight: strokeWeight,
                                opacity: 1,
                                fillColor: color,
                                fillOpacity: fillAlpha
                            };
                        },
                        pane: 'hoverPane',
                        interactive: false
                    }).addTo(map);
                }

                ensureLayerOrder();
            }

            /**
             * Kembalikan gaya dasar sebuah fitur setelah interaksi selesai.
             */
            function resetFeatureStyle(layer, styleFn) {
                clearHoverHighlight();

                if (!layer || !layer.setStyle || typeof styleFn !== 'function') {
                    return;
                }

                var baseStyle = styleFn(layer.feature);
                layer.setStyle(baseStyle);
                ensureLayerOrder();
            }

            /**
             * Buat fungsi filter untuk memilih kecamatan berdasarkan kode maupun slug.
             */
            function buildDistrictFilter(selectedCode, selectedSlug) {
                var codeVal = selectedCode ? String(selectedCode).trim() : '';
                var slugVal = selectedSlug ? normalizeName(selectedSlug) : '';
                if (!codeVal && !slugVal) {
                    return function () {
                        return true;
                    };
                }
                var selectedAliases = codeAliases(codeVal);
                var slugOptions = slugVariants(slugVal);
                return function (feature) {
                    var props = feature && feature.properties ? feature.properties : {};
                    var featureAliases = codeAliases(props.kd_kecamatan);
                    for (var i = 0; i < selectedAliases.length; i++) {
                        if (featureAliases.indexOf(selectedAliases[i]) !== -1) {
                            return true;
                        }
                    }
                    if (slugOptions.length) {
                        var featureSlug = normalizeName(props.nm_kecamatan);
                        var featureVariants = slugVariants(featureSlug);
                        for (var j = 0; j < slugOptions.length; j++) {
                            if (featureVariants.indexOf(slugOptions[j]) !== -1) {
                                return true;
                            }
                        }
                    }
                    return false;
                };
            }

            /**
             * Cari statistik kecamatan sesuai properti geojson yang sedang diakses.
             */
            function findDistrictStats(props) {
                var aliases = codeAliases(props && props.kd_kecamatan);
                for (var i = 0; i < aliases.length; i++) {
                    if (districtStatsIndex.by_code[aliases[i]]) {
                        return districtStatsIndex.by_code[aliases[i]];
                    }
                }
                var slug = normalizeName(props && props.nm_kecamatan);
                var variants = slugVariants(slug);
                for (var j = 0; j < variants.length; j++) {
                    if (districtStatsIndex.by_slug[variants[j]]) {
                        return districtStatsIndex.by_slug[variants[j]];
                    }
                }
                return null;
            }

            /**
             * Cari statistik desa/kelurahan berdasarkan kombinasi kode atau slug kecamatan dan desa.
             */
            function findVillageStats(props, districtState) {
                var stats = null;
                var districtAliases = districtState && Array.isArray(districtState.codeAliases) && districtState.codeAliases.length
                    ? districtState.codeAliases
                    : codeAliases(props && props.kd_kecamatan);
                var villageAliases = codeAliases(props && props.kd_kelurahan);

                districtAliases.some(function (dAlias) {
                    return villageAliases.some(function (vAlias) {
                        var key = dAlias + '-' + vAlias;
                        if (villageStatsIndex.by_code[key]) {
                            stats = villageStatsIndex.by_code[key];
                            return true;
                        }
                        return false;
                    });
                });

                if (!stats) {
                    var districtVariants = districtState && Array.isArray(districtState.slugVariants) && districtState.slugVariants.length
                        ? districtState.slugVariants
                        : slugVariants(normalizeName(props && props.nm_kecamatan));
                    var villageVariants = slugVariants(normalizeName(props && props.nm_kelurahan));
                    districtVariants.some(function (dSlug) {
                        return villageVariants.some(function (vSlug) {
                            var key = dSlug + '-' + vSlug;
                            if (villageStatsIndex.by_slug[key]) {
                                stats = villageStatsIndex.by_slug[key];
                                return true;
                            }
                            return false;
                        });
                    });
                }

                return stats;
            }

            /**
             * Bangun state kecamatan terpilih lengkap dengan alias kode dan slug.
             */
            function buildSelectedDistrictState(filterState) {
                filterState = filterState || { code: '', slug: '' };
                var hasSelection = Boolean(filterState.code || filterState.slug);

                if (!hasSelection || !window.kec || !Array.isArray(window.kec.features)) {
                    return null;
                }

                var filterFn = buildDistrictFilter(filterState.code, filterState.slug);
                var aliasSet = {};
                var slugSet = {};
                var districtName = null;
                var matchCount = 0;

                window.kec.features.forEach(function (feature) {
                    if (!filterFn(feature)) {
                        return;
                    }

                    matchCount += 1;

                    var props = feature && feature.properties ? feature.properties : {};

                    codeAliases(props.kd_kecamatan).forEach(function (alias) {
                        if (alias) {
                            aliasSet[alias] = true;
                        }
                    });

                    slugVariants(normalizeName(props.nm_kecamatan)).forEach(function (slug) {
                        if (slug) {
                            slugSet[slug] = true;
                        }
                    });

                    if (!districtName && props.nm_kecamatan) {
                        districtName = props.nm_kecamatan;
                    }
                });

                if (!matchCount) {
                    return null;
                }

                if (filterState.slug) {
                    slugVariants(normalizeName(filterState.slug)).forEach(function (slug) {
                        if (slug) {
                            slugSet[slug] = true;
                        }
                    });
                }

                return {
                    code: filterState.code || '',
                    slug: filterState.slug || '',
                    filterFn: filterFn,
                    aliasSet: aliasSet,
                    slugSet: Object.keys(slugSet).length ? slugSet : null,
                    name: districtName
                };
            }

            /**
             * Tampilkan keseluruhan kabupaten ketika tidak ada filter kecamatan.
             */
            function renderKabupatenOverview() {
                removeLayer(kecLayer);
                removeLayer(kelLayer);

                kecLayer = createKecamatanLayer(null);
                kelLayer = null;

                addInteractiveLayers(kecLayer);
                fitToLayers(kecLayer);
                renderDistrictLabels(null);
                renderVillageLabels([]);
                renderLegend(districtLabelData, { title: 'Keterangan Kecamatan', prefix: 'Kec.' });
            }

            /**
             * Render tampilan detail ketika pengguna memilih kecamatan tertentu.
             */
            function renderSelectedDistrict(selectionState) {
                removeLayer(kecLayer);
                removeLayer(kelLayer);
                districtLabelLayer.clearLayers();
                renderVillageLabels([]);

                var districtState = {
                    codeAliases: Object.keys(selectionState.aliasSet || {}),
                    slugVariants: selectionState.slugSet ? Object.keys(selectionState.slugSet) : [],
                    name: selectionState.name
                };

                kecLayer = createKecamatanLayer(selectionState.filterFn);

                var kelFilterFn = function (feature) {
                    var props = feature && feature.properties ? feature.properties : {};
                    var aliases = codeAliases(props.kd_kecamatan);
                    for (var i = 0; i < aliases.length; i++) {
                        if (selectionState.aliasSet[aliases[i]]) {
                            return true;
                        }
                    }

                    if (selectionState.slugSet) {
                        var slug = normalizeName(props.nm_kecamatan);
                        if (slug && selectionState.slugSet[slug]) {
                            return true;
                        }
                    }

                    return false;
                };

                kelLayer = createKelurahanLayer(districtState, kelFilterFn);

                if (kelLayer && typeof kelLayer.getLayers === 'function' && kelLayer.getLayers().length === 0) {
                    kelLayer = null;
                }

                addInteractiveLayers([kecLayer, kelLayer]);
                fitToLayers([kelLayer, kecLayer]);
                var villageLabelData = buildVillageLabelData(kelFilterFn);
                renderVillageLabels(villageLabelData);
                var legendTitle = 'Desa/Kelurahan';
                if (districtState.name) {
                    legendTitle += ' ' + districtState.name;
                }
                renderLegend(villageLabelData, { title: legendTitle, prefix: 'Desa/Kel.' });
            }

            /**
             * Kaitkan popup dan interaksi mouse untuk setiap geojson kecamatan.
             */
            function bindDistrictFeature(feature, layer) {
                var props = feature && feature.properties ? feature.properties : {};
                var stats = findDistrictStats(props);
                var name = stats && stats.name ? stats.name : (props.nm_kecamatan || 'Kecamatan');
                var rows = [];

                if (stats) {
                    rows.push({ label: 'L (Laki-laki)', value: formatNumber(stats.male) });
                    rows.push({ label: 'P (Perempuan)', value: formatNumber(stats.female) });
                    rows.push({ label: 'Total Penduduk', value: formatNumber(stats.total) });
                } else {
                    rows.push({ label: 'Informasi', value: 'Data penduduk belum tersedia' });
                }

                if (!layer._hoverColor) {
                    layer._hoverColor = '#00b4d8';
                }

                layer.on({
                    mouseover: function (e) {
                        highlightFeature(e.target, e.target._hoverColor || '#00b4d8', 2, 0.18);
                    },
                    mouseout: function (e) {
                        resetFeatureStyle(e.target, styleKec);
                    },
                    popupopen: function (e) {
                        highlightFeature(e.target, e.target._hoverColor || '#00b4d8', 2.2, 0.2);
                    },
                    popupclose: function (e) {
                        resetFeatureStyle(e.target, styleKec);
                    }
                });

                layer.bindPopup(buildPopupContent('Kecamatan ' + name, rows));
            }

            /**
             * Buat handler popup serta highlight untuk fitur desa/kelurahan.
             */
            function bindVillageFeatureFactory(districtState) {
                return function (feature, layer) {
                    var props = feature && feature.properties ? feature.properties : {};
                    var stats = districtState ? findVillageStats(props, districtState) : null;
                    var districtFallback = findDistrictStats(props);

                    var districtName = (districtState && districtState.name) ||
                        (stats && stats.district_name) ||
                        (districtFallback && districtFallback.name) ||
                        props.nm_kecamatan ||
                        '-';
                    var villageName = (stats && stats.name) || props.nm_kelurahan || 'Desa/Kelurahan';

                    var rows = [
                        { label: 'Kecamatan', value: districtName }
                    ];

                    if (stats) {
                        rows.push({ label: 'L (Laki-laki)', value: formatNumber(stats.male) });
                        rows.push({ label: 'P (Perempuan)', value: formatNumber(stats.female) });
                        rows.push({ label: 'Total Penduduk', value: formatNumber(stats.total) });
                    } else {
                        rows.push({ label: 'Informasi', value: 'Data penduduk belum tersedia' });
                    }

                    if (!layer._hoverColor) {
                        layer._hoverColor = '#00b4d8';
                    }

                    layer.on({
                        mouseover: function (e) {
                            highlightFeature(e.target, e.target._hoverColor || '#00b4d8', 1.4, 0.2);
                        },
                        mouseout: function (e) {
                            resetFeatureStyle(e.target, styleKel);
                        },
                        popupopen: function (e) {
                            highlightFeature(e.target, e.target._hoverColor || '#00b4d8', 1.6, 0.24);
                        },
                        popupclose: function (e) {
                            resetFeatureStyle(e.target, styleKel);
                        }
                    });

                    layer.bindPopup(buildPopupContent('Desa/Kelurahan ' + villageName, rows));
                };
            }

            /**
             * Render ulang layer peta sesuai pilihan kecamatan terbaru.
             */
            function rebuildDistrictLayers(filterState) {
                filterState = filterState || { code: '', slug: '' };

                var selectionState = buildSelectedDistrictState(filterState);

                if (selectionState) {
                    renderSelectedDistrict(selectionState);
                } else {
                    renderKabupatenOverview();
                }
            }

            var districtFilterEl = document.getElementById('landing-district-filter');
            var currentDistrictFilter = { code: '', slug: '' };

            if (districtFilterEl) {
                // Ambil nilai awal filter saat halaman dimuat
                var initialOption = districtFilterEl.selectedOptions && districtFilterEl.selectedOptions.length
                    ? districtFilterEl.selectedOptions[0]
                    : null;

                currentDistrictFilter = {
                    code: districtFilterEl.value || '',
                    slug: initialOption ? (initialOption.getAttribute('data-slug') || '') : ''
                };

                // Ketika pengguna memilih kecamatan berbeda, rebuild layer peta
                districtFilterEl.addEventListener('change', function () {
                    var option = this.selectedOptions && this.selectedOptions.length ? this.selectedOptions[0] : null;
                    currentDistrictFilter = {
                        code: this.value || '',
                        slug: option ? (option.getAttribute('data-slug') || '') : ''
                    };
                    rebuildDistrictLayers(currentDistrictFilter);
                });
            }

            var baseLayers = {
                'Default': cartoVoyager,
                'Light': carto,
                'Dark': cartoDark,
                'OSM': osm,
                'Satellite': googleSat
            };

            var layersControl = L.control.layers(baseLayers, {}, { collapsed: true, position: 'topright' }).addTo(map);
            var layersControlContainer = layersControl && typeof layersControl.getContainer === 'function'
                ? layersControl.getContainer()
                : null;

            if (layersControlContainer) {
                // Sisipkan legenda kustom di dalam kontainer kontrol layer
                var layersListEl = layersControlContainer.querySelector('.leaflet-control-layers-list') || layersControlContainer;
                var legendContainer = L.DomUtil.create('div', 'dk-map-legend', layersListEl);
                legendContainer.innerHTML =
                    '<div class="dk-map-legend__title">Keterangan Kecamatan</div>' +
                    '<ul class="dk-map-legend__list"></ul>' +
                    '<div class="dk-legend-lines">' +
                        '<div class="dk-map-legend__item dk-legend-line">' +
                            '<span class="dk-legend-swatch dk-legend-swatch--kab"></span>' +
                            '<span>Garis Batas Kabupaten</span>' +
                        '</div>' +
                        '<div class="dk-map-legend__item dk-legend-line">' +
                            '<span class="dk-legend-swatch dk-legend-swatch--kec"></span>' +
                            '<span>Garis Batas Kecamatan</span>' +
                        '</div>' +
                        '<div class="dk-map-legend__item dk-legend-line">' +
                            '<span class="dk-legend-swatch dk-legend-swatch--kel"></span>' +
                            '<span>Garis Batas Desa/Kelurahan</span>' +
                        '</div>' +
                    '</div>';
                districtLegendTitleEl = legendContainer.querySelector('.dk-map-legend__title');
                districtLegendEl = legendContainer.querySelector('.dk-map-legend__list');
                L.DomEvent.disableClickPropagation(legendContainer);
                L.DomEvent.disableScrollPropagation(legendContainer);
            }

            // Render legenda kecamatan saat pertama kali dimuat
            renderLegend(districtLabelData, { title: 'Keterangan Kecamatan', prefix: 'Kec.' });

            // Pastikan layer peta sesuai kondisi filter awal
            rebuildDistrictLayers(currentDistrictFilter);
        })();
    </script>
@endpush

