<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index()
    {
        return response()->json(User::all());
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'role' => 'required|string|in:admin,gasman',
            'pin' => 'required|string|size:4|unique:users,pin'
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'role' => $validated['role'],
            'pin' => Hash::make($validated['pin']), // ALWAYS hash the PIN!
            'is_active' => true
        ]);

        return response()->json($user, 201);
    }
}