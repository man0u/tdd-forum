<?php

use App\Models\Comment;
use App\Models\User;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\delete;

it('guest cannot delete a comment', function () {
    delete(route('comments.destroy', Comment::factory()->create()))
        ->assertRedirect(route('login'));
});


it('users cannot delete a comment from other users', function () {
    $comment = Comment::factory()->create();

    actingAs(User::factory()->create())
        ->delete(route('comments.destroy', $comment))
        ->assertForbidden();
});


it('users cannot delete a comment posted over an hour ago', function () {
    $this->freezeTime();

    $comment = Comment::factory()->create();

    $this->travel(1)->hour();

    actingAs($comment->user)
        ->delete(route('comments.destroy', $comment))
        ->assertForbidden();
});


it('can delete a comment', function () {
    $comment = Comment::factory()->create();

    actingAs($comment->user)
    ->delete(route('comments.destroy', $comment));

    $this->assertModelMissing($comment);
});


it('redirects to the post show page', function () {
    $comment = Comment::factory()->create();

    actingAs($comment->user)
        ->delete(route('comments.destroy', $comment))
        ->assertRedirect(route('posts.show', $comment->post_id));
});

it('redirects to the post show page with the page query param ', function () {
    $comment = Comment::factory()->create();

    actingAs($comment->user)
        ->delete(route('comments.destroy', ['comment' => $comment, 'page' => 2]))
        ->assertRedirect(route('posts.show', ['post' => $comment->post_id, 'page' => 2]));
});