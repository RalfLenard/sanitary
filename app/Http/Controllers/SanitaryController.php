<?php

namespace App\Http\Controllers;
use Inertia\Inertia;
use Illuminate\Http\Request;
use App\Models\Sanitary;
use Illuminate\Support\Facades\Validator;
use Inertia\Response;
use Carbon\Carbon;

class SanitaryController extends Controller
{
    public function newPermit(Request $request)
    {
        $validated = $request->validate([
            'name_of_establishment' => 'required|max:255',
            'name_of_owner' => 'required|max:255',
            'contact_number' => 'nullable|max:15',
            'barangay' => 'required|max:255',
            'line_of_business' => 'required|max:255',
            'inspector_name' => 'required|string|max:255',
            'renewal_year' => 'required|digits:4',
            'has_signature' => 'nullable|boolean',  // ✅ Ensure boolean validation
        ]);

        // ✅ Debugging: Log the request value
        \Log::info('Raw has_signature from Vue:', ['has_signature' => $request->has_signature]);

        // ✅ Convert to boolean properly
        $validated['has_signature'] = $request->boolean('has_signature');

        \Log::info('Processed has_signature:', ['has_signature' => $validated['has_signature']]);

        // Auto-assign the quarter based on the current month
        $validated['quarter'] = ceil(Carbon::now()->month / 3);

        // Create the sanitary permit
        Sanitary::create($validated);

        return redirect()->route('sanitary')->with('success', "Permit successfully created for Q{$validated['quarter']}!");
    }


    public function sanitary(Request $request): Response
    {
        $searchTerm = $request->query('search');

        // Fetch Sanitary Permits with Search and Filters + Pagination
        $sanitaryPermits = Sanitary::query()
            ->when($searchTerm, function ($query, $searchTerm) {
                $query->where(function ($q) use ($searchTerm) {
                    $q->where('name_of_establishment', 'like', "%{$searchTerm}%")
                        ->orWhere('name_of_owner', 'like', "%{$searchTerm}%")
                        ->orWhere('contact_number', 'like', "%{$searchTerm}%")
                        ->orWhere('barangay', 'like', "%{$searchTerm}%")
                        ->orWhere('line_of_business', 'like', "%{$searchTerm}%")
                        ->orWhere('inspector_name', 'like', "%{$searchTerm}%")
                        ->orWhere('renewal_year', 'like', "%{$searchTerm}%")
                        ->orWhere('permit_code', 'like', "%{$searchTerm}%");
                });
            })
            ->when($request->query('name_of_establishment'), fn($q, $name) => $q->where('name_of_establishment', 'like', "%{$name}%"))
            ->when($request->query('name_of_owner'), fn($q, $owner) => $q->where('name_of_owner', 'like', "%{$owner}%"))
            ->when($request->query('barangay'), fn($q, $barangay) => $q->where('barangay', 'like', "%{$barangay}%"))
            ->when($request->query('permit_code'), fn($q, $code) => $q->where('permit_code', 'like', "%{$code}%"))
            ->when($request->query('renewal_year'), fn($q, $year) => $q->where('renewal_year', $year))
            ->when($request->query('quarter'), fn($q, $quarter) => $q->where('quarter', $quarter)) // Added Quarter Filtering
            ->latest()
            ->paginate(50) // ✅ Ensure pagination
            ->appends($request->query()); // ✅ Paginate results (50 per page)

        // Get the current year and selected quarter
        $currentYear = now()->year;
        $selectedQuarter = $request->query('quarter');

        // Define quarters (1-4) to ensure all quarters are included
        $quarters = [1, 2, 3, 4];

        // Fetch quarterly data using the `quarter` column
        $query = Sanitary::selectRaw("
        CASE 
            WHEN renewed_at IS NULL THEN 'new' 
            ELSE 'renewed' 
        END as permit_type,
        QUARTER(COALESCE(renewed_at, created_at)) as quarter,
        COUNT(*) as total_count
    ")
            ->whereYear('renewed_at', $currentYear) // Filter by current year
            ->groupByRaw("permit_type, QUARTER(COALESCE(renewed_at, created_at))")
            ->orderByRaw("QUARTER(COALESCE(renewed_at, created_at))");

        // Apply quarter filter if selected
        if ($selectedQuarter) {
            $query->whereRaw("QUARTER(COALESCE(renewed_at, created_at)) = ?", [$selectedQuarter]);
        }

        $rawData = $query->get();

        // Ensure all quarters are included in the result
        $quarterlyData = collect($quarters)->map(function ($q) use ($rawData) {
            $data = $rawData->firstWhere('quarter', $q);

            return [
                'quarter' => $q,
                'new_businesses' => $data && $data->permit_type == 'new' ? $data->total_count : 0,
                'renewals' => $data && $data->permit_type == 'renewed' ? $data->total_count : 0,
            ];
        });

        return Inertia::render('Sanitary', [
            'sanitaryPermits' => $sanitaryPermits, // ✅ Paginated data
            'quarterlyData' => $quarterlyData
        ]);
    }



    public function updatePermit(Request $request, $id)
    {
        // Validate the request data
        $validated = $request->validate([
            'name_of_establishment' => 'required|max:255',
            'name_of_owner' => 'nullable|max:255',
            'contact_number' => 'nullable|max:15',
            'barangay' => 'required|max:255',
            'line_of_business' => 'nullable|max:255',
            'inspector_name' => 'nullable|string|max:255',
            'renewal_year' => 'nullable|digits:4',
            'has_signature' => 'sometimes|boolean', // ✅ Added validation for has_signature
        ]);

        // Find the existing sanitary permit record
        $sanitary = Sanitary::findOrFail($id);

        // Ensure `has_signature` is properly handled (defaults to false if not set)
        $sanitary->update([
            ...$validated,
            'has_signature' => $request->has('has_signature') ? $request->boolean('has_signature') : false,
        ]);

        return redirect()->route('sanitary')->with('success', 'Permit successfully updated');
    }



    public function inspected(Request $request, $id)
    {
        $permit = Sanitary::findOrFail($id);
        $permit->update(['status' => 'Inspected']);

        return redirect()->back()->with('success', 'Permit status updated to Inspected.');
    }


    public function renewalPermit(Request $request, $id)
    {
        $sanitary = Sanitary::findOrFail($id);

        $now = Carbon::now(); // or Carbon::now('Asia/Manila') if you want to ensure timezone
        $currentYear = $now->year;
        $currentQuarter = ceil($now->month / 3); // Q1 to Q4

        // Debug: check current quarter & year
        // dd($now->month, $currentQuarter, $currentYear);

        // Prevent duplicate renewal for same quarter
        if ($sanitary->renewal_year == $currentYear && $sanitary->quarter == $currentQuarter) {
            return back()->with('warning', "This permit has already been renewed for Q{$currentQuarter}.");
        }

        // ✅ Make sure these fields are fillable in the model or use individual assignments
        $sanitary->renewal_year = $currentYear;
        $sanitary->quarter = $currentQuarter;
        $sanitary->renewed_at = now();
        $sanitary->status = 'renewed'; // Match your ENUM exactly (case-sensitive)
        $sanitary->confirmed = false;
        $sanitary->save();

        return back()->with('success', "Permit successfully renewed for Q{$currentQuarter}!");
    }

    public function deleteSanitary($id)
    {
        try {
            $sanitary = Sanitary::findOrFail($id);
            $sanitary->delete();

            return redirect()->back()->with('success', 'Sanitary Permit deleted successfully.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to delete Sanitary Permit.');
        }
    }


}
