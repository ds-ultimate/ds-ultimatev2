@extends('layouts.temp')

@section('content')
    <div class="row justify-content-center">
        <div class="col-10">
            <div class="col-12">
                <ul id = "lang_menu">
                    <li class = "language{{ App::isLocale('de') ? ' active' : '' }}"><a href="{{ route('locale', 'de') }}">Deutsch</a></li>
                    <li class = "language{{ App::isLocale('en') ? ' active' : '' }}"><a href="{{ route('locale', 'en') }}">English</a></li>
                </ul>
            </div>
                <table id="table_id">
                    <thead>
                    <tr>
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
                    @foreach($playerArray as $player)
                        <tr>
                            <th>{{ $player->rank }}</th>
                            <td>{!! \App\Util\BasicFunctions::linkPlayer($worldData, $player->playerID, \App\Util\BasicFunctions::outputName($player->name)) !!}</td>
                            <td>{!! ($player->ally_id != 0)?\App\Util\BasicFunctions::linkAlly($worldData, $player->ally_id, \App\Util\BasicFunctions::outputName($player->allyLatest->tag)) : '-' !!}</td>
                            <td>{{ \App\Util\BasicFunctions::numberConv($player->points) }}</td>
                            <td>{{ \App\Util\BasicFunctions::numberConv($player->village_count) }}</td>
                            <td>{{ \App\Util\BasicFunctions::numberConv(($player->points == 0 || $player->village_count == 0)? 0 : ($player->points/$player->village_count)) }}</td>
                            <td>{{ \App\Util\BasicFunctions::numberConv($player->gesBash) }}</td>
                            <td>{{ \App\Util\BasicFunctions::numberConv($player->offBash) }}</td>
                            <td>{{ \App\Util\BasicFunctions::numberConv($player->deffBash) }}</td>
                            <td>{{ \App\Util\BasicFunctions::numberConv($player->gesBash - $player->offBash - $player->deffBash) }}</td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            @if ($page > 1)
                <a href="{{ route('worldPlayer', [\App\Util\BasicFunctions::getServer($worldData->get('name')), $worldData->get('world'), $page-1]) }}">{!! __('pagination.Zurück') !!}</a>
                <br>
            @endif
            <a href="{{ route('worldPlayer', [\App\Util\BasicFunctions::getServer($worldData->get('name')), $worldData->get('world'), $page+1]) }}">{!!  __('pagination.Weiter') !!}</a>
        </div>
    </div>
<script>
    $(document).ready( function () {
        $('#table_id').DataTable({
            language:{
                "decimal": ",",
                "thousands": ".",
                "sEmptyTable":   	"{!! __('datatable.sEmptyTable') !!}",
                "sInfo":         	"{!! __('datatable.sInfo') !!}",
                "sInfoEmpty":    	"{!! __('datatable.sInfoEmpty') !!}",
                "sInfoFiltered": 	"{!! __('datatable.sInfoFiltered') !!}",
                "sInfoPostFix":  	"",
                "sInfoThousands":  	"{!! __('datatable.sInfoThousands') !!}",
                "sLengthMenu":   	"{!! __('datatable.sLengthMenu') !!}",
                "sLoadingRecords": 	"{!! __('datatable.sLoadingRecords') !!}",
                "sProcessing":   	"{!! __('datatable.sProcessing') !!}",
                "sSearch":       	"{!! __('datatable.sSearch') !!}",
                "sZeroRecords":  	"{!! __('datatable.sZeroRecords') !!}",
                "oPaginate": {
                    "sFirst":    	"{!! __('datatable.oPaginate_sFirst') !!}",
                    "sPrevious": 	"{!! __('datatable.oPaginate_sPrevious') !!}",
                    "sNext":     	"{!! __('datatable.oPaginate_sNext') !!}",
                    "sLast":     	"{!! __('datatable.oPaginate_sLast') !!}"
                },
                "oAria": {
                    "sSortAscending":  "{!! __('datatable.oAria_sSortAscending') !!}",
                    "sSortDescending": "{!! __('datatable.oAria_sSortDescending') !!}"
                },
                "select": {
                    "rows": {
                        "_": "{!! __('datatable.select_rows__') !!}",
                        "0": "",
                        "1": "{!! __('datatable.select_rows_1') !!}"
                    }
                },
                "buttons": {
                    "print":	"{!! __('datatable.buttons_print') !!}",
                    "colvis":	"{!! __('datatable.buttons_colvis') !!}",
                    "copy":		"{!! __('datatable.buttons_copy') !!}",
                    "copyTitle":	"{!! __('datatable.buttons_copyTitle') !!}",
                    "copyKeys":	"{!! __('datatable.buttons_copyKeys') !!}",
                    "copySuccess": {
                        "_": "{!! __('datatable.buttons_copySuccess__') !!}",
                        "1": "{!! __('datatable.buttons_copySuccess_1') !!}"
                    },
                    "pageLength": {
                        "-1": "{!! __('datatable.buttons_pageLength_-1') !!}",
                        "_":  "{!! __('datatable.buttons_pageLength__') !!}"
                    }
                }
            }
        });
        jQuery.extend(jQuery.fn.dataTableExt.oSort, {
            "numeric-comma-pre": function (a) {
                // prepare number
                a = +(a.replace(",", "."));
                a = (isNaN(a)) ? Number.MAX_VALUE : a;
                return a;
            },
            "numeric-comma-asc": function (a, b) {
                return ((a < b) ? -1 : ((a > b) ? 1 : 0));
            },
            "numeric-comma-desc": function (a, b) {
                return ((a < b) ? 1 : ((a > b) ? -1 : 0));
            }
        });
    } );
</script>
@endsection
