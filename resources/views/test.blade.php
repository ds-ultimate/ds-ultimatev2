@extends('layouts.temp')

@section('content')
    @foreach (\App\Util\BasicFunctions::flags() as $iso)
        <span class="flag-icon flag-icon-{{ $iso }}"></span>
    @endforeach
@endsection
