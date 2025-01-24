<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
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
use Illuminate\Validation\ValidationException;

/**
 * @group User Management
 * 
 * APIs to manage the user Authentication.
 */
class ApiController extends Controller
{
    public function login(LoginRequest $request)
    {
        $validatedFields = $request->validated();

        $user = User::where("email", $validatedFields["email"])->first();

        if (!$user) {
            throw ValidationException::withMessages(["email"=> ['Email is incorrect!']]);
        }

        if (!Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect']
            ]);
        }        $token = $user->createToken('api-token')->plainTextToken;        
        return response()->json([
            'token' => $token ,
            'authenticated user' => new UserResource($user)
        ]);
    
    }

    public function register(SignupUserRequest $request)
    {
            // logger($request);
        $validatedFields = $request->validated();

        $user = User::create([
          'email'=> $validatedFields['email'],
          'name' => $validatedFields['name'],
          'password'=> Hash::make($validatedFields['password']),
          'whatsapp_number'=> $validatedFields['whatsapp_number'],
          'user_type' => $validatedFields['user_type'],
          'image' => $validatedFields['image'],
        ]);
            logger($user);
        return response()->json([
            'message' => 'User created successfully',
            'user' => new UserResource($user)
        ], 201);
    }
    
       /**
     * Logout User
     *
     * Invalidate user's access token.
     *
     * @authenticated
     *
     * @response {
     *  "message": "You are Logged out Successfully"
     * }
     */
    public function logout(Request $request)
    {
        $request->user()->tokens()->delete();        
        return response()->json([
            'message' => 'You are Logged out Successfully'
        ]);
    }

    
    
}