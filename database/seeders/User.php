<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User as ModelUser;

class User extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        ModelUser::create([
            'name' => 'Test User',
            'email' => 'admin@example.com',
            'verifikasi' => 'accept',
            'password' => bcrypt('andy12345')
        ]);
    }
}
