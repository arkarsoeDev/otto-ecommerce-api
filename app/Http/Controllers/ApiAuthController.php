<?php

namespace App\Http\Controllers;

use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class ApiAuthController extends Controller
{
    public function register(Request $request) {
        $request->validate([
            "name" => "required|min:3",
            "email" => "required|email|unique:users,email",
            "password" => "required|min:8|confirmed"
        ]);

        $user = User::create([
            "name" => $request->name,
            "email" => $request->email,
            "password" => Hash::make($request->password)
         ]);

         if(Auth::attempt($request->only('email','password'))) {
            $token = Auth::user()->createToken("main")->plainTextToken;

            return response()->json($token);
         }

         return response()->json([
            "message" => "Registeration failed"
         ],403);
    }

    public function login(Request $request){
        $request->validate([
            "email" => "required|email",
            "password" => "required|min:8"
        ]);

        if (Auth::attempt($request->only('email', 'password'))) {
            $token = Auth::user()->createToken("main")->plainTextToken;

            return response()->json(['token'=> $token,'data'=>new UserResource(Auth::user())]);
        }

        return response()->json([
            "message" => "The credentials are invalid",
        ], 403);
    }

    public function logout() {
        Auth::user()->currentAccessToken()->delete();
        
        return response()->json([
            "message" => "Logout successful", 
        ]);
    }

    public function logoutAll() {
        Auth::user()->tokens()->delete();

        return response()->json([
            "message" => "Logout all successful",
        ]);
    }
}
