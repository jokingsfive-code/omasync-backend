<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\SettingRequest;
use App\Services\SettingService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    protected SettingService $settingService;

    public function __construct(SettingService $settingService)
    {
        $this->settingService = $settingService;
    }

    public function index(Request $request): JsonResponse
    {
        $settings = $this->settingService->getAllSettings($request->all());
        return response()->json($settings);
    }

    public function store(SettingRequest $request): JsonResponse
    {
        $setting = $this->settingService->createSetting($request->validated());
        return response()->json($setting, 201);
    }

    public function show(int $id): JsonResponse
    {
        $setting = $this->settingService->getSettingById($id);
        return response()->json($setting);
    }

    public function update(SettingRequest $request, int $id): JsonResponse
    {
        $setting = $this->settingService->updateSetting($id, $request->validated());
        return response()->json($setting);
    }

    public function destroy(int $id): JsonResponse
    {
        $this->settingService->deleteSetting($id);
        return response()->json(null, 204);
    }

    public function updateProfile(Request $request): JsonResponse
    {
        $user = $this->settingService->updateUserProfile($request->all());
        return response()->json($user);
    }

    public function updatePassword(Request $request): JsonResponse
    {
        $this->settingService->updateUserPassword($request->all());
        return response()->json(['message' => 'Password updated successfully']);
    }
}
