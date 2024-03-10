<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class TestController
{

    public function __construct(){}

    public function test(Request $request)
    {
        dd("WORKING!");
    }
}
