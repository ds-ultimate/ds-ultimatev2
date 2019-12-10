@extends('layouts.temp')

@section('content')
    <div class="container mb-5 pb-3">
        <div class="cointainer">
            <div class="row justify-content-center">
                <div class="col-12">
                    <div class="col-md-5 p-lg-5 mx-auto my-1 text-center">
                        <h1 class="font-weight-normal">{{ ucfirst(__('ui.siteNotFound')) }}</h1>
                    </div>
                </div>
                @if($exception->getMessage() != "")
                    <div class="col-12">
                        <div class="card">
                            <div class="card-body text-center">
                                {{ $exception->getMessage() }}
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection