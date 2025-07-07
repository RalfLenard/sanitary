<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use app\Models\User;
use App\Models\HealthCard;
use Inertia\Inertia;
use App\Models\Death;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;


class HomeController extends Controller
{
    public function index()
    {
        $user = Auth::user();

       if ($user && in_array($user->usertype, ['admin', 'staff']))
        {

             $barangayData = HealthCard::select('barangay')
                ->groupBy('barangay')
                ->where('barangay', '!=', '')
                ->selectRaw('barangay, COUNT(*) as count')
                ->orderBy('barangay', 'asc')
                ->get();

            // Group by RHU, excluding nulls
            $rhuData = HealthCard::select('rhu')
                ->whereNotNull('rhu')
                ->groupBy('rhu')
                ->selectRaw('rhu, COUNT(*) as count')
                ->orderBy('rhu', 'asc')
                ->get();

            // Group by Month using created_at
            $monthlyData = HealthCard::select(
                DB::raw("DATE_FORMAT(created_at, '%M') as month_name"),
                DB::raw("MONTH(created_at) as month_number"),
                DB::raw("COUNT(*) as count")
            )
                ->groupBy('month_name', 'month_number')
                ->orderBy('month_number')
                ->get();

            // Group by Health Card Type
            $cardTypeData = HealthCard::select('health_card_type')
                ->groupBy('health_card_type')
                ->selectRaw('health_card_type, COUNT(*) as count')
                ->get();

            // Group by RHU per month, exclude rows with null RHU
            $rhuMonthlyRaw = HealthCard::select(
                'rhu',
                DB::raw("MONTH(created_at) as month"),
                DB::raw("DATE_FORMAT(created_at, '%M') as month_name"),
                DB::raw("COUNT(*) as count")
            )
                ->whereNotNull('rhu') // remove null RHUs
                ->groupBy('rhu', 'month', 'month_name')
                ->orderBy('month')
                ->get();

            // Build month labels (January to December)
            $months = collect(range(1, 12))->map(function ($m) {
                return date('F', mktime(0, 0, 0, $m, 1));
            })->values();

            // Get unique RHUs
            $rhus = $rhuMonthlyRaw->pluck('rhu')->unique()->filter()->values(); // filter() removes nulls/empty strings

            // Generate dataset per RHU
            $datasets = $rhus->map(function ($rhu) use ($rhuMonthlyRaw, $months) {
                $data = $months->map(function ($month) use ($rhu, $rhuMonthlyRaw) {
                    return $rhuMonthlyRaw->firstWhere(fn($item) => $item->rhu === $rhu && $item->month_name === $month)?->count ?? 0;
                });

                return [
                    'label' => $rhu,
                    'data' => $data->values(),
                    'fill' => false,
                    'borderColor' => '#' . substr(md5($rhu), 0, 6),
                ];
            })->values();

            // Available years from created_at
            $availableYears = HealthCard::selectRaw('YEAR(created_at) as year')
                ->distinct()
                ->orderBy('year', 'asc')
                ->pluck('year');

            // Extract health card type counts
            $foodCount = $cardTypeData->firstWhere('health_card_type', 'food')?->count ?? 0;
            $nonFoodCount = $cardTypeData->firstWhere('health_card_type', 'non_food')?->count ?? 0;
            $otherCount = $cardTypeData->firstWhere('health_card_type', 'others')?->count ?? 0;

            // Group by Barangay per Month issued
            $barangayMonthlyRaw = HealthCard::select(
                'barangay',
                DB::raw("MONTH(created_at) as month"),
                DB::raw("DATE_FORMAT(created_at, '%M') as month_name"),
                DB::raw("COUNT(*) as count")
            )
                ->whereNotNull('barangay') // already filters out nulls
                ->where('barangay', '!=', '') // this will filter out empty strings
                ->groupBy('barangay', 'month', 'month_name')
                ->orderBy('month')
                ->get();

            // Get unique Barangays
            $barangays = $barangayMonthlyRaw->pluck('barangay')->unique()->filter()->values();

            // Generate dataset per Barangay
            $barangayMonthlyDatasets = $barangays->map(function ($barangay) use ($barangayMonthlyRaw, $months) {
                $data = $months->map(function ($month) use ($barangay, $barangayMonthlyRaw) {
                    return $barangayMonthlyRaw->firstWhere(fn($item) => $item->barangay === $barangay && $item->month_name === $month)?->count ?? 0;
                });

                return [
                    'label' => $barangay,
                    'data' => $data->values(),
                    'fill' => false,
                    'borderColor' => '#' . substr(md5($barangay), 0, 6),
                ];
            })->values();

            // Sanitary Data (Monthly)
            $monthlySanitaryData = DB::table('sanitary')
                ->selectRaw("MONTH(created_at) as month, status, COUNT(*) as count")
                ->whereIn('status', ['New', 'Renewed'])
                ->groupBy('month', 'status')
                ->orderBy('month')
                ->get();

            // Monthly arrays for New and Renewed Permits
            $sanitaryNewPerMonth = collect(range(1, 12))->map(function ($m) use ($monthlySanitaryData) {
                return $monthlySanitaryData
                    ->firstWhere(fn($row) => $row->month == $m && $row->status === 'new')?->count ?? 0;
            });

            $sanitaryRenewedPerMonth = collect(range(1, 12))->map(function ($m) use ($monthlySanitaryData) {
                return $monthlySanitaryData
                    ->firstWhere(fn($row) => $row->month == $m && $row->status === 'renewed')?->count ?? 0;
            });

            // Sanitary Data (Quarterly) based on created_at
            $quarterlySanitaryData = DB::table('sanitary')
                ->selectRaw("QUARTER(created_at) as quarter, status, COUNT(*) as count")
                ->whereIn('status', ['new', 'renewed'])
                ->groupBy(DB::raw("QUARTER(created_at)"), 'status')  // Grouping by QUARTER of the 'created_at' field and 'status'
                ->orderBy('quarter')
                ->get();

            // Quarterly labels
            $quarterLabels = ['Q1', 'Q2', 'Q3', 'Q4'];

            // Build 4-quarter arrays for New and Renewed Permits
            $sanitaryNewPerQuarter = collect(range(1, 4))->map(function ($q) use ($quarterlySanitaryData) {
                return $quarterlySanitaryData
                    ->firstWhere(fn($row) => $row->quarter == $q && $row->status === 'new')?->count ?? 0;
            });

            $sanitaryRenewedPerQuarter = collect(range(1, 4))->map(function ($q) use ($quarterlySanitaryData) {
                return $quarterlySanitaryData
                    ->firstWhere(fn($row) => $row->quarter == $q && $row->status === 'renewed')?->count ?? 0;
            });

            $totalSanitaryCount = DB::table('sanitary')->count();

            $totalSanitaryCounts = DB::table('sanitary')
                ->where('status','new')
                ->count();


             // Death Certificate Data
            $totalDeath = Death::whereNotNull('created_at')->count();

            $deathMonthlyRaw = Death::select(
                DB::raw("residence as barangay"),
                DB::raw("MONTH(created_at) as month"),
                DB::raw("DATE_FORMAT(created_at, '%M') as month_name"),
                DB::raw("COUNT(*) as count")
            )
                ->whereNotNull('residence')
                ->where('residence', '!=', '')
                ->groupBy('barangay', 'month', 'month_name')
                ->orderBy('month')
                ->get();

            $deathBarangays = $deathMonthlyRaw->pluck('barangay')->unique()->filter()->values();

            $deathBarangayMonthlyDatasets = $deathBarangays->map(function ($barangay) use ($deathMonthlyRaw, $months) {
                $data = $months->map(function ($month) use ($barangay, $deathMonthlyRaw) {
                    return $deathMonthlyRaw
                        ->firstWhere(fn($item) => $item->barangay === $barangay && $item->month_name === $month)?->count ?? 0;
                });

                return [
                    'label' => $barangay,
                    'data' => $data->values(),
                    'fill' => false,
                    'borderColor' => '#' . substr(md5('death' . $barangay), 0, 6),
                ];
            })->values();


            // Return to Inertia
            return Inertia::render('Dashboard', [
                'chartData' => [
                    'barangay' => [
                        'labels' => $barangayData->pluck('barangay')->values(),
                        'data' => $barangayData->pluck('count')->values(),
                    ],
                    'rhuHealthCard' => [
                        'labels' => $rhuData->pluck('rhu')->values(),
                        'data' => $rhuData->pluck('count')->values(),
                    ],
                    'healthCards' => [
                        'labels' => $monthlyData->pluck('month_name')->values(),
                        'data' => $monthlyData->pluck('count')->values(),
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
                    'deathBarangayMonthly' => [
                        'labels' => $months,
                        'datasets' => $deathBarangayMonthlyDatasets,
                    ],
                    'sanitaryMonthly' => [
                        'labels' => ['JAN', 'FEB', 'MAR', 'APR', 'MAY', 'JUN', 'JUL', 'AUG', 'SEP', 'OCT', 'NOV', 'DEC'],
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
                'availableYears' => $availableYears,
                'totalSanitaryCount' => $totalSanitaryCount,
                'totalSanitaryCounts' => $totalSanitaryCounts,
                'totalDeath' => $totalDeath,
            ]);
        }else{
            return Inertia::render('Guest');
        }
    }

}
