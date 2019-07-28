@extends('layouts.admin')
@section('content')

    <div class="card">
        <div class="card-header">
            {{ trans('global.show') }} {{ trans('cruds.news.title') }}
        </div>

        <div class="card-body">
            <div>
                {!! $news->content !!}
                <a style="margin-top:20px;" class="btn btn-default" href="{{ url()->previous() }}">
                    Back
                </a>
            </div>
        </div>
    </div>
@endsection
