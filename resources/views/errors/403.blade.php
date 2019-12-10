@extends('layouts.temp')

@section('content')
    <div class="container mb-5 pb-3">
        <div class="cointainer">
            <div class="row justify-content-center">
                <div class="col-12">
                    <div class="col-md-5 p-lg-5 mx-auto my-1 text-center">
                        <h1 class="font-weight-normal">{{ ucfirst(__('ui.notAllowed')) }}</h1>
                    </div>
                </div>
                <div class="col-12">
                    <div class="card">
                        <div class="card-body text-center">
                            @if($exception->getMessage() != "")
                                {{ $exception->getMessage() }}
                            @else
                                {{ ucfirst(__('ui.notAllowedDesc')) }}
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection