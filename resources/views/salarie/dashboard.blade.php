@extends('layouts.app')

@section('content')
<div class="container">
    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        <h4>Bonjour ! {{Auth::user()->name}} {{Auth::user()->prenom}}</h4>
    </h2>
</div>
<div class="container">
    <div class="container p-6 bg-white border-b border-gray-200">
        <a href="{{route('users.reserve')}}">
            <button class="btn btn-success"> Réserver une place </button>
        </a>
    </div>
</div>
@endsection
