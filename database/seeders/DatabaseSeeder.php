<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::factory()->create([
            'name' => 'Admin',
            'email' => 'admin@example.com',
            'password' => 'password',
            'role' => 'admin'
        ]);

        User::factory()->create([
            'name' => 'Buyer',
            'email' => 'buyer@example.com',
            'password' => 'password',
            'role' => 'buyer',
        ]);

        User::factory()->create([
            'id' => 3,
            'name' => 'Seller1',
            'email' => 'seller1@example.com',
            'password' => 'password',
            'role' => 'seller'
        ]);

        User::factory()->create([
            'id' => 4,
            'name' => 'Seller2',
            'email' => 'seller2@example.com',
            'password' => 'password',
            'role' => 'seller',
        ]);

        Category::factory(10)->create();

        Product::factory(100)->create();
    }
}
