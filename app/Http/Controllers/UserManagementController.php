<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Place;
use App\Models\Reservation;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
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
        $search = $request['search'] ?? "";
        if($search != "")
        {
            $salaries = User::where('name','LIKE',"%$search%")->orWhere('email','LIKE',"%$search%")->get();
        }
        else
        {
            $salaries = User::orderBy('name')->paginate(10);
        }
        return view('users.index', compact('salaries'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('users.create');
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
    public function edit($id)
    {
        $salarie = User::findOrFail($id);
        return view('users.edit', compact('salarie'));

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
        $request->validate([
            'email'=>[
                'required',
                'email',
            ],
            'name'=> 'required',
            'prenom'=> 'required',
        ]);
        $salarie->prenom = $request->input('prenom');
        $salarie->update($request->input());
        return redirect('users')->with('status','Les informations ont bien été modifiées');
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

    public function remove($id)
    {
        $salarie = User::findOrFail($id);
        return view('users.remove', compact('salarie'));
    }

    public function delete($id)
    {
        $salarie = User::findOrFail($id);
        $salarie->delete();
        return redirect('users')->with('status','Le salarié a été supprimé');
    }

    public function reserve()
    {
        $occupes = Reservation::select('place_id')->get();
        $places = Place::select('id')->get();
        $result = $places->diffKeys($occupes);

        $reservation = new Reservation;
        $reservation->user_id = Auth::user()->id;
        $reservation->place_id = $result->first()->id;
        $reservation->duree = 3600;
        $reservation->save();

        return redirect(url()->previous());
    }
}
