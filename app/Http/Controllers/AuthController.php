<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\Hash;
use Tymon\JWTAuth\Exceptions\JWTException;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
     // User registration
     public function register(Request $request)
     {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            // 'password' => 'required|string|min:6|confirmed',
        ]);

        if($validator->fails()){
            return response()->json($validator->errors()->toJson(), 400);
        }

        // $userImage 
        $userImgObj = $request->file('profile_img');
        $imgPath = $userImgObj->store('images','public');

        $user = User::create([
            'name' => $request->get('name'),
            'email' => $request->get('email'),
            'password' => Hash::make($request->get('password')),
            'avatar' =>  asset("storage/".$imgPath),
            'email_verified_at' => now(),
        ]);

        $token = JWTAuth::fromUser($user);

        return response()->json([
            'user_data' => $user, 
            'jwt_token' => $token  
        ], 201);
     }
 
     // User login
     public function login(Request $request)
     {
        $credentials = $request->only('email', 'password');

        try {
            if (!$token = JWTAuth::attempt($credentials)) {
                return response()->json(['error' => 'Invalid credentials'], 401);
            }

            // Get the authenticated user.
            $user = JWTAuth::user();

            // return response()->json(compact('token'));
            return response()->json([
                'message' => 'login successful',
                'data' => $user,
                'token' => $token
            ]);

        } catch (JWTException $e) {
            return response()->json(['error' => 'Could not create token'], 500);
        }
     }
 
     // Get authenticated user
     public function getUser(){
        try {
            if (! $user = JWTAuth::parseToken()->authenticate()) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not found',
                ], 404);
            }
    
            return response()->json([
                'success' => true,
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    // Add more fields if needed
                ],
            ]);
    
        } catch (JWTException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid token',
            ], 400);
        }
     }
 
     // User logout
     public function logout()
     {
         JWTAuth::invalidate(JWTAuth::getToken());
 
         return response()->json(['message' => 'Successfully logged out']);
     }
}
