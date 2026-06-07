<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\MaintenanceTicket;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class MaintenanceTicketController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json(
            MaintenanceTicket::with('property')
                ->latest()
                ->get()
        );
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'property_id' => 'nullable|exists:properties,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'priority' => 'nullable|string',
            'status' => 'nullable|string',
            'reported_by' => 'nullable|string|max:255',
            'assigned_to' => 'nullable|string|max:255',
            'reported_date' => 'nullable|date',
            'completed_date' => 'nullable|date',
            'cost' => 'nullable|numeric',
        ]);

        $ticket = MaintenanceTicket::create([
            'property_id' => $validated['property_id'] ?? null,
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'priority' => $validated['priority'] ?? 'Medium',
            'status' => $validated['status'] ?? 'Open',
            'reported_by' => $validated['reported_by'] ?? null,
            'assigned_to' => $validated['assigned_to'] ?? null,
            'reported_date' => $validated['reported_date'] ?? now()->toDateString(),
            'completed_date' => $validated['completed_date'] ?? null,
            'cost' => $validated['cost'] ?? 0,
        ]);

        return response()->json([
            'message' => 'Maintenance ticket created successfully',
            'data' => $ticket->load('property'),
        ], 201);
    }

    public function update(Request $request, $id): JsonResponse
    {
        $ticket = MaintenanceTicket::find($id);

        if (!$ticket) {
            return response()->json([
                'message' => 'Maintenance ticket not found',
            ], 404);
        }

        $validated = $request->validate([
            'property_id' => 'nullable|exists:properties,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'priority' => 'nullable|string',
            'status' => 'nullable|string',
            'reported_by' => 'nullable|string|max:255',
            'assigned_to' => 'nullable|string|max:255',
            'reported_date' => 'nullable|date',
            'completed_date' => 'nullable|date',
            'cost' => 'nullable|numeric',
        ]);

        $ticket->update($validated);

        return response()->json([
            'message' => 'Maintenance ticket updated successfully',
            'data' => $ticket->fresh()->load('property'),
        ]);
    }

    public function destroy($id): JsonResponse
    {
        $ticket = MaintenanceTicket::find($id);

        if (!$ticket) {
            return response()->json([
                'message' => 'Maintenance ticket not found',
            ], 404);
        }

        $ticket->delete();

        return response()->json([
            'message' => 'Maintenance ticket deleted successfully',
        ]);
    }
}