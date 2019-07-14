@extends('layouts.admin')
@section('content')

<div class="card">
    <div class="card-header">
        {{ trans('global.edit') }} {{ trans('cruds.bugreport.title_singular') }}
    </div>

    <div class="card-body">
        <form action="{{ route("admin.bugreports.update", [$bugreport->id]) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            <div class="form-group">
                <label for="title">{{ trans('cruds.bugreport.fields.title') }}*</label>
                <input type="text" id="title" name="title" class="form-control-plaintext" value="{{ $bugreport->title }}" readonly>
            </div>
            <div class="form-group">
                <label for="description">{{ trans('cruds.bugreport.fields.description') }}*</label>
                <textarea type="text" id="description" name="description" class="form-control-plaintext" readonly>{{ $bugreport->description }}</textarea>
            </div>
            <div class="form-group {{ $errors->has('priority') ? 'has-error' : '' }}">
                <label for="priority">{{ trans('cruds.bugreport.fields.priority') }}*</label>
                <select class="form-control" name="priority">
                    <option value="0" {{ ($bugreport->priority == 0)? 'selected' : '' }}>{{ __('user.bugreport.prioritySelect.low') }}</option>
                    <option value="1" {{ ($bugreport->priority == 1)? 'selected' : '' }}>{{ __('user.bugreport.prioritySelect.normal') }}</option>
                    <option value="2" {{ ($bugreport->priority == 2)? 'selected' : '' }}>{{ __('user.bugreport.prioritySelect.high') }}</option>
                    <option value="3" {{ ($bugreport->priority == 3)? 'selected' : '' }}>{{ __('user.bugreport.prioritySelect.critical') }}</option>
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
                    <option value="0" {{ ($bugreport->status == 0)? 'selected' : '' }}>{{ __('cruds.bugreport.statusSelect.open') }}</option>
                    <option value="1" {{ ($bugreport->status == 1)? 'selected' : '' }}>{{ __('cruds.bugreport.statusSelect.inprogress') }}</option>
                    <option value="2" {{ ($bugreport->status == 2)? 'selected' : '' }}>{{ __('cruds.bugreport.statusSelect.resolved') }}</option>
                    <option value="3" {{ ($bugreport->status == 3)? 'selected' : '' }}>{{ __('cruds.bugreport.statusSelect.close') }}</option>
                </select>
                @if($errors->has('status'))
                    <em class="invalid-feedback">
                        {{ $errors->first('status') }}
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
