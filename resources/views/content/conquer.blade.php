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
            <div class="card-body">
                <table id="table_id" class="table table-hover table-sm w-100">
                    <thead>
                    <tr>
                        <th>{{ ucfirst(__('ui.table.date')) }}</th>
                        <th>{{ ucfirst(__('ui.table.villageName')) }}</th>
                        <th>{{ ucfirst(__('ui.table.old').' '.__('ui.table.owner')) }}</th>
                        <th>{{ ucfirst(__('ui.table.old').' '.__('ui.table.ally')) }}</th>
                        <th>{{ ucfirst(__('ui.table.new').' '.__('ui.table.owner')) }}</th>
                        <th>{{ ucfirst(__('ui.table.new').' '.__('ui.table.ally')) }}</th>
                    </tr>
                    </thead>
                    <tbody>
                    </tbody>
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
        $.extend( $.fn.dataTable.defaults, {
            responsive: true
        } );

        dataTable = $('#table_id').DataTable({
            "columnDefs": [
                {"targets": 2, "className": 'text-right'},
                {"targets": 4, "className": 'text-right'},
            ],
            "processing": true,
            "serverSide": true,
            "order": [[ 0, "desc" ]],
            "ajax": "{{ $routeDatatableAPI }}",
            "columns": [
                { "data": "timestamp" },
                { "data": "village_html", "orderable": false},
                { "data": "old_owner_html", "orderable": false},
                { "data": "old_owner_ally_html", "orderable": false},
                { "data": "new_owner_html", "orderable": false},
                { "data": "new_owner_ally_html", "orderable": false},
            ],
            "fnRowCallback": function(row, data) {
                if(data.type == 3 && $('#conquer-highlight-barbarian').hasClass('active')) {//barbarian
                    $('td', row).css('background-color', 'rgba(140,140,140,0.2)'); //gray
                } else if(data.type == 2 && $('#conquer-highlight-self').hasClass('active')) {//self
                    $('td', row).css('background-color', 'rrgba(235,247,64,0.2)'); //Yellow
                } else if(data.type == 1 && $('#conquer-highlight-internal').hasClass('active')) {//internal
                    $('td', row).css('background-color', 'rgba(38,79,242,0.2)'); //Blue
                } else if(data.winLoose == 1 && $('#conquer-highlight-win').hasClass('active')) {//win
                    $('td', row).css('background-color', 'rgba(42,175,71,0.2)'); //Green
                } else if(data.winLoose == -1 && $('#conquer-highlight-loose').hasClass('active')) {//loose
                    $('td', row).css('background-color', 'rgba(226,54,71,0.2)'); //Red
                }
            },
            responsive: true,
            {!! \App\Util\Datatable::language() !!}
        });
        
        $('#dropdownSettingsWrapper').children().appendTo("#table_id_filter");
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
