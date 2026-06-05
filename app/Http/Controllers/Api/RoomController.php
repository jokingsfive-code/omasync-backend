<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\RoomRequest;
use App\Services\RoomService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class RoomController extends Controller
{
    protected RoomService $roomService;

    public function __construct(RoomService $roomService)
    {
        $this->roomService = $roomService;
    }

    public function index(Request $request): JsonResponse
    {
        $rooms = $this->roomService->getAllRooms($request->all());
        return response()->json($rooms);
    }

    public function store(RoomRequest $request): JsonResponse
    {
        $room = $this->roomService->createRoom($request->validated());
        return response()->json($room, 201);
    }

    public function show(int $id): JsonResponse
    {
        $room = $this->roomService->getRoomById($id);
        return response()->json($room);
    }

    public function update(RoomRequest $request, int $id): JsonResponse
    {
        $room = $this->roomService->updateRoom($id, $request->validated());
        return response()->json($room);
    }

    public function destroy(int $id): JsonResponse
    {
        $this->roomService->deleteRoom($id);
        return response()->json(null, 204);
    }
}
