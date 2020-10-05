<?php

namespace App\Http\Controllers;

use App\Models\Project;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
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
        return response(ProjectResource::collection(auth()->user()->projects()->orderBy('created_at', 'desc')->get()));
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
        $validatedData['slug'] = Str::of($validatedData['title'])->slug('-')->append('-' . now()->format('Y-m-d') . '-' . Str::of(auth()->user()->name)->slug('-')->__toString())->__toString();
        $newProject = auth()->user()->projects()->create($validatedData);

        return response(new ProjectResource($newProject), 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Project  $project
     * @return \Illuminate\Http\Response
     */
    public function show(Project $project)
    {
        return response(new ProjectResource($project));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Project  $project
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Project $project)
    {
        $validatedData = $request->validate($this->rules($project));
        $validatedData['slug'] = Str::of($validatedData['title'])->slug('-')->append('-' . now()->format('Y-m-d') . '-' . Str::of(auth()->user()->name)->slug('-')->__toString())->__toString();
        $project->update($validatedData);

        return response(new ProjectResource($project), 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Project  $project
     * @return \Illuminate\Http\Response
     */
    public function destroy(Project $project)
    {
        $project->delete();

        return response(['message' => 'Successfully deleted'], 200);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    private function rules($project = null)
    {
        return [
            'title' => ['required', 'string', $project ? Rule::unique('projects')->ignore($project) : Rule::unique('projects')->where(function ($query) {
                return $query->where('author', auth()->user()->id);
            }), 'max:255'],
            'description' => 'nullable',
            'deadline' => 'nullable',
            'repository_url' => 'nullable|url',
            'status_project_id' => 'required'
        ];
    }
}
