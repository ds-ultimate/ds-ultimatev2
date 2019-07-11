@extends('layouts.admin')
@section('content')

    <div class="card">
        <div class="card-header">
            {{ trans('global.show') }} {{ trans('cruds.world.title') }}
        </div>

        <div class="card-body">
            <div>
                <table class="table table-bordered table-striped">
                    <tbody>
                    <tr>
                        <th>
                            {{ trans('cruds.world.fields.id') }}
                        </th>
                        <td>
                            {{ $world->id }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.world.fields.server') }}
                        </th>
                        <td>
                            <span class="flag-icon flag-icon-{{ $world->server->flag ?? '' }}"></span> {{ $world->server->code ?? '' }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.world.fields.name') }}
                        </th>
                        <td>
                            {{ $world->name }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.world.fields.ally') }}
                        </th>
                        <td>
                            {{ $world->ally_count }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.world.fields.player') }}
                        </th>
                        <td>
                            {{ $world->player_count }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.world.fields.village') }}
                        </th>
                        <td>
                            {{ $world->village_count }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.world.fields.url') }}
                        </th>
                        <td>
                            {{ $world->url }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            {{ trans('cruds.world.fields.active') }}
                        </th>
                        <td>
                            {!! ($world->active == 1)? '<span class="fas fa-check" style="color: green"></span>' : '<span class="fas fa-times" style="color: red"></span>' !!}
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
