<?php

namespace Database\Seeders;

use App\Models\StatusProject;
use Illuminate\Support\Str;
use Illuminate\Database\Seeder;

class StatusProjectSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $status_project = [
            'done',
            'pending',
            'late',
            'give up'
        ];

        foreach ($status_project as $status) {
            StatusProject::factory()->create([
                'name' => $status,
                'slug' => Str::of($status)->slug('-')->__toString(),
            ]);
        }
    }
}
