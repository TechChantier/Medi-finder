<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;


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
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Login failed',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function register(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|between:2,100',
                'email' => 'required|string|email|max:100|unique:users',
                'whatsapp_number' => 'required|string|max:12|unique:users',
                'password' => 'required|string|confirmed|min:6',
                'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048' // max 2MB
            ]);
    
            if ($validator->fails()) {
                return response()->json([
                    'message' => $validator->errors()->first()
                ], 401);
            }
    
            // Handle image upload
            $imagePath = null;
            if ($request->hasFile('image')) {
                $image = $request->file('image');
                $fileName = time() . '_' . Str::random(10) . '.' . $image->getClientOriginalExtension();
                
                // Store in public/storage/profile-images
                $imagePath = $image->storeAs('profile-images', $fileName, 'public');
            }
    
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'whatsapp_number' => $request->whatsapp_number,
                'password' => bcrypt($request->password),
                'image' => $imagePath // Save the image path to database
            ]);
    
            $tokenResult = $user->createToken('Personal Access Token');
            $token = $tokenResult->plainTextToken;
    
            return response()->json([
                'message' => 'User successfully registered!',
                'token' => $token,
                'user' => $user,
                'image_url' => $imagePath ? Storage::url($imagePath) : null
            ]);
    
        } catch (\Exception $e) {
            // Delete uploaded image if user creation fails
            if (isset($imagePath) && Storage::disk('public')->exists($imagePath)) {
                Storage::disk('public')->delete($imagePath);
            }
    
            return response()->json([
                'message' => 'Registration failed',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    
    public function logout(Request $request)
    {
        try {
            $request->user()->currentAccessToken()->delete();
            
            return response()->json([
                'status' => 'success',
                'message' => 'Successfully logged out'
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Logout failed',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function user(Request $request)
    {
        try {
            $user = $request->user();
            
            return response()->json([
                'status' => 'success',
                'user' => $user,
                'image_url' => $user->image ? asset('storage/' . $user->image) : null
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to fetch user details',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}