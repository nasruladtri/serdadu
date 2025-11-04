<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Models\District;
use App\Models\Village;

class PublicDashboardController extends Controller
{
    private const AGE_GROUPS = [
        '00-04',
        '05-09',
        '10-14',
        '15-19',
        '20-24',
        '25-29',
        '30-34',
        '35-39',
        '40-44',
        '45-49',
        '50-54',
        '55-59',
        '60-64',
        '65-69',
        '70-74',
        '75+',
    ];

    public function landing()
    {
        $period = $this->latestPeriod();
        $districts = District::orderBy('name')->get(['id', 'name', 'code']);

        if (!$period) {
            return view('public.landing', [
                'title' => 'Beranda',
                'period' => null,
                'mapStats' => $this->emptyMapStats(),
                'districtOptions' => $districts,
                'districtCount' => $districts->count(),
            ]);
        }

        $gender = $this->genderSummary($period);
        $wajibKtp = $this->wajibKtpSummary($period);
        $ageGroups = $this->ageGroupSummary($period);
        $education = $this->educationSummary($period);
        $districtRanking = $this->districtRanking($period);
        $totals = [
            'population' => $gender['total'] ?? 0,
            'male' => $gender['male'] ?? 0,
            'female' => $gender['female'] ?? 0,
        ];

        return view('public.landing', [
            'title' => 'Beranda',
            'period' => $period,
            'totals' => $totals,
            'gender' => $gender,
            'wajibKtp' => $wajibKtp,
            'ageGroups' => $ageGroups,
            'districtRanking' => $districtRanking,
            'districtCount' => $districts->count(),
            'villageCount' => Village::count(),
            'education' => $education,
            'mapStats' => $this->mapPopulationSummary($period),
            'districtOptions' => $districts,
        ]);
    }

    public function data(Request $request)
    {
        [
            'periods' => $periods,
            'period' => $period,
            'districts' => $districts,
            'villages' => $villages,
            'selectedDistrict' => $selectedDistrict,
            'selectedVillage' => $selectedVillage,
            'filters' => $filters,
            'years' => $years,
            'semesterOptions' => $availableSemesters,
            'selectedYear' => $selectedYear,
            'selectedSemester' => $selectedSemester,
        ] = $this->prepareFilterContext($request);

        $gender = $period ? $this->genderSummary($period, $filters) : ['male' => 0, 'female' => 0, 'total' => 0];
        $wajibKtp = $period ? $this->wajibKtpSummary($period, $filters) : ['male' => 0, 'female' => 0, 'total' => 0];
        $ageGroups = $period ? $this->ageGroupSummary($period, $filters) : [];
        $singleAges = $period ? $this->singleAgeSummary($period, $filters) : [];
        $education = $period ? $this->educationSummary($period, $filters) : [];
        $topOccupations = $period ? $this->occupationHighlights($period, $filters) : [];
        $marital = $period ? $this->maritalStatusSummary($period, $filters) : [];
        $headHouseholds = $period ? $this->headOfHouseholdSummary($period, $filters) : [];
        $religions = $period ? $this->religionSummary($period, $filters) : [];
        $areaTable = $this->areaPopulationTable($period, $filters);
        $educationMatrix = $this->educationMatrix($period, $filters);
        $wajibKtpMatrix = $this->wajibKtpMatrix($period, $filters);
        $maritalMatrix = $this->maritalMatrix($period, $filters);
        $headHouseholdMatrix = $this->headHouseholdMatrix($period, $filters);
        $religionMatrix = $this->religionMatrix($period, $filters);

        return view('public.data', [
            'title' => 'Data Agregat',
            'period' => $period,
            'periods' => $periods,
            'years' => $years,
            'semesterOptions' => $availableSemesters,
            'selectedYear' => $selectedYear,
            'selectedSemester' => $selectedSemester,
            'districts' => $districts,
            'villages' => $villages,
            'selectedDistrict' => $selectedDistrict,
            'selectedVillage' => $selectedVillage,
            'gender' => $gender,
            'wajibKtp' => $wajibKtp,
            'ageGroups' => $ageGroups,
            'singleAges' => $singleAges,
            'education' => $education,
            'topOccupations' => $topOccupations,
            'marital' => $marital,
            'headHouseholds' => $headHouseholds,
            'religions' => $religions,
            'areaTable' => $areaTable,
            'educationMatrix' => $educationMatrix,
            'wajibKtpMatrix' => $wajibKtpMatrix,
            'maritalMatrix' => $maritalMatrix,
            'headHouseholdMatrix' => $headHouseholdMatrix,
            'religionMatrix' => $religionMatrix,
        ]);
    }

    public function charts(Request $request)
    {
        [
            'periods' => $periods,
            'period' => $period,
            'districts' => $districts,
            'villages' => $villages,
            'selectedDistrict' => $selectedDistrict,
            'selectedVillage' => $selectedVillage,
            'filters' => $filters,
            'years' => $years,
            'semesterOptions' => $availableSemesters,
            'selectedYear' => $selectedYear,
            'selectedSemester' => $selectedSemester,
        ] = $this->prepareFilterContext($request);

        $gender = $period ? $this->genderSummary($period, $filters) : ['male' => 0, 'female' => 0, 'total' => 0];
        $wajibKtp = $period ? $this->wajibKtpSummary($period, $filters) : ['male' => 0, 'female' => 0, 'total' => 0];
        $ageGroups = $period ? $this->ageGroupSummary($period, $filters) : [];
        $singleAges = $period ? $this->singleAgeSummary($period, $filters) : [];
        $education = $period ? $this->educationSummary($period, $filters) : [];
        $topOccupations = $period ? $this->occupationHighlights($period, $filters) : [];
        $marital = $period ? $this->maritalStatusSummary($period, $filters) : [];
        $headHouseholds = $period ? $this->headOfHouseholdSummary($period, $filters) : [];
        $religions = $period ? $this->religionSummary($period, $filters) : [];

        $chartTitles = [
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
        $chartsNeedingTags = [
            'age',
            'single-age',
            'education',
            'occupation',
            'marital',
            'household',
            'religion',
        ];
        $chartsAngledTags = [
            'single-age',
            'occupation',
        ];

        $charts = [
            'gender' => $this->buildGenderChart($chartTitles['gender'], $gender),
            'age' => $this->buildSeriesChart($chartTitles['age'], $ageGroups),
            'single-age' => $this->buildSeriesChart($chartTitles['single-age'], $singleAges),
            'education' => $this->buildSeriesChart($chartTitles['education'], $education),
            'occupation' => $this->buildSeriesChart($chartTitles['occupation'], $topOccupations),
            'marital' => $this->buildSeriesChart($chartTitles['marital'], $marital),
            'household' => $this->buildSeriesChart($chartTitles['household'], $headHouseholds),
            'religion' => $this->buildSeriesChart($chartTitles['religion'], $religions),
            'wajib-ktp' => $this->buildWajibKtpChart($chartTitles['wajib-ktp'], $wajibKtp),
        ];

        return view('public.charts', [
            'title' => 'Grafik Data',
            'period' => $period,
            'periods' => $periods,
            'years' => $years,
            'semesterOptions' => $availableSemesters,
            'selectedYear' => $selectedYear,
            'selectedSemester' => $selectedSemester,
            'districts' => $districts,
            'villages' => $villages,
            'selectedDistrict' => $selectedDistrict,
            'selectedVillage' => $selectedVillage,
            'charts' => $charts,
            'chartTitles' => $chartTitles,
            'chartsNeedingTags' => $chartsNeedingTags,
            'chartsAngledTags' => $chartsAngledTags,
        ]);
    }

    private function availablePeriods(): array
    {
        return DB::table('pop_age_group')
            ->select('year', 'semester')
            ->groupBy('year', 'semester')
            ->orderByDesc('year')
            ->orderByDesc('semester')
            ->get()
            ->map(function ($row) {
                return [
                    'year' => (int) $row->year,
                    'semester' => (int) $row->semester,
                ];
            })
            ->toArray();
    }

    private function prepareFilterContext(Request $request): array
    {
        $periods = $this->availablePeriods();
        $period = $this->resolvePeriod(
            $request->input('year'),
            $request->input('semester'),
            $periods
        );

        $districts = District::orderBy('name')->get();

        $selectedDistrict = $request->input('district_id');
        $selectedVillage = $request->input('village_id');

        if ($selectedDistrict && !$districts->contains('id', (int) $selectedDistrict)) {
            $selectedDistrict = null;
        }

        $villages = collect();
        if ($selectedDistrict) {
            $villages = Village::where('district_id', $selectedDistrict)
                ->orderBy('name')
                ->get();
            if ($selectedVillage && !$villages->contains('id', (int) $selectedVillage)) {
                $selectedVillage = null;
            }
        } else {
            $selectedVillage = null;
        }

        $filters = [
            'district_id' => $selectedDistrict ? (int) $selectedDistrict : null,
            'village_id' => $selectedVillage ? (int) $selectedVillage : null,
        ];

        $years = collect($periods)->pluck('year')->unique()->sortDesc()->values()->all();
        $selectedYear = $period['year'] ?? null;
        $availableSemesters = collect($periods)
            ->when($selectedYear, fn($c) => $c->where('year', $selectedYear))
            ->pluck('semester')
            ->unique()
            ->sortDesc()
            ->values()
            ->all();
        $selectedSemester = $period['semester'] ?? null;

        return [
            'periods' => $periods,
            'period' => $period,
            'districts' => $districts,
            'villages' => $villages,
            'selectedDistrict' => $selectedDistrict,
            'selectedVillage' => $selectedVillage,
            'filters' => $filters,
            'years' => $years,
            'semesterOptions' => $availableSemesters,
            'selectedYear' => $selectedYear,
            'selectedSemester' => $selectedSemester,
        ];
    }

    private function resolvePeriod(?string $yearInput, ?string $semesterInput, array $periods): ?array
    {
        if (empty($periods)) {
            return null;
        }

        $year = $yearInput !== null ? (int) $yearInput : null;
        $semester = $semesterInput !== null ? (int) $semesterInput : null;

        if ($year !== null && $semester !== null) {
            foreach ($periods as $period) {
                if ($period['year'] === $year && $period['semester'] === $semester) {
                    return $period;
                }
            }
        }

        if ($year !== null && $semester === null) {
            foreach ($periods as $period) {
                if ($period['year'] === $year) {
                    return $period;
                }
            }
        }

        if ($semester !== null && $year === null) {
            foreach ($periods as $period) {
                if ($period['semester'] === $semester) {
                    return $period;
                }
            }
        }

        return $periods[0];
    }

    private function latestPeriod(): ?array
    {
        $periods = $this->availablePeriods();
        return $periods[0] ?? null;
    }

    private function genderSummary(?array $period, array $filters = []): array
    {
        if (!$period) {
            return ['male' => 0, 'female' => 0, 'total' => 0];
        }

        $query = DB::table('pop_gender')
            ->where('year', $period['year'])
            ->where('semester', $period['semester']);

        $this->applyAreaScope($query, $filters);

        $row = $query
            ->selectRaw('SUM(male) as male, SUM(female) as female, SUM(total) as total')
            ->first();

        if (!$row) {
            return ['male' => 0, 'female' => 0, 'total' => 0];
        }

        return [
            'male' => (int) $row->male,
            'female' => (int) $row->female,
            'total' => (int) $row->total,
        ];
    }

    private function wajibKtpSummary(?array $period, array $filters = []): array
    {
        if (!$period) {
            return ['male' => 0, 'female' => 0, 'total' => 0];
        }

        $query = DB::table('pop_wajib_ktp')
            ->where('year', $period['year'])
            ->where('semester', $period['semester']);

        $this->applyAreaScope($query, $filters);

        $row = $query
            ->selectRaw('SUM(male) as male, SUM(female) as female, SUM(total) as total')
            ->first();

        if (!$row) {
            return ['male' => 0, 'female' => 0, 'total' => 0];
        }

        return [
            'male' => (int) $row->male,
            'female' => (int) $row->female,
            'total' => (int) $row->total,
        ];
    }

    private function ageGroupSummary(?array $period, array $filters = []): array
    {
        if (!$period) {
            return [];
        }

        $query = DB::table('pop_age_group')
            ->where('year', $period['year'])
            ->where('semester', $period['semester']);

        $this->applyAreaScope($query, $filters);

        $rows = $query
            ->select('age_group')
            ->selectRaw('SUM(male) as male')
            ->selectRaw('SUM(female) as female')
            ->selectRaw('SUM(total) as total')
            ->groupBy('age_group')
            ->get()
            ->keyBy(function ($item) {
                return strtoupper(trim((string) $item->age_group));
            });

        $ordered = [];
        foreach (self::AGE_GROUPS as $group) {
            $key = strtoupper($group);
            $row = $rows->get($key);
            $ordered[] = [
                'label' => $group,
                'male' => $row ? (int) $row->male : 0,
                'female' => $row ? (int) $row->female : 0,
                'total' => $row ? (int) $row->total : 0,
            ];
        }

        return $ordered;
    }

    private function districtRanking(?array $period, int $limit = 5)
    {
        if (!$period) {
            return collect();
        }

        return DB::table('pop_gender')
            ->join('districts', 'districts.id', '=', 'pop_gender.district_id')
            ->select('districts.name')
            ->selectRaw('SUM(pop_gender.total) as total')
            ->selectRaw('SUM(pop_gender.male) as male')
            ->selectRaw('SUM(pop_gender.female) as female')
            ->where('pop_gender.year', $period['year'])
            ->where('pop_gender.semester', $period['semester'])
            ->groupBy('districts.id', 'districts.name')
            ->orderByDesc('total')
            ->limit($limit)
            ->get();
    }

    private function mapPopulationSummary(?array $period): array
    {
        if (!$period) {
            return $this->emptyMapStats();
        }

        $districtRows = DB::table('pop_gender')
            ->join('districts', 'districts.id', '=', 'pop_gender.district_id')
            ->select('districts.id', 'districts.code', 'districts.name')
            ->selectRaw('SUM(pop_gender.male) as male')
            ->selectRaw('SUM(pop_gender.female) as female')
            ->selectRaw('SUM(pop_gender.total) as total')
            ->where('pop_gender.year', $period['year'])
            ->where('pop_gender.semester', $period['semester'])
            ->groupBy('districts.id', 'districts.code', 'districts.name')
            ->get();

        $districtsByCode = [];
        $districtsBySlug = [];
        foreach ($districtRows as $row) {
            $entry = [
                'code' => $row->code,
                'name' => $row->name,
                'male' => (int) ($row->male ?? 0),
                'female' => (int) ($row->female ?? 0),
                'total' => (int) ($row->total ?? 0),
            ];

            foreach ($this->codeAliases($row->code ?? null) as $alias) {
                $districtsByCode[$alias] = $entry;
            }

            if ($slug = $this->normalizeNameKey($row->name ?? null)) {
                $districtsBySlug[$slug] = $entry;
            }
        }

        $villageRows = DB::table('pop_gender')
            ->join('villages', 'villages.id', '=', 'pop_gender.village_id')
            ->join('districts', 'districts.id', '=', 'villages.district_id')
            ->select(
                'villages.id as village_id',
                'villages.code as village_code',
                'villages.name as village_name',
                'districts.id as district_id',
                'districts.code as district_code',
                'districts.name as district_name'
            )
            ->selectRaw('SUM(pop_gender.male) as male')
            ->selectRaw('SUM(pop_gender.female) as female')
            ->selectRaw('SUM(pop_gender.total) as total')
            ->where('pop_gender.year', $period['year'])
            ->where('pop_gender.semester', $period['semester'])
            ->groupBy(
                'villages.id',
                'villages.code',
                'villages.name',
                'districts.id',
                'districts.code',
                'districts.name'
            )
            ->get();

        $villagesByCode = [];
        $villagesBySlug = [];
        foreach ($villageRows as $row) {
            $entry = [
                'code' => $row->village_code,
                'name' => $row->village_name,
                'district_code' => $row->district_code,
                'district_name' => $row->district_name,
                'male' => (int) ($row->male ?? 0),
                'female' => (int) ($row->female ?? 0),
                'total' => (int) ($row->total ?? 0),
            ];

            $districtAliases = $this->codeAliases($row->district_code ?? null);
            $villageAliases = $this->codeAliases($row->village_code ?? null);
            foreach ($districtAliases as $districtAlias) {
                foreach ($villageAliases as $villageAlias) {
                    $villagesByCode[$districtAlias . '-' . $villageAlias] = $entry;
                }
            }

            $districtSlug = $this->normalizeNameKey($row->district_name ?? null);
            $villageSlug = $this->normalizeNameKey($row->village_name ?? null);
            if ($districtSlug && $villageSlug) {
                $villagesBySlug[$districtSlug . '-' . $villageSlug] = $entry;
            }
        }

        return [
            'districts' => [
                'by_code' => $districtsByCode,
                'by_slug' => $districtsBySlug,
            ],
            'villages' => [
                'by_code' => $villagesByCode,
                'by_slug' => $villagesBySlug,
            ],
        ];
    }

    private function codeAliases($code): array
    {
        if ($code === null) {
            return [];
        }

        $digits = preg_replace('/\D/', '', (string) $code);
        if ($digits === '') {
            return [];
        }

        $aliases = [$digits];
        if (strlen($digits) >= 3) {
            $aliases[] = str_pad(substr($digits, -3), 3, '0', STR_PAD_LEFT);
        }
        if (strlen($digits) >= 4) {
            $aliases[] = str_pad(substr($digits, -4), 4, '0', STR_PAD_LEFT);
        }
        if (strlen($digits) >= 5) {
            $aliases[] = str_pad(substr($digits, -5), 5, '0', STR_PAD_LEFT);
        }

        return array_values(array_unique(array_filter($aliases)));
    }

    private function normalizeNameKey(?string $name): ?string
    {
        if ($name === null) {
            return null;
        }

        $slug = Str::of($name)
            ->lower()
            ->ascii()
            ->replaceMatches('/[^a-z0-9]+/', '-')
            ->trim('-')
            ->value();

        return $slug === '' ? null : $slug;
    }

    private function emptyMapStats(): array
    {
        return [
            'districts' => [
                'by_code' => [],
                'by_slug' => [],
            ],
            'villages' => [
                'by_code' => [],
                'by_slug' => [],
            ],
        ];
    }

    private function areaPopulationTable(?array $period, array $filters): array
    {
        $context = $this->resolveAreaContext($filters);
        $titles = [
            'district' => 'Tabel Jumlah Penduduk per Kecamatan',
            'village' => 'Tabel Jumlah Penduduk per Desa/Kelurahan',
            'single' => 'Tabel Jumlah Penduduk Desa/Kelurahan',
        ];

        if (!$period) {
            return [
                'level' => $context['level'],
                'column' => $context['columnLabel'],
                'title' => $titles[$context['level']],
                'subtitle' => null,
                'rows' => [],
                'totals' => ['male' => 0, 'female' => 0, 'total' => 0],
            ];
        }

        $subtitleParts = [];
        $periodLabel = $this->formatPeriodLabel($period);

        switch ($context['level']) {
            case 'single':
                $village = Village::with('district')->find($filters['village_id']);
                if ($village) {
                    $titles['single'] = 'Tabel Jumlah Penduduk Desa/Kelurahan ' . $village->name;
                    if ($village->district) {
                        $subtitleParts[] = 'Kecamatan ' . $village->district->name;
                    }
                } elseif (!empty($filters['district_id'])) {
                    $districtName = optional(District::find($filters['district_id']))->name;
                    if ($districtName) {
                        $subtitleParts[] = 'Kecamatan ' . $districtName;
                    }
                }
                break;
            case 'village':
                $districtName = optional(District::find($filters['district_id']))->name;
                if ($districtName) {
                    $subtitleParts[] = 'Kecamatan ' . $districtName;
                }
                break;
            default:
                $subtitleParts[] = 'Kabupaten Madiun';
                break;
        }

        if ($periodLabel) {
            $subtitleParts[] = $periodLabel;
        }

        $contextQuery = $this->prepareAreaQuery('pop_gender', $period, $filters);
        $query = $contextQuery['query']
            ->selectRaw('SUM(pop_gender.male) as male')
            ->selectRaw('SUM(pop_gender.female) as female')
            ->selectRaw('SUM(pop_gender.total) as total');

        $this->applyGroupBy($query, $contextQuery['groupBy']);
        $query->orderBy('area_name');

        $results = $query->get();

        $rows = $results->map(function ($row) use ($contextQuery) {
            return [
                'area_id' => $row->area_id ?? null,
                'name' => $row->area_name ?? '-',
                'male' => (int) ($row->male ?? 0),
                'female' => (int) ($row->female ?? 0),
                'total' => (int) ($row->total ?? 0),
                'highlight' => isset($contextQuery['highlightId']) && $contextQuery['highlightId'] !== null
                    ? ((int) $contextQuery['highlightId'] === (int) ($row->area_id ?? 0))
                    : false,
            ];
        })->toArray();

        $totals = $this->summarizeRows(array_map(function ($row) {
            return [
                'male' => $row['male'],
                'female' => $row['female'],
                'total' => $row['total'],
            ];
        }, $rows));

        return [
            'level' => $contextQuery['level'],
            'column' => $contextQuery['columnLabel'],
            'title' => $titles[$contextQuery['level']],
            'subtitle' => $this->buildSubtitle($subtitleParts),
            'rows' => $rows,
            'totals' => $totals,
        ];
    }

    private function buildSubtitle(array $parts): ?string
    {
        $parts = array_values(array_filter(array_map('trim', $parts)));
        return empty($parts) ? null : implode(' • ', $parts);
    }

    private function summarizeRows(array $rows): array
    {
        $summary = ['male' => 0, 'female' => 0, 'total' => 0];

        foreach ($rows as $row) {
            $summary['male'] += (int) ($row['male'] ?? 0);
            $summary['female'] += (int) ($row['female'] ?? 0);
            $summary['total'] += (int) ($row['total'] ?? 0);
        }

        return $summary;
    }

    private function educationMatrix(?array $period, array $filters): array
    {
        $labels = $this->educationLabels();
        if (!$period) {
            return $this->buildEmptyMatrix($labels, $this->resolveAreaContext($filters));
        }

        $context = $this->prepareAreaQuery('pop_education', $period, $filters);
        $query = $context['query'];
        foreach (array_keys($labels) as $key) {
            $query->selectRaw("SUM({$key}_m) as {$key}_m");
            $query->selectRaw("SUM({$key}_f) as {$key}_f");
        }

        $this->applyGroupBy($query, $context['groupBy']);
        $query->orderBy('area_name');

        $results = $query->get();

        return $this->formatMatrixResult($results, $labels, $context);
    }

    private function wajibKtpMatrix(?array $period, array $filters): array
    {
        $labels = ['wajib_ktp' => 'Wajib KTP'];
        if (!$period) {
            return $this->buildEmptyMatrix($labels, $this->resolveAreaContext($filters));
        }

        $context = $this->prepareAreaQuery('pop_wajib_ktp', $period, $filters);
        $query = $context['query']
            ->selectRaw('SUM(pop_wajib_ktp.male) as wajib_ktp_m')
            ->selectRaw('SUM(pop_wajib_ktp.female) as wajib_ktp_f');

        $this->applyGroupBy($query, $context['groupBy']);
        $query->orderBy('area_name');

        $results = $query->get();

        return $this->formatMatrixResult($results, $labels, $context);
    }

    private function maritalMatrix(?array $period, array $filters): array
    {
        $labels = $this->maritalLabels();
        if (!$period) {
            return $this->buildEmptyMatrix($labels, $this->resolveAreaContext($filters));
        }

        $context = $this->prepareAreaQuery('pop_marital_status', $period, $filters);
        $query = $context['query'];
        foreach (array_keys($labels) as $key) {
            $query->selectRaw("SUM({$key}_m) as {$key}_m");
            $query->selectRaw("SUM({$key}_f) as {$key}_f");
        }

        $this->applyGroupBy($query, $context['groupBy']);
        $query->orderBy('area_name');

        $results = $query->get();

        return $this->formatMatrixResult($results, $labels, $context);
    }

    private function maritalLabels(): array
    {
        return [
            'belum_kawin' => 'Belum Kawin',
            'kawin' => 'Kawin',
            'cerai_hidup' => 'Cerai Hidup',
            'cerai_mati' => 'Cerai Mati',
        ];
    }

    private function headHouseholdMatrix(?array $period, array $filters): array
    {
        $labels = $this->headHouseholdLabels();
        if (!$period) {
            return $this->buildEmptyMatrix($labels, $this->resolveAreaContext($filters));
        }

        $context = $this->prepareAreaQuery('pop_head_of_household', $period, $filters);
        $query = $context['query'];
        foreach (array_keys($labels) as $key) {
            $query->selectRaw("SUM({$key}_m) as {$key}_m");
            $query->selectRaw("SUM({$key}_f) as {$key}_f");
        }

        $this->applyGroupBy($query, $context['groupBy']);
        $query->orderBy('area_name');

        $results = $query->get();

        return $this->formatMatrixResult($results, $labels, $context);
    }

    private function headHouseholdLabels(): array
    {
        return [
            'belum_kawin' => 'Belum Kawin',
            'kawin' => 'Kawin',
            'cerai_hidup' => 'Cerai Hidup',
            'cerai_mati' => 'Cerai Mati',
        ];
    }

    private function religionMatrix(?array $period, array $filters): array
    {
        $labels = $this->religionLabels();
        if (!$period) {
            return $this->buildEmptyMatrix($labels, $this->resolveAreaContext($filters));
        }

        $context = $this->prepareAreaQuery('pop_religion', $period, $filters);
        $query = $context['query'];
        foreach (array_keys($labels) as $key) {
            $query->selectRaw("SUM({$key}_m) as {$key}_m");
            $query->selectRaw("SUM({$key}_f) as {$key}_f");
        }

        $this->applyGroupBy($query, $context['groupBy']);
        $query->orderBy('area_name');

        $results = $query->get();

        return $this->formatMatrixResult($results, $labels, $context);
    }

    private function religionLabels(): array
    {
        return [
            'islam' => 'Islam',
            'kristen' => 'Kristen',
            'katolik' => 'Katolik',
            'hindu' => 'Hindu',
            'buddha' => 'Buddha',
            'konghucu' => 'Konghucu',
            'aliran_kepercayaan' => 'Aliran Kepercayaan',
        ];
    }

    private function resolveAreaContext(array $filters): array
    {
        $districtId = $filters['district_id'] ?? null;
        $villageId = $filters['village_id'] ?? null;

        if ($districtId) {
            if ($villageId) {
                return [
                    'level' => 'single',
                    'columnLabel' => 'Desa/Kelurahan',
                    'highlightId' => (int) $villageId,
                ];
            }

            return [
                'level' => 'village',
                'columnLabel' => 'Desa/Kelurahan',
                'highlightId' => null,
            ];
        }

        return [
            'level' => 'district',
            'columnLabel' => 'SEMUA',
            'highlightId' => null,
        ];
    }

    private function prepareAreaQuery(string $table, array $period, array $filters): array
    {
        $context = $this->resolveAreaContext($filters);
        $districtId = $filters['district_id'] ?? null;
        $villageId = $filters['village_id'] ?? null;

        $query = DB::table($table)
            ->where("{$table}.year", $period['year'])
            ->where("{$table}.semester", $period['semester']);

        switch ($context['level']) {
            case 'single':
                $query->join('villages', 'villages.id', '=', "{$table}.village_id")
                    ->select("{$table}.village_id as area_id", 'villages.name as area_name')
                    ->whereNotNull("{$table}.village_id");
                if ($districtId) {
                    $query->where("{$table}.district_id", $districtId);
                }
                if ($villageId) {
                    $query->where("{$table}.village_id", $villageId);
                }
                $groupBy = ["{$table}.village_id", 'villages.name'];
                break;

            case 'village':
                $query->join('villages', 'villages.id', '=', "{$table}.village_id")
                    ->select("{$table}.village_id as area_id", 'villages.name as area_name')
                    ->whereNotNull("{$table}.village_id");
                if ($districtId) {
                    $query->where("{$table}.district_id", $districtId);
                }
                $groupBy = ["{$table}.village_id", 'villages.name'];
                break;

            default:
                $query->join('districts', 'districts.id', '=', "{$table}.district_id")
                    ->select("{$table}.district_id as area_id", 'districts.name as area_name')
                    ->whereNotNull("{$table}.district_id");
                $groupBy = ["{$table}.district_id", 'districts.name'];
                break;
        }

        return array_merge($context, [
            'query' => $query,
            'groupBy' => $groupBy,
        ]);
    }

    private function applyGroupBy($query, array $groupBy): void
    {
        if (!empty($groupBy)) {
            $query->groupBy($groupBy);
        }
    }

    private function buildEmptyMatrix(array $labels, array $context): array
    {
        $columns = [];
        $totals = [];
        foreach ($labels as $key => $label) {
            $columns[] = ['key' => $key, 'label' => $label];
            $totals[$key] = ['male' => 0, 'female' => 0, 'total' => 0];
        }

        return [
            'level' => $context['level'],
            'columnLabel' => $context['columnLabel'],
            'columns' => $columns,
            'rows' => [],
            'totals' => $totals,
            'highlightAreaId' => $context['highlightId'] ?? null,
        ];
    }

    private function formatMatrixResult($results, array $labels, array $context): array
    {
        $columns = [];
        $totals = [];
        foreach ($labels as $key => $label) {
            $columns[] = ['key' => $key, 'label' => $label];
            $totals[$key] = ['male' => 0, 'female' => 0, 'total' => 0];
        }

        $rows = [];
        foreach ($results as $row) {
            $values = [];
            foreach ($labels as $key => $label) {
                $male = (int) ($row->{$key . '_m'} ?? 0);
                $female = (int) ($row->{$key . '_f'} ?? 0);
                $total = $male + $female;

                $values[$key] = [
                    'male' => $male,
                    'female' => $female,
                    'total' => $total,
                ];

                $totals[$key]['male'] += $male;
                $totals[$key]['female'] += $female;
                $totals[$key]['total'] += $total;
            }

            $rows[] = [
                'area_id' => $row->area_id ?? null,
                'name' => $row->area_name ?? '-',
                'values' => $values,
                'highlight' => isset($context['highlightId']) && $context['highlightId'] !== null
                    ? ((int) $context['highlightId'] === (int) ($row->area_id ?? 0))
                    : false,
            ];
        }

        return [
            'level' => $context['level'],
            'columnLabel' => $context['columnLabel'],
            'columns' => $columns,
            'rows' => $rows,
            'totals' => $totals,
            'highlightAreaId' => $context['highlightId'] ?? null,
        ];
    }

    private function educationSummary(?array $period, array $filters = []): array
    {
        if (!$period) {
            return [];
        }

        $config = config('dukcapil_import.sheets.pendidikan.cols', []);
        $columns = array_filter($config, function ($col) {
            return !in_array($col, [
                'year',
                'semester',
                'district_code',
                'district_name',
                'village_code',
                'village_name',
            ]);
        });

        $query = DB::table('pop_education')
            ->where('year', $period['year'])
            ->where('semester', $period['semester']);

        $this->applyAreaScope($query, $filters);

        foreach ($columns as $col) {
            $query->selectRaw("SUM($col) as $col");
        }

        $row = $query->first();
        if (!$row) {
            return [];
        }

        $buckets = [];
        foreach ($columns as $col) {
            $value = (int) ($row->$col ?? 0);
            if (str_ends_with($col, '_m') || str_ends_with($col, '_f')) {
                $base = substr($col, 0, -2);
                if (!isset($buckets[$base])) {
                    $buckets[$base] = ['male' => 0, 'female' => 0, 'total' => null];
                }
                if (str_ends_with($col, '_m')) {
                    $buckets[$base]['male'] = $value;
                } else {
                    $buckets[$base]['female'] = $value;
                }
            } elseif (str_ends_with($col, '_total')) {
                $base = substr($col, 0, -6);
                if (!isset($buckets[$base])) {
                    $buckets[$base] = ['male' => 0, 'female' => 0, 'total' => null];
                }
                $buckets[$base]['total'] = $value;
            }
        }

        foreach ($buckets as $key => &$values) {
            $values['male'] = (int) ($values['male'] ?? 0);
            $values['female'] = (int) ($values['female'] ?? 0);
            $total = $values['total'] ?? null;
            if ($total === null) {
                $total = $values['male'] + $values['female'];
            }
            $values['total'] = (int) $total;
        }

        $labels = $this->educationLabels();
        $ordered = [];
        foreach ($labels as $key => $label) {
            if (isset($buckets[$key])) {
                $ordered[] = [
                    'key' => $key,
                    'label' => $label,
                    'male' => $buckets[$key]['male'],
                    'female' => $buckets[$key]['female'],
                    'total' => $buckets[$key]['total'],
                ];
                unset($buckets[$key]);
            }
        }

        foreach ($buckets as $key => $values) {
            $ordered[] = [
                'key' => $key,
                'label' => $this->labelize($key),
                'male' => $values['male'],
                'female' => $values['female'],
                'total' => $values['total'],
            ];
        }

        return $ordered;
    }

    private function singleAgeSummary(?array $period, array $filters = []): array
    {
        if (!$period) {
            return [];
        }

        $query = DB::table('pop_single_age')
            ->where('year', $period['year'])
            ->where('semester', $period['semester']);

        $this->applyAreaScope($query, $filters);

        $rows = $query
            ->select('age')
            ->selectRaw('SUM(male) as male')
            ->selectRaw('SUM(female) as female')
            ->selectRaw('SUM(total) as total')
            ->groupBy('age')
            ->orderBy('age')
            ->get();

        return $rows->map(function ($row) {
            $male = (int) ($row->male ?? 0);
            $female = (int) ($row->female ?? 0);
            $total = (int) ($row->total ?? ($male + $female));

            return [
                'label' => $row->age,
                'male' => $male,
                'female' => $female,
                'total' => $total,
            ];
        })->toArray();
    }

    private function occupationHighlights(?array $period, array $filters = []): array
    {
        if (!$period) {
            return [];
        }

        $config = config('dukcapil_import.sheets.pekerjaan.cols', []);
        $columns = array_filter($config, function ($col) {
            return !in_array($col, [
                'year',
                'semester',
                'district_code',
                'district_name',
                'village_code',
                'village_name',
            ]);
        });

        $query = DB::table('pop_occupation')
            ->where('year', $period['year'])
            ->where('semester', $period['semester']);

        $this->applyAreaScope($query, $filters);

        foreach ($columns as $col) {
            $query->selectRaw("SUM($col) as $col");
        }

        $row = $query->first();
        if (!$row) {
            return [];
        }

        $series = [];
        foreach ($columns as $col) {
            if (str_ends_with($col, '_m') || str_ends_with($col, '_f')) {
                $base = substr($col, 0, -2);
                if (!isset($series[$base])) {
                    $series[$base] = ['male' => 0, 'female' => 0, 'total' => 0];
                }
                if (str_ends_with($col, '_m')) {
                    $series[$base]['male'] = (int) $row->$col;
                } else {
                    $series[$base]['female'] = (int) $row->$col;
                }
            } elseif ($col === 'total') {
                continue;
            }
        }

        foreach ($series as $key => &$values) {
            $values['male'] = (int) ($values['male'] ?? 0);
            $values['female'] = (int) ($values['female'] ?? 0);
            $values['total'] = $values['male'] + $values['female'];
        }
        unset($values);

        $labels = $this->occupationLabelsCache();
        $ordered = [];
        foreach ($config as $col) {
            if (in_array($col, ['year','semester','district_code','district_name','village_code','village_name','total'])) {
                continue;
            }
            if (str_ends_with($col, '_m') || str_ends_with($col, '_f')) {
                $base = substr($col, 0, -2);
                if (isset($series[$base]) && !isset($ordered[$base])) {
                    $ordered[$base] = $series[$base] + [
                        'label' => $labels[$base] ?? $this->labelize($base),
                    ];
                    if (($ordered[$base]['total'] ?? 0) === 0) {
                        unset($ordered[$base]);
                    }
                }
            }
        }

        foreach ($series as $key => $values) {
            if (!isset($ordered[$key])) {
                if (($values['total'] ?? 0) === 0) {
                    continue;
                }
                $ordered[$key] = $values + [
                    'label' => $labels[$key] ?? $this->labelize($key),
                ];
            }
        }

        return array_values($ordered);
    }

    private function buildGenderChart(string $title, array $summary): array
    {
        $labels = ['Laki-laki', 'Perempuan'];
        $data = [
            (int) ($summary['male'] ?? 0),
            (int) ($summary['female'] ?? 0),
        ];

        return [
            'title' => $title,
            'labels' => $labels,
            'datasets' => [
                $this->makeDataset('Jumlah Penduduk', $data, ['#377dff', '#ff5c8d']),
            ],
        ];
    }

    private function buildWajibKtpChart(string $title, array $summary): array
    {
        $labels = ['Laki-laki', 'Perempuan', 'Total'];
        $data = [
            (int) ($summary['male'] ?? 0),
            (int) ($summary['female'] ?? 0),
            (int) ($summary['total'] ?? 0),
        ];

        return [
            'title' => $title,
            'labels' => $labels,
            'datasets' => [
                $this->makeDataset('Wajib KTP', $data, ['#377dff', '#ff5c8d', '#6c63ff']),
            ],
        ];
    }

    private function buildSeriesChart(string $title, array $rows): array
    {
        if (empty($rows)) {
            return [
                'title' => $title,
                'labels' => [],
                'datasets' => [],
            ];
        }

        $labels = array_map(fn($row) => $row['label'] ?? '-', $rows);
        $male = array_map(fn($row) => (int) ($row['male'] ?? 0), $rows);
        $female = array_map(fn($row) => (int) ($row['female'] ?? 0), $rows);
        $total = array_map(fn($row) => (int) ($row['total'] ?? 0), $rows);

        return [
            'title' => $title,
            'labels' => $labels,
            'datasets' => [
                $this->makeDataset('Laki-laki', $male, '#377dff'),
                $this->makeDataset('Perempuan', $female, '#ff5c8d'),
                $this->makeDataset('Total', $total, '#6c63ff'),
            ],
        ];
    }

    private function makeDataset(string $label, array $data, $color): array
    {
        $count = count($data);
        $background = is_array($color) ? $color : array_fill(0, $count, $color);
        $border = $background;

        return [
            'label' => $label,
            'data' => array_map('intval', $data),
            'backgroundColor' => $background,
            'borderColor' => $border,
            'borderWidth' => 1,
        ];
    }

    private function maritalStatusSummary(?array $period, array $filters = []): array
    {
        return $this->sumPairColumns('pop_marital_status', $period, $filters, [
            'belum_kawin',
            'kawin',
            'cerai_hidup',
            'cerai_mati',
        ]);
    }

    private function headOfHouseholdSummary(?array $period, array $filters = []): array
    {
        return $this->sumPairColumns('pop_head_of_household', $period, $filters, [
            'belum_kawin',
            'kawin',
            'cerai_hidup',
            'cerai_mati',
        ]);
    }

    private function religionSummary(?array $period, array $filters = []): array
    {
        return $this->sumPairColumns('pop_religion', $period, $filters, [
            'islam',
            'kristen',
            'katolik',
            'hindu',
            'buddha',
            'konghucu',
            'aliran_kepercayaan',
        ]);
    }

    private function sumPairColumns(string $table, ?array $period, array $filters, array $keys): array
    {
        if (!$period) {
            return [];
        }

        $query = DB::table($table)
            ->where('year', $period['year'])
            ->where('semester', $period['semester']);

        $this->applyAreaScope($query, $filters);

        foreach ($keys as $key) {
            $query->selectRaw("SUM({$key}_m) as {$key}_m");
            $query->selectRaw("SUM({$key}_f) as {$key}_f");
        }

        $row = $query->first();
        if (!$row) {
            return [];
        }

        $results = [];
        foreach ($keys as $key) {
            $male = (int) ($row->{$key . '_m'} ?? 0);
            $female = (int) ($row->{$key . '_f'} ?? 0);
            $results[] = [
                'label' => $this->labelize($key),
                'male' => $male,
                'female' => $female,
                'total' => $male + $female,
            ];
        }

        return $results;
    }

    private function formatPeriodLabel(?array $period): ?string
    {
        if (!$period) {
            return null;
        }

        $year = $period['year'] ?? null;
        $semester = $period['semester'] ?? null;

        if ($year === null && $semester === null) {
            return null;
        }

        if ($year !== null && $semester !== null) {
            return 'Semester ' . $semester . ' Tahun ' . $year;
        }

        if ($year !== null) {
            return 'Tahun ' . $year;
        }

        return 'Semester ' . $semester;
    }

    private function labelize(string $key): string
    {
        return ucwords(str_replace('_', ' ', $key));
    }

    private function educationLabels(): array
    {
        return [
            'belum_sekolah' => 'Belum / Tidak Sekolah',
            'belum_tamat_sd' => 'Belum Tamat SD',
            'tamat_sd' => 'Tamat SD',
            'tamat_sltp' => 'Tamat SLTP',
            'tamat_slta' => 'Tamat SLTA',
            'd1d2' => 'Diploma I/II',
            'd3' => 'Diploma III',
            's1' => 'Strata 1',
            's2' => 'Strata 2',
            's3' => 'Strata 3',
        ];
    }

    private array $occupationLabelCache = [];

    private function occupationLabelsCache(): array
    {
        if ($this->occupationLabelCache) {
            return $this->occupationLabelCache;
        }

        $config = config('dukcapil_import.sheets.pekerjaan.cols', []);
        $labels = [];

        foreach ($config as $col) {
            if (preg_match('/^(.*)_(m|f)$/', $col, $m)) {
                $base = $m[1];
                if (!isset($labels[$base])) {
                    $labels[$base] = $this->labelize($base);
                }
            }
        }

        return $this->occupationLabelCache = $labels;
    }

    private function applyAreaScope($query, array $filters): void
    {
        if (!empty($filters['district_id'])) {
            $query->where('district_id', $filters['district_id']);
        }
        if (!empty($filters['village_id'])) {
            $query->where('village_id', $filters['village_id']);
        }
    }

}
