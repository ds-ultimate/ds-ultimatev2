@extends('layouts.temp')

@section('content')
    <div class="row justify-content-center">
        <div class="col-12">
            <div class="col-md-5 p-lg-5 mx-auto my-1 text-center">
                <h1 class="font-weight-normal">{{ __('user.bugreport.title') }}</h1>
            </div>
        </div>
        <div class="col-12 col-md-6 mt-2">
            <div class="card">
                <div class="card-body">
                    <h2 class="card-title">{{ __('user.formular') }}:</h2>
                    <form action="{{ route('form.bugreport.store') }}" method="post">
                        <div class="form-group">
                            <label for="name">{{ __('user.bugreport.name') }}*</label>
                            <input type="text" class="form-control" id="name" name="name" aria-describedby="nameHelp" placeholder="{{ __('user.bugreport.name') }}" required>
                            <small id="nameHelp" class="form-text text-muted">{{ __('user.bugreport.name_help') }}</small>
                        </div>
                        <div class="form-group">
                            <label for="email">{{ __('user.bugreport.email') }}*</label>
                            <input type="email" class="form-control" id="email" name="email" aria-describedby="emailHelp" placeholder="{{ __('user.bugreport.email') }}" required>
                            <small id="emailHelp" class="form-text text-muted">{{ __('user.bugreport.email_help') }}</small>
                        </div>
                        <div class="form-group">
                            <label for="title">{{ __('user.bugreport.form_title') }}*</label>
                            <input type="text" class="form-control" id="title" name="title" aria-describedby="titleHelp" placeholder="{{ __('user.bugreport.form_title') }}" required>
                            <small id="titleHelp" class="form-text text-muted">{{ __('user.bugreport.form_title_help') }}</small>
                        </div>
                        <div class="form-group">
                            <label for="priority">{{ __('user.bugreport.priority') }}*</label>
                            <select class="form-control" name="priority">
                                <option value="1">{{ __('user.bugreport.prioritySelect.low') }}</option>
                                <option value="2">{{ __('user.bugreport.prioritySelect.normal') }}</option>
                                <option value="3">{{ __('user.bugreport.prioritySelect.high') }}</option>
                                <option value="4">{{ __('user.bugreport.prioritySelect.critical') }}</option>
                            </select>
                            <small id="priorityHelp" class="form-text text-muted">{{ __('user.bugreport.priority_help') }}</small>
                        </div>
                        <div class="form-group">
                            <label for="description">{{ __('user.bugreport.description') }}*</label>
                            <textarea type="text" class="form-control" id="description" name="description" aria-describedby="descriptionHelp" placeholder="{{ __('user.bugreport.description') }}" required></textarea>
                            <small id="descriptionHelp" class="form-text text-muted">{{ __('user.bugreport.description_help') }}</small>
                        </div>
                        <div class="form-group">
                            <label for="url">{{ __('user.bugreport.url') }}</label>
                            <input type="text" class="form-control" id="url" name="url" aria-describedby="urlHelp" placeholder="{{ __('user.bugreport.url') }}">
                            <small id="urlHelp" class="form-text text-muted">{{ __('user.bugreport.url_help') }}</small>
                        </div>
                        @csrf
                        <div class="form-group form-check">
                            <input type="checkbox" class="form-check-input" id="exampleCheck1">
                            <label class="form-check-label" for="exampleCheck1">Check me out</label>
                        </div>
                        <button type="submit" class="btn btn-primary">{{ __('global.submit') }}</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
