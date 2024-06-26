<?php

use App\Models\Comment;

use App\Models\User;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\put;

it('requires authentication', function () {
    put(route('comments.update', Comment::factory()->create()))
        ->assertRedirectToRoute('login');
});

it('can update a comment', function () {
    $comment = Comment::factory()->create([
        'body' => 'This is the old body'
    ]);
    $newBody = 'This is the new body';

    actingAs($comment->user)
        ->put(route('comments.update', $comment), ['body' => $newBody]);

    $this->assertDatabaseHas(Comment::class, [
        'id' => $comment->id,
        'body' => $newBody
    ]);

});

it('redirects to the post show page', function () {
    $comment = Comment::factory()->create();

    actingAs($comment->user)
        ->put(route('comments.update', $comment), ['body' => 'This is the new body'])
        ->assertRedirectToRoute('posts.show', $comment->post);
});

it('redirects to the correct page of comments', function () {
    $comment = Comment::factory()->create();

    actingAs($comment->user)
        ->put(route('comments.update', ['comment' => $comment, 'page' => 2]), ['body' => 'This is the new body'])
        ->assertRedirectToRoute('posts.show', ['post' => $comment->post, 'page' => 2]);
});

it('cannot update a comment of other user', function () {
    $comment = Comment::factory()->create();

    actingAs(User::factory()->create())
        ->put(route('comments.update', ['comment' => $comment]), ['body' => 'This is the new body'])
        ->assertForbidden();
});

it('it requires a valid body', function ($value) {
    $comment = Comment::factory()->create();

    actingAs($comment->user)
        ->put(route('comments.update', $comment), [
            'body' => $value
        ])
        ->assertInvalid('body');
})->with([
    null,
    1,
    true,
    1.5,
    str_repeat('a', 2501)
]);