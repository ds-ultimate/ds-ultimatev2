@extends('layouts.app')

@section('titel', $typeName.': '.$who)

@section('content')
<div class="row justify-content-center">
    <!-- Titel für Tablet | PC -->
    <div class="p-lg-5 mx-auto my-1 text-center d-none d-lg-block">
        <h1 class="font-weight-normal">{{ $typeName.': '. $who }}</h1>
    </div>
    <!-- ENDE Titel für Tablet | PC -->
    <!-- Titel für Mobile Geräte -->
    <div class="p-lg-5 mx-auto my-1 text-center d-lg-none truncate">
        <h1 class="font-weight-normal">
            {{ $typeName.': ' }}
        </h1>
        <h4>
            {{ $who }}
        </h4>
    </div>
    <!-- ENDE Titel für Tablet | PC -->
    <!-- Datachart Eroberungen -->
    <div class="col-12 mt-2">
        <div id="dropdownSettingsWrapper" style="display: none">
            <button id="dropdown-settings" type="button" class="btn dropdown-toggle ml-2" data-toggle="dropdown">
                <i class="fas fa-cog"></i>
            </button>
            <div id="dropdown-settings-div" class="dropdown-menu dropdown-menu-left" aria-labelledby="dropdown-settings">
                <a id="conquer-highlight-all" class="dropdown-item conquer-highlight">{{ __('ui.conquer.highlight.all') }}</a>
                @foreach(\App\Profile::$CONQUER_HIGHLIGHT_MAPPING as $key => $value)
                    @if(in_array($key, $allHighlight))
                        <a id="conquer-highlight-{{ $value }}" class="{{ in_array($key, $userHighlight)?'active ':'' }}dropdown-item conquer-highlight">{{ __('ui.conquer.highlight.'.$value) }}</a>
                    @endif
                @endforeach
            </div>
        </div>
        <div class="card">
            <div class="card-body cust-responsive">
                <div id="legend">
                    @foreach(\App\Profile::$CONQUER_HIGHLIGHT_MAPPING as $key => $value)
                    <span class="mr-4 nowrap">
                        <div style="width: 1em; height: 1em; background-color: {{ \App\Profile::$CONQUER_HIGHLIGHT_MAPPING_COLORS[$key][0] }}; display: inline-block; border: 1px solid #ccc" ></div>
                        {{ __('ui.conquer.highlight.'.$value) }}
                    </span>
                    @endforeach
                </div>
                <table id="table_id" class="small-mdd table table-hover table-sm w-100 nowrap">
                    <thead><tr>
                        <th>{{ ucfirst(__('ui.table.villageName')) }}</th>
                        <th>{{ ucfirst(__('ui.table.old').' '.__('ui.table.owner')) }}</th>
                        <th>{{ ucfirst(__('ui.table.old').' '.__('ui.table.ally')) }}</th>
                        <th>{{ ucfirst(__('ui.table.new').' '.__('ui.table.owner')) }}</th>
                        <th>{{ ucfirst(__('ui.table.new').' '.__('ui.table.ally')) }}</th>
                        <th>{{ ucfirst(__('ui.table.points')) }}</th>
                        <th>{{ ucfirst(__('ui.table.date')) }}</th>
                    </tr></thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>
    <!-- ENDE Datachart Eroberungen -->
</div>
@endsection

@push('js')
<script>
    var dataTable;
    $(document).ready( function () {
        dataTable = $('#table_id').DataTable({
            "processing": true,
            "serverSide": true,
            "order": [[ 6, "desc" ]],
            "searching": true,
            "ajax": "{{ $routeDatatableAPI }}",
            "columns": [
                { "data": "village", "orderable": false, "searchable": false},
                { "data": "old_owner_name", "name": "old_owner_name"},
                { "data": "old_ally_tag", "name": "old_ally_tag"},
                { "data": "new_owner_name", "name": "new_owner_name"},
                { "data": "new_ally_tag", "name": "new_ally_tag"},
                { "data": "points", "searchable": false},
                { "data": "timestamp", "searchable": false},
            ],
            "columnDefs": [
                {"targets": [0, 1, 2, 3, 4, 5, 6], "className": 'dt-left'},
            ],
            "fnRowCallback": function(row, data) {
                if(data.type == 3 && $('#conquer-highlight-barbarian').hasClass('active')) {//barbarian
                    $('td', row).css('background-color', '{{ \App\Profile::$CONQUER_HIGHLIGHT_MAPPING_COLORS['b'][1] }}'); //gray
                } else if(data.type == 2 && $('#conquer-highlight-self').hasClass('active')) {//self
                    $('td', row).css('background-color', '{{ \App\Profile::$CONQUER_HIGHLIGHT_MAPPING_COLORS['s'][1] }}'); //Yellow
                } else if(data.type == 1 && $('#conquer-highlight-internal').hasClass('active')) {//internal
                    $('td', row).css('background-color', '{{ \App\Profile::$CONQUER_HIGHLIGHT_MAPPING_COLORS['i'][1] }}'); //Blue
                } else if(data.winLoose == 1 && $('#conquer-highlight-win').hasClass('active')) {//win
                    $('td', row).css('background-color', '{{ \App\Profile::$CONQUER_HIGHLIGHT_MAPPING_COLORS['w'][1] }}'); //Green
                } else if(data.winLoose == -1 && $('#conquer-highlight-loose').hasClass('active')) {//loose
                    $('td', row).css('background-color', '{{ \App\Profile::$CONQUER_HIGHLIGHT_MAPPING_COLORS['l'][1] }}'); //Red
                }
            },
            stateSave: true,
            customName: "{{ $tableStateName }}",
            {!! \App\Util\Datatable::language() !!}
        });

        $('#dropdownSettingsWrapper').children().appendTo("#table_id_wrapper .row:first-child");
        $('.conquer-highlight').click(function (e) {
            e.stopPropagation();
            $(this).toggleClass('active');
            if(this.id == "conquer-highlight-all") {
                if($(this).hasClass('active')) {
                    $('.conquer-highlight').addClass('active');
                } else {
                    $('.conquer-highlight').removeClass('active');
                }
            }
            dataTable.draw(false);

            @auth
            storeHighlighting();
            @endauth
        });
    });

    @auth
    function storeHighlighting() {
        axios.post('{{ $routeHighlightSaving }}', {
            @foreach(\App\Profile::$CONQUER_HIGHLIGHT_MAPPING as $key => $value)
                @if(in_array($key, $allHighlight)) {{ $value }}: $('#conquer-highlight-{{ $value }}').hasClass('active'), @endif
            @endforeach
        })
            .then((response) => {
            })
            .catch((error) => {
            });
    }
    @endauth
</script>
@endpush
