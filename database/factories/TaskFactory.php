<?php

namespace Database\Factories;

use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Factories\Factory;

class TaskFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Task::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $body = $this->faker->sentence(6);
        return [
            'body' => $body,
            'slug' => Str::of($body)->slug('-')->__toString(),
            'done' => $this->faker->boolean(),
            'author' => User::all()->pluck('id')->random(),
            'project_id' => Project::all()->pluck('id')->random()
        ];
    }
}
