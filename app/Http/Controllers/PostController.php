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
        $posts = Post::with('user', 'categories:id,name')->paginate(10);
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
            'categories' => 'sometimes|array',
            'categories.*' => 'exists:categories,id',
            'user_id' => 'required|integer|exists:users,id', //se puede cambiar por auth()->id() si se protege la ruta
        ]);

        $post = Post::create($validated);

        if ($request->has('categories')) {
            $post->categories()->attach($request->input('categories'));
        }

        $post->load('user', 'categories');

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
            'categories' => 'sometimes|array',
            'categories.*' => 'exists:categories,id',
            'status' => ['sometimes', new Enum(PostStatus::class)],
        ]);

        $post->update(collect($validated)->except('categories')->toArray());

        if (isset($validated['categories'])) {
            $post->categories()->sync($validated['categories']);
        }

        $post->load('categories');

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

    public function postWithUser(string $id) {
        $post = Post::with('user', 'categories')->findOrFail($id);
        return response()->json($post, 200);
    }

    public function postsByStatus(Request $request, string $status) {

        if (!PostStatus::tryFrom($status)) {
            return response()->json([
                'message' => 'El estado proporcionado no es valido'
            ], 422);
        }

        $posts = Post::with('user:id,name', 'categories:id,name')->where('status', $status)->get();
        return response()->json($posts, 200);
    }
}
