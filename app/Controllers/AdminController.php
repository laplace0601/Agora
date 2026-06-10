<?php

namespace App\Controllers;

class AdminController extends BaseController
{
    public function comunidad()
    {
        return view('admin/comunidad');
    }

    public function finanzas()
    {
        return view('admin/finanzas');
    }
}
