<?php

namespace Database\Seeders;

use App\Models\ComboOrder;
use Illuminate\Database\Seeder;

class ComboOrderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        ComboOrder::factory(5000)->create();
    }
}
