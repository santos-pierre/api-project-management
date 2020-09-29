<?php

use App\Models\Task;
use App\Models\Project;
use App\Models\User;
use Laravel\Sanctum\Sanctum;

beforeEach(function () {
    $this->artisan('migrate:fresh --seed');
    $this->project = Project::factory()->create();
    $this->tasks = Task::factory()->count(5)->create(['project_id' => $this->project->id]);

    $this->user = User::factory()->create();
    $this->userProject = $this->user->projects()->create(Project::factory()->raw());
    $this->tasksUserForProject = Task::factory()->count(5)->create(['author' => $this->user->id, 'project_id' => $this->userProject->id]);
});

test('guest cannot retrieve tasks from a project', function () {
    $response = $this->withHeaders([
        'Accept' => 'application/json'
    ])->get(route('tasks.index', $this->project->slug));

    $response
        ->assertStatus(401)
        ->assertJSon([
            'message' => 'Unauthenticated.'
        ]);;
});

test('guest cannot retrieve a single tasks from a project', function () {
    $response = $this->withHeaders([
        'Accept' => 'application/json'
    ])->get(route('tasks.show', [$this->project->slug, $this->tasks->first()->id]));

    $response
        ->assertStatus(401)
        ->assertJSon([
            'message' => 'Unauthenticated.'
        ]);;
});

test('User can retrieve tasks from a project', function () {
    Sanctum::actingAs(
        $this->user,
        ['*']
    );

    $response = $this->withHeaders([
        'Accept' => 'application/json'
    ])->get(route('tasks.index', $this->userProject->slug));

    $response
        ->assertStatus(200)
        ->assertJsonStructure(['data']);
});

test('User can retrieve a single task from a project', function () {
    Sanctum::actingAs(
        $this->user,
        ['*']
    );

    $response = $this->withHeaders([
        'Accept' => 'application/json'
    ])->get(route('tasks.show', [$this->userProject->slug, $this->tasksUserForProject->first()->id]));

    $response
        ->assertStatus(200)
        ->assertJsonStructure([
            'data' => [
                'slug',
                'body',
                'owner',
                'project',
                'done'
            ]
        ]);
});
