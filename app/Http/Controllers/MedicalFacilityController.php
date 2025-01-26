<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\MedicalFacility;
use App\Http\Resources\MedicalFacilityResource;
use App\Http\Requests\UpdateMedicalFacilityRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class MedicalFacilityController extends Controller
{
    /**
     * Apply authentication middleware except for public endpoints
     */
    public function __construct()
    {
        $this->middleware('auth:sanctum')->except(['index', 'show']);
    }
    /**
     * List all medical facilities with pagination
     *
     * @return JsonResponse List of facilities with meta pagination data
     */
    public function index(): JsonResponse
    {
        // Eager load relationships to avoid N+1 queries
        $facilities = MedicalFacility::with(['user', 'units'])
            ->paginate(config('pagination.per_page', 15));
        return response()->json([
            'data' => MedicalFacilityResource::collection($facilities),
            'meta' => [
                'total' => $facilities->total(),
                'per_page' => $facilities->perPage(),
                'current_page' => $facilities->currentPage(),
                'last_page' => $facilities->lastPage()
            ]
        ]);
    }



    /**
     * Show single medical facility details
     *
     * @param MedicalFacility $facility Facility to show (Route Model Binding)
     * @return JsonResponse Facility details with related data
     */
    public function show(MedicalFacility $facility): JsonResponse
    {
        // Eager load relationships
        $facility->load(['user']);
        return response()->json([
            'data' => new MedicalFacilityResource($facility)
        ]);
    }



    /**
     * Delete a medical facility and its relationships
     *
     * @param MedicalFacility $facility Facility to delete
     * @return JsonResponse Success/failure message
     */
    public function destroy(MedicalFacility $facility): JsonResponse
    {
        try {
            DB::beginTransaction();            // Remove relationship records first
            $facility->units()->detach();            // Delete the facility
            $facility->delete();
            DB::commit();
            return response()->json([
                'message' => 'Facility deleted successfully'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();            // Log the error for debugging
            logger()->error('Failed to delete facility', [
                'error' => $e->getMessage(),
                'facility_id' => $facility->id
            ]);
            return response()->json([
                'message' => 'Failed to delete facility'
            ], 500);
        }
    }


    /**
     * Update medical facility information
     */
    public function update(UpdateMedicalFacilityRequest $request): JsonResponse
    {
        try {
            DB::beginTransaction();
            $facility = $request->user()->medicalFacility;
            $facility->update($request->validated());
            if ($request->has('units')) {
                $facility->units()->sync($request->units);
            }
            DB::commit();
            return response()->json([
                'message' => 'Facility updated successfully',
                'data' => new MedicalFacilityResource($facility)
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            logger()->error('Failed to update facility', [
                'error' => $e->getMessage(),
                'user_id' => $request->user()->id
            ]);
            return response()->json([
                'message' => 'Failed to update facility'
            ], 500);
        }
    }
}
