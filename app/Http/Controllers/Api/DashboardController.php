<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\DashboardService;
use Illuminate\Http\JsonResponse;

class DashboardController extends Controller
{
    protected DashboardService $dashboardService;

    public function __construct(DashboardService $dashboardService)
    {
        $this->dashboardService = $dashboardService;
    }

    public function index(): JsonResponse
    {
        $stats = $this->dashboardService->getDashboardStats();
        $recentReservations = $this->dashboardService->getRecentReservations();
        $housekeepingTasks = $this->dashboardService->getHousekeepingTasks();

        return response()->json([
            'stats' => $stats,
            'recent_reservations' => $recentReservations,
            'housekeeping_tasks' => $housekeepingTasks,
        ]);
    }

    public function occupancyRate(): JsonResponse
    {
        $data = $this->dashboardService->getOccupancyRate();
        return response()->json($data);
    }

    public function revenue(): JsonResponse
    {
        $data = $this->dashboardService->getRevenue();
        return response()->json($data);
    }

    public function checkInsToday(): JsonResponse
    {
        $data = $this->dashboardService->getCheckInsToday();
        return response()->json($data);
    }

    public function checkOutsToday(): JsonResponse
    {
        $data = $this->dashboardService->getCheckOutsToday();
        return response()->json($data);
    }

    public function availableRooms(): JsonResponse
    {
        $data = $this->dashboardService->getAvailableRooms();
        return response()->json($data);
    }
}
