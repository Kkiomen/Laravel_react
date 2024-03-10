<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

/**
 * Controller to test developers
 */
class TestController
{

    public function __construct(){}

    public function test(Request $request)
    {
        dd("WORKING!");
    }
}
