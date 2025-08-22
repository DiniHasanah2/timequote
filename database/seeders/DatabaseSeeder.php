<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    
    /*public function run(): void
    {
        User::create([
            'name' => 'Admin User',
            'username' => 'admin',
            'email' => 'admin@example.com',
            'password' => Hash::make('admin123'),
            'role' => 'admin'
        ]);
        
        User::create([
            'name' => 'Presale User',
            'username' => 'presale',
            'email' => 'presale@example.com',
            'password' => Hash::make('presale123'),
            'role' => 'presale'
        ]);
        
        // Add more users as needed
        User::create([
            'name' => 'John Doe',
            'username' => 'john',
            'email' => 'john@example.com',
            'password' => Hash::make('password123'),
            'role' => 'presale'
        ]);
        $this->call(CustomerSeeder::class);
    }*/
    public function run(): void
{
    $this->call([
        PFlavourMapSeeder::class,
    ]);
}


}