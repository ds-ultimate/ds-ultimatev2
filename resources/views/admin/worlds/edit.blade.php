@extends('layouts.admin')
@section('content')

<div class="card">
    <div class="card-header">
        {{ trans('global.edit') }} {{ trans('cruds.world.title_singular') }}
    </div>

    <div class="card-body">
        <form action="{{ route("admin.worlds.update", [$world->id]) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            <div class="form-group {{ $errors->has('server_id') ? 'has-error' : '' }}">
                <label for="server_id">{{ trans('cruds.world.fields.server') }}*</label>
                <select id="server_id" name="server_id" class="form-control">
                    @foreach (\App\Server::getServer() as $server)
                        <option value="{{ $server->id }}" {{ ($world->server->id == $server->id)? 'selected' : '' }}>{{ $server->code }}</option>
                    @endforeach
                </select>
                @if($errors->has('server_id'))
                    <em class="invalid-feedback">
                        {{ $errors->first('server_id') }}
                    </em>
                @endif
                <p class="helper-block">
                    {{ trans('cruds.world.fields.server_helper') }}
                </p>
            </div>
            <div class="form-group {{ $errors->has('name') ? 'has-error' : '' }}">
                <label for="name">{{ trans('cruds.world.fields.name') }}*</label>
                <input type="text" id="name" name="name" class="form-control" value="{{ old('name', isset($world) ? $world->name : '') }}" required>
                @if($errors->has('name'))
                    <em class="invalid-feedback">
                        {{ $errors->first('name') }}
                    </em>
                @endif
                <p class="helper-block">
                    {{ trans('cruds.world.fields.name_helper') }}
                </p>
            </div>
            <div class="form-group {{ $errors->has('ally') ? 'has-error' : '' }}">
                <label for="ally">{{ trans('cruds.world.fields.ally') }}*</label>
                <input type="text" id="ally" name="ally" class="form-control" value="{{ old('ally', isset($world) ? $world->ally_count : '') }}" required>
                @if($errors->has('ally'))
                    <em class="invalid-feedback">
                        {{ $errors->first('ally') }}
                    </em>
                @endif
                <p class="helper-block">
                    {{ trans('cruds.world.fields.ally_helper') }}
                </p>
            </div>
            <div class="form-group {{ $errors->has('player') ? 'has-error' : '' }}">
                <label for="player">{{ trans('cruds.world.fields.player') }}*</label>
                <input type="text" id="player" name="player" class="form-control" value="{{ old('player', isset($world) ? $world->player_count : '') }}" required>
                @if($errors->has('player'))
                    <em class="invalid-feedback">
                        {{ $errors->first('player') }}
                    </em>
                @endif
                <p class="helper-block">
                    {{ trans('cruds.world.fields.player_helper') }}
                </p>
            </div>
            <div class="form-group {{ $errors->has('village') ? 'has-error' : '' }}">
                <label for="village">{{ trans('cruds.world.fields.village') }}*</label>
                <input type="text" id="village" name="village" class="form-control" value="{{ old('village', isset($world) ? $world->village_count : '') }}" required>
                @if($errors->has('village'))
                    <em class="invalid-feedback">
                        {{ $errors->first('village') }}
                    </em>
                @endif
                <p class="helper-block">
                    {{ trans('cruds.world.fields.village_helper') }}
                </p>
            </div>
            <div class="form-group {{ $errors->has('url') ? 'has-error' : '' }}">
                <label for="url">{{ trans('cruds.world.fields.url') }}*</label>
                <input type="text" id="url" name="url" class="form-control" value="{{ old('url', isset($world) ? $world->url : '') }}" required>
                @if($errors->has('url'))
                    <em class="invalid-feedback">
                        {{ $errors->first('url') }}
                    </em>
                @endif
                <p class="helper-block">
                    {{ trans('cruds.world.fields.url_helper') }}
                </p>
            </div>
            <div class="form-group {{ $errors->has('config') ? 'has-error' : '' }}">
                <label for="config">{{ trans('cruds.world.fields.config') }}*</label>
                <textarea id="config" name="config" class="form-control" required>{{ old('config', isset($world) ? $world->config : '') }}</textarea>
                @if($errors->has('config'))
                    <em class="invalid-feedback">
                        {{ $errors->first('config') }}
                    </em>
                @endif
                <p class="helper-block">
                    {{ trans('cruds.world.fields.config_helper') }}
                </p>
            </div>
            <div class="form-check {{ $errors->has('active') ? 'has-error' : '' }}">
                <input type="checkbox" id="active" name="active" class="form-check-input" {{ ((isset($world))?($world->active == 1)? 'checked' : '': '') }}>
                <label for="active">{{ trans('cruds.world.fields.active') }}</label>
                @if($errors->has('active'))
                    <em class="invalid-feedback">
                        {{ $errors->first('active') }}
                    </em>
                @endif
                <p class="helper-block">
                    {{ trans('cruds.world.fields.active_helper') }}
                </p>
            </div>
            <div>
                <input class="btn btn-danger" type="submit" value="{{ trans('global.save') }}">
            </div>
        </form>
    </div>
</div>
@endsection
