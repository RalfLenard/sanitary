<?php

namespace App\Http\Controllers;

use App\Models\Death;
use Illuminate\Http\Request;
use Spatie\Browsershot\Browsershot;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\Response;
use Carbon\Carbon;
use Inertia\Inertia;
use Illuminate\Support\Facades\Validator;

class DeathController extends Controller
{public function death(Request $request)
    {
        $deaths = Death::latest()->get(); // or use pagination: ->paginate(10)
    
        return Inertia::render('Death', [
            'deaths' => $deaths
        ]);
    }
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'province' => 'nullable|string',
            'municipality' => 'nullable|string',
            'first_name' => 'nullable|string',
            'middle_name' => 'nullable|string',
            'last_name' => 'nullable|string',
            'sex' => 'nullable|string',
            'date_of_death' => 'nullable|date',
            'birthdate' => 'nullable|date',
            'age' => 'nullable|string',
            'place_of_death' => 'nullable|string',
            'civil_status' => 'nullable|string',
            'religion' => 'nullable|string',
            'citizenship' => 'nullable|string',
            'residence' => 'nullable|string',
            'occupation' => 'nullable|string',
            'address' => 'nullable|string',
            'cause_of_death_a' => 'nullable|string',
            'cause_of_death_b' => 'nullable|string',
            'cause_of_death_c' => 'nullable|string',
            'doctor' => 'nullable|string',
            'reviewed_by' => 'nullable|string',
            'informant_name' => 'nullable|string',
            'relationship' => 'nullable|string',
            'informant_address' => 'nullable|string',
            'prepared_by_name' => 'nullable|string',
            'prepared_by_position' => 'nullable|string',
            'remarks' => 'nullable|string',
            'date' => 'nullable|date',
            'doctor_position' => 'nullable|string',
            'reviewed_position' => 'nullable|string',
            'name_of_father' => 'nullable|string',
            'name_of_mother' => 'nullable|string',
            'gender' => 'nullable|string',

        ]);

        Death::create($validatedData);


        return redirect()->back()->with('success', 'Death record created successfully');
    }

    public function generatePdf(Request $request, $id)
    {
        // If you need to fetch multiple death records, you can pass a list of IDs
        $selectedIds = explode(',', $id);  // In case you pass multiple IDs as a comma-separated string
    
        // Fetch death records based on selected IDs
        $deaths = Death::whereIn('id', $selectedIds)->get();
    
        // Define PDF Path
        $pdfPath = storage_path('app/public/death_records.pdf');
    
        // Generate PDF using Browsershot (Legal size: 8.5x13 inches)
        $html = view('printDeath', compact('deaths'))->render();
    
        Browsershot::html($html)
            ->ignoreHttpsErrors()
            ->format('LEGAL') // Set correct dimensions
            ->showBackground()
            ->savePdf($pdfPath);
    
        // Return PDF inline
        return response()->file($pdfPath, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="death_records.pdf"',
        ]);
    }
    

    public function update(Request $request, Death $death)
    {
        // Validate input data
        $validatedData = $request->validate([
            'province' => 'nullable|string',
            'municipality' => 'nullable|string',
            'first_name' => 'nullable|string',
            'middle_name' => 'nullable|string',
            'last_name' => 'nullable|string',
            'sex' => 'nullable|string',
            'date_of_death' => 'nullable|date',
            'birthdate' => 'nullable|date',
            'age' => 'nullable|string',
            'place_of_death' => 'nullable|string',
            'civil_status' => 'nullable|string',
            'religion' => 'nullable|string',
            'citizenship' => 'nullable|string',
            'residence' => 'nullable|string',
            'occupation' => 'nullable|string',
            'address' => 'nullable|string',
            'cause_of_death_a' => 'nullable|string',
            'cause_of_death_b' => 'nullable|string',
            'cause_of_death_c' => 'nullable|string',
            'doctor' => 'nullable|string',
            'reviewed_by' => 'nullable|string',
            'informant_name' => 'nullable|string',
            'relationship' => 'nullable|string',
            'informant_address' => 'nullable|string',
            'prepared_by_name' => 'nullable|string',
            'prepared_by_position' => 'nullable|string',
            'remarks' => 'nullable|string',
            'date' => 'nullable|date',
            'doctor_position' => 'nullable|string',
            'reviewed_position' => 'nullable|string',
            'name_of_father' => 'nullable|string',
            'name_of_mother' => 'nullable|string',
            'gender' => 'nullable|string',
        ]);
    
        // Update the death record
        $death->update($validatedData);
    
        // Redirect back with a success message
        return redirect()->back()->with('success', 'Death record updated successfully');
    }

    public function delete($id)
    {
        $death = Death::findOrFail($id);
        $death->delete();

        return response()->json(['message' => 'Death record deleted successfully']);
    }


    
}