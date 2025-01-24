<?php

namespace App\Http\Controllers;

use App\Http\Requests\SignupUserRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;


/**
 * @group User Management
 * 
 * APIs to manage the user Authentication.
 */
class ApiController extends Controller
{
    public function login(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'email' => 'required|email',
                'password' => 'required|string|min:6'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 'error',
                    'message' => $validator->errors()->first()
                ], 401);
            }

            $credentials = $request->only('email', 'password');

            if (!Auth::guard('web')->attempt($credentials)) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Unauthorized, check your login credentials'
                ], 401);
            }

            $user = Auth::user();
            $tokenResult = $user->createToken('Personal Access Token');
            $token = $tokenResult->plainTextToken;

            return response()->json([
                'status' => 'success',
                'token' => $token,
                'user' => $user,
                'image_url' => $user->image ? asset('storage/' . $user->image) : null
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Login failed',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function register(SignupUserRequest $request)
    {

        $validatedFields = $request->validated();

        $user = User::create([
          'email'=> $validatedFields['email'],
          'name' => $validatedFields['name'],
          'password'=> Hash::make($validatedFields['password']),
          'whatsapp_number'=> $validatedFields['whatsapp_number'],
          'user_type' => $validatedFields['user_type'],
          'image' => $validatedFields['image'],
        ]);

        return response()->json([
            'message' => 'User created successfully',
            'user' => new UserResource($user)
        ], 201);
    }
    

    public function logout(Request $request)
{
    try {
        // Get the current authenticated user
        $user = $request->user();

        // Check if a user is authenticated
        if (!$user) {
            return response()->json([
                'status' => 'error',
                'message' => 'No authenticated user found'
            ], 401);
        }

        // Delete all tokens for the user
        $user->tokens()->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Successfully logged out.'
        ], 200);
    } catch (\Exception $e) {
        // Log the error for debugging
        Log::error('Logout error: ' . $e->getMessage());

        return response()->json([
            'status' => 'error',
            'message' => 'Logout failed',
            'error' => $e->getMessage()
        ], 500);
    }
}
    // public function logout(Request $request)
    // {
    //     try {
    //         $request->user()->currentAccessToken()->delete();
            
    //         return response()->json([
    //             'status' => 'success',
    //             'message' => 'Successfully logged out'
    //         ]);
            
    //     } catch (\Exception $e) {
    //         return response()->json([
    //             'status' => 'error',
    //             'message' => 'Logout failed',
    //             'error' => $e->getMessage()
    //         ], 500);
    //     }
    // }

    public function user(Request $request)
    {
        try {
            $user = $request->user();
            
            return response()->json([
                'status' => 'success',
                'user' => $user,
                'image_url' => $user->image ? asset('storage/' . $user->image) : null
            ], 200);
            
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to fetch user details',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}