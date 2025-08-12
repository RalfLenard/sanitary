<?php

namespace App\Http\Controllers;

use App\Models\HealthCard;
use Spatie\LaravelPdf\Facades\Pdf;
use App\Models\Sanitary;
use App\Models\PrintCode;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Spatie\Browsershot\Browsershot;

use Illuminate\Support\Facades\View;


class PrintController extends Controller
{
    public function print(Request $request, $id)
    {
        $inspection = Sanitary::findOrFail($id);
        $today = Carbon::now()->format('F d, Y');
        $lastDayOfYear = Carbon::now()->endOfYear()->format('F d, Y');
        $currentYear = Carbon::now()->year;
    
        $inspection->confirmed = true;
        $inspection->save();
    
        if (!empty($inspection->permit_code)) {
            $permitCode = $inspection->permit_code;
        } else {
            $existingPermit = PrintCode::where('permit_code', $inspection->permit_code)->first();
    
            if ($existingPermit) {
                $permitCode = $existingPermit->permit_code;
            } else {
                $lastPermit = PrintCode::where('year', $currentYear)->orderBy('sequence', 'desc')->first();
                $sequence = $lastPermit ? $lastPermit->sequence + 1 : 1;
                $formattedSequence = str_pad($sequence, 4, '0', STR_PAD_LEFT);
                $permitCode = "SP-{$formattedSequence}";
    
                $newPermit = PrintCode::create([
                    'permit_code' => $permitCode,
                    'year' => $currentYear,
                    'sequence' => $sequence
                ]);
    
                $inspection->permit_code = $newPermit->permit_code;
                $inspection->save();
            }
        }
    
        // ✅ Render the view as HTML
        $html = view('print', compact('inspection', 'today', 'lastDayOfYear', 'permitCode'))->render();
    
        // ✅ Generate the PDF using Browsershot
        $pdfContent = Browsershot::html($html)
            ->setChromePath('C:\Program Files\Google\Chrome\Application\chrome.exe')
            ->addChromiumArguments(['--no-sandbox', '--disable-gpu'])
            ->format('A4')
            ->pdf();
    
        return response($pdfContent, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="sanitary-inspection.pdf"',
        ]);
    }

    public function reportRhu(Request $request)
    {
        // Validate input
        $validated = $request->validate([
            'rhu' => 'required|string',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);
    
        $rhu = $validated['rhu'];
        $startDate = $validated['start_date'];
        $endDate = $validated['end_date'];
    
        // Get filtered data
        $records = HealthCard::where('rhu', $rhu)
            ->whereBetween('date_of_issuance', [$startDate, $endDate])
            ->orderBy('date_of_issuance', 'asc')
            ->get();
    
        // Render Blade view
        $html = View::make('rhuPRint', [
            'records' => $records,
            'rhu' => $rhu,
            'startDate' => $startDate,
            'endDate' => $endDate,
        ])->render();
    
        // Path for PDF
        $pdfPath = storage_path("app/public/report_rhu.pdf");
    
        // Generate PDF with Browsershot
        Browsershot::html($html)
            ->format('A4')
            ->landscape()
            ->showBackground()
            ->save($pdfPath);
    
        // Open PDF in browser instead of download
        return response()->file($pdfPath, [
            'Content-Type' => 'application/pdf',
        ]);
    }
}
