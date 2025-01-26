<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Unit;
use App\Http\Resources\UnitResource;
use App\Http\Requests\UpdateUnitRequest;
use App\Http\Requests\StoreUnitRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

class UnitController extends Controller
{
    /**
     * Apply auth middleware except for public endpoints
     */
    public function __construct()
    {
        $this->middleware('auth:sanctum')->except(['index', 'show']);
    }
    /**
     * List all medical units with pagination
     */
    public function index(): JsonResponse
    {
        $units = Unit::paginate(config('pagination.per_page', 15));
        return response()->json([
            'data' => UnitResource::collection($units),
            'meta' => [
                'total' => $units->total(),
                'per_page' => $units->perPage(),
                'current_page' => $units->currentPage(),
                'last_page' => $units->lastPage()
            ]
        ]);
    }
    /**
     * Show single unit details
     * @param Unit $unit Unit to show (Route Model Binding)
     */
    public function show(Unit $unit): JsonResponse
    {
        return response()->json([
            'data' => new UnitResource($unit),
        ]);
    }


    public function store(StoreUnitRequest $request): JsonResponse
    {
        logger()->info('Store request received', $request->validated());

        try {
            $facilityId = auth()->user()->medicalFacility->id; // Assuming `medicalFacility` is the relationship on the User model.

            $unit = Unit::create(array_merge($request->validated(), [
                'facility_id' => $facilityId,
            ]));

            return response()->json([
                'message' => 'Unit created successfully',
                'data' => new UnitResource($unit)
            ], 201);
        } catch (\Exception $e) {
            logger()->error('Error creating unit', ['error' => $e->getMessage()]);
            return response()->json([
                'message' => 'Internal server error',
            ], 500);
        }
    }



    /**
     * Update existing unit
     */
    public function update(UpdateUnitRequest $request, Unit $unit): JsonResponse
    {
        $unit->update($request->validated());
        return response()->json([
            'message' => 'Unit updated successfully',
            'data' => new UnitResource($unit)
        ]);
    }
    /**
     * Delete a unit
     */
    public function destroy(Unit $unit): JsonResponse
    {
        $unit->delete();
        return response()->json(['message' => 'Unit deleted successfully']);
    }
}
