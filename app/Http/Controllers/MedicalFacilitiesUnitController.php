<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Unit;

class MedicalFacilitiesUnitController extends Controller
{
    /**
     * Display a listing of the units.
     */
    public function index()
    {
        $units = Unit::with('facility')->get();
        return response()->json([
            'success' => true,
            'data' => $units
        ]);
    }

    /**
     * Store a newly created unit.
     */
   /**
 * Store a newly created unit.
 */
public function store(Request $request)
{
    try {
        $validator = Validator::make($request->all(), [
            'facility_id' => 'required|exists:medical_facilities,id',
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'specialization' => 'nullable|string|max:255',
            'status' => 'required|in:active,inactive'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $unit = Unit::create($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Unit created successfully',
            'data' => $unit
        ], 201);

    } catch (\Illuminate\Database\QueryException $e) {
        return response()->json([
            'success' => false,
            'message' => 'Database error occurred',
            'error' => $e->getMessage()
        ], 500);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'An error occurred while creating the unit',
            'error' => $e->getMessage()
        ], 500);
    }
}

    /**
     * Display the specified unit.
     */
    public function show(Unit $unit)
    {
        return response()->json([
            'success' => true,
            'data' => $unit->load('facility')
        ]);
    }

    /**
     * Update the specified unit.
     */
    public function update(Request $request, Unit $unit)
    {
        $validator = Validator::make($request->all(), [
            'facility_id' => 'exists:medical_facilities,id',
            'name' => 'string|max:255',
            'description' => 'nullable|string',
            'specialization' => 'nullable|string|max:255',
            'status' => 'in:active,inactive'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $unit->update($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Unit successfully updated',
            'data' => $unit
        ]);
    }

    /**
     * Remove the specified unit.
     */
    public function destroy(Unit $unit)
    {
        $unit->delete();

        return response()->json([
            'success' => true,
            'message' => 'Unit deleted successfully'
        ]);
    }

    /**
     * Get units by facility.
     */
    public function getUnitsByFacility($facilityId)
    {
        $units = Unit::where('facility_id', $facilityId)->get();
        
        return response()->json([
            'success' => true,
            'data' => $units
        ]);
    }
}
