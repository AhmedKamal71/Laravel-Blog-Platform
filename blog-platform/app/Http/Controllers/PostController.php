<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Post;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\Cache;

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

        Cache::forget('posts');

        return response()->json(['message' => 'Post created successfully', 'post' => $post], 201);
    }

    public function index(Request $request)
    {
        $validatedData = $request->validate([
            'category' => 'nullable|string|max:255',
            'title' => 'nullable|string|max:255',
            'author_id' => 'nullable|integer|exists:users,id',
            'date_from' => 'nullable|date',
            'date_to' => 'nullable|date|after_or_equal:date_from',
        ]);

        $cacheKey = 'posts_' . md5(serialize($validatedData));

        $posts = Cache::remember($cacheKey, now()->addMinutes(10), function () use ($validatedData) {
            $query = Post::query();

            if (!empty($validatedData['category'])) {
                $query->where('category', 'like', '%' . $validatedData['category'] . '%');
            }

            if (!empty($validatedData['title'])) {
                $query->where('title', 'like', '%' . $validatedData['title'] . '%');
            }

            if (!empty($validatedData['author_id'])) {
                $query->where('author_id', $validatedData['author_id']);
            }

            if (!empty($validatedData['date_from']) && !empty($validatedData['date_to'])) {
                $query->whereBetween('created_at', [$validatedData['date_from'], $validatedData['date_to']]);
            }

            return $query->paginate(10);
        });

        return response()->json($posts);
    }

    public function show($id)
    {
        $post = Post::with('user')->find($id);

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

        Cache::forget('posts');

        return response()->json(['message' => 'Post updated successfully', 'post' => $post]);
    }

    public function destroy($id)
    {
        $post = Post::find($id);
        $user = JWTAuth::parseToken()->authenticate();

        if (!$post) {
            return response()->json(['message' => 'Post not found'], 404);
        }

        if ($post->author_id !== $user->id && $user->role !== 'admin') {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $post->delete();

        Cache::forget('posts');

        return response()->json(['message' => 'Post deleted successfully']);
    }
}
