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
</div>
@endsection
@section('scripts')
@parent

@endsection
