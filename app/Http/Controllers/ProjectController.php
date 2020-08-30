<?php

namespace App\Http\Controllers;

use App\Project;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Resources\ProjectResource;

class ProjectController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return ProjectResource::collection(auth()->user()->projects);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate($this->rules());
        $validatedData['slug'] = Str::of($validatedData['title'])->slug('-')->__toString();
        $newProject = auth()->user()->projects()->create($validatedData);

        return response(new ProjectResource($newProject), 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Project  $project
     * @return \Illuminate\Http\Response
     */
    public function show(Project $project)
    {
        return new ProjectResource($project);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Project  $project
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Project $project)
    {
        $validatedData = $request->validate($this->rules());
        $validatedData['slug'] = Str::of($validatedData['title'])->slug('-')->__toString();
        $project->update($validatedData);

        return response(new ProjectResource($project), 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Project  $project
     * @return \Illuminate\Http\Response
     */
    public function destroy(Project $project)
    {
        $project->delete();

        return response(["message" => "Successfully deleted"], 200);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    private function rules()
    {
        return [
            'title' => 'required|unique:projects|max:255',
            'description' => 'nullable',
            'deadline' => 'nullable',
            'repository_url' => 'nullable|url',
            'status_project_id' => 'required'
        ];
    }
}
