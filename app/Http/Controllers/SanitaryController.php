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
        // Default to current year if no year is selected
        $selectedYear = $request->query('renewal_year', now()->year);
        $selectedQuarter = $request->query('quarter');
    
        // 1. Fetch Paginated Permits
        $sanitaryPermits = Sanitary::query()
            ->when($searchTerm, function ($query, $searchTerm) {
                $query->where(function ($q) use ($searchTerm) {
                    $q->where('name_of_establishment', 'like', "%{$searchTerm}%")
                      ->orWhere('name_of_owner', 'like', "%{$searchTerm}%")
                      ->orWhere('barangay', 'like', "%{$searchTerm}%")
                      ->orWhere('permit_code', 'like', "%{$searchTerm}%");
                });
            })
            ->when($request->filled('name_of_establishment'), fn($q) => $q->where('name_of_establishment', 'like', "%{$request->name_of_establishment}%"))
            ->when($request->filled('name_of_owner'), fn($q) => $q->where('name_of_owner', 'like', "%{$request->name_of_owner}%"))
            ->when($request->filled('barangay'), fn($q) => $q->where('barangay', 'like', "%{$request->barangay}%"))
            // Filter by the specific year selected
            ->where('renewal_year', $selectedYear) 
            ->when($selectedQuarter, fn($q) => $q->where('quarter', $selectedQuarter))
            ->latest()
            ->paginate(50)
            ->withQueryString();
    
        // 2. Fetch Quarterly Stats for the Selected Year
        $quarters = [1, 2, 3, 4];
        $rawData = Sanitary::selectRaw("
                CASE 
                    WHEN renewed_at IS NULL THEN 'new' 
                    ELSE 'renewed' 
                END as permit_type,
                quarter,
                COUNT(*) as total_count
            ")
            ->where('renewal_year', $selectedYear) // Key change: Match statistics to the selected year
            ->groupBy('permit_type', 'quarter')
            ->get();
    
        $quarterlyData = collect($quarters)->map(function ($q) use ($rawData) {
            $new = $rawData->where('quarter', $q)->where('permit_type', 'new')->first();
            $renewed = $rawData->where('quarter', $q)->where('permit_type', 'renewed')->first();
    
            return [
                'quarter' => $q,
                'new_businesses' => $new ? $new->total_count : 0,
                'renewals' => $renewed ? $renewed->total_count : 0,
            ];
        });
    
        return Inertia::render('Sanitary', [
            'sanitaryPermits' => $sanitaryPermits,
            'quarterlyData' => $quarterlyData,
            'filters' => $request->all(['search', 'renewal_year', 'quarter']) // Pass filters back to Vue
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
