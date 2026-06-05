<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ReservationRequest;
use App\Services\ReservationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ReservationController extends Controller
{
    protected ReservationService $reservationService;

    public function __construct(ReservationService $reservationService)
    {
        $this->reservationService = $reservationService;
    }

    public function index(Request $request): JsonResponse
    {
        $reservations = $this->reservationService->getAllReservations($request->all());
        return response()->json($reservations);
    }

    public function store(ReservationRequest $request): JsonResponse
    {
        $reservation = $this->reservationService->createReservation($request->validated());
        return response()->json($reservation, 201);
    }

    public function show(int $id): JsonResponse
    {
        $reservation = $this->reservationService->getReservationById($id);
        return response()->json($reservation);
    }

    public function update(ReservationRequest $request, int $id): JsonResponse
    {
        $reservation = $this->reservationService->updateReservation($id, $request->validated());
        return response()->json($reservation);
    }

    public function destroy(int $id): JsonResponse
    {
        $this->reservationService->deleteReservation($id);
        return response()->json(null, 204);
    }

    public function confirm(int $id): JsonResponse
    {
        $reservation = $this->reservationService->confirmReservation($id);
        return response()->json($reservation);
    }

    public function cancel(int $id, Request $request): JsonResponse
    {
        $reservation = $this->reservationService->cancelReservation($id, $request->input('reason'));
        return response()->json($reservation);
    }
}
