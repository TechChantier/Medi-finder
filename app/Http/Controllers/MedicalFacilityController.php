<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\SearchRequest;
use App\Http\Requests\SignupUserRequest;
use App\Models\MedicalFacility;
use App\Http\Resources\MedicalFacilityResource;
use App\Http\Requests\UpdateMedicalFacilityRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Http\Resources\UpdateResource;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;

class MedicalFacilityController extends Controller
{
    /**
     * Apply authentication middleware except for public endpoints
     */
    public function __construct()
    {
        $this->middleware('auth:sanctum')->except(['index', 'show', 'search']);
    }



    /**
     * Perform a general search across medical facilities
     *
     * @param SearchRequest $request The validated search request
     * @return JsonResponse Paginated list of matching facilities
     */

    public function search(SearchRequest $request): JsonResponse
    {
        try {
            // Get the validated search query
            $query = $request->input('query', '');

            // Log the search query
            Log::info('Search query:', ['query' => $query]);

            // Base query
            $facilitiesQuery = MedicalFacility::query();

            if (!empty($query)) {
                $facilitiesQuery->where(function ($q) use ($query) {
                    $q->where('address', 'like', "%{$query}%")
                        ->orWhere('description', 'like', "%{$query}%")
                        ->orWhere('services', 'like', "%{$query}%")
                        ->orWhere('status', 'like', "%{$query}%")
                        ->orWhere('units', 'like', "%{$query}%")
                        ->orWhere('emergency_contact', 'like', "%{$query}%");

                    // Only try JSON search if query is not empty
                    if ($query) {
                        $q->orWhereJsonContains('units', $query);
                    }
                });
            }

            // Eager load relationships
            $facilitiesQuery->with(['user']);

            // Get paginated results
            $facilities = $facilitiesQuery->paginate(
                config('pagination.per_page', 15)
            );

            // Log the results count
            // Log::info('Search results:', [
            //     'count' => $facilities->count(),
            //     'total' => $facilities->total()
            // ]);

            return response()->json([
                'data' => MedicalFacilityResource::collection($facilities),
                'meta' => [
                    'total' => $facilities->total(),
                    'per_page' => $facilities->perPage(),
                    'current_page' => $facilities->currentPage(),
                    'last_page' => $facilities->lastPage(),
                ],
            ]);
        } catch (\Exception $e) {
            Log::error('Search error:', [
                'message' => $e->getMessage(),
                // 'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'error' => 'An error occurred while searching',
                'message' => $e->getMessage(),
                // 'debug' => config('app.debug') ? $e->getTraceAsString() : null
            ], 500);
        }
    }

    /**
     * List all medical facilities with pagination
     *
     * @return JsonResponse List of facilities with meta pagination data
     */
    public function index(): JsonResponse
    {
        // Eager load relationships to avoid N+1 queries
        $facilities = MedicalFacility::with(['user'])
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
    // public function update(UpdateMedicalFacilityRequest $request, UpdateUserRequest $userResquest): JsonResponse
    // {
    //     try {
    //         DB::beginTransaction();

    //         $user = $request->user();
    //         $facility = $user->medicalFacility;

    //         // Update user data
    //         $userData = [
    //             'name' => $userResquest->input('name', $user->name),
    //             'email' => $userResquest->input('email', $user->email),
    //             'whatsapp_number' => $userResquest->input('whatsapp_number', $user->whatsapp_number),
    //         ];




    //         // Handle image upload
    //         if ($request->hasFile('image')) {
    //             // Delete old image if exists
    //             if ($user->image) {
    //                 Storage::disk('public')->delete($user->image);
    //             }
    //             $userData['image'] = $request->file('image')->store('users', 'public');
    //         }

    //         // Update user
    //         $user->update($userData);

    //         // Update facility
    //         $facility->update($request->validated());

    //         // Update units if provided
    //         if ($request->has('units')) {
    //             $facility->units()->sync($request->units);
    //         }

    //         DB::commit();

    //         logger()->info('User Update Data:', $userResquest->all());

    //         $facility->load(['user']);
    //         return response()->json([
    //             'message' => 'Facility and user information updated successfully',
    //             'data' => new UpdateResource($facility)
    //         ]);
    //     } catch (\Exception $e) {
    //         DB::rollBack();
    //         logger()->error('Failed to update facility and user information', [
    //             'error' => $e->getMessage(),
    //             'user_id' => $request->user()->id
    //         ]);

    //         return response()->json([
    //             'message' => 'Failed to update facility and user information'
    //         ], 500);
    //     }
    // }
    public function update(UpdateMedicalFacilityRequest $request): JsonResponse
    {
        try {
            DB::beginTransaction();
            logger('Request Data: ', $request->all());
            $user = $request->user();
            logger('Request User: ', [$user]);
            $facility = $user->medicalFacility;
            logger('Request Facility: ', [$facility]);

            // Only update fields that are provided
            $userData = [];
            if ($request->has('name')) {
                $userData['name'] = $request->name;
            }
            if ($request->has('email')) {
                $userData['email'] = $request->email;
            }
            if ($request->has('whatsapp_number')) {
                $userData['whatsapp_number'] = $request->whatsapp_number;
            }

            logger('Generated User Data To Update: ', [$userData]);

            // Handle image upload
            if ($request->hasFile('image')) {
                if ($user->image) {
                    Storage::disk('public')->delete($user->image);
                }
                $userData['image'] = $request->file('image')->store('users', 'public');
            }

            // Update user only if there's data
            if (!empty($userData)) {
                logger('There is user data so I am updating');
                $user->update($userData);

                logger('New Updated User Object: ', [$user]);
            }

            // Update facility
            $facility->update($request->validated());
            logger('New Updated Facility Object: ', [$facility]);

            // Update units if provided
            if ($request->has('units')) {
                $facility->units()->sync($request->units);
            }

            DB::commit();

            $facility->load(['user']);

            logger('New Updated API Resource Object: ', [$facility]);
            return response()->json([
                'message' => 'Facility and user information updated successfully',
                'data' => new UpdateResource($facility)
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            logger()->error('Failed to update facility and user information', [
                'error' => $e->getMessage(),
                'user_id' => $request->user()->id
            ]);
            return response()->json([
                'message' => 'Failed to update facility and user information'
            ], 500);
        }
    }
}
