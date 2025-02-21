<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;

class TasksTableSeeder extends Seeder
{
    public function run()
    {
        $faker = Faker::create();

        // Menambahkan 25 task dengan user_id = 8
        for ($i = 0; $i < 25; $i++) {
            DB::table('tasks')->insert([
                'user_id' => 8,
                'judul' => $faker->sentence(5),
                'detail' => $faker->text(200),
                'deadline_date' => $faker->date(),
                'deadline_time' => $faker->time(),
                'status' => $faker->randomElement(['open', 'in_progress', 'done']),
                'prioritas' => $faker->randomElement(['high', 'medium', 'low']),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
