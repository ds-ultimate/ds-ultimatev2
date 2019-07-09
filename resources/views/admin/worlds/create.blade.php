@extends('layouts.admin')
@section('content')
<div class="card">
    <div class="card-header">
        {{ trans('global.create') }} {{ trans('cruds.server.title_singular') }}
    </div>

    <div class="card-body">
        <form action="{{ route("admin.server.store") }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="form-group {{ $errors->has('code') ? 'has-error' : '' }}">
                <label for="code">{{ trans('cruds.server.fields.code') }}*</label>
                <input type="text" id="code" name="code" class="form-control" value="{{ old('code', isset($server) ? $server->code : '') }}" required>
                @if($errors->has('code'))
                    <em class="invalid-feedback">
                        {{ $errors->first('code') }}
                    </em>
                @endif
                <p class="helper-block">
                    {{ trans('cruds.server.fields.code_helper') }}
                </p>
            </div>
            <div class="form-group {{ $errors->has('flag') ? 'has-error' : '' }}">
                <label for="flag">{{ trans('cruds.server.fields.flag') }}*</label>
                <select id="flag" name="flag" class="form-control">
                    @foreach (\App\Util\BasicFunctions::flags() as $iso)
                        <option>{{ $iso }}</option>
                    @endforeach
                </select>
                @if($errors->has('flag'))
                    <em class="invalid-feedback">
                        {{ $errors->first('flag') }}
                    </em>
                @endif
                <p class="helper-block">
                    {{ trans('cruds.server.fields.flag_helper') }}
                </p>
            </div>
            <div class="form-group {{ $errors->has('url') ? 'has-error' : '' }}">
                <label for="url">{{ trans('cruds.server.fields.url') }}*</label>
                <input type="text" id="url" name="url" class="form-control" value="{{ old('url', isset($server) ? $server->url : '') }}" required>
                @if($errors->has('url'))
                    <em class="invalid-feedback">
                        {{ $errors->first('url') }}
                    </em>
                @endif
                <p class="helper-block">
                    {{ trans('cruds.server.fields.url_helper') }}
                </p>
            </div>
            <div class="form-check {{ $errors->has('active') ? 'has-error' : '' }}">
                <input type="checkbox" id="active" name="active" class="form-check-input" {{ ((isset($server))?($server->active == 1)? 'checked' : '': '') }}>
                <label for="active">{{ trans('cruds.server.fields.active') }}</label>
                @if($errors->has('active'))
                    <em class="invalid-feedback">
                        {{ $errors->first('active') }}
                    </em>
                @endif
                <p class="helper-block">
                    {{ trans('cruds.server.fields.active_helper') }}
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
