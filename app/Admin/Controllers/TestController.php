<?php

namespace App\Admin\Controllers;

use App\Http\Controllers\Controller;

class TestController extends Controller
{
    public function simulator()
    {
        return view('admin.test.simulator');
    }
}
