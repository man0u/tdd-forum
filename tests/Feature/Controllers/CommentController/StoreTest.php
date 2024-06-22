<?php

use App\Models\Comment;
use App\Models\Post;
use App\Models\User;
use function Pest\Laravel\actingAs;

it('can store a comment', function () {
    $user = User::factory()->create();
    $post = Post::factory()->create();

    actingAs($user)->post(route('posts.comments.store', $post), [
        'body' => 'This is a comment'
    ]);

    $this->assertDatabaseHas(Comment::class, [
       'user_id' => $user->id,
       'post_id' => $post->id,
       'body' => 'This is a comment'
    ]);
});
