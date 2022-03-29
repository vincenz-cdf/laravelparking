@extends('layouts.app')

@section('content')
<div class="container">
    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        <h4>Vous pouvez changer la durée des réservations ici :</h4>
    </h2>
</div>
<div class="container">
    <div class="container p-6 bg-white border-b border-gray-200">

        {!! Form::open(['route' => 'users.updateduree'])!!}
            La durée est fixé à{{ Form::number('modif', $duree) }} jours
            <br>
            {{ Form::submit('Valider', ['class' => 'btn btn-success'])}}
        {!! Form::close() !!}
    </div>
</div>
@endsection
