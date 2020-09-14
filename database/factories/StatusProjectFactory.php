<?php

namespace Database\Factories;

use App\Models\StatusProject;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class StatusProjectFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = StatusProject::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $name = $this->faker->word;
        $slug = Str::of($name)->slug('-')->__toString();
        return [
            'name' => $name,
            'slug' => $slug
        ];
    }
}
