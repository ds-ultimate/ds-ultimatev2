@extends('layouts.admin')
@section('content')

<div class="card">
    <div class="card-header">
        {{ trans('global.create') }} {{ trans('cruds.bugreport.title_singular') }}
    </div>

    <div class="card-body">
        <form action="{{ route("admin.bugreports.store") }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="form-group {{ $errors->has('name') ? 'has-error' : '' }}">
                <label for="name">{{ trans('cruds.bugreport.fields.name') }}*</label>
                <input type="text" id="name" name="name" class="form-control" value="{{ Auth::user()->name }}" readonly required>
                @if($errors->has('name'))
                    <em class="invalid-feedback">
                        {{ $errors->first('name') }}
                    </em>
                @endif
            </div>
            <div class="form-group {{ $errors->has('email') ? 'has-error' : '' }}">
                <label for="email">{{ trans('cruds.bugreport.fields.email') }}*</label>
                <input type="text" id="email" name="email" class="form-control" value="{{ Auth::user()->email }}" readonly required>
                @if($errors->has('email'))
                    <em class="invalid-feedback">
                        {{ $errors->first('email') }}
                    </em>
                @endif
            </div>
            <div class="form-group {{ $errors->has('title') ? 'has-error' : '' }}">
                <label for="title">{{ trans('cruds.bugreport.fields.title') }}*</label>
                <input type="text" id="title" name="title" class="form-control" value="{{ old('title', isset($bugreport) ? $bugreport->title : '') }}" required>
                @if($errors->has('title'))
                    <em class="invalid-feedback">
                        {{ $errors->first('title') }}
                    </em>
                @endif
            </div>
            <div class="form-group {{ $errors->has('priority') ? 'has-error' : '' }}">
                <label for="priority">{{ trans('cruds.bugreport.fields.priority') }}*</label>
                <select class="form-control" name="priority">
                    <option value="0" {{ old('priority', isset($bugreport) ? ($bugreport->priority == 0 ? 'selected' : '') : '') }}>{{ __('user.bugreport.prioritySelect.low') }}</option>
                    <option value="1" {{ old('priority', isset($bugreport) ? ($bugreport->priority == 1 ? 'selected' : '') : '') }}>{{ __('user.bugreport.prioritySelect.normal') }}</option>
                    <option value="2" {{ old('priority', isset($bugreport) ? ($bugreport->priority == 2 ? 'selected' : '') : '') }}>{{ __('user.bugreport.prioritySelect.high') }}</option>
                    <option value="3" {{ old('priority', isset($bugreport) ? ($bugreport->priority == 3 ? 'selected' : '') : '') }}>{{ __('user.bugreport.prioritySelect.critical') }}</option>
                </select>
                @if($errors->has('priority'))
                    <em class="invalid-feedback">
                        {{ $errors->first('priority') }}
                    </em>
                @endif
            </div>
            <div class="form-group {{ $errors->has('status') ? 'has-error' : '' }}">
                <label for="status">{{ trans('cruds.bugreport.fields.status') }}*</label>
                <select class="form-control" name="status">
                    <option value="0" {{ old('priority', isset($bugreport) ? ($bugreport->status == 0 ? 'selected' : '') : '') }}>{{ __('cruds.bugreport.statusSelect.open') }}</option>
                    <option value="1" {{ old('priority', isset($bugreport) ? ($bugreport->status == 1 ? 'selected' : '') : '') }}>{{ __('cruds.bugreport.statusSelect.inprogress') }}</option>
                    <option value="2" {{ old('priority', isset($bugreport) ? ($bugreport->status == 2 ? 'selected' : '') : '') }}>{{ __('cruds.bugreport.statusSelect.resolved') }}</option>
                    <option value="3" {{ old('priority', isset($bugreport) ? ($bugreport->status == 3 ? 'selected' : '') : '') }}>{{ __('cruds.bugreport.statusSelect.close') }}</option>
                </select>
                @if($errors->has('status'))
                    <em class="invalid-feedback">
                        {{ $errors->first('status') }}
                    </em>
                @endif
            </div>
            <div class="form-group {{ $errors->has('description') ? 'has-error' : '' }}">
                <label for="description">{{ trans('cruds.bugreport.fields.description') }}*</label>
                <textarea type="text" id="description" name="description" class="form-control" required>{{ old('description', isset($bugreport) ? $bugreport->description : '') }}</textarea>
                @if($errors->has('description'))
                    <em class="invalid-feedback">
                        {{ $errors->first('description') }}
                    </em>
                @endif
            </div>
            <div class="form-group {{ $errors->has('url') ? 'has-error' : '' }}">
                <label for="url">{{ trans('cruds.bugreport.fields.url') }}</label>
                <input type="text" id="url" name="url" class="form-control" value="{{ old('url', isset($bugreport) ? $bugreport->url : '') }}" >
                @if($errors->has('url'))
                    <em class="invalid-feedback">
                        {{ $errors->first('url') }}
                    </em>
                @endif
            </div>
            <div>
                <input class="btn btn-danger" type="submit" value="{{ trans('global.save') }}">
            </div>
        </form>
    </div>
</div>
@endsection
