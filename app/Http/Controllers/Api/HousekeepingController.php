<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\HousekeepingRequest;
use App\Services\HousekeepingService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class HousekeepingController extends Controller
{
    protected HousekeepingService $housekeepingService;

    public function __construct(HousekeepingService $housekeepingService)
    {
        $this->housekeepingService = $housekeepingService;
    }

    public function index(Request $request): JsonResponse
    {
        $tasks = $this->housekeepingService->getAllTasks($request->all());
        return response()->json($tasks);
    }

    public function store(HousekeepingRequest $request): JsonResponse
    {
        $task = $this->housekeepingService->createTask($request->validated());
        return response()->json($task, 201);
    }

    public function show(int $id): JsonResponse
    {
        $task = $this->housekeepingService->getTaskById($id);
        return response()->json($task);
    }

    public function update(HousekeepingRequest $request, int $id): JsonResponse
    {
        $task = $this->housekeepingService->updateTask($id, $request->validated());
        return response()->json($task);
    }

    public function destroy(int $id): JsonResponse
    {
        $this->housekeepingService->deleteTask($id);
        return response()->json(null, 204);
    }

    public function start(int $id): JsonResponse
    {
        $task = $this->housekeepingService->startTask($id);
        return response()->json($task);
    }

    public function complete(int $id): JsonResponse
    {
        $task = $this->housekeepingService->completeTask($id);
        return response()->json($task);
    }
}
