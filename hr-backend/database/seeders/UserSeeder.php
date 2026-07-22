<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        $password = Hash::make('password123');

        \DB::table('users')->truncate();
        \DB::table('users')->insert([
            [
                'id' => 1,
                'name' => 'HR Admin',
                'email' => 'admin@gmail.com',
                'password' => $password,
                'role_id' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 2,
                'name' => 'HR Staff',
                'email' => 'staff@gmail.com',
                'password' => $password,
                'role_id' => 2,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 3,
                'name' => 'Interviewer',
                'email' => 'interviewer@gmail.com',
                'password' => $password,
                'role_id' => 3,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 4,
                'name' => 'Candidate',
                'email' => 'candidate@gmail.com',
                'password' => $password,
                'role_id' => 4,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
