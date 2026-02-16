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
        // Default to current year if no year is selected in filters
        $selectedYear = $request->query('renewal_year', now()->year);
        $selectedQuarter = $request->query('quarter');
    
        // 1. Fetch Paginated Permits
        $sanitaryPermits = Sanitary::query()
            ->when($searchTerm, function ($query, $searchTerm) {
                $query->where(function ($q) use ($searchTerm) {
                    $q->where('name_of_establishment', 'like', "%{$searchTerm}%")
                      ->orWhere('name_of_owner', 'like', "%{$searchTerm}%")
                      ->orWhere('contact_number', 'like', "%{$searchTerm}%")
                      ->orWhere('barangay', 'like', "%{$searchTerm}%")
                      ->orWhere('line_of_business', 'like', "%{$searchTerm}%")
                      ->orWhere('inspector_name', 'like', "%{$searchTerm}%")
                      ->orWhere('sanitary_code', 'like', "%{$searchTerm}%");
                });
            })
            ->when($request->query('name_of_establishment'), fn($q, $name) => $q->where('name_of_establishment', 'like', "%{$name}%"))
            ->when($request->query('name_of_owner'), fn($q, $owner) => $q->where('name_of_owner', 'like', "%{$owner}%"))
            ->when($request->query('barangay'), fn($q, $barangay) => $q->where('barangay', 'like', "%{$barangay}%"))
            ->when($request->query('sanitary_code'), fn($q, $code) => $q->where('sanitary_code', 'like', "%{$code}%"))
            // Apply the Year filter strictly
            ->where('renewal_year', $selectedYear)
            ->when($selectedQuarter, fn($q, $quarter) => $q->where('quarter', $quarter))
            ->latest()
            ->paginate(50)
            ->appends($request->query());
    
        // 2. Fetch Quarterly Stats based on STATUS
        $quarters = [1, 2, 3, 4];
        
        $query = Sanitary::selectRaw("
                CASE 
                    WHEN status = 'New' THEN 'new' 
                    ELSE 'renewed' 
                END as permit_category,
                quarter,
                COUNT(*) as total_count
            ")
            ->where('renewal_year', $selectedYear) 
            ->groupBy('permit_category', 'quarter');
    
        if ($selectedQuarter) {
            $query->where('quarter', $selectedQuarter);
        }
    
        $rawData = $query->get();
    
        $quarterlyData = collect($quarters)->map(function ($q) use ($rawData) {
            // Filter from rawData based on our new CASE category
            $new = $rawData->where('quarter', $q)->where('permit_category', 'new')->first();
            $renewed = $rawData->where('quarter', $q)->where('permit_category', 'renewed')->first();
    
            return [
                'quarter' => $q,
                'new_businesses' => $new ? $new->total_count : 0,
                'renewals' => $renewed ? $renewed->total_count : 0,
            ];
        });
    
        return Inertia::render('Sanitary', [
            'sanitaryPermits' => $sanitaryPermits,
            'quarterlyData' => $quarterlyData,
            'filters' => $request->all(['search', 'renewal_year', 'quarter', 'name_of_establishment', 'name_of_owner', 'barangay', 'sanitary_code'])
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
    
        $now = Carbon::now();
        $currentYear = $now->year;
        $currentQuarter = ceil($now->month / 3);
    
        // Prevent duplicate renewal for same quarter/year
        if ($sanitary->renewal_year == $currentYear && $sanitary->quarter == $currentQuarter) {
            return redirect()->route('sanitary')->with('error', "This permit has already been renewed for Q{$currentQuarter}.");
        }
    
        // Update fields
        $sanitary->renewal_year = $currentYear;
        $sanitary->quarter      = $currentQuarter;
        $sanitary->renewed_at   = now();
        $sanitary->status       = 'renewed';
        $sanitary->confirmed    = false;
    
        // ✅ Clear the sanitary/permit code upon renewal
        // This ensures they must be issued a new code after re-inspection
        $sanitary->sanitary_code = null; 
        
        $sanitary->save();
    
        return redirect()->route('sanitary')->with('success', "Permit successfully renewed for Q{$currentQuarter}. The old permit code has been cleared.");
    }

    public function deleteSanitary($id)
    {
        try {
            $sanitary = Sanitary::findOrFail($id);
            $sanitary->delete();

            return redirect()->route('sanitary')->with('success', 'Sanitary Permit deleted successfully.');
        } catch (\Exception $e) {
            return redirect()->route('sanitary')->with('error', 'Failed to delete Sanitary Permit.');
        }
    }


}
