<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        User::create([
            'name' => 'Example User',
            'email' => 'viandwicyber@gmail.com',
            'pin' => Hash::make('63945'),
            'password' => Hash::make('AlfianDwiN63945'),
        ]);
    }
}
