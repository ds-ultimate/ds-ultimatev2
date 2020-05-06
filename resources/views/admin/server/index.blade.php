@extends('layouts.admin')

@section('content')
<div class="card">
    <div class="card-header">
        {{ __('admin.server.title') }}
        
        @can('server_create')
            <div class="float-right position-relative">
                <a class="btn btn-success" href="{{ route("admin.server.create") }}">
                    {{ __('admin.server.create') }}
                </a>
            </div>
        @endcan
    </div>

    <div class="card-body">
        <div class="table-responsive">
            <table class=" table table-bordered table-striped table-hover datatable w-100">
                <thead>
                    <tr>
                        <th>
                            {{ __('admin.server.id') }}
                        </th>
                        <th>
                            {{ __('admin.server.code') }}
                        </th>
                        <th>
                            {{ __('admin.server.flag') }}
                        </th>
                        <th>
                            {{ __('admin.server.url') }}
                        </th>
                        <th>
                            {{ __('admin.server.active') }}
                        </th>
                        <th>
                            &nbsp;
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($servers as $key => $server)
                        <tr data-entry-id="{{ $server->id }}">
                            <td>
                                {{ $server->id ?? '' }}
                            </td>
                            <td>
                                {{ $server->code ?? '' }}
                            </td>
                            <td>
                                <span class="flag-icon flag-icon-{{ $server->flag ?? '' }}"></span> [{{ $server->flag }}]
                            </td>
                            <td>
                                {{ $server->url ?? '' }}
                            </td>
                            <td>
                                {!! ($server->active == 1)? '<span class="fas fa-check" style="color: green"></span>' : '<span class="fas fa-times" style="color: red"></span>' !!}
                            </td>
                            <td>
                                @can('server_show')
                                    <a class="btn btn-xs btn-primary" href="{{ route('admin.server.show', $server->id) }}">
                                        {{ __('global.view') }}
                                    </a>
                                @endcan
                                @can('server_edit')
                                    <a class="btn btn-xs btn-info" href="{{ route('admin.server.edit', $server->id) }}">
                                        {{ __('global.edit') }}
                                    </a>
                                @endcan
                                @can('server_delete')
                                    <form action="{{ route('admin.server.destroy', $server->id) }}" method="POST" onsubmit="return confirm('{{ __('global.areYouSure') }}');" style="display: inline-block;">
                                        <input type="hidden" name="_method" value="DELETE">
                                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                        <input type="submit" class="btn btn-xs btn-danger" value="{{ __('global.delete') }}">
                                    </form>
                                @endcan
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@section('scripts')
@endsection
