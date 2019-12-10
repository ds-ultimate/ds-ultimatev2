@extends('layouts.temp')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">{{ __('global.home') }}</div>

                    <div class="card-body">
                        @if (session('status'))
                            <div class="alert alert-success" role="alert">
                                {{ session('status') }}
                            </div>
                        @endif

                        {{ __('global.youAreLoggedIn') }}
                        <br>
                        <br>
                        {!! __('global.youAreLoggedInMessage') !!}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
