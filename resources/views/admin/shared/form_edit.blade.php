@extends('layouts.admin')

@section('content')
<div class="card">
    <div class="card-header">{{ $header }}</div>

    <div class="card-body">
        <form action="{{ $route }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method($method)
            @foreach($formEntries as $formEntry)
            @php
                $value = old($formEntry['id'], $formEntry['value'] ?? '');
            @endphp
            <div class="form-group {{ $errors->has($formEntry['id']) ? 'has-error' : '' }}">
                @if($formEntry['type'] != "check")
                    <h4><label for="{{ $formEntry['id'] }}">{{ $formEntry['name'] }}{{ $formEntry['required']?' *':'' }}</label></h4>
                @endif
                
                @switch($formEntry['type'])
                @case('text')
                    <input type="text" id="{{ $formEntry['id'] }}" name="{{ $formEntry['id'] }}"  class="form-control{{ $errors->has($formEntry['id']) ? ' is-invalid' : '' }}" value="{{ $value }}" {{
                           $formEntry['readonly']?' readonly':'' }}{{ $formEntry['required']?' required':'' }}>
                    @break
                
                @case('password')
                    <input type="password" id="{{ $formEntry['id'] }}" name="{{ $formEntry['id'] }}"  class="form-control{{ $errors->has($formEntry['id']) ? ' is-invalid' : '' }}" {{
                           $formEntry['readonly']?' readonly':'' }}{{ $formEntry['required']?' required':'' }}>
                    @break
                
                @case('select')
                    @if(!$formEntry['multiple'])
                        <select id="{{ $formEntry['id'] }}" name="{{ $formEntry['id'] }}"  class="select2 select2-single form-control{{ $errors->has($formEntry['id']) ? ' is-invalid' : '' }}" {{
                               $formEntry['readonly']?' readonly':'' }}{{ $formEntry['required']?' required':'' }}>
                            @foreach($formEntry['options'] as $key => $val)
                                @if(isset($formEntry['raw']) && $formEntry['raw'])
                                    <option value="{{ $key }}" {{ $value==$key ? 'selected' : '' }} data-content="{!! $val !!}">{{ $key }}</option>
                                @else
                                    <option value="{{ $key }}" {{ $value==$key ? 'selected' : '' }}>{{ $val }}</option>
                                @endif
                            @endforeach
                        </select>
                    @else
                        <select id="{{ $formEntry['id'] }}" name="{{ $formEntry['id'] }}"  class="select2 select2-multi form-control{{ $errors->has($formEntry['id']) ? ' is-invalid' : '' }}" {{
                               $formEntry['readonly']?' readonly':'' }}{{ $formEntry['required']?' required':'' }} multiple="multiple">
                            {{ print_r($value) }}
                            @foreach($formEntry['options'] as $key => $val)
                                @if(isset($formEntry['raw']) && $formEntry['raw'])
                                    <option value="{{ $key }}" {{ $value->contains($key) ? 'selected' : '' }} data-content="{!! $val !!}">{{ $key }}</option>
                                @else
                                    <option value="{{ $key }}" {{ $value->contains($key) ? 'selected' : '' }}>{{ $val }}</option>
                                @endif
                            @endforeach
                        </select>
                    @endif
                    @break
                
                @case('tinymce')
                    <div class='{{ $errors->has($formEntry['id']) ? ' is-invalid' : '' }}'>
                        <textarea name="{{ $formEntry['id'] }}" class="description w-100">{{ $value }}</textarea>
                    </div>
                    @break
                
                @case('textarea')
                    <div class='{{ $errors->has($formEntry['id']) ? ' is-invalid' : '' }}'>
                        <textarea name="{{ $formEntry['id'] }}" class="w-100">{{ $value }}</textarea>
                    </div>
                    @break
                
                @case('fas')
                    <select id="{{ $formEntry['id'] }}" name="{{ $formEntry['id'] }}" class="form-control fas-icon-picker {{ $errors->has($formEntry['id']) ? ' is-invalid' : '' }}" {{
                           $formEntry['readonly']?' readonly':'' }}{{ $formEntry['required']?' required':'' }}>
                        <option @if($value=='') selected="selected" @endif value="">No icon</option>
                        @foreach (\App\Util\Icon::fontawesome() as $key =>$icon)
                            <option @if($value==$key) selected="selected" @endif value='{{ $key }}'>{{ $key }}</option>
                        @endforeach
                    </select>
                    @break
                
                @case('optionColor')
                    @foreach($formEntry['options'] as $option)
                        <div class="form-check form-check-inline {{ $errors->has($formEntry['id']) ? ' is-invalid' : '' }}">
                            <input class="form-check-input" type="radio" name="{{ $formEntry['id'] }}" id="{{ $formEntry['id'].$loop->iteration }}" value="{{ $option }}" {{ ($formEntry['value'] == $option)? 'checked' : '' }}>
                            <label class="form-check-label" for="{{ $formEntry['id'].$loop->iteration }}" style="width: 20px; height: 20px; background-color: {{ $option }}"></label>
                        </div>
                    @endforeach
                    @break
                
                @case('check')
                    <div class="form-check form-check-inline {{ $errors->has($formEntry['id']) ? ' is-invalid' : '' }}">
                        <input type="checkbox" id="{{ $formEntry['id'] }}" name="{{ $formEntry['id'] }}" class="form-check-input" {{ $formEntry['value'] ? 'checked' : '' }}>
                        <label for="{{ $formEntry['id'] }}">{{ $formEntry['name'] }}{{ $formEntry['required']?' *':'' }}</label>
                    </div>
                    @break
                @endswitch
                
                @if($errors->has($formEntry['id']))
                    <em class="invalid-feedback">
                        {{ $errors->first($formEntry['id']) }}
                    </em>
                @endif
            </div>
            @endforeach
            <div>
                <input class="btn btn-danger" type="submit" value="{{ trans('global.save') }}">
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
@setFalse($needTinymce)
@setFalse($needFasPicker)
@setFalse($needSelect2)
@foreach($formEntries as $formEntry)
    @if($formEntry['type'] == 'tinymce') @setTrue($needTinymce) @endif
    @if($formEntry['type'] == 'fas') @setTrue($needFasPicker) @endif
    @if($formEntry['type'] == 'select') @setTrue($needSelect2) @endif
@endforeach
@if($needTinymce)
    <script src="{{ asset('plugin/tinymce/tinymce.min.js') }}"></script>
    <script>
        tinymce.init({
            selector:'textarea.description',
            plugins: 'print preview fullpage powerpaste searchreplace autolink directionality advcode visualblocks visualchars fullscreen image link media mediaembed template codesample table charmap hr pagebreak nonbreaking anchor toc insertdatetime advlist lists wordcount tinymcespellchecker a11ychecker imagetools textpattern help formatpainter permanentpen pageembed tinycomments mentions linkchecker',
            toolbar: 'formatselect | bold italic strikethrough forecolor backcolor permanentpen formatpainter | link image media pageembed | alignleft aligncenter alignright alignjustify  | numlist bullist outdent indent | removeformat | addcomment',
            mobile: {
                theme: 'silver'
            }
        });
    </script>
@endif
@if($needFasPicker)
    <script src="{{ asset('plugin/fontIconPicker/jquery.fonticonpicker.min.js') }}"></script>
    <script>
        var picker =$('.fas-icon-picker').fontIconPicker({
            theme: 'fip-bootstrap'
        });
    </script>
@endif
@if($needSelect2)
    <script src="{{ asset('plugin/select2/select2.full.min.js') }}"></script>
    <script>
        function loadText(state) {
            if(!state.element) return state.text;
            
            if(!state.element.attributes['data-content']) return state.text;
            console.log($(state.element.attributes['data-content'].nodeValue));
            return $("<p>"+state.element.attributes['data-content'].nodeValue+"</p>");
        }
        
        $('.select2-single').select2({
            theme: "bootstrap4",
            minimumResultsForSearch: 0,
            templateResult: loadText,
        });
        $('.select2-multi').select2({
            theme: "bootstrap4",
            minimumResultsForSearch: 0,
            closeOnSelect: false,
            templateResult: loadText,
        });
    </script>
@endif
@endsection

@section('styles')
@setFalse($needTinymce)
@setFalse($needFasPicker)
@setFalse($needSelect2)
@foreach($formEntries as $formEntry)
    @if($formEntry['type'] == 'tinymce') @setTrue($needTinymce) @endif
    @if($formEntry['type'] == 'fas') @setTrue($needFasPicker) @endif
    @if($formEntry['type'] == 'select') @setTrue($needSelect2) @endif
@endforeach
@if($needFasPicker)
    <link href="{{ asset('plugin/fontIconPicker/jquery.fonticonpicker.min.css') }}" rel="stylesheet">
    <link href="{{ asset('plugin/fontIconPicker/jquery.fonticonpicker.bootstrap.min.css') }}" rel="stylesheet">
@endif
@if($needSelect2)
    <link href="{{ asset('plugin/select2/select2.min.css') }}" rel="stylesheet" />
    <link href="{{ asset('plugin/select2/select2-bootstrap4.min.css') }}" rel="stylesheet" />
@endif
@endsection
