<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Reservation;
use Barryvdh\DomPDF\Facade\Pdf;

class InvoiceController extends Controller
{
    public function download($reservationId)
    {
        $reservation = Reservation::with('property')->find($reservationId);

        if (!$reservation) {
            return response()->json([
                'message' => 'Reservation not found'
            ], 404);
        }

        $invoiceNumber = 'INV-' . now()->format('Y') . '-' . str_pad($reservation->id, 5, '0', STR_PAD_LEFT);

        $pdf = Pdf::loadView('invoices.reservation', [
            'reservation' => $reservation,
            'invoiceNumber' => $invoiceNumber,
        ]);

        return $pdf->download($invoiceNumber . '.pdf');
    }
}