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
            'pin' => 'required|string|min:4|unique:users,pin',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'role' => $validated['role'],
            'pin' => Hash::make($validated['pin']), // ALWAYS hash the PIN!
            'is_active' => true
        ]);

        return response()->json($user, 201);
    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        // Basic validation rules
        $rules = [
            'name' => 'required|string|max:255',
            'role' => 'required|string|in:admin,gasman',
        ];

        // If they typed a new PIN, we must validate it!
        // We tell Laravel to make sure it's unique, but ignore THIS user's current PIN
        if ($request->filled('pin')) {
            $rules['pin'] = 'required|string|size:4|unique:users,pin,' . $user->id;
        }

        $validated = $request->validate($rules);

        // Update the user's basic info
        $user->name = $validated['name'];
        $user->role = $validated['role'];
        
        // Only update the PIN if they actually typed a new one
        if ($request->filled('pin')) {
            $user->pin = \Illuminate\Support\Facades\Hash::make($validated['pin']);
        }

        $user->save();

        return response()->json(['message' => 'Staff updated successfully', 'user' => $user]);
    }

    public function destroy($id)
    {
        $user = User::findOrFail($id);
        
        if ($user->role === 'admin') {
            $adminCount = User::where('role', 'admin')->count();
            
            if ($adminCount <= 1) {
                return response()->json([
                    'message' => 'Action denied. This is the only Admin account remaining.'
                ], 403);
            }
        }

        if ($user->role === 'admin' && auth()->id() !== $user->id) {
            return response()->json([
                'message' => 'You cannot delete other Admin accounts. Admins can only remove themselves.'
            ], 403);
        }

        // If it passes the checks (or if it's just a regular gasman), delete them!
        $user->delete();

        return response()->json(['message' => 'Staff deleted successfully']);
    }
}