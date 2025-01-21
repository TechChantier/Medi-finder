<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\MedicalFacility;
use Illuminate\Support\Facades\Log;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\ValidationException;

class MedicalFacilitiesController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $facilities = MedicalFacility::with(['users'])->get();
            
            return response()->json([
                'status' => 'success',
                'data' => $facilities
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to fetch medical facilities',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'user_id' => 'required|exists:users,id',
                'name' => 'required|string|max:255',
                'address' => 'required|string',
                'whatsapp_number' => 'required|string|max:12',
                'email' => 'nullable|email',
                'description' => 'required|string',
                'operating_hours' => 'nullable|json',
                'latitude' => 'nullable|numeric|between:-90,90',
                'longitude' => 'nullable|numeric|between:-180,180',
                'status' => 'required|in:Open,Closed',
                'units' => 'required|array|max:255',
                'units.*' => 'string'
            ]);

            if ($validator->fails()) {
                throw new ValidationException($validator);
            }

            $facility = MedicalFacility::create(array_merge(
                $validator->validated(), 
                ['units' => json_encode($request->units)]
            ));


            return response()->json([
                'status' => 'success',
                'message' => 'Medical facility created successfully',
                'data' => $facility->load(['users'])
            ], 201);

        } catch (ValidationException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $e->validator->errors()
            ], 422);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to create medical facility',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified medical facility.
     */
    public function show($id)
    {
        try {
            $facility = MedicalFacility::with(['users'])->findOrFail($id);
            
            return response()->json([
                'status' => 'success',
                'data' => $facility
            ]);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Medical facility not found'
            ], 404);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to fetch medical facility',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        try {
            $facility = MedicalFacility::findOrFail($id);
            
            $validator = Validator::make($request->all(), [
                'user_id' => 'sometimes|exists:users,id',
                'name' => 'sometimes|string|max:255',
                'address' => 'sometimes|string',
                'whatsapp_number' => 'sometimes|string|max:12',
                'email' => 'nullable|email',
                'description' => 'sometimes|string',
                'operating_hours' => 'nullable|json',
                'latitude' => 'nullable|numeric|between:-90,90',
                'longitude' => 'nullable|numeric|between:-180,180',
                'status' => 'sometimes|in:Open,Closed',
                'units' => 'sometimes|array|max:255',
                'units.*' => 'string'
            ]);

            if ($validator->fails()) {
                throw new ValidationException($validator);
            }

            $validatedData = $validator->validated();
            
            if (isset($validatedData['units'])) {
                $validatedData['units'] = json_encode($request->units);
            }

            $facility->update($validatedData);

            return response()->json([
                'status' => 'success',
                'message' => 'Medical facility updated successfully',
                'data' => $facility->load(['users'])
            ]);

        } catch (ModelNotFoundException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Medical facility not found'
            ], 404);
        } catch (ValidationException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $e->validator->errors()
            ], 422);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to update medical facility',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try {
            $facility = MedicalFacility::findOrFail($id);
            $facility->delete();
            

            return response()->json([
                'status' => 'success',
                'message' => 'Medical facility deleted successfully'
            ]);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Medical facility not found'
            ], 404);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to delete medical facility',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Search medical facilities with various filters
     */
    public function search(Request $request)
    {
        try {
            $query = MedicalFacility::query();

            if ($request->filled('query')) {
                $searchTerm = $request->get('query');
                $query->where(function($q) use ($searchTerm) {
                    $q->where('name', 'LIKE', "%{$searchTerm}%")
                      ->orWhere('address', 'LIKE', "%{$searchTerm}%")
                      ->orWhere('description', 'LIKE', "%{$searchTerm}%");
                });
            }

            $facilities = $query->get();
            
            return response()->json([
                'status' => 'success',
                'data' => $facilities
            ]);

        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to search medical facilities',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Filter medical facilities based on various criteria
     */
    public function filter(Request $request)
    {
        try {
            $query = MedicalFacility::query();

            if ($request->filled('name')) {
                $query->where('name', 'LIKE', "%{$request->name}%");
            }

            if ($request->filled('address')) {
                $query->where('address', 'LIKE', "%{$request->address}%");
            }

            if ($request->filled('status')) {
                $query->where('status', $request->status);
            }

            if ($request->filled('units')) {
                $query->where('units', 'LIKE', "%{$request->units}%");
            }

            if ($request->filled('services')) {
                $query->where('services', 'LIKE', "%{$request->services}%");
            }

            $facilities = $query->get();
            
            return response()->json([
                'status' => 'success',
                'data' => $facilities
            ]);

        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to filter medical facilities',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}