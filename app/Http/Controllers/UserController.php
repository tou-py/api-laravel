<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $users = User::paginate(10);
        return response()->json($users, 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:128',
            'email'=> 'required|email|max:128|unique:users,email',
            'password' => 'required|string|min:8'
        ]);

        $validated['password'] = Hash::make($validated['password']);

        $user = User::create($validated);

        return response()->json($user, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $user = User::findOrFail($id);

        return response()->json($user, 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {

        $user = User::findOrFail($id);

        $validated = $request->validate([
            'name' => 'sometimes|string|min:3|max:128',
            'email' => 'sometimes|email|min:3|max:128|unique:users,email,'.$id,
            'password' => 'sometimes|min:8'
        ]);

        if (isset($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        }

        $user->update($validated);

        return response()->json($user, 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        User::findOrFail($id)->delete();
        return response()->noContent();
    }

    public function userWithPosts (string $id) {
        $user = User::with('posts')->findOrFail($id);
        return response()->json($user, 200);
    }

    public function usersWithPosts () {
        $user = User::with('posts')->paginate(10);
        return response()->json($user, 200);
    }
}
