@extends('layouts.dukcapil', ['title' => 'Beranda'])

@push('styles')
    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
    <style>
        /* Landing page map tweaks */
        .dk-map .leaflet-container {
            width: 100%;
            height: 100%;
            border-radius: 6px;
        }
    </style>
@endpush

@section('content')
    @if (!$period)
        <div class="alert alert-warning border-0 dk-card">
            <strong>Data belum tersedia.</strong> Silakan unggah dataset terlebih dahulu melalui halaman admin.
        </div>
    @else
        <div class="row g-4">
            <div class="col-xl-4">
                <div class="dk-card h-100">
                    <div class="card-body p-4">
                        <h6 class="dk-card__title mb-3">Data Agregat Kependudukan Terbaru</h6>
                        <div class="mb-4">
                            <h6 class="text-uppercase text-xs text-muted mb-2">Wilayah</h6>
                            <div class="table-responsive">
                                <table class="table table-sm dk-table mb-3">
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

                        <div class="mb-4">
                            <h6 class="text-uppercase text-xs text-muted mb-2">Jumlah Penduduk</h6>
                            <div class="row g-3">
                                <div class="col-12">
                                    <div class="p-3 rounded-3 dk-metric--light">
                                        <div class="dk-metric__label text-white-50">Total Penduduk</div>
                                        <div class="dk-metric text-white">{{ number_format($totals['population']) }}</div>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="bg-light rounded-3 p-3">
                                        <div class="dk-metric__label">Laki-laki</div>
                                        <div class="dk-metric" style="font-size: 1.2rem;">
                                            {{ number_format($totals['male']) }}
                                        </div>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="bg-light rounded-3 p-3">
                                        <div class="dk-metric__label">Perempuan</div>
                                        <div class="dk-metric" style="font-size: 1.2rem;">
                                            {{ number_format($totals['female']) }}
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="bg-light rounded-3 p-3">
                                        <div class="dk-metric__label">Wajib KTP (&ge; 17 tahun)</div>
                                        <div class="dk-metric" style="font-size: 1.2rem;">
                                            {{ number_format($wajibKtp['total']) }}
                                        </div>
                                        <small class="text-muted d-block">
                                            L: {{ number_format($wajibKtp['male']) }} &bull;
                                            P: {{ number_format($wajibKtp['female']) }}
                                        </small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-8">
                <div class="dk-card h-100">
                    <div class="card-body p-0">
                        <div class="p-4 pb-0 d-flex flex-column flex-lg-row align-items-lg-center gap-3">
                            <h6 class="dk-card__title mb-0">Peta Madiun</h6>
                            @if(!empty($districtOptions) && $districtOptions->count())
                                <div class="ms-lg-auto w-100 w-lg-auto">
                                    <label for="landing-district-filter" class="form-label text-xs text-muted mb-1">Kecamatan</label>
                                    <select id="landing-district-filter" class="form-select form-select-sm">
                                        <option value="">Seluruh Kecamatan</option>
                                        @foreach($districtOptions as $district)
                                            <option value="{{ $district->code }}" data-slug="{{ \Illuminate\Support\Str::slug($district->name) }}">
                                                {{ $district->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            @endif
                        </div>
                        <div class="p-4 pt-0">
                            <div class="ratio ratio-16x9 dk-map">
                                <div id="landing-map"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
@endsection


@push('scripts')
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
            var mapStats = @json($mapStats);

            function ensureStats(section) {
                section = section || {};
                return {
                    by_code: section.by_code || {},
                    by_slug: section.by_slug || {},
                };
            }

            var districtStatsIndex = ensureStats(mapStats.districts);
            var villageStatsIndex = ensureStats(mapStats.villages);

            var carto = L.tileLayer('https://{s}.basemaps.cartocdn.com/light_all/{z}/{x}/{y}{r}.png', {
                maxZoom: 19,
                attribution: '&copy; OpenStreetMap contributors &copy; CARTO',
            });
            var osm = L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                maxZoom: 19,
                attribution: '&copy; OpenStreetMap contributors',
            });
            var satellite = L.tileLayer('https://api.mapbox.com/styles/v1/mapbox/satellite-v9/tiles/{z}/{x}/{y}?access_token=pk.eyJ1IjoibmFzcnVsYWR0ciIsImEiOiJjbWhoanA3amcwc2N1MnJwcTZybDM3NzhpIn0.WcsQUaPJbiXxWNJWfwbD1w', {
                maxZoom: 20,
                tileSize: 512,
                zoomOffset: -1,
                attribution: 'Imagery &copy; Mapbox, &copy; OpenStreetMap',
            });

            var map = L.map('landing-map', {
                center: [-7.629, 111.515],
                zoom: 11,
                layers: [carto],
            });

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

            L.control.scale({ imperial: false, maxWidth: 160 }).addTo(map);

            function styleKab() {
                return { color: '#c0392b', weight: 2, fillOpacity: 0, fill: false };
            }

            function styleKec() {
                return { color: '#faff09', weight: 1.7, fillColor: '#faff09', fillOpacity: 0 };
            }

            function styleKel() {
                return { color: '#00b4d8', weight: 1.3, fillColor: '#48cae4', fillOpacity: 0 };
            }

            function formatNumber(value) {
                var num = Number(value);
                return Number.isFinite(num) ? num.toLocaleString('id-ID') : '-';
            }

            function buildPopupContent(title, rows) {
                var html = '<div style="max-width:280px">';
                if (title) {
                    html += '<strong>' + title + '</strong>';
                }
                if (rows && rows.length) {
                    html += '<table style="margin-top:6px">';
                    rows.forEach(function (row) {
                        html += '<tr><td style="padding:2px 6px;vertical-align:top;color:#555;font-size:13px"><strong>'
                            + row.label + '</strong></td><td style="padding:2px 6px;vertical-align:top;color:#222;font-size:13px">'
                            + row.value + '</td></tr>';
                    });
                    html += '</table>';
                }
                html += '</div>';
                return html;
            }

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

            var kabLayer = window.kab ? L.geoJSON(window.kab, { style: styleKab, pane: 'kabPane' }).addTo(map) : L.layerGroup().addTo(map);
            var kecLayer = null;
            var kelLayer = null;
            var hoverOutlineLayer = null;

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

            function clearHoverOutline() {
                if (hoverOutlineLayer && map.hasLayer(hoverOutlineLayer)) {
                    map.removeLayer(hoverOutlineLayer);
                }
                hoverOutlineLayer = null;
            }

            function highlightFeature(layer, color, weight, fillOpacity) {
                if (!layer) {
                    return;
                }

                clearHoverOutline();

                if (layer.bringToFront) {
                    layer.bringToFront();
                }

                if (typeof layer.toGeoJSON === 'function') {
                    var outlineWeight = Math.max(0.6, Number(weight) - 0.4);
                    hoverOutlineLayer = L.geoJSON(layer.toGeoJSON(), {
                        style: function () {
                            return {
                                color: color,
                                weight: outlineWeight,
                                opacity: 1,
                                fill: Boolean(fillOpacity && fillOpacity > 0),
                                fillColor: color,
                                fillOpacity: fillOpacity || 0,
                                pane: 'hoverPane'
                            };
                        },
                        interactive: false,
                        pane: 'hoverPane'
                    }).addTo(map);
                }

                ensureLayerOrder();
            }

            function resetFeatureStyle(layer, styleFn) {
                clearHoverOutline();

                if (!layer || !layer.setStyle || typeof styleFn !== 'function') {
                    return;
                }

                var baseStyle = styleFn(layer.feature);
                layer.setStyle(baseStyle);
                ensureLayerOrder();
            }

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
                    layer._hoverColor = '#27ae60';
                }

                layer.on({
                    mouseover: function (e) {
                        highlightFeature(e.target, e.target._hoverColor || '#27ae60', 2, 0.18);
                    },
                    mouseout: function (e) {
                        resetFeatureStyle(e.target, styleKec);
                    },
                    popupopen: function (e) {
                        highlightFeature(e.target, e.target._hoverColor || '#27ae60', 2.2, 0.2);
                    },
                    popupclose: function (e) {
                        resetFeatureStyle(e.target, styleKec);
                    }
                });

                layer.bindPopup(buildPopupContent('Kecamatan ' + name, rows));
            }

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
                        layer._hoverColor = '#27ae60';
                    }

                    layer.on({
                        mouseover: function (e) {
                            highlightFeature(e.target, e.target._hoverColor || '#27ae60', 1.4, 0.2);
                        },
                        mouseout: function (e) {
                            resetFeatureStyle(e.target, styleKel);
                        },
                        popupopen: function (e) {
                            highlightFeature(e.target, e.target._hoverColor || '#27ae60', 1.6, 0.24);
                        },
                        popupclose: function (e) {
                            resetFeatureStyle(e.target, styleKel);
                        }
                    });

                    layer.bindPopup(buildPopupContent('Desa/Kelurahan ' + villageName, rows));
                };
            }

            function rebuildDistrictLayers(filterState) {
                filterState = filterState || { code: '', slug: '' };
                clearHoverOutline();
                var hasSelection = Boolean(filterState.code || filterState.slug);
                var kecFilterFn = buildDistrictFilter(filterState.code, filterState.slug);

                var selectedKecAliasSet = null;
                var selectedKecSlugSet = null;
                var selectedDistrictName = null;

                if (hasSelection && window.kec && Array.isArray(window.kec.features)) {
                    selectedKecAliasSet = {};
                    selectedKecSlugSet = {};
                    window.kec.features.forEach(function (feature) {
                        if (!kecFilterFn(feature)) {
                            return;
                        }
                        var props = feature && feature.properties ? feature.properties : {};
                        codeAliases(props.kd_kecamatan).forEach(function (alias) {
                            if (alias) {
                                selectedKecAliasSet[alias] = true;
                            }
                        });
                        slugVariants(normalizeName(props.nm_kecamatan)).forEach(function (slug) {
                            if (slug) {
                                selectedKecSlugSet[slug] = true;
                            }
                        });
                        if (!selectedDistrictName && props.nm_kecamatan) {
                            selectedDistrictName = props.nm_kecamatan;
                        }
                    });

                    if (filterState.slug) {
                        slugVariants(normalizeName(filterState.slug)).forEach(function (slug) {
                            if (slug) {
                                if (!selectedKecSlugSet) {
                                    selectedKecSlugSet = {};
                                }
                                selectedKecSlugSet[slug] = true;
                            }
                        });
                    }
                }

                if (kecLayer && map.hasLayer(kecLayer)) {
                    map.removeLayer(kecLayer);
                }
                if (kelLayer && map.hasLayer(kelLayer)) {
                    map.removeLayer(kelLayer);
                }

                kecLayer = window.kec ? L.geoJSON(window.kec, {
                    filter: kecFilterFn,
                    style: styleKec,
                    onEachFeature: bindDistrictFeature,
                    pane: 'kecPane'
                }) : L.layerGroup();

                if (hasSelection && selectedKecAliasSet && Object.keys(selectedKecAliasSet).length) {
                    var districtState = {
                        codeAliases: Object.keys(selectedKecAliasSet),
                        slugVariants: selectedKecSlugSet ? Object.keys(selectedKecSlugSet) : [],
                        name: selectedDistrictName
                    };

                    var kelFilterFn = function (feature) {
                        var props = feature && feature.properties ? feature.properties : {};
                        var aliases = codeAliases(props.kd_kecamatan);
                        for (var i = 0; i < aliases.length; i++) {
                            if (selectedKecAliasSet[aliases[i]]) {
                                return true;
                            }
                        }
                        return false;
                    };

                    kelLayer = window.kel ? L.geoJSON(window.kel, {
                        filter: kelFilterFn,
                        style: styleKel,
                        onEachFeature: bindVillageFeatureFactory(districtState),
                        pane: 'kelPane'
                    }) : L.layerGroup();
                } else {
                    kelLayer = null;
                }

                if (kecLayer && kecLayer.addTo) {
                    kecLayer.addTo(map);
                }
                if (kelLayer && kelLayer.addTo) {
                    kelLayer.addTo(map);
                }
                if (!map.hasLayer(kabLayer)) {
                    kabLayer.addTo(map);
                }

                ensureLayerOrder();

                var bounds = null;
                if (kelLayer && kelLayer.getBounds) {
                    var kelBounds = kelLayer.getBounds();
                    if (kelBounds && kelBounds.isValid()) {
                        bounds = kelBounds;
                    }
                }
                if (!bounds && kecLayer && kecLayer.getBounds) {
                    var kecBounds = kecLayer.getBounds();
                    if (kecBounds && kecBounds.isValid()) {
                        bounds = kecBounds;
                    }
                }
                if (!bounds && kabLayer && kabLayer.getBounds) {
                    var kabBounds = kabLayer.getBounds();
                    if (kabBounds && kabBounds.isValid()) {
                        bounds = kabBounds;
                    }
                }
                if (bounds && bounds.isValid()) {
                    map.fitBounds(bounds.pad(0.05));
                }
            }

            var districtFilterEl = document.getElementById('landing-district-filter');
            var currentDistrictFilter = { code: '', slug: '' };

            if (districtFilterEl) {
                var initialOption = districtFilterEl.selectedOptions && districtFilterEl.selectedOptions.length
                    ? districtFilterEl.selectedOptions[0]
                    : null;

                currentDistrictFilter = {
                    code: districtFilterEl.value || '',
                    slug: initialOption ? (initialOption.getAttribute('data-slug') || '') : ''
                };

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
                'Carto Light': carto,
                'OpenStreetMap': osm,
                'Citra Satelit (HD)': satellite
            };

            L.control.layers(baseLayers, {}, { collapsed: false, position: 'topright' }).addTo(map);

            rebuildDistrictLayers(currentDistrictFilter);
        })();
    </script>
@endpush

