@props(['formEntry'])

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
            <textarea name="{{ $formEntry['id'] }}" class="tinymceEdit w-100" style="height: 500px">{{ $value }}</textarea>
        </div>
        @break

    @case('textarea')
        <div class='{{ $errors->has($formEntry['id']) ? ' is-invalid' : '' }}'>
            <textarea name="{{ $formEntry['id'] }}" class="w-100" style="height: 500px">{{ $value }}</textarea>
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