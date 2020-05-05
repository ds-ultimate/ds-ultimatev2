@extends('layouts.admin')

@section('content')
<div class="card">
    <div class="card-header">
        {{ __('admin.worlds.title') }}

        @can('world_create')
            <div class="float-right">
                <a class="btn btn-success" href="{{ route("admin.worlds.create") }}">
                    {{ __('admin.worlds.create') }}
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
                        {{ trans('admin.worlds.id') }}
                    </th>
                    <th>
                        {{ trans('admin.worlds.server') }}
                    </th>
                    <th>
                        {{ trans('admin.worlds.name') }}
                    </th>
                    <th>
                        {{ trans('admin.worlds.ally') }}
                    </th>
                    <th>
                        {{ trans('admin.worlds.player') }}
                    </th>
                    <th>
                        {{ trans('admin.worlds.village') }}
                    </th>
                    <th>
                        {{ trans('admin.worlds.url') }}
                    </th>
                    <th>
                        {{ trans('admin.worlds.active') }}
                    </th>
                    <th>
                        {{ trans('admin.worlds.update') }}
                    </th>
                    <th>
                        {{ trans('admin.worlds.clean') }}
                    </th>
                    <th>
                        &nbsp;
                    </th>
                </tr>
                </thead>
                <tbody>
                @foreach($worlds as $key => $world)
                    <tr data-entry-id="{{ $world->id }}">
                        <td>
                            {{ $world->id ?? '' }}
                        </td>
                        <td>
                            <span class="flag-icon flag-icon-{{ $world->server->flag ?? '' }}"></span> {{ $world->server->code ?? '' }}
                        </td>
                        <td>
                            {{ $world->name ?? '' }}
                        </td>
                        <td>
                            {{ \App\Util\BasicFunctions::numberConv($world->ally_count) ?? '' }}
                        </td>
                        <td>
                            {{ \App\Util\BasicFunctions::numberConv($world->player_count) ?? '' }}
                        </td>
                        <td>
                            {{ \App\Util\BasicFunctions::numberConv($world->village_count) ?? '' }}
                        </td>
                        <td>
                            <a href="{{ $world->url ?? '' }}" target="_blank">{{ $world->url ?? '' }}</a>
                        </td>
                        <td>
                            {!! \App\Util\BasicFunctions::worldStatus($world->active) !!}
                        </td>
                        <td class="{{ ($world->active != null)?(($now->diffInSeconds($world->worldUpdated_at) >= ((60*60)*config('dsUltimate.db_update_every_hours'))*2)? 'bg-danger' : ''): '' }}">
                            {{ $world->worldUpdated_at->diffForHumans() }}
                        </td>
                        <td class="{{ ($world->active != null)?(($now->diffInSeconds($world->worldCleaned_at) >= ((60*60)*config('dsUltimate.db_clean_every_hours'))*2)? 'bg-danger' : ''): '' }}">
                            {{ $world->worldCleaned_at->diffForHumans() }}
                        </td>
                        <td>
                            @can('world_show')
                                <a class="btn btn-xs btn-primary" href="{{ route('admin.worlds.show', $world->id) }}">
                                    {{ trans('global.view') }}
                                </a>
                            @endcan
                            @can('world_edit')
                                <a class="btn btn-xs btn-info" href="{{ route('admin.worlds.edit', $world->id) }}">
                                    {{ trans('global.edit') }}
                                </a>
                            @endcan
                            @can('world_delete')
                                <form action="{{ route('admin.worlds.destroy', $world->id) }}" method="POST" onsubmit="return confirm('{{ trans('global.areYouSure') }}');" style="display: inline-block;">
                                    <input type="hidden" name="_method" value="DELETE">
                                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                    <input type="submit" class="btn btn-xs btn-danger" value="{{ trans('global.delete') }}">
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
