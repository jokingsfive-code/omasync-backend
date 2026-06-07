<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\HousekeepingTask;
use Illuminate\Http\Request;

class HousekeepingController extends Controller
{
    public function index()
    {
        return HousekeepingTask::with('property')
            ->latest()
            ->get();
    }

    public function store(Request $request)
    {
        return HousekeepingTask::create(
            $request->all()
        );
    }

    public function update(Request $request, $id)
    {
        $task = HousekeepingTask::findOrFail($id);

        $task->update($request->all());

        return $task;
    }

    public function destroy($id)
    {
        HousekeepingTask::findOrFail($id)->delete();

        return response()->json([
            'message' => 'Deleted'
        ]);
    }
}