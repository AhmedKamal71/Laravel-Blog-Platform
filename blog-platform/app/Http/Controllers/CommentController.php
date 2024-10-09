<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Models\Post;
use App\Models\Comment;


class CommentController extends Controller
{
    public function store(Request $request, $id)
    {
        $validatedData = $request->validate([
            'comment' => 'required|string',
        ]);

        $user = JWTAuth::parseToken()->authenticate();
        $post = Post::find($id);

        if (!$post) {
            return response()->json(['message' => 'Post not found'], 404);
        }

        if (!$user) {
            return response()->json(['message' => 'Unauthorized'], 404);
        }

        $comment = $post->comments()->create([
            'user_id' => $user->id,
            'comment' => $validatedData['comment'],
        ]);

        return response()->json(['message' => 'Comment created successfully', 'comment' => $comment], 201);
    }

    public function update(Request $request, $id)
    {
        $validatedData = $request->validate([
            'comment' => 'required|string',
        ]);

        $user = JWTAuth::parseToken()->authenticate();
        $comment = Comment::find($id);

        if (!$comment) {
            return response()->json(['message' => 'Comment not found'], 404);
        }

        if ($comment->user_id !== $user->id) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $comment->update($validatedData);

        return response()->json(['message' => 'Comment updated successfully', 'comment' => $comment], 200);
    }

    public function destroy($id)
    {
        $user = JWTAuth::parseToken()->authenticate();
        $comment = Comment::find($id);

        if (!$comment) {
            return response()->json(['message' => 'Comment not found'], 404);
        }

        if ($comment->user_id !== $user->id && $user->role !== 'admin') {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $comment->delete();

        return response()->json(['message' => 'Comment deleted successfully'], 200);
    }

    public function index()
    {
        $user = JWTAuth::parseToken()->authenticate();
        if ($user->role === 'admin') {
            $comments = Comment::all();
            return response()->json(["All Comments" => $comments], 200);
        } else {
            $comments = Comment::where('user_id', $user->id)->get();
            return response()->json(["Your Comments" => $comments], 200);
        }
    }
}
