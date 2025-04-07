<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\HealthCard;
use Inertia\Inertia;
use Illuminate\Support\Facades\DB;
class DashboardController extends Controller
{

public function index(Request $request)
{
    // Group by Barangay
    $barangayData = HealthCard::select('barangay')
        ->groupBy('barangay')
        ->selectRaw('barangay, COUNT(*) as count')
        ->orderBy('barangay', 'asc')
        ->get();

    // Group by RHU
    $rhuData = HealthCard::select('rhu')
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

    // Dynamic available years
    $availableYears = HealthCard::selectRaw('YEAR(created_at) as year')
        ->distinct()
        ->orderBy('year', 'asc')
        ->pluck('year');

    // Extract category counts
    $foodCount = $cardTypeData->firstWhere('health_card_type', 'food')?->count ?? 0;
    $nonFoodCount = $cardTypeData->firstWhere('health_card_type', 'non_food')?->count ?? 0;
    $otherCount = $cardTypeData->firstWhere('health_card_type', 'others')?->count ?? 0;

    return Inertia::render('Dashboard', [
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
        ],
        'availableYears' => $availableYears,
    ]);
}



}
