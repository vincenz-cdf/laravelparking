@extends('layouts.app')

@section('content')
<div class="container">
    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        <h4>Bonjour ! {{Auth::user()->name}} {{Auth::user()->prenom}}</h4>
    </h2>
</div>
<div class="container">
    <div class="container p-6 bg-white border-b border-gray-200">

        @forelse($reservations as $reservation)
            @if($reservation->finished_at > $currentDateTime)
                Vous avez actuellement une place au :
                {{ $reservation->place->libelle }}
                <br>
                <a href="{{route('users.dereserve', $reservation->id)}}">
                    <button class="btn btn-warning"> Renoncer à la place </button>
                </a>
            @else
                @if($reservation->finished_at < $currentDateTime)
                    <span>Vous n'avez actuellement pas de place attribué</span>
                    <br>
                    <span>Cliquez sur le bouton en dessous vous en avoir une</span>
                    <br>
                    <a href="{{route('users.reserve')}}">
                        <button class="btn btn-success"> Réserver une place </button>
                    </a>
                @endif
            @endif
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
