<?php

namespace App\Http\Controllers;

use App\Models\Place;
use App\Models\Reservation;
use Illuminate\Http\Request;

class PlaceController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $search = $request['search'] ?? "";
        if($search != "")
        {
            $places = Place::where('libelle','LIKE',"%$search%")->get();
        }
        else
        {
            $places = Place::orderBy('id')->paginate(10);
        }

        return view('places.index', compact('places'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('places.create');
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
    public function edit($id)
    {
        $place = Place::findOrFail($id);
        return view('places.edit', compact('place'));
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
        $request->validate([
            'libelle'=> 'required',
        ]);

        $place->libelle = $request->input('libelle');
        $place->update();
        return redirect('places')->with('status','Les informations ont bien été modifiées');
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

    public function downgrade($id)
    {
        $place = Place::findOrFail($id);
        return view('places.remove', compact('place'));
    }

    public function erase($id)
    {
        $place = Place::findOrFail($id);
        $place->forceDelete();
        return redirect('places')->with('status','La place a été supprimé');
    }
}
