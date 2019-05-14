@extends('layouts.temp')

@section('content')
    @for ($i = 0; $i < count($flags); $i++)
        <div>
            <h1><i class="flag-icon flag-icon-{{ $flags[$i] }}" title="{{ $flags[$i] }}" id="{{ $flags[$i] }}"></i></h1>
        </div>
    @endfor
@endsection
