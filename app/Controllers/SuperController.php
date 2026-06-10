<?php

namespace App\Controllers;

class SuperController extends BaseController
{
    public function panel()
    {
        return view('super/welcome_message');
    }
}
