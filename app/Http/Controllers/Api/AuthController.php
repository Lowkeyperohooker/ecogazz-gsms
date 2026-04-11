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
            'pin' => 'required|string|min:4'
        ]);
        $users = User::where('is_active', true)->get();
        $authenticatedUser = null;

        foreach ($users as $user) {
            if (Hash::check($request->pin, $user->pin)) {
                $authenticatedUser = $user;
                break;
            }
        }

        // 3. Reject if wrong Password
        if (!$authenticatedUser) {
            return response()->json(['message' => 'Invalid password entered.'], 401);
        }

        // 4. Create the token
        $token = $authenticatedUser->createToken('gsms-pos-token')->plainTextToken;

        // 5. Explicitly create the $frontendRole variable
        $dbRole = strtolower($authenticatedUser->role);
        $frontendRole = in_array($dbRole, ['manager', 'admin']) ? 'admin' : 'staff';

        // 6. Return the response using the variable we just created
        return response()->json([
            'message' => 'Login successful',
            'user' => $authenticatedUser,
            'role' => $frontendRole,
            'token' => $token
        ]);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json(['message' => 'Logged out successfully']);
    }
}