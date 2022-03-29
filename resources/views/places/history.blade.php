@extends('layouts.app')

@section('content')
<div class="container">
    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        <h4>Reservations</h4>
    </h2>
</div>
@if (session('status'))
<div class="alert alert-success">
    <br>
    <h4 align="center">{{ session('status') }}</h4>
</div>
@endif

<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="container">
            <a href="{{ url()->previous() }}"><button class="btn btn-secondary btn-sm">&#x21A9 Retour</button></a>
            </div>
            <form action="">
                {!! Form::open() !!}
                    <div class="form-group" align="center">
                        {{ Form::search('search', '', ['placeholder' => 'Rechercher par email ou place']) }}
                        {{ Form::submit('Rechercher', ['class' => 'btn btn-info'])}}
                    </div>
                {!! Form::close() !!}
                <br>

            <div class="container">
                <br>
                <table cellpadding="2" cellspacing="0">
                    <thead>
                        <tr align="center">
                            <th width="30%">
                                Date début
                            </th>
                            <th width="30%">
                                Date fin
                            </th>
                            <th width="20%">
                                Etat
                            </th>
                            <th width="20%">
                                Place
                            </th>
                        </tr>
                    </thead>
                    @forelse($reservations as $reservation)
                    <tbody align="center">
                        <tr>
                            <td>
                                {{ $reservation->created_at }}
                            </td>
                            @if($reservation->deleted_at != NULL)
                                <td>
                                    {{ $reservation->deleted_at}}
                                </td>
                            @else
                                <td>
                                    {{ $reservation->finished_at}}
                                </td>
                            @endif
                            @if($reservation->deleted_at != NULL)
                                <td>
                                    Annulé
                                </td>
                            @else
                                @if($reservation->finished_at > $currentDateTime)
                                    <td>
                                        Occupé en ce moment
                                    </td>
                                @else
                                    <td>
                                        Terminé
                                    </td>
                                @endif
                            @endif
                            <td>
                                {{ $reservation->libelle }}
                            </td>
                        </tbody>
                        @empty
                            <span>Aucune place n'a été crée</span>
                        @endforelse               
                    </table>
                    <br>
                    <nav>
                        <div>
                            {!! $reservations->links() !!}
                        </div>
                    </nav>   
            </div>     
@endsection
