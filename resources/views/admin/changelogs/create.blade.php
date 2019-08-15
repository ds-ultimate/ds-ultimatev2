@extends('layouts.admin')

@section('styles')
    
@stop

@section('content')
<div class="card">
    <div class="card-header">
        {{ trans('global.create') }} {{ trans('cruds.changelog.title_singular') }}
    </div>
    <div class="card-body">
        <form action="{{ route("admin.changelogs.store") }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="form-group {{ $errors->has('version') ? 'has-error' : '' }}">
                <h4><label for="version">{{ trans('cruds.changelog.fields.version') }}</label></h4>
                <input type="text" id="version" name="version" class="form-control" value="{{ old('version', isset($changelog) ? $changelog->version : '') }}">
                @if($errors->has('version'))
                    <em class="invalid-feedback">
                        {{ $errors->first('version') }}
                    </em>
                @endif
            </div>
            <div class="form-group">
                <h4><label for="icon">{{ trans('cruds.changelog.fields.icon') }}*</label></h4>
                <select id="icon" name="icon" class="form-control">
                    <option value="">No icon</option>
                    @foreach (\App\Util\BasicFunctions::fontawesome() as $key =>$icon)
                        <option>{{ $key }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <h4><label for="icon">{{ trans('cruds.changelog.fields.color') }}</label></h4>
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="inlineRadioOptions" id="color" value="#20a8d8">
                    <label class="form-check-label bg-primary" for="inlineRadio1" style="width: 20px; height: 20px;"></label>
                </div>
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="inlineRadioOptions" id="color" value="#f86c6b">
                    <label class="form-check-label bg-danger" for="inlineRadio1" style="width: 20px; height: 20px;"></label>
                </div>
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="inlineRadioOptions" id="color" value="#c8ced3">
                    <label class="form-check-label bg-secondary" for="inlineRadio1" style="width: 20px; height: 20px;"></label>
                </div>
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="inlineRadioOptions" id="color" value="#4dbd74">
                    <label class="form-check-label bg-success" for="inlineRadio1" style="width: 20px; height: 20px;"></label>
                </div>
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="inlineRadioOptions" id="color" value="#ffc107">
                    <label class="form-check-label bg-warning" for="inlineRadio1" style="width: 20px; height: 20px;"></label>
                </div>
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="inlineRadioOptions" id="color" value="#000000">
                    <label class="form-check-label" for="inlineRadio1" style="background-color: #000000; width: 20px; height: 20px;"></label>
                </div>
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="inlineRadioOptions" id="color" value="#63c2de">
                    <label class="form-check-label bg-info" for="inlineRadio1" style="width: 20px; height: 20px;"></label>
                </div>
            </div>
            <div class="form-group {{ $errors->has('title') ? 'has-error' : '' }}">
                <h4><label for="title">{{ trans('cruds.changelog.fields.title') }}*</label></h4>
                <input type="text" id="title" name="title" class="form-control" value="{{ old('title', isset($changelog) ? $changelog->title : '') }}">
                @if($errors->has('title'))
                    <em class="invalid-feedback">
                        {{ $errors->first('title') }}
                    </em>
                @endif
            </div>
            <div class="form-group {{ $errors->has('content') ? 'has-error' : '' }}">
                <h4><label for="content">{{ trans('cruds.changelog.fields.content') }}</label></h4>
                <textarea name="content" class="description">{{ old('content', isset($changelog) ? $changelog->content : '') }}</textarea>
                @if($errors->has('content'))
                    <em class="invalid-feedback">
                        {{ $errors->first('content') }}
                    </em>
                @endif
                <p class="helper-block">
                    {{ trans('cruds.changelog.fields.content_helper') }}
                </p>
            </div>
            <div class="form-group {{ $errors->has('repository_html_url') ? 'has-error' : '' }}">
                <h4><label for="repository_html_url">{{ trans('cruds.changelog.fields.repository_html_url') }}</label></h4>
                <input type="text" id="repository_html_url" name="repository_html_url" class="form-control" value="{{ old('repository_html_url', isset($changelog) ? $changelog->repository_html_url : '') }}">
                @if($errors->has('repository_html_url'))
                    <em class="invalid-feedback">
                        {{ $errors->first('repository_html_url') }}
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

@section('scripts')
    <script src="{{ asset('plugin/tinymce/tinymce.min.js') }}"></script>
    <script src="{{ asset('plugin/fontIconPicker/jquery.fonticonpicker.min.js') }}"></script>
    <link href="{{ asset('plugin/fontIconPicker/jquery.fonticonpicker.min.css') }}" rel="stylesheet">
    <link href="{{ asset('plugin/fontIconPicker/jquery.fonticonpicker.bootstrap.min.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="https://kit-free.fontawesome.com/releases/latest/css/free.min.css" media="all">

    <script>
        $(document).ready(function () {
            var picker =$('#icon').fontIconPicker({
                theme: 'fip-bootstrap'
            });

            picker.setIcon('fab fa-github-square');

            tinymce.init({
                selector:'textarea.description',
                width: 'auto',
                height: 300,
                plugins: 'print preview fullpage powerpaste searchreplace autolink directionality advcode visualblocks visualchars fullscreen image link media mediaembed template codesample table charmap hr pagebreak nonbreaking anchor toc insertdatetime advlist lists wordcount tinymcespellchecker a11ychecker imagetools textpattern help formatpainter permanentpen pageembed tinycomments mentions linkchecker',
                toolbar: 'formatselect | bold italic strikethrough forecolor backcolor permanentpen formatpainter | link image media pageembed | alignleft aligncenter alignright alignjustify  | numlist bullist outdent indent | removeformat | addcomment',
                mobile: {
                    theme: 'silver'
                }
            });
        });
    </script>
@stop
