@extends('layouts.admin')
@section('content')

<div class="card">
    <div class="card-header">
        {{ trans('global.show') }} {{ trans('cruds.bugreport.title') }}
    </div>

    <div class="card-body">
        <h1>{{ $bugreport->title }}</h1>
        <input type="hidden" name="id" value="{{ $bugreport->id }}">
        <table class="table table-bordered table-striped">
            <tbody>
            <tr>
                <th width="150">
                    {{ trans('cruds.bugreport.fields.name') }}
                </th>
                <td>
                    {{ $bugreport->name }}
                </td>
            </tr>
            <tr>
                <th>
                    {{ trans('cruds.bugreport.fields.email') }}
                </th>
                <td>
                    {{ $bugreport->email }}
                </td>
            </tr>
            <tr>
                <th>
                    {{ trans('cruds.bugreport.fields.priority') }}
                </th>
                <td>
                    <h4>{!! $bugreport->getPriorityBadge() !!}</h4>
                </td>
            </tr>
            <tr>
                <th>
                    {{ trans('cruds.bugreport.fields.status') }}
                </th>
                <td>
                    <h4>{!! $bugreport->getStatusBadge() !!}</h4>
                </td>
            </tr>
            <tr>
                <th>
                    {{ trans('cruds.bugreport.fields.created') }}
                </th>
                <td>
                    {{ $bugreport->created_at }}
                </td>
            </tr>
            <tr>
                <th>
                    {{ trans('cruds.bugreport.fields.url') }}
                </th>
                <td>
                    <a href="{{ $bugreport->url ?? '' }}" target="_blank">{{ $bugreport->url ?? '' }}</a>
                </td>
            </tr>
            <tr>
                <th>
                    {{ trans('cruds.bugreport.fields.description') }}
                </th>
                <td>
                    {{ $bugreport->description }}
                </td>
            </tr>
            <tr>
                <th>
                    {{ trans('cruds.bugreport.fields.seen') }}
                </th>
                <td>
                    {{ $bugreport->firstSeenUser->name }} || {{ $bugreport->firstSeen }} || <small class="text-muted">{{ $bugreport->created_at->diffForHumans() }}</small>
                </td>
            </tr>
            </tbody>
        </table>
        <a style="margin-top:20px;" class="btn btn-default" href="{{ url()->previous() }}">
            Back
        </a>
    </div>
</div>
@endsection
