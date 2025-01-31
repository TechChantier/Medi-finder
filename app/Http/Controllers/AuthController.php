<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\SignupUserRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Models\MedicalFacility;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /**
     * Handle user login
     */
    public function login(LoginRequest $request): JsonResponse
    {
        $user = User::where('email', $request->email)
            ->with('medicalFacility') // Eager load medical facility if exists
            ->first();
        if (!$user || !Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.']
            ]);
        }
        return response()->json([
            'message' => 'User login successfully ',
            'user' => new UserResource($user)
        ]);
    }
    /**
     * Handle user registration with medical facility support
     */


    public function register(SignupUserRequest $request): JsonResponse
    {
        try {
            DB::beginTransaction();


            $userData = [
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'image' => $request->image,
                'whatsapp_number' => $request->whatsapp_number,
                'user_type' => $request->user_type
            ];
            if ($request->hasFile('image')) {
                $userData['image'] = $request->file('image')->store('users', 'public');
            }
            $user = User::create($userData);
            if ($request->user_type === 'medical_facility') {
                // Create facility with direct JSON data
                $user->medicalFacility()->create([
                    'address' => $request->address,
                    'description' => $request->description,
                    'operating_hours' => $request->operating_hours,
                    'status' => 'Open',
                    'units' => $request->units ?? [] // Store directly as JSON
                ]);
            }
            DB::commit();
            return response()->json([
                'message' => 'Registration successful',
                'token' => $user->createToken('api-token')->plainTextToken,
                'user' => new UserResource($user->load('medicalFacility'))
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            logger()->error('Registration failed', [
                'error' => $e->getMessage(),
                'data' => $request->except('password')
            ]);
            return response()->json(['message' => 'Registration failed. ' . $e->getMessage()], 500);
        }
    }

    /**
     * Handle user logout
     */
    public function logout(): JsonResponse
    {
        auth()->user()->tokens()->delete();
        return response()->json([
            'message' => 'Logged out successfully'
        ]);
    }
    /**
     * Get authenticated user profile
     */
    public function profile(): JsonResponse
    {
        $user = auth()->user()->load('medicalFacility.units');
        return response()->json([
            'data' => new UserResource($user)
        ]);
    }
}
