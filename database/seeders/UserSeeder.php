<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;


class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = User ::create([
            'first_name' => "admin",
            'last_name' => "admin",
            'email' => "mhmdouali@gmail.com",
            'email_verified_at' => now(),
            'password' => Hash::make('password'),
            'remember_token' => Str::random(10),
            'phone' => '1234567890',
            'address' => '123 Main Street, City',
        ]);
        $user->assignRole('SuperAdmin');

    }
}
