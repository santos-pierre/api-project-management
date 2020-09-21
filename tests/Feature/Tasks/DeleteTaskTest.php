<?php

use App\Models\Task;
use App\Models\User;
use App\Models\Project;
use Laravel\Sanctum\Sanctum;


beforeEach(function () {
    $this->artisan('migrate:fresh --seed');

    $this->user = User::factory()->create();
    $this->userProject = $this->user->projects()->create(Project::factory()->raw());
    $this->tasksUserForProject = Task::factory()->count(5)->create(['author' => $this->user->id, 'project_id' => $this->userProject->id]);
    $this->newTaskData = Task::factory()->raw(['author' => $this->user->id]);
});

test('guest cannot delete a task from a project', function () {

    $response = $this->withHeaders([
        'Accept' => 'application/json'
    ])->delete(route('tasks.destroy', [$this->userProject->id, $this->tasksUserForProject->first()->id]), $this->newTaskData);

    $response
        ->assertStatus(401)
        ->assertJson([
            'message' => 'Unauthenticated.'
        ]);;
});

test('user can delete a task from a project', function () {
    Sanctum::actingAs(
        $this->user,
        ['*']
    );

    $response = $this->withHeaders([
        'Accept' => 'application/json'
    ])->delete(route('tasks.destroy', [$this->userProject->id, $this->tasksUserForProject->first()->id]), $this->newTaskData);

    $response
        ->assertStatus(200)
        ->assertExactJson(['message' => 'Successfully deleted']);

    $this->assertDeleted('tasks', ['id' => $this->tasksUserForProject->first()->id]);
});

test('user cannot delete an unexisting task from a project', function () {
    Sanctum::actingAs(
        $this->user,
        ['*']
    );

    $response = $this->withHeaders([
        'Accept' => 'application/json'
    ])->delete(route('tasks.destroy', [$this->userProject->id, 55152515]), $this->newTaskData);

    $response
        ->assertStatus(404)
        ->assertExactJson(['error' => 'not found', 'error_message' => 'Please check the URL you submitted']);
});
