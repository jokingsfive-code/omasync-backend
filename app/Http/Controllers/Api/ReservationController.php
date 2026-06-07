<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Reservation;
use App\Models\HousekeepingTask;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class ReservationController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json(
            Reservation::orderBy('check_in', 'asc')->get()
        );
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'property_id' => 'required|exists:properties,id',
            'guest_name' => 'required|string|max:255',
            'channel' => 'required|string|max:255',
            'check_in' => 'required|date',
            'check_out' => 'required|date|after:check_in',
            'total_price' => 'nullable|numeric',
            'status' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
        ]);

        $overlap = Reservation::where('property_id', $validated['property_id'])
            ->where('status', '!=', 'Cancelled')
            ->where(function ($query) use ($validated) {
                $query->where('check_in', '<', $validated['check_out'])
                    ->where('check_out', '>', $validated['check_in']);
            })
            ->exists();

        if ($overlap) {
            return response()->json([
                'message' => 'This date is fully booked for the selected property.'
            ], 422);
        }

        $reservation = Reservation::create([
            'property_id' => $validated['property_id'],
            'guest_name' => $validated['guest_name'],
            'channel' => $validated['channel'],
            'check_in' => $validated['check_in'],
            'check_out' => $validated['check_out'],
            'total_price' => $validated['total_price'] ?? 0,
            'status' => $validated['status'] ?? 'Confirmed',
            'notes' => $validated['notes'] ?? null,
        ]);

        $this->createHousekeepingTaskIfCheckedOut($reservation);

        return response()->json([
            'message' => 'Reservation created successfully',
            'data' => $reservation
        ], 201);
    }

    public function update(Request $request, $id): JsonResponse
    {
        $reservation = Reservation::find($id);

        if (!$reservation) {
            return response()->json([
                'message' => 'Reservation not found'
            ], 404);
        }

        $validated = $request->validate([
            'property_id' => 'required|exists:properties,id',
            'guest_name' => 'required|string|max:255',
            'channel' => 'required|string|max:255',
            'check_in' => 'required|date',
            'check_out' => 'required|date|after:check_in',
            'total_price' => 'nullable|numeric',
            'status' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
        ]);

        $overlap = Reservation::where('property_id', $validated['property_id'])
            ->where('id', '!=', $id)
            ->where('status', '!=', 'Cancelled')
            ->where(function ($query) use ($validated) {
                $query->where('check_in', '<', $validated['check_out'])
                    ->where('check_out', '>', $validated['check_in']);
            })
            ->exists();

        if ($overlap) {
            return response()->json([
                'message' => 'This date is fully booked for the selected property.'
            ], 422);
        }

        $reservation->update([
            'property_id' => $validated['property_id'],
            'guest_name' => $validated['guest_name'],
            'channel' => $validated['channel'],
            'check_in' => $validated['check_in'],
            'check_out' => $validated['check_out'],
            'total_price' => $validated['total_price'] ?? 0,
            'status' => $validated['status'] ?? 'Confirmed',
            'notes' => $validated['notes'] ?? null,
        ]);

        $this->createHousekeepingTaskIfCheckedOut($reservation);

        return response()->json([
            'message' => 'Reservation updated successfully',
            'data' => $reservation
        ]);
    }

    public function destroy($id): JsonResponse
    {
        $reservation = Reservation::find($id);

        if (!$reservation) {
            return response()->json([
                'message' => 'Reservation not found'
            ], 404);
        }

        HousekeepingTask::where('reservation_id', $reservation->id)->delete();

        $reservation->delete();

        return response()->json([
            'message' => 'Reservation deleted successfully'
        ]);
    }

    private function createHousekeepingTaskIfCheckedOut(Reservation $reservation): void
    {
        if ($reservation->status !== 'Checked Out') {
            return;
        }

        $existingTask = HousekeepingTask::where('reservation_id', $reservation->id)
            ->first();

        if ($existingTask) {
            $existingTask->update([
                'property_id' => $reservation->property_id,
                'guest_name' => $reservation->guest_name,
                'checkout_date' => $reservation->check_out,
            ]);

            return;
        }

        HousekeepingTask::create([
            'reservation_id' => $reservation->id,
            'property_id' => $reservation->property_id,
            'guest_name' => $reservation->guest_name,
            'checkout_date' => $reservation->check_out,
            'status' => 'Pending',
            'notes' => 'Auto-created after guest checkout.',
        ]);
    }
}