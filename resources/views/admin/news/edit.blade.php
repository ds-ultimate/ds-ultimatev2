@extends('layouts.admin')
@section('content')

<div class="card">
    <div class="card-header">
        {{ trans('global.edit') }} {{ trans('cruds.news.title_singular') }}
    </div>

    <div class="card-body">
        <form action="{{ route("admin.news.update", [$news->id]) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            <div class="form-group {{ $errors->has('content_de') ? 'has-error' : '' }}">
                <h2><label for="content_de">{{ trans('cruds.news.fields.content') }} DE</label></h2>
                <textarea name="content_de" class="description">{{ old('content_de', isset($news) ? $news->content_de : '') }}</textarea>
                @if($errors->has('content_de'))
                    <em class="invalid-feedback">
                        {{ $errors->first('content_de') }}
                    </em>
                @endif
                <p class="helper-block">
                    {{ trans('cruds.news.fields.content_helper') }}
                </p>
            </div>
            <div class="form-group {{ $errors->has('content_en') ? 'has-error' : '' }}">
                <h2><label for="content_en">{{ trans('cruds.news.fields.content') }} EN</label></h2>
                <textarea name="content_en" class="description">{{ old('content_en', isset($news) ? $news->content_en : '') }}</textarea>
                @if($errors->has('content_en'))
                    <em class="invalid-feedback">
                        {{ $errors->first('content_en') }}
                    </em>
                @endif
                <p class="helper-block">
                    {{ trans('cruds.news.fields.content_helper') }}
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
    <script src="{{ asset('plugin/tinymce/tinymce.min.js') }}"></script>
    <script>
        tinymce.init({
            selector:'textarea.description',
            width: 1500,
            height: 300,
            plugins: 'print preview fullpage powerpaste searchreplace autolink directionality advcode visualblocks visualchars fullscreen image link media mediaembed template codesample table charmap hr pagebreak nonbreaking anchor toc insertdatetime advlist lists wordcount tinymcespellchecker a11ychecker imagetools textpattern help formatpainter permanentpen pageembed tinycomments mentions linkchecker',
            toolbar: 'formatselect | bold italic strikethrough forecolor backcolor permanentpen formatpainter | link image media pageembed | alignleft aligncenter alignright alignjustify  | numlist bullist outdent indent | removeformat | addcomment',
            mobile: {
                theme: 'silver'
            }
        });
    </script>
@stop
