@extends('layouts.app')

@section('titel', $worldData->displayName().': '.__('ui.titel.overview').' '.__('ui.titel.player'))

@section('content')
    <div class="row justify-content-center">
        <div class="col-12">
            <div class="col-md-5 p-lg-5 mx-auto my-1 text-center">
                <h1 class="font-weight-normal">{{ $worldData->displayName() }}<br>{{ __('ui.tabletitel.top10').' '.__('ui.tabletitel.player') }}</h1>
            </div>
        </div>
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-8">
                            <img class="h-100 w-100" src="{{ route('tools.top10p', [$worldData->server->code, $worldData->name]) }}" />
                        </div>
                        <div class="col-4">
                            <table border="0">
                            @foreach ($ps as $p)
                                <tr>
                                    <td class="border" style="height: 40px; width: 100px; background-color: rgb({{ $p['color'][0] }},{{ $p['color'][1] }},{{ $p['color'][2] }})">

                                    </td>
                                    <td>
                                        {{ \App\Util\BasicFunctions::decodeName($p['name']) }}
                                    </td>
                                </tr>
                            @endforeach
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('js')
    <script>

    </script>
@endsection