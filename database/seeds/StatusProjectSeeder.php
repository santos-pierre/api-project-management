<?php

use App\StatusProject;
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
            factory(StatusProject::class)->create([
                'name' => $status,
                'slug' => Str::of($status)->slug('-')->__toString(),
            ]);
        }
    }
}
