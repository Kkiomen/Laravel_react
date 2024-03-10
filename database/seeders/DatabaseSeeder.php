<?php

namespace Database\Seeders;

use App\Models\Avatar;
use App\Models\Client;
use App\Models\Order;
use App\Models\OrderPosition;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        User::create([
            'name' => 'User',
            'email' => 'user@example.com',
            'password' => bcrypt('user'),
        ]);
    }
}
