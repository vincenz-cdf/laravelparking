<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        if(Auth::User()->admin == 1)
            return view('users.dashboard');
        else
            return view('salarie.dashboard');
    }
}
