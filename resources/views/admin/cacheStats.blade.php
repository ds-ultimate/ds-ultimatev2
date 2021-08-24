@extends('layouts.admin')

@section('content')
@foreach($cacheStatistics as $key => $cacheStat)
    <h1 class="">{{ __('admin.cacheStats.' . $key) }}</h1>
    <div class="d-flex mb-4">
        <div class="col-lg-4">
            <div class="card m-2">
                <div class="card-body">
                    {{ __('admin.cacheStats.size') }} {{ $cacheStat['size'] }}<br>
                    {{ __('admin.cacheStats.num') }} {{ $cacheStat['num'] }}
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="card m-2">
                <div class="card-body">
                    <h3 class="m-0">{{ __('admin.cacheStats.hitrate') }}</h3>
                    <div id='{{ $key }}Hit'></div>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="card m-2">
                <div class="card-body">
                    <h3 class="m-0">{{ __('admin.cacheStats.hits') }}</h3>
                    <div id='{{ $key }}Ges'></div>
                </div>
            </div>
        </div>
    </div>
@endforeach
@endsection

@push('js')
@foreach($cacheStatistics as $key => $cacheStat)
    {!! $cacheStat["charts"] !!}
@endforeach
@endpush