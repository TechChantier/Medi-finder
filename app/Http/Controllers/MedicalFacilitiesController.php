<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\MedicalFacility;

class MedicalFacilitiesController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
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
            'units' => 'required|array|max:255', // Ensure at least one unit
            'units.*' => 'string'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'errors' => $validator->errors()
            ], 422);
        }

        $facility = MedicalFacility::create(array_merge($validator->validated(), ['units' => json_encode($request->units)]));
        logger($facility);

        // Attach units
        // if ($request->has('units')) {
        //     $facility->medical_facility_units->attach($request->units);
        // }

        return response()->json([
            'status' => 'success',
            'message' => 'Medical facility created successfully',
            'data' => $facility->load(['users'])
        ], 201);
    }


      /**
     * Display the specified medical facility.
     */
    public function show($id)
    {
        $facility = MedicalFacility::with(['users'])->find($id);
        
        if (!$facility) {
            return response()->json([
                'status' => 'error',
                'message' => 'Medical facility not found'
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'data' => $facility
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $facility = MedicalFacility::find($id);
        logger($request);
    
        if (!$facility) {
            return response()->json([
                'status' => 'error',
                'message' => 'Medical facility not found'
            ], 404);
        }
    
        // Use the same validation rules as store, but  optional for update
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
            return response()->json([
                'status' => 'error',
                'errors' => $validator->errors()
            ], 422);
        }
    
        // Get validated data
        $validatedData = $validator->validated();
    
        // Handle units separately if they exist in the request
        if (isset($validatedData['units'])) {
            $validatedData['units'] = json_encode($request->units);
        }
    
        // Update only the fields that were provided in the request
        $facility->update($validatedData);
        // $facility->save();
    
        return response()->json([
            'status' => 'success',
            'message' => 'Medical facility updated successfully',
            'data' => $facility->load(['users'])
        ]);
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $facility = MedicalFacility::find($id);

        if (!$facility) {
            return response()->json([
                'status' => 'error',
                'message' => 'Medical facility not found'
            ], 404);
        }

        // Delete the facility (relationships will be cascade deleted due to foreign key constraint)
        $facility->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Medical facility deleted successfully'
        ]);
    }
}
