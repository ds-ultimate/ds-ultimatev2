@extends('layouts.admin')
@section('content')
<div class="content">
    <div class="row">
        <div class="col-lg-12">
            <h1>{{ trans('global.dashboard') }}</h1>
            <div class="card-body">
                Willkommen im Admin Bereich von DS-Ultimate <i>(asdadad <b>by skatecram</b>)</i>
            </div>
        </div>
    </div>
    <div class="row">
        <!-- Column -->
        <div class="col-md-6 col-lg-3 col-xlg-3">
            <div class="card card-inverse bg-info">
                <div class="box bg-info text-center p-3">
                    <h1 class="font-light text-white">{{ \App\Bugreport::countNew() }}</h1>
                    <h6 class="text-white">{{ __('cruds.bugreport.new') }} {{ __('cruds.bugreport.title') }}</h6>
                </div>
            </div>
        </div>
        <!-- Column -->
        <div class="col-md-6 col-lg-3 col-xlg-3">
            <div class="card card-inverse bg-warning">
                <div class="box text-center p-3">
                    <h1 class="font-light text-white">{{ \App\Util\BasicFunctions::numberConv($counter['maps']) }}</h1>
                    <h6 class="text-white">{{ __('tool.map.title') }}</h6>
                </div>
            </div>
        </div>
        <!-- Column -->
        <div class="col-md-6 col-lg-3 col-xlg-3">
            <div class="card card-primary bg-danger">
                <div class="box text-center p-3">
                    <h1 class="font-light text-white">{{ \App\Util\BasicFunctions::numberConv($counter['attackplaner']) }}</h1>
                    <h6 class="text-white">{{ __('tool.attackPlanner.title') }}</h6>
                </div>
            </div>
        </div>
        <!-- Column -->
        <div class="col-md-6 col-lg-3 col-xlg-3">
            <div class="card card-inverse bg-success">
                <div class="box text-center p-3">
                    <h1 class="font-light text-white">{{ \App\Util\BasicFunctions::numberConv($counter['attacks']) }}</h1>
                    <h6 class="text-white">{{ __('tool.attackPlanner.attackTotal') }}</h6>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@section('scripts')
@parent

@endsection
