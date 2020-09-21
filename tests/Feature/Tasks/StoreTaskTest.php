<?php

use App\Models\Task;
use App\Models\User;
use App\Models\Project;
use Illuminate\Support\Str;
use Laravel\Sanctum\Sanctum;
use Illuminate\Support\Carbon;

beforeEach(function () {
    $this->artisan('migrate:fresh --seed');

    $this->user = User::factory()->create();
    $this->userProject = $this->user->projects()->create(Project::factory()->raw());
    $this->tasksUserForProject = Task::factory()->count(5)->create(['author' => $this->user->id, 'project_id' => $this->userProject->id]);
    $this->newTaskData = Task::factory()->raw(['author' => $this->user->id]);
});

test('guest cannot create a task for a project', function () {
    $response = $this->withHeaders([
        'Accept' => 'application/json'
    ])->post(route('tasks.store', [$this->userProject->id]), $this->newTaskData);

    $response
        ->assertStatus(401)
        ->assertJson([
            'message' => 'Unauthenticated.'
        ]);;
});

test('user can create a task for a project', function () {
    Sanctum::actingAs(
        $this->user,
        ['*']
    );
    $response = $this->withHeaders([
        'Accept' => 'application/json'
    ])->post(route('tasks.store', [$this->userProject->id]), $this->newTaskData);

    $response
        ->assertStatus(201)
        ->assertJson([
            'slug' => $this->newTaskData['slug'],
            'body' => Str::of($this->newTaskData['body'])->title()->__toString(),
            'owner' => [
                'name' => Str::of($this->user->name)->title()->__toString(),
                'email' => $this->user->email,
            ],
            'project' => [
                'slug' => $this->userProject->slug,
                'title' => Str::of($this->userProject->title)->title()->__toString(),
                'author' => $this->userProject->owner->name,
                'description' => $this->userProject->description,
                'deadline' => Carbon::create($this->userProject->deadline)->unix(),
                'repository_url' => $this->userProject->repository_url,
                'status' => $this->userProject->status->name,
            ],
            'done' => $this->newTaskData['done'],
        ]);
    $this->assertDatabaseHas('tasks', ['body' => $this->newTaskData['body'], 'slug' => $this->newTaskData['slug']]);
});

test('user cannot create a task without a body', function () {
    Sanctum::actingAs(
        $this->user,
        ['*']
    );

    $newTask = Task::factory()->raw(['body' => '']);

    $response = $this->withHeaders([
        'Accept' => 'application/json'
    ])->post(route('tasks.store', [$this->userProject->id]), $newTask);


    $response
        ->assertStatus(422)
        ->assertJsonStructure([
            'message',
            'errors' => [
                'body'
            ]
        ]);
});

test('user cannot create a task with a body bigger than 255 characters', function () {
    Sanctum::actingAs(
        $this->user,
        ['*']
    );

    $newTask = Task::factory()->raw(['body' => Str::random(256)]);

    $response = $this->withHeaders([
        'Accept' => 'application/json'
    ])->post(route('tasks.store', [$this->userProject->id]), $newTask);


    $response
        ->assertStatus(422)
        ->assertJsonStructure([
            'message',
            'errors' => [
                'body'
            ]
        ]);
});


test('guest cannot update a task', function () {
    $response = $this->withHeaders([
        'Accept' => 'application/json'
    ])->patch(route('tasks.update', [$this->userProject->id, $this->tasksUserForProject->first()->id]), ['body' => 'hey', 'done' => true]);

    $response
        ->assertStatus(401)
        ->assertJson([
            'message' => 'Unauthenticated.'
        ]);
});

test('user can update a task', function () {
    Sanctum::actingAs(
        $this->user,
        ['*']
    );

    $response = $this->withHeaders([
        'Accept' => 'application/json'
    ])->patch(route('tasks.update', [$this->userProject->id, $this->tasksUserForProject->first()->id]), ['body' => 'hey', 'done' => true]);

    $response
        ->assertStatus(200)
        ->assertJson([
            'slug' => 'hey',
            'body' => 'Hey',
            'owner' => [
                'name' => Str::of($this->user->name)->title()->__toString(),
                'email' => $this->user->email,
            ],
            'project' => [
                'slug' => $this->userProject->slug,
                'title' => Str::of($this->userProject->title)->title()->__toString(),
                'author' => $this->userProject->owner->name,
                'description' => $this->userProject->description,
                'deadline' => Carbon::create($this->userProject->deadline)->unix(),
                'repository_url' => $this->userProject->repository_url,
                'status' => $this->userProject->status->name,
            ],
            'done' => true,
        ]);
    $this->assertDatabaseHas('tasks', ['body' => 'hey', 'slug' => 'Hey']);
});


test('user cannot update a task without a body', function () {
    Sanctum::actingAs(
        $this->user,
        ['*']
    );

    $response = $this->withHeaders([
        'Accept' => 'application/json'
    ])->patch(route('tasks.update', [$this->userProject->id, $this->tasksUserForProject->first()->id]), ['body' => '', 'done' => true]);

    $response
        ->assertStatus(422)
        ->assertJsonStructure([
            'message',
            'errors' => [
                'body'
            ]
        ]);
});
