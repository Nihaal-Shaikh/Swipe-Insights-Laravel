<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Carbon;

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

            // Additional check for updated entries
            $updatedEntriesCount = $user->images()
                ->where('customer_id', $user->id)
                ->where('updated_at', '>', Carbon::now()->subDay())
                ->count();

            return response()->json([
                'token' => $token,
                'name' => $user->name,
                'updatedFiveEntries' => ($updatedEntriesCount >= 5) ? 1 : 0
            ], 200);
        }

        throw ValidationException::withMessages(['error' => 'Invalid credentials']);
    }
}