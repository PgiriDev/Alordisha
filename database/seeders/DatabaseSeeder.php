<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call(KnowledgeItemSeeder::class);

        // ---------------------------
        // 1ST ADMIN USER
        // ---------------------------
        User::updateOrCreate(
            ['phone' => '7384979072'],
            [
                'name'     => 'Prakash Giri',
                'email'    => 'prakash222326@gmail.com',
                'password' => Hash::make('Prakash72..'),
                'role'     => 'admin',
            ]
        );
        
        // 2ND ADMIN USER
        // ---------------------------
        User::updateOrCreate(
            ['phone' => '7407917787'],
            [
                'name'     => 'Satyaki Sir',
                'email'    => 'satyakimv@gmail.com',
                'password' => Hash::make('Satyaki@2018'),
                'role'     => 'admin',
            ]
        );

        // ---------------------------
        // TEACHER USER
        // ---------------------------
        User::updateOrCreate(
            ['phone' => '7384291529'],
            [
                'name'     => 'Satyaki Pahari',
                'email'    => 'adsp0000001@gmail.com',
                'password' => Hash::make('Satyaki@1508'),
                'role'     => 'teacher',
            ]
        );
    }
}
