<?php

namespace App\Http\Controllers;
use Inertia\Inertia;
use Illuminate\Http\Request;
use App\Models\Sanitary;
use Illuminate\Support\Facades\Validator;
use Inertia\Response;

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
            'renewal_year' => 'required|digits:4', // Ensure it's a valid year
        ]);

        // Create the sanitary permit
        Sanitary::create($validated);

        return redirect()->route('sanitary')->with('success', 'Permit successfully created');
    }

    public function sanitary(): Response
    {
        $sanitaryPermits = Sanitary::latest()->get(); // Fetch all permits

        return Inertia::render('Sanitary', [
            'sanitaryPermits' => $sanitaryPermits
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
        ]);

        // Find the existing sanitary permit record
        $sanitary = Sanitary::findOrFail($id);

        // Update the permit
        $sanitary->update($validated);

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
        // Find the permit record by ID
        $sanitary = Sanitary::findOrFail($id);
        $currentYear = date('Y');

        // Check if the permit has already been renewed this year
        if ($sanitary->renewal_year == $currentYear) {
            return back()->with('warning', 'This permit has already been renewed for the current year.');
        }

        // Update the renewal year to the current year
        $sanitary->update([
            'renewal_year' => $currentYear,
            'renewed_at' => now(),
            'status' => 'Renewed',
            'confirmed' => false,
        ]);

        return back()->with('success', 'Permit successfully renewed!');
    }




}
