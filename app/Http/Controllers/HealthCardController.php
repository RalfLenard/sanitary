<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Validator;
use App\Models\HealthCard;
use Spatie\Browsershot\Browsershot;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\Response;
use Carbon\Carbon;
use Illuminate\Validation\Rule;



class HealthCardController extends Controller
{
    public function healthCard(Request $request)
    {
        $search = $request->input('search');
        $category = $request->input('category', 'all');
        $sort = $request->input('sort', 'created-desc');

        $healthCards = HealthCard::select(
            'id',
            'full_name',
            'health_card_type',
            'age',
            'gender',
            'place_of_employment',
            'designation',
            'date_of_issuance',
            'date_of_expiration',
            'print_code',
            'inspector_name',
            'barangay',
            'created_at',
            'rhu',
            'confirmed'
        )
            ->when($search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('full_name', 'like', "%{$search}%")
                        ->orWhere('health_card_type', 'like', "%{$search}%")
                        ->orWhere('place_of_employment', 'like', "%{$search}%")
                        ->orWhere('barangay', 'like', "%{$search}%")
                        ->orWhere('inspector_name', 'like', "%{$search}%")
                        ->orWhere('designation', 'like', "%{$search}%");
                });
            })
            ->when($category && $category !== 'all', function ($query) use ($category) {
                if (strtoupper($category) === 'NOT_PRINTED') {
                    // For unprinted cards, check if 'confirmed' is false or NULL
                    $query->where(function ($q) {
                        $q->where('confirmed', false) // false represents not printed
                          ->orWhereNull('confirmed');  // Handle NULL values where the status is not set
                    });
                } else {
                    // Filter by health_card_type if category is other than "Not Printed"
                    $query->where('health_card_type', $category);
                }
            })
            
            ->orderBy($this->getSortColumn($sort), $this->getSortDirection($sort))
            ->paginate(50)
            ->appends([
                'search' => $search,
                'category' => $category,
                'sort' => $sort,
            ]);

        return Inertia::render('HealthCard', [
            'healthCards' => $healthCards->items(),
            'pagination' => $healthCards,
            'filters' => [
                'search' => $search,
                'category' => $category,
                'sort' => $sort,
            ],
            'routes' => [
                'generate_pdf' => route('generate_pdf'),
            ]
        ]);
    }



    private function getSortColumn($sort)
    {
        switch ($sort) {
            case 'name-asc':
            case 'name-desc':
                return 'full_name';
            case 'date-asc':
            case 'date-desc':
                return 'date_of_issuance';
            case 'expiry-asc':
            case 'expiry-desc':
                return 'date_of_expiration';
            case 'created-asc':
            case 'created-desc':
                return 'created_at';
            default:
                return 'created_at'; // Default sorting column is created_at
        }
    }

    private function getSortDirection($sort)
    {
        return in_array($sort, ['name-asc', 'date-asc', 'expiry-asc', 'created-asc']) ? 'asc' : 'desc';
    }




    public function newHealthCard(Request $request)
    {
        $request->validate([
            'full_name' => 'required|max:255',
            'health_card_type' => 'required|max:255',
            'age' => 'max:255',
            'gender' => 'nullable|max:15',
            'place_of_employment' => 'required|max:255',
            'designation' => 'max:255',
            'date_of_issuance' => 'required|date',
            'barangay' => 'nullable|max:255',
            'inspector_name' => 'nullable|max:255',
            'rhu' => 'nullable|max:255',
        ]);

        // Generate Print Code
        $printCode = HealthCard::generatePrintCode();

        // Calculate Expiration Date
        $issuanceDate = Carbon::parse($request->date_of_issuance);
        $expirationDate = null;

        if (strtolower($request->health_card_type) === 'non_food') {
            $expirationDate = $issuanceDate->copy()->addMonths(12);
        } elseif (strtolower($request->health_card_type) === 'food') {
            $expirationDate = $issuanceDate->copy()->addMonths(6);
        } elseif ($request->health_card_type === 'others') {
            $issueDate = Carbon::parse($request->date_of_issuance);
        
            if ($issueDate->month >= 11) {
                // If issue date is in November or December → next year
                $expirationDate = $issueDate->copy()->addYear()->endOfYear();
            } else {
                // Otherwise → current year
                $expirationDate = $issueDate->copy()->endOfYear();
            }
        }
        

        // Create Health Card with all fields uppercased
        $healthCard = HealthCard::create([
            'full_name' => strtoupper($request->full_name),
            'health_card_type' => $request->health_card_type,
            'age' => $request->age,
            'gender' => $request->gender,
            'place_of_employment' => strtoupper($request->place_of_employment),
            'designation' => strtoupper($request->designation ?? ''),
            'date_of_issuance' => $issuanceDate,
            'date_of_expiration' => $expirationDate,
            'print_code' => strtoupper($printCode),
            'barangay' => strtoupper($request->barangay ?? ''),
            'inspector_name' => strtoupper($request->inspector_name ?? ''),
            'rhu' => strtoupper($request->rhu ?? ''),
        ]);

        return redirect()->back()->with('success', 'Health card added successfully');
    }


    public function updateHealthCard(Request $request, $id)
    {
        $request->validate([
            'full_name' => 'required|max:255',
            'health_card_type' => 'required|max:255|in:food,non_food,others',
            'age' => 'nullable|max:255',
            'gender' => 'nullable|max:15',
            'place_of_employment' => 'nullable|max:255',
            'designation' => 'nullable|max:255',
            'date_of_issuance' => 'required|date',
            'barangay' => 'nullable|max:255',
            'inspector_name' => 'nullable|max:255',
            'rhu' => 'nullable|max:255',
        ]);

        $issuanceDate = Carbon::parse($request->date_of_issuance);
        $expirationDate = null;

        // Determine expiration date based on health card type
        if ($request->health_card_type === 'non_food') {
            $expirationDate = $issuanceDate->copy()->addMonths(12); // 12 months from issuance
        } elseif ($request->health_card_type === 'food') {
            $expirationDate = $issuanceDate->copy()->addMonths(6); // 6 months from issuance
        } elseif ($request->health_card_type === 'others') {
            $issueDate = Carbon::parse($request->date_of_issuance);
        
            if ($issueDate->month >= 11) {
                // If issue date is in November or December → next year
                $expirationDate = $issueDate->copy()->addYear()->endOfYear();
            } else {
                // Otherwise → current year
                $expirationDate = $issueDate->copy()->endOfYear();
            }
        }

        try {
            // Find health card or throw ModelNotFoundException if not found
            $health = HealthCard::findOrFail($id);

            // Update the health card
            $health->update([
                'full_name' => strtoupper($request->full_name),
                'health_card_type' => ($request->health_card_type),
                'age' => $request->age,
                'gender' => $request->gender,
                'place_of_employment' => strtoupper($request->place_of_employment ?? ''),
                'designation' => strtoupper($request->designation ?? ''),
                'date_of_issuance' => $issuanceDate,
                'date_of_expiration' => $expirationDate,
                'barangay' => strtoupper($request->barangay ?? ''),
                'inspector_name' => strtoupper($request->inspector_name ?? ''),
                'rhu' => strtoupper($request->rhu ?? ''),
            ]);

            session()->flash('success', 'Health Card updated successfully');
            return redirect()->back();

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return redirect()->back()
                ->withInput()
                ->withErrors(['error' => 'Health card not found. Please try again.']);
        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->withErrors(['error' => 'Failed to update health card. Please try again.']);
        }
    }


    public function generatePdf(Request $request)
    {
        $selectedIds = explode(',', $request->query('selected_health_cards'));

        if (empty($selectedIds)) {
            return back()->withErrors(['error' => 'No health cards selected for PDF generation.']);
        }

        // Fetch selected health cards
        $healthCards = HealthCard::whereIn('id', $selectedIds)->get();

        // ✅ Correct way to update all models
        foreach ($healthCards as $card) {
            $card->confirmed = true;
            $card->save();
        }

        // Convert Blade view to HTML
        $html = view('printHealth', compact('healthCards'))->render();

        // Generate PDF using Browsershot and stream it directly
        $pdfContent = Browsershot::html($html)
            ->setChromePath('C:\Program Files\Google\Chrome\Application\chrome.exe')
            ->addChromiumArguments(['--no-sandbox', '--disable-gpu'])
            ->format('A4')
            ->orientation('landscape')
            ->pdf();


        // Stream the PDF directly to the browser
        return response($pdfContent, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="health_cards.pdf"',
        ]);
    }


    public function deleteHealth($id)
    {
        try {
            $healthCard = HealthCard::findOrFail($id);
            $healthCard->delete();

            return redirect()->back()->with('success', 'Health card deleted successfully.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to delete health card.');
        }
    }





}