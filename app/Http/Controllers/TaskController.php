<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\Project;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Http\Resources\TaskResource;

class TaskController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Project $project)
    {
        return TaskResource::collection($project->tasks);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Project $project, Request $request)
    {
        $validatedData = $request->validate($this->rules());
        $validatedData['slug'] = Str::of($validatedData['body'])->slug('-')->__toString();
        $validatedData['author'] = auth()->user()->id;
        $newTask = $project->tasks()->create($validatedData);

        return response(new TaskResource($newTask), 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Task  $task
     * @return \Illuminate\Http\Response
     */
    public function show(Project $project, Task $task)
    {
        return new TaskResource($task);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Task  $task
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Project $project, Task $task)
    {
        $validatedData = $request->validate($this->rules($task));
        $validatedData['slug'] = Str::of($validatedData['body'])->slug('-')->__toString();
        $task->update($validatedData);

        return response(new TaskResource($task), 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Task  $task
     * @return \Illuminate\Http\Response
     */
    public function destroy(Project $project, Task $task)
    {
        $task->delete();

        return response(['message' => 'Successfully deleted'], 200);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    private function rules($task = null)
    {
        return [
            'body' => ['required', 'string', 'max:255'],
            'done' => 'nullable',
        ];
    }
}
