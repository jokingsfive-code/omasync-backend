<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\ReportService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    protected ReportService $reportService;

    public function __construct(ReportService $reportService)
    {
        $this->reportService = $reportService;
    }

    public function occupancy(Request $request): JsonResponse
    {
        $report = $this->reportService->getOccupancyReport($request->all());
        return response()->json($report);
    }

    public function revenue(Request $request): JsonResponse
    {
        $report = $this->reportService->getRevenueReport($request->all());
        return response()->json($report);
    }

    public function guest(Request $request): JsonResponse
    {
        $report = $this->reportService->getGuestReport($request->all());
        return response()->json($report);
    }

    public function reservation(Request $request): JsonResponse
    {
        $report = $this->reportService->getReservationReport($request->all());
        return response()->json($report);
    }

    public function generate(Request $request): JsonResponse
    {
        $report = $this->reportService->generateCustomReport($request->all());
        return response()->json($report);
    }
}
