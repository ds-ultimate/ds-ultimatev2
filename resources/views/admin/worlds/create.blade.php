@extends('layouts.admin')
@section('content')
<div class="card">
    <div class="card-header">
        {{ trans('global.create') }} {{ trans('cruds.server.title_singular') }}
    </div>

    <div class="card-body">
        <form action="{{ route("admin.worlds.store") }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="form-group {{ $errors->has('server_id') ? 'has-error' : '' }}">
                <label for="server_id">{{ trans('cruds.world.fields.server') }}*</label>
                <select id="server_id" name="server_id" class="form-control">
                    @foreach (\App\Server::getServer() as $server)
                        <option value="{{ $server->id }}" {{ old('server_id', (isset($world) ? ($world->server->id == $server->id)? 'selected' : '' : '')) }}>{{ $server->code }}</option>
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
            <div class="form-group {{ $errors->has('ally_count') ? 'has-error' : '' }}">
                <label for="ally_count">{{ trans('cruds.world.fields.ally') }}</label>
                <input type="text" id="ally_count" name="ally_count" class="form-control" value="{{ old('ally_count', isset($world) ? $world->ally_count : '') }}">
                @if($errors->has('ally_count'))
                    <em class="invalid-feedback">
                        {{ $errors->first('ally_count') }}
                    </em>
                @endif
                <p class="helper-block">
                    {{ trans('cruds.world.fields.ally_helper') }}
                </p>
            </div>
            <div class="form-group {{ $errors->has('player_count') ? 'has-error' : '' }}">
                <label for="player_count">{{ trans('cruds.world.fields.player') }}</label>
                <input type="text" id="player_count" name="player_count" class="form-control" value="{{ old('player_count', isset($world) ? $world->player_count : '') }}">
                @if($errors->has('player_count'))
                    <em class="invalid-feedback">
                        {{ $errors->first('player_count') }}
                    </em>
                @endif
                <p class="helper-block">
                    {{ trans('cruds.world.fields.player_helper') }}
                </p>
            </div>
            <div class="form-group {{ $errors->has('village_count') ? 'has-error' : '' }}">
                <label for="village_count">{{ trans('cruds.world.fields.village') }}</label>
                <input type="text" id="village_count" name="village_count" class="form-control" value="{{ old('village_count', isset($world) ? $world->village_count : '') }}">
                @if($errors->has('village_count'))
                    <em class="invalid-feedback">
                        {{ $errors->first('village_count') }}
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

@section('scripts')
    <script>
        $(function(){
            $('#flag').selectpicker();
        });
    </script>
@stop
