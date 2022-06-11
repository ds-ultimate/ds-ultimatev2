@extends('layouts.app')

@section('content')
    <div class="row justify-content-center">
        <div class="col-12">
            <div class="col-md-5 p-lg-5 mx-auto my-1 text-center">
                <h1 class="font-weight-normal">{{ __('user.bugreport.title') }}</h1>
            </div>
        </div>
        <div class="col-12 col-md-8 mt-2">
            <div class="card">
                <div class="card-body">
                    <h2 class="card-title">{{ __('user.formular') }}:</h2>
                    <form action="{{ route('form.bugreport.store') }}" method="post">
                        <div class="form-group">
                            <label for="name">{{ __('user.bugreport.name') }}*</label>
                            <input type="text" class="form-control {{ $errors->has('name') ? 'is-invalid' : '' }}" id="name" value="{{ old('name', '') }}" name="name" aria-describedby="nameHelp" placeholder="{{ __('user.bugreport.name') }}" required>
                            @if($errors->has('name'))
                                <div class="invalid-feedback">
                                    {{ $errors->first('name') }}
                                </div>
                            @endif
                            <small id="nameHelp" class="form-text text-muted">{{ __('user.bugreport.name_help') }}</small>
                        </div>
                        <div class="form-group">
                            <label for="email">{{ __('user.bugreport.email') }}*</label>
                            <input type="email" class="form-control {{ $errors->has('email') ? 'is-invalid' : '' }}" id="email" value="{{ old('email', '') }}" name="email" aria-describedby="emailHelp" placeholder="{{ __('user.bugreport.email') }}" required>
                            @if($errors->has('email'))
                                <div class="invalid-feedback">
                                    {{ $errors->first('email') }}
                                </div>
                            @endif
                            <small id="emailHelp" class="form-text text-muted">{{ __('user.bugreport.email_help') }}</small>
                        </div>
                        <div class="form-group">
                            <label for="title">{{ __('user.bugreport.form_title') }}*</label>
                            <input type="text" class="form-control {{ $errors->has('title') ? 'is-invalid' : '' }}" id="title" value="{{ old('title', '') }}" name="title" aria-describedby="titleHelp" placeholder="{{ __('user.bugreport.form_title') }}" required>
                            @if($errors->has('title'))
                                <div class="invalid-feedback">
                                    {{ $errors->first('title') }}
                                </div>
                            @endif
                            <small id="titleHelp" class="form-text text-muted">{{ __('user.bugreport.form_title_help') }}</small>
                        </div>
                        <div class="form-group">
                            <label for="priority">{{ __('user.bugreport.priority') }}*</label>
                            <select class="form-control {{ $errors->has('priority') ? 'is-invalid' : '' }}" name="priority">
                                <option value="0" @selected((old('priority', 0)==0)) >{{ __('user.bugreport.prioritySelect.low') }}</option>
                                <option value="1" @selected((old('priority', 0)==1)) >{{ __('user.bugreport.prioritySelect.normal') }}</option>
                                <option value="2" @selected((old('priority', 0)==2)) >{{ __('user.bugreport.prioritySelect.high') }}</option>
                                <option value="3" @selected((old('priority', 0)==3)) >{{ __('user.bugreport.prioritySelect.critical') }}</option>
                            </select>
                            @if($errors->has('priority'))
                                <div class="in valid-feedback">
                                    {{ $errors->first('priority') }}
                                </div>
                            @endif
                            <small id="priorityHelp" class="form-text text-muted">{{ __('user.bugreport.priority_help') }}</small>
                        </div>
                        <div class="form-group">
                            <label for="description">{{ __('user.bugreport.description') }}*</label>
                            <textarea type="text" class="form-control {{ $errors->has('description') ? 'is-invalid' : '' }}" id="description" name="description" aria-describedby="descriptionHelp" placeholder="{{ __('user.bugreport.description') }}" required>{{ old('description', '') }}</textarea>
                            @if($errors->has('description'))
                                <div class="invalid-feedback">
                                    {{ $errors->first('description') }}
                                </div>
                            @endif
                            <small id="descriptionHelp" class="form-text text-muted">{{ __('user.bugreport.description_help') }}</small>
                        </div>
                        <div class="form-group">
                            <label for="url">{{ __('user.bugreport.url') }}</label>
                            <input type="text" class="form-control" id="url" value="{{ old('url', '') }}" name="url" aria-describedby="urlHelp" placeholder="{{ __('user.bugreport.url') }}">
                            <small id="urlHelp" class="form-text text-muted">{{ __('user.bugreport.url_help') }}</small>
                        </div>
                        @csrf
                        <div class="form-group">
                            {!! Captcha::display() !!}
                            @if($errors->has('g-recaptcha-response'))
                                <div class="text-danger">
                                    {{ $errors->first('g-recaptcha-response') }}
                                </div>
                            @endif
                        </div>
                        <button type="submit" class="btn btn-primary">{{ __('global.submit') }}</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
