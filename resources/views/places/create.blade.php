@extends('layouts.app')

@section('content')

<div class="container" align="center">
    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        Ajouter une nouvelle place
    </h2>
</div>
                <div class="container p-6 bg-white border-b border-gray-200">
                    <a href="{{ url()->previous() }}"><button class="btn btn-secondary btn-sm">&#x21A9 Retour</button></a>
                    @if($errors->any())
                        @foreach($errors->all() as $error)
                            <div class='text-red-500'>{{ $error }}</div>
                        @endforeach
                    @endif
                    {!! Form::open(['route' => 'places.store', 'method' => 'post']) !!}
                        {{ Form::label('libelle','Libelle : ') }}
                        {{ Form::text('libelle', '', ['class' => 'form-control']) }}
                        <br>
                        {{ Form::submit('Valider', ['class' => 'btn btn-success'])}}
                    {!! Form::close() !!}
                </div>
@endsection
