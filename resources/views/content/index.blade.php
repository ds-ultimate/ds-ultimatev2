@extends('layouts.app')

@section('content')
    @if (session()->has('successBugreport'))
        <div class="toast" style="position: absolute; top: 60px; right: 10px; z-index: 100;" data-delay="6000">
            <div class="toast-header">
                <strong class="mr-auto">{{ __('user.bugreport.title') }}</strong>
                <small>{{ __('global.now') }}</small>
                <button type="button" class="ml-2 mb-1 close" data-dismiss="toast" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="toast-body">
                {{ session()->get('successBugreport') }}
                <br>
                <b>{{ __('global.thankYou') }}</b>
            </div>
        </div>
    @endif
    <div class="row justify-content-center">
        <div class="col-12">
            <div class="col-md-5 p-lg-3 mx-auto my-1 text-center">
                <h1 class="font-weight-normal">{{ config('app.name') }}</h1>
            </div>
        </div>
    </div>
    @if (count($news) > 0)
    <!-- News -->
    <div class="row justify-content-center">
        <div class="col-12 col-md-10 mt-1 mb-3">
            <div id="carouselExampleControls" class="carousel slide" data-ride="carousel" data-interval="10000" style="border: 1px solid #edd492; border-radius: 0.25rem;">
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
                        @foreach($news as $new)
                            <div class="carousel-item {{ (isset($active))? '' : 'active' }}">
                                @if (App::getLocale() == 'de')
                                    {!! $new->content_de !!}
                                @else
                                    {!! $new->content_en !!}
                                @endif
                            </div>
                            @php
                                $active = true;
                            @endphp
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- ENDE News -->
    @endif
    <!-- Help Text -->
    <div class="row justify-content-center">
        <div class="col-12 col-lg-6 mt-2">
            <div class="card">
                <div class="card-body">
                    {{ ucfirst(__("ui.index.help")) }}
                </div>
            </div>
        </div>
    </div>
    <!-- ENDE Help Text -->
    <!-- Normale Welten -->
    <div class="row justify-content-center">
        <div class="col-12 col-lg-6 mt-2">
            <div class="card">
                <div class="card-body">
                    <h2 class="card-title">{{ __('ui.server.choose') }}:</h2>
                    <table class="table table-hover table-striped text-break">
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
                                <td style="word-break: break-all;"><a target="_blank" href="{{$serverData->url}}">{{ $serverData->url }}</a></td>
                                <td>{{ $serverData->worlds->count() }}</td>
                                <td><a href="{{ route('server', [$serverData->code]) }}" class="btn btn-primary btn-sm">{{ __('ui.server.show') }}</a></td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <!-- ENDE Normale Welten -->
@endsection

@push('js')
    <script>
        $(document).ready(function(){
            $('.toast').toast('show');
        });
    </script>
@endpush
