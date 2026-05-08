<?php

namespace App\Controllers;

use App\Controllers\BaseController;

class StaffController extends BaseController
{
    public function index()
    {
        return view('staff/index');
    }
}
