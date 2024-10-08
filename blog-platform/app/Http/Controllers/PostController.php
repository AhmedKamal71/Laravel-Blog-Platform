<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Post;
use Tymon\JWTAuth\Facades\JWTAuth;

class PostController extends Controller
{
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required',
            'category' => 'required|in:Technology,Lifestyle,Education,News,Sports',
        ]);

        $user = JWTAuth::parseToken()->authenticate();

        $post = Post::create([
            'title' => $validatedData['title'],
            'content' => $validatedData['content'],
            'category' => $validatedData['category'],
            'author_id' => $user->id,
        ]);

        return response()->json(['message' => 'Post created successfully', 'post' => $post], 201);
    }

    public function index(Request $request)
    {
        $validatedData = $request->validate([
            'category' => 'nullable|string|max:255',
            'author_id' => 'nullable|integer|exists:users,id',
            'date_from' => 'nullable|date',
            'date_to' => 'nullable|date|after_or_equal:date_from',
        ]);

        $query = Post::query();

        if ($request->has('category')) {
            $query->where('category', $validatedData['category']);
        }

        if ($request->has('author_id')) {
            $query->where('author_id', $validatedData['author_id']);
        }

        if ($request->has('date_from') && $request->has('date_to')) {
            $query->whereBetween('created_at', [$validatedData['date_from'], $validatedData['date_to']]);
        }

        $posts = $query->paginate(10);

        return response()->json($posts);
    }

    public function show($id)
    {
        $post = Post::with('author')->find($id);

        if (!$post) {
            return response()->json(['message' => 'Post not found'], 404);
        }

        return response()->json($post);
    }

    public function update(Request $request, $id)
    {
        $post = Post::find($id);
        $user = JWTAuth::parseToken()->authenticate();

        if (!$post || ($post->author_id !== $user->id && $user->role !== 'admin')) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $this->validate($request, [
            'title' => 'required|string|max:255',
            'content' => 'required',
            'category' => 'required|in:Technology,Lifestyle,Education,News,Sports',
        ]);

        $post->update($request->only('title', 'content', 'category'));

        return response()->json(['message' => 'Post updated successfully', 'post' => $post]);
    }

    public function destroy($id)
    {
        $post = Post::find($id);
        $user = JWTAuth::parseToken()->authenticate();

        if (!$post || ($post->author_id !== $user->id && $user->role !== 'admin')) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $post->delete();

        return response()->json(['message' => 'Post deleted successfully']);
    }
}
