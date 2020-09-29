<?php

use App\Models\Project;
use App\Models\User;
use Laravel\Sanctum\Sanctum;

beforeEach(function () {
    $this->artisan('migrate:fresh --seed');
    $this->user = User::factory()->create();
    $this->projects = Project::all();
});

test('Guest cannot delete project', function () {
    $response = $this->withHeaders([
        'Accept' => 'application/json'
    ])->delete(route('projects.destroy', $this->projects->first()->slug));

    $response
        ->assertStatus(401)
        ->assertJSon([
            'message' => 'Unauthenticated.'
        ]);
});

test('User can delete a project', function () {
    Sanctum::actingAs(
        $this->user,
        ['*']
    );

    $response = $this->withHeaders([
        'Accept' => 'application/json'
    ])->delete(route('projects.destroy', $this->projects->first()->slug));

    $response
        ->assertStatus(200)
        ->assertExactJson(['message' => 'Successfully deleted']);

    $this->assertDeleted('projects', ['slug' => $this->projects->first()->slug]);
});

test('User cannot delete an unexisting project', function () {
    Sanctum::actingAs(
        $this->user,
        ['*']
    );

    $response = $this->withHeaders([
        'Accept' => 'application/json'
    ])->delete(route('projects.destroy', 566327637));

    $response
        ->assertStatus(404)
        ->assertExactJson(['error' => 'not found', 'error_message' => 'Please check the URL you submitted']);
});
