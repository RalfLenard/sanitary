<?php

namespace App\Http\Controllers;

use App\Models\Sanitary;
use Illuminate\Http\Request;
use app\Models\User;
use App\Models\HealthCard;
use Inertia\Inertia;
use App\Models\Death;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use phpDocumentor\Reflection\PseudoTypes\True_;


class HomeController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
    
        if (!$user || !in_array($user->usertype, ['admin', 'staff'])) {
            return Inertia::render('Guest');
        }
    
        $selectedYear = $request->query('year', now()->year);
    
        // Barangay data
        $barangayData = HealthCard::select('barangay')
            ->whereYear('created_at', $selectedYear)
            ->where('barangay', '!=', '')
            ->groupBy('barangay')
            ->selectRaw('barangay, COUNT(*) as count')
            ->orderBy('barangay', 'asc')
            ->get();
    
        // RHU data
        $rhuData = HealthCard::select('rhu')
            ->whereNotNull('rhu')
            ->whereYear('created_at', $selectedYear)
            ->groupBy('rhu')
            ->selectRaw('rhu, COUNT(*) as count')
            ->orderBy('rhu', 'asc')
            ->get();
    
        // Monthly HealthCard data
        $monthlyData = HealthCard::select(
            DB::raw("DATE_FORMAT(created_at, '%M') as month_name"),
            DB::raw("MONTH(created_at) as month_number"),
            DB::raw("COUNT(*) as count")
        )
            ->whereYear('created_at', $selectedYear)
            ->groupBy('month_name', 'month_number')
            ->orderBy('month_number')
            ->get();
    
        // Health card type
        $cardTypeData = HealthCard::select('health_card_type')
            ->whereYear('created_at', $selectedYear)
            ->groupBy('health_card_type')
            ->selectRaw('health_card_type, COUNT(*) as count')
            ->get();
    
        $foodCount = $cardTypeData->firstWhere('health_card_type', 'food')?->count ?? 0;
        $nonFoodCount = $cardTypeData->firstWhere('health_card_type', 'non_food')?->count ?? 0;
        $otherCount = $cardTypeData->firstWhere('health_card_type', 'others')?->count ?? 0;
    
        // RHU per month
        $rhuMonthlyRaw = HealthCard::select(
            'rhu',
            DB::raw("MONTH(created_at) as month"),
            DB::raw("DATE_FORMAT(created_at, '%M') as month_name"),
            DB::raw("COUNT(*) as count")
        )
            ->whereNotNull('rhu')
            ->whereYear('created_at', $selectedYear)
            ->groupBy('rhu', 'month', 'month_name')
            ->orderBy('month')
            ->get();
    
        $months = collect(range(1, 12))->map(fn($m) => date('F', mktime(0, 0, 0, $m, 1)))->values();
    
        $rhus = $rhuMonthlyRaw->pluck('rhu')->unique()->filter()->values();
        $datasets = $rhus->map(function ($rhu) use ($rhuMonthlyRaw, $months) {
            $data = $months->map(function ($month) use ($rhu, $rhuMonthlyRaw) {
                return $rhuMonthlyRaw->firstWhere(fn($item) => $item->rhu === $rhu && $item->month_name === $month)?->count ?? 0;
            });
            return [
                'label' => $rhu,
                'data' => $data,
                'fill' => false,
                'borderColor' => '#' . substr(md5($rhu), 0, 6),
            ];
        });
    
        // Barangay per month
        $barangayMonthlyRaw = HealthCard::select(
            'barangay',
            DB::raw("MONTH(created_at) as month"),
            DB::raw("DATE_FORMAT(created_at, '%M') as month_name"),
            DB::raw("COUNT(*) as count")
        )
            ->whereNotNull('barangay')
            ->where('barangay', '!=', '')
            ->whereYear('created_at', $selectedYear)
            ->groupBy('barangay', 'month', 'month_name')
            ->orderBy('month')
            ->get();
    
        $barangays = $barangayMonthlyRaw->pluck('barangay')->unique()->filter()->values();
        $barangayMonthlyDatasets = $barangays->map(function ($barangay) use ($barangayMonthlyRaw, $months) {
            $data = $months->map(function ($month) use ($barangay, $barangayMonthlyRaw) {
                return $barangayMonthlyRaw->firstWhere(fn($item) => $item->barangay === $barangay && $item->month_name === $month)?->count ?? 0;
            });
            return [
                'label' => $barangay,
                'data' => $data,
                'fill' => false,
                'borderColor' => '#' . substr(md5($barangay), 0, 6),
            ];
        });
    
        // Sanitary monthly
        $monthlySanitaryData = DB::table('sanitary')
            ->selectRaw("MONTH(renewed_at) as month, status, COUNT(*) as count")
            ->whereNotNull('renewed_at')
            ->whereYear('renewed_at', $selectedYear)
            ->whereIn('status', ['new', 'renewed'])
            ->groupBy(DB::raw("MONTH(renewed_at)"), 'status')
            ->orderBy('month')
            ->get();
    
        $sanitaryNewPerMonth = collect(range(1, 12))->map(fn($m) =>
            $monthlySanitaryData->where('month', $m)->where('status', 'new')->sum('count')
        );
        $sanitaryRenewedPerMonth = collect(range(1, 12))->map(fn($m) =>
            $monthlySanitaryData->where('month', $m)->where('status', 'renewed')->sum('count')
        );
    
        // Sanitary quarterly
        $quarterlySanitaryData = DB::table('sanitary')
            ->selectRaw("QUARTER(renewed_at) as quarter, status, COUNT(*) as count")
            ->whereNotNull('renewed_at')
            ->whereYear('renewed_at', $selectedYear)
            ->whereIn('status', ['new', 'renewed'])
            ->groupBy(DB::raw("QUARTER(renewed_at)"), 'status')
            ->orderBy('quarter')
            ->get();
    
        $quarterLabels = ['Q1', 'Q2', 'Q3', 'Q4'];
        $sanitaryNewPerQuarter = collect(range(1, 4))->map(fn($q) =>
            $quarterlySanitaryData->where('quarter', $q)->where('status', 'new')->sum('count')
        );
        $sanitaryRenewedPerQuarter = collect(range(1, 4))->map(fn($q) =>
            $quarterlySanitaryData->where('quarter', $q)->where('status', 'renewed')->sum('count')
        );
    
        // Death certificate
        $totalDeath = Death::whereYear('created_at', $selectedYear)->count();
    
        $deathMonthlyData = Death::select(
            DB::raw("DATE_FORMAT(created_at, '%M') as month_name"),
            DB::raw("MONTH(created_at) as month_number"),
            DB::raw("COUNT(*) as count")
        )
            ->whereYear('created_at', $selectedYear)
            ->groupBy('month_name', 'month_number')
            ->orderBy('month_number')
            ->get();
    
        // Printed permits
        $totalPermitPrinted = Sanitary::where('confirmed', true)
            ->whereYear('renewed_at', $selectedYear)
            ->count();
    
        $printedSanitaryMonthlyData = DB::table('sanitary')
            ->selectRaw("MONTH(renewed_at) as month, DATE_FORMAT(renewed_at, '%M') as month_name, COUNT(*) as count")
            ->where('confirmed', true)
            ->whereYear('renewed_at', $selectedYear)
            ->whereNotNull('renewed_at')
            ->groupBy(DB::raw("MONTH(renewed_at)"), DB::raw("DATE_FORMAT(renewed_at, '%M')"))
            ->orderBy(DB::raw("MONTH(renewed_at)"))
            ->get();
    
        $printedPermitMonths = collect(range(1, 12))->map(fn($m) => date('F', mktime(0, 0, 0, $m, 1)));
        $printedPermitCounts = $printedPermitMonths->map(fn($month) =>
            $printedSanitaryMonthlyData->firstWhere('month_name', $month)?->count ?? 0
        );
    
        $availableYears = HealthCard::selectRaw('YEAR(created_at) as year')
            ->distinct()
            ->orderBy('year', 'asc')
            ->pluck('year');
    
        $totalSanitaryCount = DB::table('sanitary')->whereYear('renewed_at', $selectedYear)->count();
        $totalSanitaryCounts = DB::table('sanitary')->where('status', 'new')->whereYear('renewed_at', $selectedYear)->count();
    
        return Inertia::render('Dashboard', [
            'selectedYear' => (int) $selectedYear,
            'availableYears' => $availableYears,
            'chartData' => [
                'barangay' => [
                    'labels' => $barangayData->pluck('barangay'),
                    'data' => $barangayData->pluck('count'),
                ],
                'rhuHealthCard' => [
                    'labels' => $rhuData->pluck('rhu'),
                    'data' => $rhuData->pluck('count'),
                ],
                'healthCards' => [
                    'labels' => $monthlyData->pluck('month_name'),
                    'data' => $monthlyData->pluck('count'),
                ],
                'categories' => [
                    'labels' => ['Food', 'Non-Food', 'Other'],
                    'data' => [$foodCount, $nonFoodCount, $otherCount],
                ],
                'rhuMonthly' => [
                    'labels' => $months,
                    'datasets' => $datasets,
                ],
                'barangayMonthly' => [
                    'labels' => $months,
                    'datasets' => $barangayMonthlyDatasets,
                ],
                'deathCert' => [
                    'labels' => $deathMonthlyData->pluck('month_name'),
                    'data' => $deathMonthlyData->pluck('count'),
                ],
                'printedPermits' => [
                    'labels' => $printedPermitMonths,
                    'data' => $printedPermitCounts,
                ],
                'sanitaryMonthly' => [
                    'labels' => $printedPermitMonths,
                    'datasets' => [
                        [
                            'label' => 'New Permits',
                            'data' => $sanitaryNewPerMonth,
                            'backgroundColor' => 'rgba(16, 185, 129, 0.5)',
                            'borderColor' => 'rgb(16, 185, 129)',
                            'borderWidth' => 2,
                        ],
                        [
                            'label' => 'Renewed Permits',
                            'data' => $sanitaryRenewedPerMonth,
                            'backgroundColor' => 'rgba(245, 158, 11, 0.5)',
                            'borderColor' => 'rgb(245, 158, 11)',
                            'borderWidth' => 2,
                        ]
                    ],
                ],
                'sanitaryQuarterly' => [
                    'labels' => $quarterLabels,
                    'datasets' => [
                        [
                            'label' => 'New Permits',
                            'data' => $sanitaryNewPerQuarter,
                            'backgroundColor' => 'rgba(16, 185, 129, 0.5)',
                            'borderColor' => 'rgb(16, 185, 129)',
                            'borderWidth' => 2,
                        ],
                        [
                            'label' => 'Renewed Permits',
                            'data' => $sanitaryRenewedPerQuarter,
                            'backgroundColor' => 'rgba(245, 158, 11, 0.5)',
                            'borderColor' => 'rgb(245, 158, 11)',
                            'borderWidth' => 2,
                        ]
                    ],
                ],
            ],
            'totalSanitaryCount' => $totalSanitaryCount,
            'totalSanitaryCounts' => $totalSanitaryCounts,
            'totalDeath' => $totalDeath,
            'totalPermitPrinted' => $totalPermitPrinted,
        ]);
    }
    

}
