@extends('layouts.admin')

@section('content')
<div class="card">
    <div class="card-header">{{ $header }}</div>

    <div class="card-body">
        <form action="{{ $route }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method($method)
            @foreach($formEntries as $formEntry)
                <x-edit_element :formEntry="$formEntry"/>
            @endforeach
            <div>
                <input class="btn btn-danger" type="submit" value="{{ __('global.save') }}">
            </div>
        </form>
    </div>
</div>
@endsection

@push('js')
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
        $(function() {
            tinymce.init({
                selector:'textarea.tinymceEdit',
                plugins: 'print preview fullpage powerpaste searchreplace autolink directionality advcode visualblocks visualchars fullscreen image link media mediaembed template codesample table charmap hr pagebreak nonbreaking anchor toc insertdatetime advlist lists wordcount tinymcespellchecker a11ychecker imagetools textpattern help formatpainter permanentpen pageembed tinycomments mentions linkchecker',
                toolbar: 'formatselect | bold italic strikethrough forecolor backcolor permanentpen formatpainter | link image media pageembed | alignleft aligncenter alignright alignjustify  | numlist bullist outdent indent | removeformat | addcomment',
                mobile: {
                    theme: 'silver'
                }
            })
        });
    </script>
@endif
@if($needFasPicker)
    <script src="{{ asset('plugin/fontIconPicker/jquery.fonticonpicker.min.js') }}"></script>
    <script>
        $(function() {
            var picker =$('.fas-icon-picker').fontIconPicker({
                theme: 'fip-bootstrap'
            });
            $('.fas-icon-picker').hide()
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
        
        $(function() {
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
        });
    </script>
@endif
@endpush

@push('style')
@setFalse($needTinymce)
@setFalse($needFasPicker)
@foreach($formEntries as $formEntry)
    @if($formEntry['type'] == 'tinymce') @setTrue($needTinymce) @endif
    @if($formEntry['type'] == 'fas') @setTrue($needFasPicker) @endif
@endforeach
@if($needFasPicker)
    <link href="{{ asset('plugin/fontIconPicker/jquery.fonticonpicker.min.css') }}" rel="stylesheet">
    <link href="{{ asset('plugin/fontIconPicker/jquery.fonticonpicker.bootstrap.min.css') }}" rel="stylesheet">
@endif
@endpush
