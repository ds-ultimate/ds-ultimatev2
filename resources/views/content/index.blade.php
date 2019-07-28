@extends('layouts.temp')

@section('content')
    <div class="row justify-content-center">
        <div class="col-12">
            <div class="col-md-5 p-lg-3 mx-auto my-1 text-center">
                <h1 class="font-weight-normal">{{ env('APP_NAME') }}</h1>
            </div>
        </div>
        <!-- News -->
        <div class="col col-md-1"></div>
        <div class="col-12 col-md-10 mt-1 mb-3">
            <div id="carouselExampleControls" class="carousel slide" data-ride="carousel" data-interval="10000" style="background-color: #fbf6e9; border: 1px solid #edd492; border-radius: 0.25rem;">
                <div class="card-body">
                    <h5 class="card-title mb-1">{{ __('ui.news.title') }}:</h5>
                    <ol class="carousel-indicators">
                        @php
                            $count = 0;
                        @endphp
                        @foreach($news as $list)
                            <li data-target="#carouselExampleControls" style="background-color: #edd492" data-slide-to="{{ $count }}" class="{{ ($count == 0)? 'active' : '' }}"></li>
                            @php
                                $count++;
                            @endphp
                        @endforeach
                    </ol>
                    <div class="carousel-inner">
                        @foreach($news as $news)
                            <div class="carousel-item {{ (isset($active))? '' : 'active' }}">
                                {!! $news->content !!}
                            </div>
                            @php
                                $active = true;
                            @endphp
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
        <div class="col col-md-1"></div>
        <!-- ENDE News -->
        <!-- Normale Welten -->
        <div class="col-12 col-md-6 mt-2">
            <div class="card">
                <div class="card-body">
                    <h2 class="card-title">{{ __('ui.server.title') }}:</h2>
                    <table class="table table-hover table-striped no-wrap">
                        <thead>
                        <tr>
                            <th></th>
                            <th>{{ ucfirst(__('ui.server.code')) }}</th>
                            <th>{{ ucfirst(__('ui.server.dsLink')) }}</th>
                            <th>{{ ucfirst(__('ui.server.worlds')) }}</th>
                            <th></th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($serverArray as $serverData)
                            <tr>
                                <td><span class="flag-icon flag-icon-{{ $serverData->flag }}"></span></td>
                                <td>{{ $serverData->code }}</td>
                                <td><a href="{{$serverData->url}}">{{ $serverData->url }}</a></td>
                                <td>{{ $serverData->worlds->count() }}</td>
                                <td><a href="{{ route('server', [$serverData->code]) }}" class="btn btn-primary btn-sm">{{ __('ui.server.show') }}</a></td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <!-- ENDE Normale Welten -->
    </div>
@endsection

@section('js')

@stop
