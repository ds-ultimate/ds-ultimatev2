@extends('layouts.admin')
@section('content')

    <div class="card">
        <div class="card-header">
            {{ trans('global.show') }} {{ trans('cruds.news.title') }}
        </div>

        <div class="card-body">
            <div>
                <h2 class="card-title">DE</h2>
                <br>
                {!! $news->content_de !!}
                <br>
                <br>
                <br>
                <div class="dropdown-divider"></div>
                <br>
                <h2 class="card-title">EN</h2>
                <br>
                {!! $news->content_en !!}
                <a style="margin-top:20px;" class="btn btn-default" href="{{ url()->previous() }}">
                    Back
                </a>
            </div>
        </div>
    </div>
@endsection
