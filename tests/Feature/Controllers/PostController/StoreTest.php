<?php

use App\Models\Post;
use App\Models\User;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\post;

beforeEach(function () {
    $this->validData = [
        'title' => str_repeat('a', 50),
        'body' => str_repeat('a', 500)
    ];
});

it('requires authentication', function () {
    post(route('posts.store'))->assertRedirectToRoute('login');
});

it('stores a post', function () {
    $user = User::factory()->create();

    actingAs($user)
        ->post(route('posts.store'), $this->validData);

    $this->assertDatabaseHas(Post::class, [
        ...$this->validData,
        'user_id' => $user->id
    ]);
});

it('redirects to post show page after store', function () {
    $user = User::factory()->create();

    actingAs($user)
        ->post(route('posts.store'), $this->validData)
        ->assertRedirectToRoute('posts.show', Post::latest('id')->first());

    $this->assertDatabaseHas(Post::class, [
        ...$this->validData,
        'user_id' => $user->id
    ]);
});

it('requires valid data', function (array $invalidData, array|string $errors) {

    actingAs( User::factory()->create())
        ->post(route('posts.store'), [...$this->validData, ...$invalidData])
        ->assertInvalid($errors);
})->with([
    [['title' => null], 'title'],
    [['title' => true], 'title'],
    [['title' => 1], 'title'],
    [['title' => 1.5], 'title'],
    [['title' => str_repeat('a', 121)], 'title'],
    [['title' => str_repeat('a', 9)], 'title'],
    [['body' => null] , 'body'],
    [['body' => true] , 'body'],
    [['body' => 1] , 'body'],
    [['body' => 1.5] , 'body'],
    [['body' => str_repeat('a', 10_001)] , 'body'],
    [['body' => str_repeat('a', 99)] , 'body'],
]);