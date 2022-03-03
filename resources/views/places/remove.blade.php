@extends('layouts.app')

@section('content')

<div class="container" align="center">
    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        Etes vous sur de vouloir supprimé cette place
    </h2>
</div>
                <div class="container p-6 bg-white border-b border-gray-200" align="center">
                    <a href="{{route('places.erase', $place->id)}}">
                        <button class="btn btn-danger">
                            Oui
                        </button>
                    </a>
                    <a href="{{ url()->previous() }}">
                        <button class="btn btn-success">
                            Non
                        </button>
                    </a>
                </div>
@endsection
