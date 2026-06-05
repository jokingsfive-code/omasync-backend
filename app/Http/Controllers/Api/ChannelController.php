<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ChannelRequest;
use App\Services\ChannelService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ChannelController extends Controller
{
    protected ChannelService $channelService;

    public function __construct(ChannelService $channelService)
    {
        $this->channelService = $channelService;
    }

    public function index(Request $request): JsonResponse
    {
        $channels = $this->channelService->getAllChannels($request->all());
        return response()->json($channels);
    }

    public function store(ChannelRequest $request): JsonResponse
    {
        $channel = $this->channelService->createChannel($request->validated());
        return response()->json($channel, 201);
    }

    public function show(int $id): JsonResponse
    {
        $channel = $this->channelService->getChannelById($id);
        return response()->json($channel);
    }

    public function update(ChannelRequest $request, int $id): JsonResponse
    {
        $channel = $this->channelService->updateChannel($id, $request->validated());
        return response()->json($channel);
    }

    public function destroy(int $id): JsonResponse
    {
        $this->channelService->deleteChannel($id);
        return response()->json(null, 204);
    }

    public function sync(int $id): JsonResponse
    {
        $result = $this->channelService->syncChannel($id);
        return response()->json($result);
    }
}
