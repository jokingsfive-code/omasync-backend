<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Property;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class PropertyController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json(Property::all());
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'location' => 'required|string|max:255',
            'price' => 'nullable|numeric',
            'description' => 'nullable|string',
        ]);

        $property = Property::create($validated);

        return response()->json([
            'message' => 'Property created successfully',
            'data' => $property
        ], 201);
    }

    public function update(Request $request, $id): JsonResponse
    {
        $property = Property::find($id);

        if (!$property) {
            return response()->json([
                'message' => 'Property not found'
            ], 404);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'location' => 'required|string|max:255',
            'price' => 'nullable|numeric',
            'description' => 'nullable|string',
        ]);

        $property->update($validated);

        return response()->json([
            'message' => 'Property updated successfully',
            'data' => $property
        ]);
    }

    public function destroy($id): JsonResponse
    {
        $property = Property::find($id);

        if (!$property) {
            return response()->json([
                'message' => 'Property not found'
            ], 404);
        }

        $property->delete();

        return response()->json([
            'message' => 'Property deleted successfully'
        ]);
    }
}