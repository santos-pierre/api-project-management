<?php

namespace Database\Factories;

use App\Models\Project;
use App\Models\StatusProject;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProjectFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Project::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $title = $this->faker->sentence(4);
        $slug = Str::of($title)->slug('-')->__toString();
        return [
            'slug' => $slug,
            'title' => $title,
            'author' => function () {
                return User::factory()->create()->id;
            },
            'description' => $this->faker->text,
            'deadline' => now()->addDay(3)->format('Y-m-d'),
            'repository_url' => $this->faker->url,
            'status_project_id' => function () {
                return StatusProject::all()->pluck('id')->random();
            },
        ];
    }
}
