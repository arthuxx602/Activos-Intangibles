<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    /**
     * Muestra el dashboard del administrador
     */
    public function index()
    {
        return view('Admin.dashboard');
    }
}
