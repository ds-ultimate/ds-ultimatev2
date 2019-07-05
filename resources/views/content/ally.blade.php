@extends('layouts.temp')

@section('titel', ucfirst(__('Stamm')).': '.\App\Util\BasicFunctions::outputName($allyData->name))

@section('content')
    <div class="row">
        <div class="p-lg-5 mx-auto my-1 text-center">
            <h1 class="font-weight-normal">{{ ucfirst(__('Stamm')).': '.\App\Util\BasicFunctions::outputName($allyData->name).' ['.\App\Util\BasicFunctions::outputName($allyData->tag).']' }}</h1>
        </div>
        <div class="col-12 mx-2">
            <div class="card">
                <table class="table table-bordered no-wrap">
                    <thead>
                    <tr>
                        <th>{{ ucfirst(__('Rang')) }}</th>
                        <th>{{ ucfirst(__('Name')) }}</th>
                        <th>{{ ucfirst(__('Tag')) }}</th>
                        <th>{{ ucfirst(__('Punkte')) }}</th>
                        <th>{{ ucfirst(__('Dörfer')) }}</th>
                        <th>{{ ucfirst(__('Mitglieder')) }}</th>
                        <th>{{ ucfirst(__('Punkte pro Spieler')) }}</th>
                        <th>{{ ucfirst(__('Punkte pro Dorf')) }}</th>
                        <th>{{ ucfirst(__('Eroberungen')) }}</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr>
                        <th>{{ \App\Util\BasicFunctions::numberConv($allyData->rank) }}</th>
                        <td>{{ \App\Util\BasicFunctions::outputName($allyData->name) }}</td>
                        <td>{{ \App\Util\BasicFunctions::outputName($allyData->tag) }}</td>
                        <td>{{ \App\Util\BasicFunctions::numberConv($allyData->points) }}</td>
                        <td>{{ \App\Util\BasicFunctions::numberConv($allyData->village_count) }}</td>
                        <td>{{ \App\Util\BasicFunctions::numberConv($allyData->member_count) }}</td>
                        <td>{{ ($allyData->points != 0 && $allyData->member_count != 0)?\App\Util\BasicFunctions::numberConv($allyData->points/$allyData->member_count): '-' }}</td>
                        <td>{{ ($allyData->points != 0 && $allyData->village_count != 0)?\App\Util\BasicFunctions::numberConv($allyData->points/$allyData->village_count): '-' }}</td>
                        <td>{{ $conquer->get('total') }}(<i class="text-success">{{ $conquer->get('new') }}</i>-<i class="text-danger">{{ $conquer->get('old') }}</i>)</td>
                    </tr>
                    </tbody>
                </table>
                <br>
                <table class="table table-bordered no-wrap">
                    <thead>
                    <tr>
                        <th colspan="3">{{ __('Besiegte Gegner') }}-{{ __('Insgesamt') }}</th>
                        <th colspan="2">{{ __('Besiegte Gegner') }}-{{ __('Angreifer') }}</th>
                        <th colspan="2">{{ __('Besiegte Gegner') }}-{{ __('Verteidiger') }}</th>
                    </tr>
                    <tr>
                        <th>{{ ucfirst(__('Rang')) }}</th>
                        <th>{{ ucfirst(__('Punkte')) }}</th>
                        <th>{{ ucfirst(__('KP-Rate')) }}</th>
                        <th>{{ ucfirst(__('Rang')) }}</th>
                        <th>{{ ucfirst(__('Punkte')) }}</th>
                        <th>{{ ucfirst(__('Rang')) }}</th>
                        <th>{{ ucfirst(__('Punkte')) }}</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr>
                        <td>{{ \App\Util\BasicFunctions::numberConv($allyData->gesBashRank) }}</td>
                        <td>{{ \App\Util\BasicFunctions::numberConv($allyData->gesBash) }}</td>
                        <td>{{ \App\Util\BasicFunctions::numberConv(($allyData->gesBash/$allyData->points)*100) }}%</td>
                        <th>{{ \App\Util\BasicFunctions::numberConv($allyData->offBashRank) }}</th>
                        <td>{{ \App\Util\BasicFunctions::numberConv($allyData->offBash) }}</td>
                        <th>{{ \App\Util\BasicFunctions::numberConv($allyData->defBashRank) }}</th>
                        <td>{{ \App\Util\BasicFunctions::numberConv($allyData->defBash) }}</td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="col-12"><h2>{{ ucfirst(__('Spieler')) }}</h2></div>
        <div class="col-1"></div>
        <div class="col-10">
            <table id="table_id" class="table table-hover table-sm w-100">
                <thead>
                <tr class="d-none d-md-table-row">
                    <th colspan="6">{{ ucfirst(__('Allgemein')) }}</th>
                    <th colspan="4">{{ ucfirst(__('Besiegte Gegner')) }}</th>
                </tr>
                <tr>
                    <th>{{ ucfirst(__('Rang')) }}</th>
                    <th>{{ ucfirst(__('Name')) }}</th>
                    <th>{{ ucfirst(__('Stamm')) }}</th>
                    <th>{{ ucfirst(__('Punkte')) }}</th>
                    <th>{{ ucfirst(__('Dörfer')) }}</th>
                    <th>{{ ucfirst(__('Punkte pro Dorf')) }}</th>
                    <th>{{ ucfirst(__('Insgesamt')) }}</th>
                    <th>{{ ucfirst(__('Angreifer')) }}</th>
                    <th>{{ ucfirst(__('Verteidiger')) }}</th>
                    <th>{{ ucfirst(__('Unterstützer')) }}</th>
                </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
        </div>
        <div class="col-1"></div>
        <div class="col-6">
            <div class="col-12">
                Diagramm:
                <select id="statsGeneral" class="form-control">
                    @for($i = 0; $i < count($statsGeneral); $i++)
                        <option value="{{ $statsGeneral[$i] }}" {{ ($i == 0)? 'selected=""' : null }}>{{ __('chart.titel_'.$statsGeneral[$i]) }}</option>
                    @endfor
                </select>
            </div>
            <div class="col-12">
                @for($i = 0; $i < count($statsGeneral); $i++)
                    <div id="{{ $statsGeneral[$i] }}" class="col-12 position-absolute px-0">
                        <div class="card">
                            <div id="chart-{{ $statsGeneral[$i] }}"></div>
                        </div>
                    </div>
                @endfor
            </div>
        </div>
        <div class="col-6">
            <div class="col-12">
                Diagramm:
                <select id="statsBash" class="form-control">
                    @for($i = 0; $i < count($statsBash); $i++)
                        <option value="{{ $statsBash[$i] }}" {{ ($i == 0)? 'selected=""' : null }}>{{ __('chart.titel_'.$statsBash[$i]) }}</option>
                    @endfor
                </select>
            </div>
            <div class="col-12">
                @for($i = 0; $i < count($statsBash); $i++)
                    <div id="{{ $statsBash[$i] }}" class="col-12 position-absolute px-0">
                        <div class="card">
                            <div id="chart-{{ $statsBash[$i] }}"></div>
                        </div>
                    </div>
                @endfor
            </div>
        </div>
    </div>
@endsection

@section('js')
    <script>
        $(document).ready(function () {
            $("#{{ $statsGeneral[0] }}").css('visibility', 'visible');
            $("#{{ $statsGeneral[1] }}").css('visibility', 'hidden');
            $("#{{ $statsGeneral[2] }}").css('visibility', 'hidden');
            $("#{{ $statsBash[0] }}").css('visibility', 'visible');
            $("#{{ $statsBash[1] }}").css('visibility', 'hidden');
            $("#{{ $statsBash[2] }}").css('visibility', 'hidden');
        });

        $("#statsGeneral").change(function () {
            var option1 = $("#statsGeneral").val();
            if (option1 == '{{ $statsGeneral[0] }}') {
                $("#{{ $statsGeneral[0] }}").css('visibility', 'visible');
                $("#{{ $statsGeneral[1] }}").css('visibility', 'hidden');
                $("#{{ $statsGeneral[2] }}").css('visibility', 'hidden');
            }
            if (option1 == '{{ $statsGeneral[1] }}') {
                $("#{{ $statsGeneral[0] }}").css('visibility', 'hidden');
                $("#{{ $statsGeneral[1] }}").css('visibility', 'visible');
                $("#{{ $statsGeneral[2] }}").css('visibility', 'hidden');
            }
            if (option1 == '{{ $statsGeneral[2] }}') {
                $("#{{ $statsGeneral[0] }}").css('visibility', 'hidden');
                $("#{{ $statsGeneral[1] }}").css('visibility', 'hidden');
                $("#{{ $statsGeneral[2] }}").css('visibility', 'visible');
            }
        });

        $("#statsBash").change(function () {
            var option1 = $("#statsBash").val();
            if (option1 == '{{ $statsBash[0] }}') {
                $("#{{ $statsBash[0] }}").css('visibility', 'visible');
                $("#{{ $statsBash[1] }}").css('visibility', 'hidden');
                $("#{{ $statsBash[2] }}").css('visibility', 'hidden');
            }
            if (option1 == '{{ $statsBash[1] }}') {
                $("#{{ $statsBash[0] }}").css('visibility', 'hidden');
                $("#{{ $statsBash[1] }}").css('visibility', 'visible');
                $("#{{ $statsBash[2] }}").css('visibility', 'hidden');
            }
            if (option1 == '{{ $statsBash[2] }}') {
                $("#{{ $statsBash[0] }}").css('visibility', 'hidden');
                $("#{{ $statsBash[1] }}").css('visibility', 'hidden');
                $("#{{ $statsBash[2] }}").css('visibility', 'visible');
            }
        });

    </script>
    <script>

        $(document).ready( function () {
            $.extend( $.fn.dataTable.defaults, {
                responsive: true
            } );

            $('#table_id').DataTable({
                "columnDefs": [
                    {"targets": 3, "className": 'text-right'},
                    {"targets": 4, "className": 'text-right'},
                    {"targets": 5, "className": 'text-right'},
                    {"targets": 6, "className": 'text-right'},
                    {"targets": 7, "className": 'text-right'},
                    {"targets": 8, "className": 'text-right'},
                    {"targets": 9, "className": 'text-right'},
                ],
                "processing": true,
                "serverSide": true,
                "ajax": "{{ route('api.allyPlayer', [$worldData->get('server'), $worldData->get('world'), $allyData->allyID]) }}",
                "columns": [
                    { "data": "rank" },
                    { "data": "name", "render": function (value, type, row) {return "<a href='{{ route('world', [$worldData->get('server'), $worldData->get('worldID')]) }}/player/"+ row.playerID +"'>"+ value +'</a>'}},
                    { "data": "ally", "render": function (value, type, row) {return "<a href='{{ route('world', [$worldData->get('server'), $worldData->get('worldID')]) }}/ally/"+ row.ally_id +"'>"+ value +'</a>'}, "orderable": false},
                    { "data": "points", "render": function (value) {return numeral(value).format('0.[00] a')}},
                    { "data": "village_count", "render": function (value) {return numeral(value).format('0,0')}},
                    { "data": "village_points", "render": function (value) {return numeral(value).format('0,0')}, "orderable": false},
                    { "data": "gesBash" , "render": function (value) {return numeral(value).format('0.[00] a')}},
                    { "data": "offBash", "render": function (value) {return numeral(value).format('0.[00] a')} },
                    { "data": "defBash", "render": function (value) {return numeral(value).format('0.[00] a')} },
                    { "data": "utBash", "render": function (value) {return numeral(value).format('0.[00] a')}, "orderable": false},
                ],
                responsive: true,
                {!! \App\Util\Datatable::language() !!}
            });
        } );
    </script>
    {!! $chartJS !!}
@endsection
