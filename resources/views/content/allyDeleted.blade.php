@extends('layouts.app')

@section('titel', ucfirst(__('ui.titel.ally')).': '.\App\Util\BasicFunctions::decodeName($allyTopData->name))

@section('content')
    <div class="row justify-content-center">
        <!-- Titel für Tablet | PC -->
        <div class="p-lg-3 mx-auto my-1 text-center d-none d-lg-block">
            <h1 class="font-weight-normal">{{ ucfirst(__('ui.titel.ally')).': '.\App\Util\BasicFunctions::decodeName($allyTopData->name).' ['.\App\Util\BasicFunctions::decodeName($allyTopData->tag).']' }}</h1>
        </div>
        <!-- ENDE Titel für Tablet | PC -->
        <!-- Titel für Mobile Geräte -->
        <div class="p-lg-3 mx-auto my-1 text-center d-lg-none truncate">
            <h1 class="font-weight-normal">
                {{ ucfirst(__('ui.titel.ally')).': ' }}
            </h1>
            <h4>
                {{ \App\Util\BasicFunctions::decodeName($allyTopData->name) }}
                <br>
                [{{ \App\Util\BasicFunctions::decodeName($allyTopData->tag) }}]
            </h4>
        </div>
        <!-- ENDE Titel für Tablet | PC -->
        <!-- Informationen -->
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <x-record.stat_elm_ally_top :data='$allyTopData' :worldData='$worldData' :conquer='$conquer' :allyChanges='$allyChanges' exists="false"/>
                </div>
            </div>
        </div>
        <!-- ENDE Informationen -->
    </div>
@endsection
