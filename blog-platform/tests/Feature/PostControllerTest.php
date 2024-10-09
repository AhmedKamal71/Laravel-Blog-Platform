<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Post;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Tymon\JWTAuth\Facades\JWTAuth;

class PostControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test store method (creating a new post).
     */
    public function testStore()
    {
        $user = User::factory()->create();
        $token = JWTAuth::fromUser($user);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->postJson('/api/posts', [
            'title' => 'Test Post',
            'content' => 'This is the content of the test post.',
            'category' => 'Technology'
        ]);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'message',
                'post' => [
                    'id',
                    'title',
                    'content',
                    'category',
                    'author_id'
                ]
            ]);
    }


    public function testShow()
    {
        $post = Post::factory()->create();

        $response = $this->getJson('/api/posts/' . $post->id);

        $response->assertStatus(200)
            ->assertJson([
                'id' => $post->id,
                'title' => $post->title,
                'content' => $post->content,
                'category' => $post->category
            ]);
    }

    public function testUpdate()
    {
        $user = User::factory()->create(['role' => 'author']);
        $token = JWTAuth::fromUser($user);
        $post = Post::factory()->create(['author_id' => $user->id]);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->putJson('/api/posts/' . $post->id, [
            'title' => 'Updated Title',
            'content' => 'Updated content.',
            'category' => 'Technology'
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Post updated successfully',
                'post' => [
                    'id' => $post->id,
                    'title' => 'Updated Title',
                    'content' => 'Updated content.',
                ]
            ]);
    }

    public function testDestroy()
    {
        $user = User::factory()->create(['role' => 'author']);
        $token = JWTAuth::fromUser($user);
        $post = Post::factory()->create(['author_id' => $user->id]);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->deleteJson('/api/posts/' . $post->id);

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Post deleted successfully',
            ]);
    }
}
