@extends('layouts.admin')
@section('content')

<div class="card">
    <div class="card-header">
        {{ trans('global.show') }} {{ trans('cruds.server.title') }}
    </div>

    <div class="card-body">
        <div>
            <table class="table table-bordered table-striped w-100">
                <tbody>
                    <tr>
                        <th>
                            {{ trans('cruds.server.fields.code') }}
                        </th>
                        <td>
                            {{ $server->code }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.server.fields.flag') }}
                        </th>
                        <td>
                            <span class="flag-icon flag-icon-{{ $server->flag ?? '' }}"></span> [{{ $server->flag }}]
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.server.fields.url') }}
                        </th>
                        <td>
                            {{ $server->url }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.server.fields.active') }}
                        </th>
                        <td>
                            {!! ($server->active == 1)? '<span class="fas fa-check" style="color: green"></span>' : '<span class="fas fa-times" style="color: red"></span>' !!}
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
