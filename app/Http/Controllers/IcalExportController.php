<?php

namespace App\Http\Controllers;

use App\Models\Reservation;
use Illuminate\Http\Response;

class IcalExportController extends Controller
{
    public function export($propertyId)
    {
        $reservations = Reservation::where('property_id', $propertyId)
    ->whereNotNull('check_in')
    ->whereNotNull('check_out')
    ->orderBy('check_in')
    ->get();

        $calendar = "BEGIN:VCALENDAR\r\n";
        $calendar .= "VERSION:2.0\r\n";
        $calendar .= "PRODID:-//OMASYNC//Calendar Export//EN\r\n";
        $calendar .= "CALSCALE:GREGORIAN\r\n";
        $calendar .= "METHOD:PUBLISH\r\n";

        foreach ($reservations as $reservation) {
            $uid = "omasync-reservation-" . $reservation->id . "@omasync.com";

            $summary = $this->escapeText(
                $reservation->guest_name
                    ? "Reserved - " . $reservation->guest_name
                    : "Reserved"
            );

            $description = $this->escapeText("Exported from OMASYNC");

            $dtStart = date('Ymd', strtotime($reservation->check_in));
            $dtEnd = date('Ymd', strtotime($reservation->check_out));
            $dtStamp = gmdate('Ymd\THis\Z');

            $calendar .= "BEGIN:VEVENT\r\n";
            $calendar .= "UID:{$uid}\r\n";
            $calendar .= "DTSTAMP:{$dtStamp}\r\n";
            $calendar .= "DTSTART;VALUE=DATE:{$dtStart}\r\n";
            $calendar .= "DTEND;VALUE=DATE:{$dtEnd}\r\n";
            $calendar .= "SUMMARY:{$summary}\r\n";
            $calendar .= "DESCRIPTION:{$description}\r\n";
            $calendar .= "END:VEVENT\r\n";
        }

        $calendar .= "END:VCALENDAR\r\n";

        return response($calendar, 200)
            ->header('Content-Type', 'text/calendar; charset=utf-8')
            ->header('Content-Disposition', 'inline; filename="omasync-property-' . $propertyId . '.ics"');
    }

    private function escapeText($text)
    {
        return str_replace(
            ["\\", ";", ",", "\n", "\r"],
            ["\\\\", "\;", "\,", "\\n", ""],
            $text
        );
    }
}