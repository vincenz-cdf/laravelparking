@extends('layouts.app')

@section('content')
<div class="container">
    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        <h4>Bonjour ! {{Auth::user()->name}} {{Auth::user()->prenom}}</h4>
    </h2>
</div>
<div class="container">
    <div class="container p-6 bg-white border-b border-gray-200">

        @forelse(Auth::User()->reservations as $reservation)
            Vous avez actuellement une place au :
            {{ $reservation->place->libelle }}
        @empty
            <span>Vous n'avez actuellement pas de place attribué</span>
            <br>
            <span>Cliquez sur le bouton en dessous vous en avoir une</span>
            <br>
            <a href="{{route('users.reserve')}}">
                <button class="btn btn-success"> Réserver une place </button>
            </a>
        @endforelse
    </div>
</div>
@endsection
