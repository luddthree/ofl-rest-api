<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class UserController extends Controller
{
    public function register(Request $request)
    {
        // registerer bruker
        $validated = $request->validate([
            'name' => 'required|string',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:8|confirmed',
            'role' => 'required|in:user,admin',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => $validated['role'],
        ]);

        return response()->json(['user' => $user], 201);
    }


    public function login(Request $request)
    {
        // login funksjon
        $validated = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (!Auth::attempt($validated)) {
            return response()->json(['error' => 'Invalid credentials'], 401);
        }

        $user = Auth::user();
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'Login successful',
            'user' => $user,
            'token' => $token,
        ]);
    }

public function getAuthenticatedUser(Request $request)
{
    // sjekker om bruker er autentisert
    if (!$request->user()) {
        \Log::info('User is not authenticated');
        return response()->json(['error' => 'Unauthenticated'], 401);
    }

    \Log::info('Authenticated user:', ['user' => $request->user()]);

    return response()->json([
        'user' => $request->user(),
    ]);
}

    public function updateProfile(Request $request)
    {
        // oppdaterer brukerprofil
        $user = Auth::user();
        $validated = $request->validate([
            'name' => 'sometimes|string',
            'email' => 'sometimes|email|unique:users,email,' . $user->id,
        ]);

        $user->update($validated);
        return response()->json(['user' => $user]);
    }

    public function updatePassword(Request $request)
    {
        // oppdaterer passord
        $request->validate([
            'current_password' => 'required',
            'new_password' => 'required|min:8|confirmed',
        ]);

        $user = Auth::user();
        if (!Hash::check($request->current_password, $user->password)) {
            return response()->json(['error' => 'Current password is incorrect'], 403);
        }

        $user->update(['password' => Hash::make($request->new_password)]);
        return response()->json(['message' => 'Password updated successfully']);
    }

}