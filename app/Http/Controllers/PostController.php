<?php

namespace App\Http\Controllers;

use App\Enums\PostStatus;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Validation\Rules\Enum;

class PostController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $posts = Post::all();
        return response()->json($posts, 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:128',
            'content'=> 'required|string|max:1024',
            'status' => ['sometimes', new Enum(PostStatus::class)],
            'user_id' => 'required|integer|exists:users,id', // se usa solo para validar si no se protege a ruta
        ]);

        $post = Post::create($validated);

        // este se usa solo cuando se protege la ruta y accede un usuario autenticado
        // $post = $request->user()->posts()->create($validated);

        return response()->json($post, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $post = Post::findOrFail($id);

        return response()->json($post, 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {

        $post = Post::findOrFail($id);

        $validated = $request->validate([
            'title' => 'sometimes|string|min:3|max:128',
            'content'=> 'sometimes|string|min:3|max:1024',
            'status' => ['sometimes', new Enum(PostStatus::class)],
        ]);

        $post->update($validated);

        return response()->json($post, 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        Post::findOrFail($id)->delete();
        return response()->noContent();
    }
}
