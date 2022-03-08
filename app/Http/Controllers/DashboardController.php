<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        if(Auth::User()->admin === 1 && Auth::User()->active === 1)
            return view('users.dashboard');
        elseif(Auth::User()->active === 1)
            return view('salarie.dashboard');
        else
        Auth::guard('web')->logout();
        return redirect('/login')->with('status','Votre compte est en attente de validation');
    }
}
