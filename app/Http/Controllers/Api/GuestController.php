<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\GuestRequest;
use App\Services\GuestService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class GuestController extends Controller
{
    protected GuestService $guestService;

    public function __construct(GuestService $guestService)
    {
        $this->guestService = $guestService;
    }

    public function index(Request $request): JsonResponse
    {
        $guests = $this->guestService->getAllGuests($request->all());
        return response()->json($guests);
    }

    public function store(GuestRequest $request): JsonResponse
    {
        $guest = $this->guestService->createGuest($request->validated());
        return response()->json($guest, 201);
    }

    public function show(int $id): JsonResponse
    {
        $guest = $this->guestService->getGuestById($id);
        return response()->json($guest);
    }

    public function update(GuestRequest $request, int $id): JsonResponse
    {
        $guest = $this->guestService->updateGuest($id, $request->validated());
        return response()->json($guest);
    }

    public function destroy(int $id): JsonResponse
    {
        $this->guestService->deleteGuest($id);
        return response()->json(null, 204);
    }
}
