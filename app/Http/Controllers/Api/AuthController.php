<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'pin' => 'required|string|size:4'
        ]);

        // Fetch all active users. (Since PINs are hashed, we must verify them in memory. 
        // This is perfectly fine and fast for a small team of gas station staff.)
        $users = User::where('is_active', true)->get();
        $authenticatedUser = null;

        foreach ($users as $user) {
            if (Hash::check($request->pin, $user->pin)) {
                $authenticatedUser = $user;
                break;
            }
        }

        // If no PIN matched
        if (!$authenticatedUser) {
            return response()->json(['message' => 'Invalid PIN entered.'], 401);
        }

        // Create the Sanctum API Token
        $token = $authenticatedUser->createToken('gsms-pos-token')->plainTextToken;

        // Map database roles to your Vue frontend roles
        $frontendRole = strtolower($authenticatedUser->role) === 'manager' ? 'admin' : 'staff';

        return response()->json([
            'message' => 'Login successful',
            'user' => $authenticatedUser,
            'role' => $frontendRole,
            'token' => $token
        ]);
    }

    public function logout(Request $request)
    {
        // Revoke the token that was used to authenticate the current request
        $request->user()->currentAccessToken()->delete();

        return response()->json(['message' => 'Logged out successfully']);
    }
}