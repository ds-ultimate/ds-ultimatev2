@extends('layouts.admin')
@section('content')

<div class="card">
    <div class="card-header">
        {{ trans('global.show') }} {{ trans('cruds.bugreport.title') }}
    </div>

    <div class="card-body">
            <h1>{{ $bugreport->title }}</h1>
            <form action="{{ route('admin.bugreports.internalUpdate') }}" method="post">
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
                            <select class="form-control" name="priority">
                                <option value="0" {{ ($bugreport->priority == 0)? 'selected' : '' }}>{{ __('user.bugreport.prioritySelect.low') }}</option>
                                <option value="1" {{ ($bugreport->priority == 1)? 'selected' : '' }}>{{ __('user.bugreport.prioritySelect.normal') }}</option>
                                <option value="2" {{ ($bugreport->priority == 2)? 'selected' : '' }}>{{ __('user.bugreport.prioritySelect.high') }}</option>
                                <option value="3" {{ ($bugreport->priority == 3)? 'selected' : '' }}>{{ __('user.bugreport.prioritySelect.critical') }}</option>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.bugreport.fields.status') }}
                        </th>
                        <td>
                            <h4>{!! $bugreport->getStatusBadge() !!}</h4>
                            <select class="form-control" name="status">
                                <option value="0" {{ ($bugreport->status == 0)? 'selected' : '' }}>{{ __('cruds.bugreport.statusSelect.open') }}</option>
                                <option value="1" {{ ($bugreport->status == 1)? 'selected' : '' }}>{{ __('cruds.bugreport.statusSelect.inprogress') }}</option>
                                <option value="2" {{ ($bugreport->status == 2)? 'selected' : '' }}>{{ __('cruds.bugreport.statusSelect.resolved') }}</option>
                                <option value="3" {{ ($bugreport->status == 3)? 'selected' : '' }}>{{ __('cruds.bugreport.statusSelect.close') }}</option>
                            </select>
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
                            {{ $bugreport->firstSeenUser }} || {{ $bugreport->firstSeen }} || <small class="text-muted">{{ $bugreport->created_at->diffForHumans() }}</small>
                        </td>
                    </tr>
                </tbody>
            </table>
            <a style="margin-top:20px;" class="btn btn-default" href="{{ url()->previous() }}">
                Back
            </a>
                @csrf
            <div>
                <input class="btn btn-danger" type="submit" value="{{ trans('global.save') }}">
            </div>
        </form>
    </div>
</div>
@endsection
