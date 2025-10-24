<?php

use App\Http\Controllers\CategoryController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');

Route::middleware('auth:sanctum')->group(function () {
    //
});

Route::get('users/posts', [UserController::class, 'usersWithPosts']);
Route::get('users/{id}/posts', [UserController::class, 'userWithPosts']);
Route::apiResource('users', UserController::class);


Route::get('posts/by-status/{status}', [PostController::class, 'postsByStatus']);
Route::get('posts/{id}/user', [UserController::class, 'postWithUser']);
Route::apiResource('posts', PostController::class);


Route::apiResource('categories', CategoryController::class);
