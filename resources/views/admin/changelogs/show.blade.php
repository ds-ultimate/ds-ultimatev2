@extends('layouts.admin')
@section('content')

    <div class="card">
        <div class="card-header">
            {{ trans('global.show') }} {{ trans('cruds.news.title') }}
        </div>

        <div class="card-body">
            <div>
                <table class="table table-bordered table-striped">
                    <tbody>
                        <tr>
                            <th>
                                {{ trans('cruds.changelog.fields.version') }}
                            </th>
                            <td>
                                {{ $changelog->version }}
                            </td>
                        </tr>
                        <tr>
                            <th>
                                {{ trans('cruds.changelog.fields.icon') }}
                            </th>
                            <td>
                                <h1 class="mb-0"><i class="{{ $changelog->icon }}"></i></h1>
                            </td>
                        </tr>
                        <tr>
                            <th>
                                {{ trans('cruds.changelog.fields.title') }}
                            </th>
                            <td>
                                {{ $changelog->title }}
                            </td>
                        </tr>
                        <tr>
                            <th>
                                {{ trans('cruds.changelog.fields.content') }}
                            </th>
                            <td>
                                {!! $changelog->content !!}
                            </td>
                        </tr>
                        <tr>
                            <th>
                                {{ trans('cruds.changelog.fields.repository_html_url') }}
                            </th>
                            <td>
                                {{ $changelog->repository_html_url }}
                            </td>
                        </tr>
                        <tr>
                            <th>
                                {{ trans('cruds.changelog.fields.buffer') }}
                            </th>
                            <td>
                                {!! $changelog->buffer !!}
                            </td>
                        </tr>
                        <tr>
                            <th>
                                {{ trans('cruds.changelog.fields.update') }}
                            </th>
                            <td>
                                {{ $changelog->updated_at->diffForHumans() }}
                            </td>
                        </tr>
                    </tbody>
                </table>
                <a style="margin-top:20px;" class="btn btn-default" href="{{ url()->previous() }}">
                    Back
                </a>
            </div>
        </div>
    </div>
@endsection
