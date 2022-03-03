@extends('layouts.app')

@section('content')

<div class="container" align="center">
    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        Ajouter un nouveau salarié
    </h2>
</div>

                <div class="container p-6 bg-white border-b border-gray-200">
                    <a href="{{ url()->previous() }}"><button class="btn btn-secondary btn-sm">&#x21A9 Retour</button></a>
                    @if($errors->any())
                        @foreach($errors->all() as $error)
                            <div class='text-red-500'>{{ $error }}</div>
                        @endforeach
                    @endif
                    {!! Form::open(['route' => 'users.store', 'method' => 'post']) !!}
                        {{ Form::label('email','Email : ') }}
                        {{ Form::email('email', '', ['class' => 'form-control']) }}
                        <br>
                        {{ Form::label('name','Nom : ') }}
                        {{ Form::text('name', '', ['class' => 'form-control']) }}
                        <br>
                        {{ Form::label('prenom','Prenom : ') }}
                        {{ Form::text('prenom', '', ['class' => 'form-control']) }}
                        <br>

                        Un mdp sera généré et envoyé automatiquement à l'adresse mail que vous aurez inscrit.<br><br>
                        {{ Form::submit('Valider', ['class' => 'btn btn-success'])}}
                    {!! Form::close() !!}
                </div>
@endsection
