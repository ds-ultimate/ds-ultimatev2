@extends('layouts.temp')

@section('content')
    <div class="row justify-content-center">
        <div class="col-12">
            <div class="col-md-5 p-lg-5 mx-auto my-1 text-center">
                <h1 class="font-weight-normal">{{ env('APP_NAME') }}</h1>
            </div>
        </div>
        <!-- Normale Welten -->
        <div class="col-12 col-md-6 mt-2">
            <div class="card">
                <div class="card-body">
                    <h2 class="card-title">{{ __('ui.server.title') }}:</h2>
                    <table class="table table-hover table-striped no-wrap">
                        <thead>
                        <tr>
                            <th></th>
                            <th>{{ ucfirst(__('ui.server.code')) }}</th>
                            <th>{{ ucfirst(__('ui.server.dsLink')) }}</th>
                            <th>{{ ucfirst(__('ui.server.worlds')) }}</th>
                            <th></th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($serverArray as $serverData)
                            <tr>
                                <td><span class="flag-icon flag-icon-{{ $serverData->flag }}"></span></td>
                                <td>{{ $serverData->code }}</td>
                                <td><a href="{{$serverData->url}}">{{ $serverData->url }}</a></td>
                                <td>{{ $serverData->worlds->count() }}</td>
                                <td><a href="{{ route('server', [$serverData->code]) }}" class="btn btn-primary btn-sm">{{ __('ui.server.show') }}</a></td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <!-- ENDE Normale Welten -->
    </div>
@endsection
