<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|string',
            'password' => 'required|string',
        ]);

        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials)) {
            $user = Auth::user();
            $token = Auth::user()->createToken('auth-token')->accessToken;
            return response()->json([
                'token' => $token,
                'name' => $user->name
            ], 200);
        }

        throw ValidationException::withMessages(['error' => 'Invalid credentials']);
    }
}