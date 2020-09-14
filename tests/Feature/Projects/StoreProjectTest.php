<?php

use App\Models\Project;
use App\Models\StatusProject;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Laravel\Sanctum\Sanctum;
use function Pest\Faker\faker;

beforeEach(function () {
    $this->artisan('migrate:fresh --seed');
    $this->status = StatusProject::find(2);
    $this->user = User::factory()->create();
    $this->project = $this->user->projects()->create(Project::factory()->raw(['title' => 'My awesome Title', 'status_project_id' => 2]));
    $this->projectData = Project::factory()->raw(['status_project_id' => 2]);

    $this->projectToUpdate = $this->user->projects()->create(Project::factory()->raw(['status_project_id' => 2]));
});

test('Guest cannot create projects', function () {
    $response = $this->withHeaders([
        'Accept' => 'application/json'
    ])->get(route('projects.store'));

    $response
        ->assertStatus(401)
        ->assertJSon([
            'message' => 'Unauthenticated.'
        ]);
});

test('User can create a new project', function () {
    Sanctum::actingAs(
        $this->user,
        ['*']
    );

    $response = $this->withHeaders([
        'Accept' => 'application/json'
    ])->post(route('projects.store'), $this->projectData);

    $response
        ->assertStatus(201)
        ->assertJson([
            'slug' => $this->projectData['slug'],
            'title' => Str::of($this->projectData['title'])->title()->__toString(),
            'author' => $this->user->name,
            'description' => $this->projectData['description'],
            'deadline' => Carbon::create($this->projectData['deadline'])->unix(),
            'repository_url' => $this->projectData['repository_url'],
            'status' => $this->status->name,
        ]);
    $this->assertDatabaseHas('projects', ['title' => $this->projectData['title']]);
});

test('User can update a project title', function () {
    Sanctum::actingAs(
        $this->user,
        ['*']
    );

    $dataToUpdate = $this->projectToUpdate->attributesToArray();
    $dataToUpdate['title'] = 'My new Title';

    $response = $this->withHeaders([
        'Accept' => 'application/json'
    ])->patch(route('projects.update', $this->projectToUpdate->id), $dataToUpdate);

    $response
        ->assertStatus(200)
        ->assertJson([
            'slug' => Str::of($dataToUpdate['title'])->slug()->__toString(),
            'title' => Str::of($dataToUpdate['title'])->title()->__toString(),
            'author' => $this->user->name,
            'description' => $dataToUpdate['description'],
            'deadline' => Carbon::create($dataToUpdate['deadline'])->unix(),
            'repository_url' => $dataToUpdate['repository_url'],
            'status' => $this->status->name,
        ]);
    $this->assertDatabaseHas('projects', ['title' => $dataToUpdate['title']]);
});

test('User can update a project deadline', function () {
    Sanctum::actingAs(
        $this->user,
        ['*']
    );

    $dataToUpdate = $this->projectToUpdate->attributesToArray();
    $dataToUpdate['deadline'] = '2020-10-11';

    $response = $this->withHeaders([
        'Accept' => 'application/json'
    ])->patch(route('projects.update', $this->projectToUpdate->id), $dataToUpdate);

    $response
        ->assertStatus(200)
        ->assertJson([
            'slug' => Str::of($dataToUpdate['title'])->slug()->__toString(),
            'title' => Str::of($dataToUpdate['title'])->title()->__toString(),
            'author' => $this->user->name,
            'description' => $dataToUpdate['description'],
            'deadline' => Carbon::create($dataToUpdate['deadline'])->unix(),
            'repository_url' => $dataToUpdate['repository_url'],
            'status' => $this->status->name,
        ]);
    $this->assertDatabaseHas('projects', ['title' => $dataToUpdate['title'], 'deadline' => $dataToUpdate['deadline']]);
});

test('User can update a project description', function () {
    Sanctum::actingAs(
        $this->user,
        ['*']
    );

    $dataToUpdate = $this->projectToUpdate->attributesToArray();
    $dataToUpdate['description'] = faker()->text();

    $response = $this->withHeaders([
        'Accept' => 'application/json'
    ])->patch(route('projects.update', $this->projectToUpdate->id), $dataToUpdate);

    $response
        ->assertStatus(200)
        ->assertJson([
            'slug' => Str::of($dataToUpdate['title'])->slug()->__toString(),
            'title' => Str::of($dataToUpdate['title'])->title()->__toString(),
            'author' => $this->user->name,
            'description' => $dataToUpdate['description'],
            'deadline' => Carbon::create($dataToUpdate['deadline'])->unix(),
            'repository_url' => $dataToUpdate['repository_url'],
            'status' => $this->status->name,
        ]);
    $this->assertDatabaseHas('projects', ['title' => $dataToUpdate['title'], 'description' => $dataToUpdate['description']]);
});

test('User can update a project repository_url', function () {
    Sanctum::actingAs(
        $this->user,
        ['*']
    );

    $dataToUpdate = $this->projectToUpdate->attributesToArray();
    $dataToUpdate['repository_url'] = faker()->url;

    $response = $this->withHeaders([
        'Accept' => 'application/json'
    ])->patch(route('projects.update', $this->projectToUpdate->id), $dataToUpdate);

    $response
        ->assertStatus(200)
        ->assertJson([
            'slug' => Str::of($dataToUpdate['title'])->slug()->__toString(),
            'title' => Str::of($dataToUpdate['title'])->title()->__toString(),
            'author' => $this->user->name,
            'description' => $dataToUpdate['description'],
            'deadline' => Carbon::create($dataToUpdate['deadline'])->unix(),
            'repository_url' => $dataToUpdate['repository_url'],
            'status' => $this->status->name,
        ]);
    $this->assertDatabaseHas('projects', ['title' => $dataToUpdate['title'], 'repository_url' => $dataToUpdate['repository_url']]);
});


//Error handling store project

test('User cannot create a new project with an already existing title', function () {
    Sanctum::actingAs(
        $this->user,
        ['*']
    );

    $newProject = Project::factory()->raw(['title' => 'My awesome Title', 'status_project_id' => 2]);

    $response = $this->withHeaders([
        'Accept' => 'application/json'
    ])->post(route('projects.store'), $newProject);

    $response
        ->assertStatus(422)
        ->assertJsonStructure([
            'message',
            'errors' => [
                'title'
            ]
        ]);
});

test('User cannot create a new project without a title', function () {
    Sanctum::actingAs(
        $this->user,
        ['*']
    );

    $newProject = Project::factory()->raw(['title' => '', 'status_project_id' => 2]);

    $response = $this->withHeaders([
        'Accept' => 'application/json'
    ])->post(route('projects.store'), $newProject);

    $response
        ->assertStatus(422)
        ->assertJsonStructure([
            'message',
            'errors' => [
                'title'
            ]
        ]);
});

test('User cannot create a new project with a title that exceed 255 characters', function () {
    Sanctum::actingAs(
        $this->user,
        ['*']
    );

    $newProject = Project::factory()->raw(['title' => Str::random(300), 'status_project_id' => 2]);

    $response = $this->withHeaders([
        'Accept' => 'application/json'
    ])->post(route('projects.store'), $newProject);

    $response
        ->assertStatus(422)
        ->assertJsonStructure([
            'message',
            'errors' => [
                'title'
            ]
        ]);
});

test('User cannot create a new project without status project', function () {
    Sanctum::actingAs(
        $this->user,
        ['*']
    );

    $newProject = Project::factory()->raw(['status_project_id' => '']);

    $response = $this->withHeaders([
        'Accept' => 'application/json'
    ])->post(route('projects.store'), $newProject);

    $response
        ->assertStatus(422)
        ->assertJsonStructure([
            'message',
            'errors' => [
                'status_project_id'
            ]
        ]);
});

test('User cannot create a new project with an invalid url for the repository', function () {
    Sanctum::actingAs(
        $this->user,
        ['*']
    );

    $newProject = Project::factory()->raw(['repository_url' => 'not an url']);

    $response = $this->withHeaders([
        'Accept' => 'application/json'
    ])->post(route('projects.store'), $newProject);

    $response
        ->assertStatus(422)
        ->assertJsonStructure([
            'message',
            'errors' => [
                'repository_url'
            ]
        ]);
});

//Error handling update project

test('User cannot update with an already existing title', function () {
    Sanctum::actingAs(
        $this->user,
        ['*']
    );

    $newProject = Project::factory()->raw(['title' => 'My awesome Title', 'status_project_id' => 2]);

    $response = $this->withHeaders([
        'Accept' => 'application/json'
    ])->patch(route('projects.update', $this->projectToUpdate), $newProject);

    $response
        ->assertStatus(422)
        ->assertJsonStructure([
            'message',
            'errors' => [
                'title'
            ]
        ]);
});

test('User cannot update without a title', function () {
    Sanctum::actingAs(
        $this->user,
        ['*']
    );

    $newProject = Project::factory()->raw(['title' => '', 'status_project_id' => 2]);

    $response = $this->withHeaders([
        'Accept' => 'application/json'
    ])->patch(route('projects.update', $this->projectToUpdate), $newProject);

    $response
        ->assertStatus(422)
        ->assertJsonStructure([
            'message',
            'errors' => [
                'title'
            ]
        ]);
});

test('User cannot update with a title that exceed 255 characters', function () {
    Sanctum::actingAs(
        $this->user,
        ['*']
    );

    $newProject = Project::factory()->raw(['title' => Str::random(300), 'status_project_id' => 2]);

    $response = $this->withHeaders([
        'Accept' => 'application/json'
    ])->patch(route('projects.update', $this->projectToUpdate), $newProject);

    $response
        ->assertStatus(422)
        ->assertJsonStructure([
            'message',
            'errors' => [
                'title'
            ]
        ]);
});

test('User cannot update without status project', function () {
    Sanctum::actingAs(
        $this->user,
        ['*']
    );

    $newProject = Project::factory()->raw(['status_project_id' => '']);

    $response = $this->withHeaders([
        'Accept' => 'application/json'
    ])->patch(route('projects.update', $this->projectToUpdate), $newProject);

    $response
        ->assertStatus(422)
        ->assertJsonStructure([
            'message',
            'errors' => [
                'status_project_id'
            ]
        ]);
});

test('User cannot update with an invalid url for the repository', function () {
    Sanctum::actingAs(
        $this->user,
        ['*']
    );

    $newProject = Project::factory()->raw(['repository_url' => 'not an url']);

    $response = $this->withHeaders([
        'Accept' => 'application/json'
    ])->patch(route('projects.update', $this->projectToUpdate), $newProject);

    $response
        ->assertStatus(422)
        ->assertJsonStructure([
            'message',
            'errors' => [
                'repository_url'
            ]
        ]);
});
