<?php

use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Http\UploadedFile;
use Laravel\Sanctum\Sanctum;

beforeEach(function () {
    $this->artisan('migrate:fresh --seed');
    $this->user = User::factory()->create(['name' => 'John', 'email' => 'john@admin.com']);
});

test('guest cannot update', function () {
    $response = $this->withHeaders([
        'Accept' => 'application/json'
    ])->post(route('users.update'), [
        'name' => 'hello',
        'email' => 'mynewemail@gmail.com',
        'photo' => UploadedFile::fake()->image('profile.jpg')
    ]);

    $response
        ->assertStatus(401)
        ->assertJSon([
            'message' => 'Unauthenticated.'
        ]);;
});

test('user can update his profile', function () {
    Sanctum::actingAs(
        $this->user,
        ['*']
    );
    // dd(UploadedFile::fake()->image('profile'));


    $response = $this->withHeaders([
        'Accept' => 'application/json'
    ])->post(route('users.update'), [
        'name' => 'hello',
        'email' => 'mynewemail@gmail.com',
        'photo' => UploadedFile::fake()->image('profile.jpg'),
        'password' => '',
        'password_confirmation' => '',
    ]);

    $response
        ->assertStatus(200)
        ->assertJSon([
            'name' => 'Hello',
            'email' => 'mynewemail@gmail.com',
            'photo' => Str::of(env('APP_URL'))->append($this->user->profile_photo_path)->__toString()
        ]);;
});

test('user cannot update his profile with an empty name', function () {
    Sanctum::actingAs(
        $this->user,
        ['*']
    );

    $response = $this->withHeaders([
        'Accept' => 'application/json'
    ])->post(route('users.update'), [
        'name' => '',
        'email' => 'mynewemail@gmail.com',
        'photo' => UploadedFile::fake()->image('profile.jpg')
    ]);

    $response
        ->assertStatus(422)
        ->assertJsonStructure([
            'message',
            'errors' => [
                'name'
            ]
        ]);
});

test('user cannot update his profile with an empty email', function () {
    Sanctum::actingAs(
        $this->user,
        ['*']
    );

    $response = $this->withHeaders([
        'Accept' => 'application/json'
    ])->post(route('users.update'), [
        'name' => 'hello',
        'email' => '',
        'photo' => UploadedFile::fake()->image('profile.jpg')
    ]);

    $response
        ->assertStatus(422)
        ->assertJsonStructure([
            'message',
            'errors' => [
                'email'
            ]
        ]);
});

test('user cannot update his profile with an invalid file', function () {
    Sanctum::actingAs(
        $this->user,
        ['*']
    );

    $response = $this->withHeaders([
        'Accept' => 'application/json'
    ])->post(route('users.update'), [
        'name' => 'hello',
        'email' => '',
        'photo' => UploadedFile::fake()->create('image.pdf', 1000)
    ]);

    $response
        ->assertStatus(422)
        ->assertJsonStructure([
            'message',
            'errors' => [
                'photo'
            ]
        ]);
});

test('user can update his password', function () {
    Sanctum::actingAs(
        $this->user,
        ['*']
    );

    $response = $this->withHeaders([
        'Accept' => 'application/json'
    ])->post(route('users.update'), [
        'name' => 'Hello',
        'email' => 'mynewemail@gmail.com',
        'password' => 'mynewpassword',
        'password_confirmation' => 'mynewpassword',
    ]);

    $response
        ->assertStatus(200)
        ->assertJSon([
            'name' => 'Hello',
            'email' => 'mynewemail@gmail.com',
            'photo' => null
        ]);
});

test('wrong password confirmation', function () {
    Sanctum::actingAs(
        $this->user,
        ['*']
    );

    $response = $this->withHeaders([
        'Accept' => 'application/json'
    ])->post(route('users.update'), [
        'name' => 'Hello',
        'email' => 'mynewemail@gmail.com',
        'password' => 'mynewpassword',
        'password_confirmation' => 'mynewpasswordc',
    ]);

    $response
        ->assertStatus(422)
        ->assertJsonStructure([
            'message',
            'errors' => [
                'password'
            ]
        ]);
});

test('wrong password length', function () {
    Sanctum::actingAs(
        $this->user,
        ['*']
    );

    $response = $this->withHeaders([
        'Accept' => 'application/json'
    ])->post(route('users.update'), [
        'name' => 'Hello',
        'email' => 'mynewemail@gmail.com',
        'password' => 'myn',
        'password_confirmation' => 'myn',
    ]);

    $response
        ->assertStatus(422)
        ->assertJsonStructure([
            'message',
            'errors' => [
                'password'
            ]
        ]);
});
