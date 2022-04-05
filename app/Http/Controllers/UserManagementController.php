<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Place;
use App\Models\Settings;
use App\Models\Reservation;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class UserManagementController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if($request->user()->can('viewAny',User::class))
        {
            $search = $request['search'] ?? "";
            if($search != "")
            {
                $salaries = User::where('name','LIKE',"%$search%")
                ->orWhere('email','LIKE',"%$search%")
                ->paginate(10);
            }
            else
            {
                $salaries = User::orderBy('name')->paginate(10);
            }
            return view('users.index', compact('salaries'));
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        if($request->user()->can('create',User::class))
        {
            return view('users.create');
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'email'=>[
                'required',
                'email',
                'unique:users,email'
            ],
            'name'=> 'required',
            'prenom'=> 'required',
        ]);

        $mdp = Str::random(8);
        $salarie = new User;
        $salarie->name = $request->name;
        $salarie->prenom = $request->prenom;
        $salarie->email = $request->email;
        $salarie->admin=0;
        $salarie->password = bcrypt($mdp);
        $salarie->save();

        //Mettre en place la notification

        return redirect('users')->with('status','Le salarié a bien été ajoutée');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $id)
    {
        $currentDateTime = Carbon::now();
        $salarie=User::findOrFail($id);
        if($request->user()->can('view', $salarie))
        {
            $reservations = DB::table('reservations')
            ->select('reservations.created_at', 'libelle', 'reservations.deleted_at', 'reservations.finished_at')
            ->join('users', 'reservations.user_id','=','users.id')
            ->join('places', 'reservations.place_id','=','places.id')
            ->where('user_id', $id)
            ->orderBy('reservations.created_at', 'desc')
            ->paginate(10);

            return view('salarie.history', compact('reservations', 'currentDateTime'));
        }
    }

    /**
     * Show the form for editing the specified resource.
     *->with('salarie')
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request,$id)
    {

        $salarie = User::findOrFail($id);
        if($request->user()->can('update', $salarie))
        {
            return view('users.edit', compact('salarie'));
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $salarie = User::findOrFail($id);
        if($request->user()->can('update', $salarie))
        {
            $request->validate([
                'email'=>[
                    'required',
                    'email',
                ],
                'name'=> 'required',
                'prenom'=> 'required',
            ]);
            $salarie->update($request->input());
            return redirect('users')->with('status','Les informations ont bien été modifiées');
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {

    }

    public function remove(Request $request, $id)
    {
        if($request->user()->can('viewAny',User::class))
        {
            $salarie = User::findOrFail($id);
            return view('users.remove', compact('salarie'));
        }
    }

    public function delete($id)
    {
        $salarie = User::findOrFail($id);
        $salarie->delete();
        return redirect('users')->with('status','Le salarié a été supprimé');
    }

    public function reserve()
    {
        $currentDateTime = Carbon::now();
        $currentSetting = Settings::select('duree')->value('duree');
        $libres = Reservation::select('place_id')->where('finished_at','>',$currentDateTime)->get();
        $places = Place::select('id')->get();
        $result = $places->diffKeys($libres);

        $reservation = new Reservation;
        $reservation->user_id = Auth::user()->id;
        $reservation->place_id = $result->first()->id;
        $reservation->finished_at = Carbon::now()->addDays($currentSetting);
        $reservation->save();

        return redirect(url()->previous());
    }

    public function dereserve($id)
    {
        $reservation = Reservation::findOrFail($id);
        $reservation->delete();
        return redirect('dashboard')->with('reservation')->with('status','Action effectué');
    }
    
    public function activate($id)
    {
        $salarie = User::findOrFail($id);
        $salarie->active = 1;
        $salarie->update();

        return redirect('users')->with('salarie')
        ->with('status', 'Le compte a été validé');
    }

    public function setduree(Request $request)
    {
        $duree = Settings::select('duree')->value('duree');
        if($request->user()->can('viewAny',User::class))
        {
            return view('users.setduree', compact('duree'));
        }
    }

    public function updateduree(Request $request)
    {
        $modif = $request['modif'];
        $setting = Settings::firstWhere('id', 1);
        $setting->duree = $modif;
        $setting->update();
        return redirect('users')
        ->with('status', 'Réglage effectué !');
    }
}
