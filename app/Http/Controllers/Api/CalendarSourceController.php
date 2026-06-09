<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CalendarSource;
use App\Models\Reservation;
use ICal\ICal;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Carbon\Carbon;

class CalendarSourceController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json(
            CalendarSource::with('property')
                ->orderBy('created_at', 'desc')
                ->get()
        );
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'property_id' => 'required|exists:properties,id',
            'channel' => 'required|string|max:255',
            'ical_url' => 'required|url',
            'is_active' => 'nullable|boolean',
        ]);

        $source = CalendarSource::create([
            'property_id' => $validated['property_id'],
            'channel' => $validated['channel'],
            'ical_url' => $validated['ical_url'],
            'is_active' => $validated['is_active'] ?? true,
        ]);

        return response()->json([
            'message' => 'Calendar source created successfully',
            'data' => $source->load('property'),
        ], 201);
    }

    public function update(Request $request, $id): JsonResponse
    {
        $source = CalendarSource::find($id);

        if (!$source) {
            return response()->json([
                'message' => 'Calendar source not found',
            ], 404);
        }

        $validated = $request->validate([
            'property_id' => 'required|exists:properties,id',
            'channel' => 'required|string|max:255',
            'ical_url' => 'required|url',
            'is_active' => 'nullable|boolean',
        ]);

        $source->update([
            'property_id' => $validated['property_id'],
            'channel' => $validated['channel'],
            'ical_url' => $validated['ical_url'],
            'is_active' => $validated['is_active'] ?? true,
        ]);

        return response()->json([
            'message' => 'Calendar source updated successfully',
            'data' => $source->load('property'),
        ]);
    }

    public function destroy($id): JsonResponse
    {
        $source = CalendarSource::find($id);

        if (!$source) {
            return response()->json([
                'message' => 'Calendar source not found',
            ], 404);
        }

        $source->delete();

        return response()->json([
            'message' => 'Calendar source deleted successfully',
        ]);
    }

    public function syncNow($id): JsonResponse
    {
        $source = CalendarSource::with('property')->find($id);

        if (!$source) {
            return response()->json([
                'message' => 'Calendar source not found',
            ], 404);
        }

        if (!$source->is_active) {
            return response()->json([
                'message' => 'This calendar source is inactive',
            ], 422);
        }

        try {
            $response = Http::timeout(20)
                ->withHeaders([
                    'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0 Safari/537.36',
                    'Accept' => 'text/calendar, text/plain, */*',
                    'Accept-Language' => 'en-US,en;q=0.9',
                ])
                ->get($source->ical_url);

            if (!$response->successful()) {
                return response()->json([
                    'message' => 'Failed to fetch iCal URL',
                    'status' => $response->status(),
                ], 422);
            }

            $icalContent = $response->body();

            $ical = new ICal(false, [
                'defaultSpan' => 2,
                'defaultTimeZone' => 'Asia/Kuala_Lumpur',
                'defaultWeekStart' => 'MO',
                'skipRecurrence' => true,
            ]);

            $ical->initString($icalContent);
            $events = $ical->events();

            $created = 0;
            $skippedExisting = 0;
            $skippedConflict = 0;
            $skippedInvalidDate = 0;
            $imported = [];

            foreach ($events as $event) {
                $checkIn = $this->formatIcalDate($event->dtstart_array[2] ?? $event->dtstart ?? null);
                $checkOut = $this->formatIcalDate($event->dtend_array[2] ?? $event->dtend ?? null);

                if (!$checkIn || !$checkOut) {
                    $skippedInvalidDate++;
                    continue;
                }

                if (strtolower(trim($source->channel)) === 'agoda') {
                    $checkIn = Carbon::parse($checkIn)->subDay()->format('Y-m-d');
                    $checkOut = Carbon::parse($checkOut)->subDay()->format('Y-m-d');
                }

                if ($checkIn < '2000-01-01' || $checkOut < '2000-01-01') {
                    $skippedInvalidDate++;
                    continue;
                }

                if ($checkOut <= $checkIn) {
                    $skippedInvalidDate++;
                    continue;
                }

                $uid = $event->uid ?? md5(($event->summary ?? '') . $checkIn . $checkOut);
                $summary = $event->summary ?? 'Imported Booking';

                $guestName = $this->cleanGuestName($summary, $source->channel);

                $existing = Reservation::where('notes', 'like', '%ICS UID: ' . $uid . '%')
                    ->where('property_id', $source->property_id)
                    ->first();

                if ($existing) {
                    $skippedExisting++;
                    continue;
                }

                $hasConflict = Reservation::where('property_id', $source->property_id)
                    ->where('status', '!=', 'Cancelled')
                    ->where(function ($query) use ($checkIn, $checkOut) {
                        $query->where('check_in', '<', $checkOut)
                            ->where('check_out', '>', $checkIn);
                    })
                    ->exists();

                if ($hasConflict) {
                    $skippedConflict++;
                    continue;
                }

                $reservation = Reservation::create([
                    'property_id' => $source->property_id,
                    'guest_name' => $guestName,
                    'channel' => $source->channel,
                    'check_in' => $checkIn,
                    'check_out' => $checkOut,
                    'total_price' => 0,
                    'status' => 'Confirmed',
                    'notes' => 'Imported from iCal. ICS UID: ' . $uid,
                ]);

                $created++;
                $imported[] = $reservation;
            }

            $source->update([
                'last_synced_at' => now(),
            ]);

            return response()->json([
                'message' => 'Calendar synced successfully',
                'summary' => [
                    'events_found' => count($events),
                    'created' => $created,
                    'skipped_existing' => $skippedExisting,
                    'skipped_conflict' => $skippedConflict,
                    'skipped_invalid_date' => $skippedInvalidDate,
                ],
                'data' => $source->fresh()->load('property'),
                'imported' => $imported,
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'message' => 'Sync failed',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    private function formatIcalDate($value): ?string
    {
        if (!$value) {
            return null;
        }

        try {
            if (is_array($value)) {
                if (isset($value[2])) {
                    return $this->formatIcalDate($value[2]);
                }

                return null;
            }

            if ($value instanceof \DateTimeInterface) {
                return $value->format('Y-m-d');
            }

            $value = trim((string) $value);

            if (preg_match('/^\d{8}$/', $value)) {
                return substr($value, 0, 4) . '-' . substr($value, 4, 2) . '-' . substr($value, 6, 2);
            }

            if (preg_match('/^(\d{8})T\d{6}Z?$/', $value, $matches)) {
                $date = $matches[1];

                return substr($date, 0, 4) . '-' . substr($date, 4, 2) . '-' . substr($date, 6, 2);
            }

            if (is_numeric($value)) {
                $timestamp = (int) $value;

                if ($timestamp > 9999999999) {
                    $timestamp = (int) floor($timestamp / 1000);
                }

                return Carbon::createFromTimestamp($timestamp)->format('Y-m-d');
            }

            return Carbon::parse($value)->format('Y-m-d');
        } catch (\Throwable $e) {
            return null;
        }
    }

    private function cleanGuestName(string $summary, string $channel): string
    {
        $name = trim($summary);

        $removeWords = [
            'Reserved',
            'Reservation',
            'Booked',
            'Booking',
            'Airbnb',
            'Agoda',
            'Booking.com',
            $channel,
        ];

        foreach ($removeWords as $word) {
            $name = str_ireplace($word, '', $name);
        }

        $name = trim(preg_replace('/\s+/', ' ', $name));
        $name = trim($name, '-:|');

        if (!$name) {
            return $channel . ' Guest';
        }

        return Str::limit($name, 80, '');
    }
}