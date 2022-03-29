<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Reservation;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $currentDateTime = Carbon::now();
        $reservations = Reservation::where('finished_at','>',$currentDateTime)->where('user_id',Auth::user()->id)->get();

        if(Auth::User()->admin === 1 && Auth::User()->active === 1)
            return view('users.dashboard');
        elseif(Auth::User()->active === 1)
            return view('salarie.dashboard', compact('currentDateTime' , 'reservations'));
        else
        Auth::guard('web')->logout();
        return redirect('/login')->with('status','Votre compte est en attente de validation');
    }
}
