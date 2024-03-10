<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class TestController
{

    public function __construct(){}

    public function test(Request $request)
    {
        User::create([
            'name' => 'User',
            'email' => 'user@example.com',
            'password' => bcrypt('user'),
        ]);
        dd("WORKING!");
    }
}
