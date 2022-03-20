<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Place;
use App\Models\Reservation;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class PlaceController extends Controller
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
            $currentDateTime = Carbon::now();
            $search = $request['search'] ?? "";
            if($search != "")
            {
                $places = Place::where('libelle','LIKE',"%$search%")->paginate(10);
            }
            else
            {
                $places = Place::orderBy('libelle')->paginate(10);
            }
            return view('places.index', compact('places', 'currentDateTime'));
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
            return view('places.create');
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
            'libelle'=> 'required',
        ]);

        $place = new Place;
        $place->libelle = $request->libelle;
        $place->save();

        return redirect('places')->with('status','La place a bien été ajoutée');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request,$id)
    {
        $place = Place::findOrFail($id);
        if(Auth::user()->admin === 1)
        {
            return view('places.edit', compact('place'));
        }
        else
        {
            return abort('403');
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
        $place = Place::findOrFail($id);
        if(Auth::user()->admin === 1)
        {
            $request->validate([
                'libelle'=> 'required',
            ]);

            $place->libelle = $request->input('libelle');
            $place->update();
            return redirect('places')->with('status','Les informations ont bien été modifiées');
        }
        else
        {
            return abort('403');
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
        //
    }

    public function downgrade(Request $request, $id)
    {
        if($request->user()->can('viewAny',User::class))
        {
            $place = Place::findOrFail($id);
            return view('places.remove', compact('place'));
        }
    }

    public function erase($id)
    {
        $place = Place::findOrFail($id);
        $place->forceDelete();
        return redirect('places')->with('status','La place a été supprimé');
    }

    public function history(Request $request)
    {
        $currentDateTime = Carbon::now();
        if($request->user()->can('viewAny',User::class))
        {
            $search = $request['search'] ?? "";
            if($search != "")
            {
                $reservations = DB::table('reservations')
                ->join('users', 'reservations.user_id','=','users.id')
                ->join('places', 'reservations.place_id','=','places.id')
                ->where('libelle','LIKE',"%$search%")
                ->orWhere('email','LIKE',"%$search%")
                ->paginate(10);
            }
            else
            {
                $reservations = DB::table('reservations')
                ->select('reservations.created_at', 'name', 'libelle', 'reservations.deleted_at', 'reservations.finished_at')
                ->join('users', 'reservations.user_id','=','users.id')
                ->join('places', 'reservations.place_id','=','places.id')
                ->orderBy('reservations.created_at', 'desc')
                ->paginate(10);
            }

            return view('places.history', compact('reservations', 'currentDateTime'));
        }
    }
}