<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Group;

class DatabaseSeeder extends Seeder {
    public function run(): void {
        User::firstOrCreate(
            ['username' => 'admin'],
            [
                'display_name' => 'Administrator',
                'name' => 'Administrator',
                'email' => null,
                'password' => Hash::make('changeMeNow'),
                'role' => 'admin',
            ]
        );

        foreach (['1g','2E','Plus-Klasse'] as $name) {
            Group::firstOrCreate(['name' => $name]);
        }
    }
}
