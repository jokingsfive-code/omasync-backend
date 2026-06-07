<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Reservation;
use App\Models\Expense;
use App\Models\Property;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function monthlyPdf(Request $request)
    {
        $month = (int) $request->query('month', now()->month);
        $year = (int) $request->query('year', now()->year);
        $propertyId = $request->query('property_id');

        $reservations = Reservation::where('status', '!=', 'Cancelled')
            ->whereMonth('check_in', $month)
            ->whereYear('check_in', $year)
            ->when($propertyId && $propertyId !== 'all', function ($query) use ($propertyId) {
                $query->where('property_id', $propertyId);
            })
            ->get();

        $expenses = Expense::whereMonth('expense_date', $month)
            ->whereYear('expense_date', $year)
            ->when($propertyId && $propertyId !== 'all', function ($query) use ($propertyId) {
                $query->where('property_id', $propertyId);
            })
            ->get();

        $properties = Property::all();

        $revenue = $reservations->sum('total_price');
        $expenseTotal = $expenses->sum('amount');
        $netProfit = $revenue - $expenseTotal;

        $bookingCount = $reservations->count();
        $adr = $bookingCount > 0 ? $revenue / $bookingCount : 0;

        $daysInMonth = date('t', strtotime("$year-$month-01"));
        $propertyCount = $propertyId && $propertyId !== 'all' ? 1 : max($properties->count(), 1);
        $availableNights = $daysInMonth * $propertyCount;

        $bookedNights = $reservations->sum(function ($reservation) {
            return max(
                0,
                strtotime($reservation->check_out) - strtotime($reservation->check_in)
            ) / 86400;
        });

        $occupancy = $availableNights > 0 ? ($bookedNights / $availableNights) * 100 : 0;
        $revpar = $availableNights > 0 ? $revenue / $availableNights : 0;

        $pdf = Pdf::loadView('reports.monthly', [
            'month' => $month,
            'year' => $year,
            'reservations' => $reservations,
            'expenses' => $expenses,
            'properties' => $properties,
            'revenue' => $revenue,
            'expenseTotal' => $expenseTotal,
            'netProfit' => $netProfit,
            'bookingCount' => $bookingCount,
            'adr' => $adr,
            'occupancy' => $occupancy,
            'revpar' => $revpar,
        ]);

        return $pdf->download("omasync-report-$month-$year.pdf");
    }
}