<?php

use App\Models\Project;
use App\Models\User;
use Laravel\Sanctum\Sanctum;

beforeEach(function () {
    $this->artisan('migrate:fresh --seed');
    $this->user = User::factory()->create();
    $this->project = $this->user->projects()->create(Project::factory()->raw());
});

test('Guest cannot access projects', function () {
    $response = $this->withHeaders([
        'Accept' => 'application/json'
    ])->get(route('projects.index'));

    $response
        ->assertStatus(401)
        ->assertJSon([
            'message' => 'Unauthenticated.'
        ]);
});

test('Guest cannot retrieve a single project', function () {
    $response = $this->withHeaders([
        'Accept' => 'application/json'
    ])->get(route('projects.show', $this->project->slug));

    $response
        ->assertStatus(401)
        ->assertJSon([
            'message' => 'Unauthenticated.'
        ]);
});

test('User can retrieve his projects', function () {
    Sanctum::actingAs(
        $this->user,
        ['*']
    );

    $response = $this->withHeaders([
        'Accept' => 'application/json'
    ])->get(route('projects.index'));

    $response
        ->assertStatus(200)
        ->assertJsonCount(1);
});

test('User can retrieve a single project', function () {
    Sanctum::actingAs(
        $this->user,
        ['*']
    );

    $response = $this->withHeaders([
        'Accept' => 'application/json'
    ])->get(route('projects.show', $this->project->slug));

    $response->assertOk();
});
