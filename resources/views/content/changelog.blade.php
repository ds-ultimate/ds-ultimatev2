@extends('layouts.temp')
@section('content')
    <ul class="timeline">
        @foreach($changelogs as $key => $changelog)
            <li class="{{ ($key % 2 != 0)? 'timeline-inverted' : '' }}">
                <div class="timeline-badge" style="background-color: {{ $changelog->color }}"><i class="{{ $changelog->icon }}"></i></div>
                <div class="timeline-panel bg-white">
                    <div class="timeline-heading">
                        <h4 class="timeline-title truncate">{{ $changelog->title }} <i class="float-right small truncate"><b>{{ $changelog->version }}</b></i></h4>
                        <p><small class="text-muted"><i class="fas fa-clock"></i> {{ $changelog->created_at->diffForHumans() }}</small></p>
                    </div>
                    <div class="timeline-body">
                        <p>{!! $changelog->content !!}</p>
                        @if ($changelog->repository_html_url != null)
                            <p class="float-right"><a class="link-black" style="color: black; font-size: 1.75rem" href="{{ $changelog->repository_html_url }}" target="_blank"><i class="fab fa-github"></i></a></p>
                        @endif
                    </div>
                </div>
            </li>
        @endforeach
    </ul>
@endsection
