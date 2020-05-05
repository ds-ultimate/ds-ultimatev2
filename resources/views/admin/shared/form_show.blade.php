@extends('layouts.admin')

@section('content')
<div class="card">
    <div class="card-header">{{ $header }}</div>

    <div class="card-body">
        <h1>{{ $title }}</h1>
        
        <table class="table table-bordered table-striped w-100"><tbody>
            @foreach($formEntries as $formEntry)
            <tr>
                <th>{{ $formEntry['name'] }}</th>
                @if($formEntry['escape'])
                <td>{{ $formEntry['value'] }}</td>
                @else
                <td>{!! $formEntry['value'] !!}</td>
                @endif
            </tr>
            @endforeach
        </tbody></table>
        @yield('additional_content')
        <a style="margin-top:20px;" class="btn btn-default" href="{{ url()->previous() }}">
            {{ __('global.back') }}
        </a>
    </div>
</div>
@endsection
