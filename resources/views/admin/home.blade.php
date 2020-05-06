@extends('layouts.admin')

@section('content')
<div class="row">
    <div class="col-lg-12">
        <h1>{{ __('global.dashboard') }}</h1>
        <div class="card-body">
            Willkommen im Admin Bereich von DS-Ultimate <i>(<b>by skatecram</b>)</i>
        </div>
    </div>
</div>
<div class="row">
    <!-- Column -->
    <div class="col-sm-6 col-lg-3">
        <div class="card card-inverse bg-info">
            <div class="box bg-info text-center p-3">
                <h1 class="font-light text-white">{{ \App\Bugreport::countNew() }}</h1>
                <h6 class="text-white">{{ __('admin.dashboard.bugreports') }}</h6>
            </div>
        </div>
    </div>
    <!-- Column -->
    <div class="col-sm-6 col-lg-3">
        <div class="card card-inverse bg-warning">
            <div class="box text-center p-3">
                <h1 class="font-light text-white">{{ \App\Util\BasicFunctions::numberConv($counter['maps']) }}</h1>
                <h6 class="text-white">{{ __('admin.dashboard.maps') }}</h6>
            </div>
        </div>
    </div>
    <!-- Column -->
    <div class="col-sm-6 col-lg-3">
        <div class="card card-primary bg-danger">
            <div class="box text-center p-3">
                <h1 class="font-light text-white">{{ \App\Util\BasicFunctions::numberConv($counter['attackplaner']) }}</h1>
                <h6 class="text-white">{{ __('admin.dashboard.attackplanner') }}</h6>
            </div>
        </div>
    </div>
    <!-- Column -->
    <div class="col-sm-6 col-lg-3">
        <div class="card card-inverse bg-success">
            <div class="box text-center p-3">
                <h1 class="font-light text-white">{{ \App\Util\BasicFunctions::numberConv($counter['attacks']) }}</h1>
                <h6 class="text-white">{{ __('admin.dashboard.attacks') }}</h6>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-lg-3">
        <div class="card card-inverse bg-info">
            <div class="box text-center p-3">
                <h1 class="font-light text-white">{{ \App\Util\BasicFunctions::numberConv($counter['users']) }}</h1>
                <h6 class="text-white">{{ __('admin.dashboard.users') }}</h6>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-lg-3">
        <div class="card card-inverse bg-warning">
            <div class="box text-center p-3">
                <h1 class="font-light text-white">
                @foreach (\App\Http\Controllers\User\LoginController::getDriver() as $driver)
                    <i class="{{ $driver['icon'] }} h3" style="color: {{ $driver['color'] }}"></i> {{ $counter[$driver['name']] }}
                @endforeach
                </h1>
                <h6 class="text-white">{{ __('admin.dashboard.connections') }}</h6>
            </div>
        </div>
    </div>
</div>
@endsection
