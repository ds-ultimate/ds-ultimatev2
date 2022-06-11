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
                        <option value="{{ $key }}" @selected($value==$key) data-content="{!! $val !!}">{{ $key }}</option>
                    @else
                        <option value="{{ $key }}" @selected($value==$key) >{{ $val }}</option>
                    @endif
                @endforeach
            </select>
        @else
            <select id="{{ $formEntry['id'] }}" name="{{ $formEntry['id'] }}"  class="select2 select2-multi form-control{{ $errors->has($formEntry['id']) ? ' is-invalid' : '' }}" {{
                   $formEntry['readonly']?' readonly':'' }}{{ $formEntry['required']?' required':'' }} multiple="multiple">
                {{ print_r($value) }}
                @foreach($formEntry['options'] as $key => $val)
                    @if(isset($formEntry['raw']) && $formEntry['raw'])
                        <option value="{{ $key }}" @selected($value->contains($key)) data-content="{!! $val !!}">{{ $key }}</option>
                    @else
                        <option value="{{ $key }}" @selected($value->contains($key)) >{{ $val }}</option>
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
            <option @selected(value=="") value="">No icon</option>
            @foreach (\App\Util\Icon::fontawesome() as $key =>$icon)
                <option @selected(value==$key) value='{{ $key }}'>{{ $key }}</option>
            @endforeach
        </select>
        @break

    @case('optionColor')
        @foreach($formEntry['options'] as $option)
            <div class="form-check form-check-inline {{ $errors->has($formEntry['id']) ? ' is-invalid' : '' }}">
                <input class="form-check-input" type="radio" name="{{ $formEntry['id'] }}" id="{{ $formEntry['id'].$loop->iteration }}" value="{{ $option }}" @checked($formEntry['value'] == $option) >
                <label class="form-check-label" for="{{ $formEntry['id'].$loop->iteration }}" style="width: 20px; height: 20px; background-color: {{ $option }}"></label>
            </div>
        @endforeach
        @break

    @case('check')
        <div class="form-check form-check-inline {{ $errors->has($formEntry['id']) ? ' is-invalid' : '' }}">
            <input type="checkbox" id="{{ $formEntry['id'] }}" name="{{ $formEntry['id'] }}" class="form-check-input" @checked($formEntry['value']) >
            <label for="{{ $formEntry['id'] }}">{{ $formEntry['name'] }}{{ $formEntry['required']?' *':'' }}</label>
        </div>
        @break

    @case('time')
        <div class="{{ $errors->has($formEntry['id']) ? ' is-invalid' : '' }}">
            <input type="date" id="{{ $formEntry['id'] }}_date" name="{{ $formEntry['id'] }}_date" value="{{ $formEntry['value']['d'] }}">
            <input type="time" id="{{ $formEntry['id'] }}_time" name="{{ $formEntry['id'] }}_time" value="{{ $formEntry['value']['t'] }}">
        </div>
        @if($errors->has($formEntry['id'] . "_date"))
            <em class="invalid-feedback d-block">
                {{ $errors->first($formEntry['id'] . "_date") }}
            </em>
        @endif
        @if($errors->has($formEntry['id'] . "_time"))
            <em class="invalid-feedback d-block">
                {{ $errors->first($formEntry['id'] . "_time") }}
            </em>
        @endif
        @break
    @endswitch

    @if($errors->has($formEntry['id']))
        <em class="invalid-feedback d-block">
            {{ $errors->first($formEntry['id']) }}
        </em>
    @endif
</div>