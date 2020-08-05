@extends('layouts.app')

@section('content')
    <div class="justify-content-center">
        <div class="col-12 text-center">
            <a class="btn btn-primary" href="{{ url()->previous() }}">{{ __('global.back') }}</a>
        </div>
    </div>
    <ul class="timeline">
        @foreach($changelogs as $key => $changelog)
            <li class="{{ ($key % 2 != 0)? 'timeline-inverted' : '' }}">
                <div class="timeline-badge" style="background-color: {{ $changelog->color }}"><i class="{{ $changelog->icon }}"></i></div>
                <div class="timeline-panel bg-white">
                    <div class="timeline-heading">
                        <div class="d-none d-lg-block">
                            <div class="timeline-title form-inline">
                                <h4 class="text-truncate" style="width: 70%;">{{ $changelog->title }}</h4>
                                <h4 class="text-truncate" style="width: 30%;">
                                    <small><b class="float-right">{{ $changelog->version }}</b></small>
                                </h4>
                            </div>
                        </div>
                        <div class="d-lg-none">
                            <h4>{{ $changelog->title }}</h4>
                            <h4>
                                <small><b>{{ $changelog->version }}</b></small>
                            </h4>
                        </div>
                        <p><small class="text-muted"><i class="fas fa-clock"></i> {{ $changelog->created_at->diffForHumans() }}</small></p>
                    </div>
                    <div class="timeline-body">
                        <p>{!! ($changelog->$locale != null)? $changelog->$locale : $changelog->de !!}</p>
                        @if ($changelog->repository_html_url != null)
                            <p class="float-right"><a class="link-black" style="color: black; font-size: 1.75rem" href="{{ $changelog->repository_html_url }}" target="_blank"><i class="fab fa-github"></i></a></p>
                        @endif
                    </div>
                </div>
            </li>
        @endforeach
    </ul>
@endsection
