@extends('layouts.app')

@section('titel', $worldData->displayName(),': '.__('tool.attackPlanner.title'))

@push('style')
    <link href="{{ asset('plugin/jquery-ui/jquery-ui.min.css') }}" rel="stylesheet">
    <style>
        table.dataTable thead .sorting:before,table.dataTable thead .sorting:after,table.dataTable thead .sorting_asc:before,table.dataTable thead .sorting_asc:after,table.dataTable thead .sorting_desc:before,table.dataTable thead .sorting_desc:after,table.dataTable thead .sorting_asc_disabled:before,table.dataTable thead .sorting_asc_disabled:after,table.dataTable thead .sorting_desc_disabled:before,table.dataTable thead .sorting_desc_disabled:after{position:absolute;bottom:0.3em;display:block;opacity:0.3}
        table.dataTable thead .sorting_asc:before,table.dataTable thead .sorting_desc:after{opacity:1}table.dataTable thead .sorting_asc_disabled:before,table.dataTable thead .sorting_desc_disabled:after{opacity:0}
        table.dataTable tbody tr.selected a, table.dataTable tbody th.selected a, table.dataTable tbody td.selected a {color: #7d510f;}
        table.dataTable tbody tr.selected, table.dataTable tbody th.selected, table.dataTable tbody td.selected {color: #212529;}
        table.dataTable tbody>tr.selected, table.dataTable tbody>tr>.selected {background-color: rgba(237, 212, 146, 0.4);}
    </style>
@endpush

@php
$tabList = [
    'create' => ['name' => __('global.create'), 'active' => true],
    'multiedit' => ['name' => __('tool.attackPlanner.multiedit'), 'active' => false],
    'link' => ['name' => __('tool.attackPlanner.links'), 'active' => false],
    'import' => ['name' => __('tool.attackPlanner.importExport'), 'active' => false],
    'stats' => ['name' => __('tool.attackPlanner.statistics'), 'active' => false],
    'tips' => ['name' => __('tool.attackPlanner.tips'), 'active' => false]
    ];
@endphp

@section('content')
    <div class="row justify-content-center">
        <!-- Titel für Tablet | PC -->
        <div class="col-12 p-lg-5 mx-auto my-1 text-center d-none d-lg-block">
            @auth
            <div class="col-2 position-absolute dropdown">
                <button class="btn btn-secondary dropdown-toggle" type="button" id="ownedPlanners" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    {{ ucfirst(__('tool.attackPlanner.fastSwitch')) }}
                </button>
                <div class="dropdown-menu" aria-labelledby="ownedPlanners">
                    @foreach($ownPlanners as $planner)
                        <a class="dropdown-item" href="{{
                            route('tools.attackPlannerMode', [$planner->id, 'edit', $planner->edit_key])
                            }}">{{ $planner->getTitle().' ['.$planner->world->displayName().']' }}</a>
                    @endforeach
                </div>
            </div>
            @endauth
            <h1 class="font-weight-normal">{{ $attackList->getTitle().' ['.$worldData->displayName().']' }}</h1>
        </div>
        <!-- ENDE Titel für Tablet | PC -->
        <!-- Titel für Mobile Geräte -->
        <div class="p-lg-5 mx-auto my-1 text-center d-lg-none truncate">
            <h1 class="font-weight-normal">
                {{ $attackList->getTitle().' ' }}
            </h1>
            <h4>
                {{ '['.$worldData->displayName().']' }}
            </h4>
        </div>
        <!-- ENDE Titel für Mobile Geräte -->
        @if($mode == 'edit')
        <!-- Village Card -->
        <div class="col-12">
            @if($attackList->title === null)
            <div class="card mt-2 p-3">
                {{ __('tool.attackPlanner.withoutTitle') }}
            </div>
            @endif
            <div class="card mt-2">
                <ul class="nav nav-tabs" id="myTab" role="tablist">
                    @foreach($tabList as $key => $tab)
                        <li class="nav-item">
                            <a class="nav-link {{ ($tab['active'])?'active':'' }}" id="{{ $key }}-tab" data-toggle="tab" href="#{{ $key }}" role="tab" aria-controls="{{ $key }}" aria-selected="true">{{ $tab['name'] }}</a>
                        </li>
                    @endforeach
                </ul>
                <div class="card-body tab-content">
                    @foreach($tabList as $key => $tab)
                        @include('tools.attackPlanner.'.$key, ['active' => $tab['active']])
                    @endforeach
                </div>
            </div>
        </div>
        <!-- ENDE Village Card -->
        @endif
        <!-- Unit Card -->
        <div class="col-12 mt-2">
            <div class="card">
                <div class="card-body table-responsive">
                    <table id="data1" class="table table-bordered table-striped no-wrap w-100">
                        <thead>
                            <tr>
                                @if($mode == 'edit')
                                    <th style="min-width: 25px">&nbsp;</th>
                                @endif
                                <th>{{ __('tool.attackPlanner.startVillage') }}</th>
                                <th>{{ __('tool.attackPlanner.attacker') }}</th>
                                <th>{{ __('tool.attackPlanner.targetVillage') }}</th>
                                <th>{{ __('tool.attackPlanner.defender') }}</th>
                                <th>{{ __('global.unit') }}</th>
                                <th>{{ __('tool.attackPlanner.type') }}</th>
                                <th>{{ __('tool.attackPlanner.sendTime') }}</th>
                                <th>{{ __('tool.attackPlanner.arrivalTime') }}</th>
                                <th width="95px">{{ __('tool.attackPlanner.countdown') }}</th>
                                <th style="min-width: 25px">&nbsp;</th>
                                <th style="min-width: 25px">&nbsp;</th>
                                @if($mode == 'edit')
                                    <th style="min-width: 50px">&nbsp;</th>
                                @endif
                            </tr>
                        </thead>
                        <tbody class="small">
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <!-- ENDE Unit Card -->
    </div>
    <!-- START Modal -->
    @include('tools.attackPlanner.edit')
    <!-- ENDE Modal -->
@endsection

@push('js')
    <audio controls class="d-none">
        <source src="{{ asset('sounds/attackplanner/420661__kinoton__alarm-siren-fast-oscillations.mp3') }}" type="audio/mpeg">
        Your browser does not support the audio element.
    </audio>
    <script type="text/javascript" src="{{ asset('plugin/jquery.countdown/jquery.countdown.min.js') }}"></script>
    <script>
        var muteaudio = false;
        var keyArray = {};
        var audioTiming = 60;
        var now;
        var table =
            $('#data1').DataTable({
                ordering: true,
                processing: true,
                serverSide: true,
                pageLength: 25,
                searching: false,
                @if($mode == 'edit')
                select: true,
                @endif
                order:[[{{ ($mode == 'edit')?'7':'6' }}, 'desc']],
                ajax: '{!! route('tools.attackListItem.data', [ $attackList->id , $attackList->show_key]) !!}',
                columns: [
                    @if($mode == 'edit')
                    { data: 'select', name: 'select'},
                    @endif
                    { data: 'start_village', name: 'start_village'},
                    { data: 'attacker', name: 'attacker'},
                    { data: 'target_village', name: 'target_village'},
                    { data: 'defender', name: 'defender'},
                    { data: 'slowest_unit', name: 'slowest_unit'},
                    { data: 'type', name: 'type'},
                    { data: 'send_time', name: 'send_time'},
                    { data: 'arrival_time', name: 'arrival_time'},
                    { data: 'time', name: 'time'},
                    { data: 'info', name: 'info'},
                    { data: 'action', name: 'action'},
                    @if($mode == 'edit')
                    { data: 'delete', name: 'action' },
                    @endif
                ],
                columnDefs: [
                    {
                        'orderable': false,
                        @if($mode == 'edit')
                        'targets': [1,3,9,10,11,12]
                        @else
                        'targets': [0,2,8,9,10,]
                        @endif
                    },
                    @if($mode == 'edit')
                    {
                        orderable: false,
                        className: 'select-checkbox',
                        targets:   0
                    }
                    @endif
                ],
                "drawCallback": function(settings, json) {
                    @if($mode == 'edit')
                    exportWB();
                    exportBB();
                    exportIGM();
                    @endif
                    countdown();
                    popover();
                    $('#data1_wrapper div:first-child div:eq(2)').html('<div class="form-inline">' +
                        '<div class="col-9">' +
                            '<label id="audioTimingText" for="customRange2">{!! str_replace('%S%', '<input id="audioTimingInput" class="form-control form-control-sm mx-1" style="width: 50px;" type="text" value="">', __('tool.attackPlanner.audioTiming')) !!}</label>' +
                            '<input type="range" class="custom-range" min="0" max="60" id="audioTiming" value="' + audioTiming + '">' +
                        '</div>' +
                        '<div class="col-2">' +
                            '<h5>' +
                                '<a class="btn btn-outline-dark float-right" onclick="muteAudio()" role="button">' +
                                    '<i id="audioMuteIcon" class="fas fa-volume-up"></i>' +
                                '</a>' +
                            '</h5>' +
                        '</div>' +
                        @auth
                            @if($attackList->user_id != Auth::user()->id)
                                @if($attackList->follows()->where('user_id', Auth::user()->id)->count() > 0)
                                    '<div class="col-1">' +
                                        '<h5>' +
                                            '<i id="follow-icon" style="cursor:pointer; text-shadow: 0 0 15px #000;" onclick="changeFollow()" class="fas fa-star h4 text-warning mt-2"></i>' +
                                        '</h5>' +
                                    '</div>' +
                                @else
                                    '<div class="col-1">' +
                                        '<h5>' +
                                            '<i id="follow-icon" style="cursor:pointer" onclick="changeFollow()" class="far text-muted fa-star h4 text-muted mt-2"></i>' +
                                        '</h5>' +
                                    '</div>' +
                                @endif
                            @endif
                        @endauth
                        '</div>')
                    $('#audioTimingInput').val(audioTiming);
                },
                {!! \App\Util\Datatable::language() !!}
            });

        $(document).on('input', '#audioTiming', function () {
            var value = this.value;
            $('#audioTimingInput').val(value > 60 ? 60 : value);
            audioTiming = value;
        }).on('keyup', '#audioTimingInput', function (e) {
            var value = this.value;
            $('#audioTimingInput').val(value > 60 ? 60 : value);
            $('#audioTiming').val(value > 60 ? 60 : value);
            audioTiming = value;
        })

        @if($mode == 'edit')
        $(document).ready(function () {
            $('[data-toggle="tooltip"]').tooltip();
        });

        function titleEdit() {
            var input = $('#title-input');
            var title = $('#title-show');
            var edit = $('#title-edit');
            var save = $('#title-save');
            var t = (title.html() === '{{ __('ui.noTitle') }}')? '': title.html();
            title.hide();
            edit.hide();
            input.val(t).show().focus();
            save.show();
        }

        function titleSave() {
            var input = $('#title-input');
            var title = $('#title-show');
            var edit = $('#title-edit');
            var save = $('#title-save');
            var t = (input.val() === '')? '{{ __('ui.noTitle') }}': input.val();
            axios.post('{{ route('index') }}/tools/attackPlanner/{{ $attackList->id }}/title/{{ $attackList->edit_key }}/' + t, {
            })
                .then((response) => {
                    input.hide();
                    save.hide();
                    title.html(t).show();
                    edit.show();
                })
                .catch((error) => {

                });
        }

        function changeTime(type, target = '') {
            $('#' + target + 'time_type').val(type);
            switch (type) {
                case 0: $('#' + target + 'time_title').html("{{ __('tool.attackPlanner.arrivalTime') }}")
                    break;
                case 1: $('#' + target + 'time_title').html("{{ __('tool.attackPlanner.sendTime') }}")
                    break;
            }
        }

        function destroy(id,key) {
            $.ajax(
                {
                    url: "{{ route('tools.attackListItem.store') }}/"+id,
                    type: 'DELETE',
                    dataType: "JSON",
                    data: {
                        "id": id,
                        "_method": 'DELETE',
                        "key": key,
                        "_token": '{{ csrf_token() }}',
                    },
                    success: function ()
                    {
                        table.ajax.reload();
                    }
                });
        }

        function destroyOutdated() {
            $.ajax(
                {
                    url: '{{ route('tools.attackPlannerMode', [$attackList->id, 'destroyOutdated', $attackList->edit_key]) }}',
                    type: 'GET',
                    dataType: "JSON",
                    success: function ()
                    {
                        table.ajax.reload();
                    }
                });
        }

        function store() {
            axios.post('{{ route('tools.attackListItem.store') }}', {
                'attack_list_id' : $('#attack_list_id').val(),
                'type' : $('#type').val(),
                'xStart' : $('#xStart').val(),
                'yStart' : $('#yStart').val(),
                'xTarget' : $('#xTarget').val(),
                'yTarget' : $('#yTarget').val(),
                'slowest_unit' : $('#slowest_unit').val(),
                'note' : $('#note').val(),
                'day' : $('#day').val(),
                'time' : $('#time').val(),
                'time_type' : $('#time_type').val(),
                'key' : '{{ $attackList->edit_key }}',
                'spear': $('#spear').val() != 0 ? $('#spear').val() : 0,
                'sword': $('#sword').val() != 0 ? $('#sword').val() : 0,
                'axe': $('#axe').val() != 0 ? $('#axe').val() : 0,
                'archer': $('#archer').val() != 0 ? $('#archer').val() : 0,
                'spy': $('#spy').val() != 0 ? $('#spy').val() : 0,
                'light': $('#light').val() != 0 ? $('#light').val() : 0,
                'marcher': $('#marcher').val() != 0 ? $('#marcher').val() : 0,
                'heavy': $('#heavy').val() != 0 ? $('#heavy').val() : 0,
                'ram': $('#ram').val() != 0 ? $('#ram').val() : 0,
                'catapult': $('#catapult').val() != 0 ? $('#catapult').val() : 0,
                'knight': $('#knight').val() != 0 ? $('#knight').val() : 0,
                'snob': $('#snob').val() != 0 ? $('#snob').val() : 0,
            })
                .then((response) => {
                    var data = response.data;
                    table.ajax.reload();
                    createToast(data, data['title'], '{{ __('global.now') }}', data['data'] === 'success'? 'fas fa-check-circle text-success' :'fas fa-exclamation-circle text-danger')
                })
                .catch((error) => {

                });
        }

        function update() {
            axios.patch('{{ route('index') }}/tools/attackPlanner/attackListItem/' + $('#attack_list_item').val(), {
                'attack_list_id' : $('#attack_list_id').val(),
                'type' : $('#edit_type').val(),
                'xStart' : $('#edit_xStart').val(),
                'yStart' : $('#edit_yStart').val(),
                'xTarget' : $('#edit_xTarget').val(),
                'yTarget' : $('#edit_yTarget').val(),
                'slowest_unit' : $('#edit_slowest_unit').val(),
                'note' : $('#edit_note').val(),
                'day' : $('#edit_day').val(),
                'time' : $('#edit_time').val(),
                'ms' : $('#edit_ms').val(),
                'time_type' : $('#edit_time_type').val(),
                'key' : '{{ $attackList->edit_key }}',
                'spear': $('#edit_spear').val() != 0 ? $('#edit_spear').val() : 0,
                'sword': $('#edit_sword').val() != 0 ? $('#edit_sword').val() : 0,
                'axe': $('#edit_axe').val() != 0 ? $('#edit_axe').val() : 0,
                'archer': $('#edit_archer').val() != 0 ? $('#edit_archer').val() : 0,
                'spy': $('#edit_spy').val() != 0 ? $('#edit_spy').val() : 0,
                'light': $('#edit_light').val() != 0 ? $('#edit_light').val() : 0,
                'marcher': $('#edit_marcher').val() != 0 ? $('#edit_marcher').val() : 0,
                'heavy': $('#edit_heavy').val() != 0 ? $('#edit_heavy').val() : 0,
                'ram': $('#edit_ram').val() != 0 ? $('#edit_ram').val() : 0,
                'catapult': $('#edit_catapult').val() != 0 ? $('#edit_catapult').val() : 0,
                'knight': $('#edit_knight').val() != 0 ? $('#edit_knight').val() : 0,
                'snob': $('#edit_snob').val() != 0 ? $('#edit_snob').val() : 0,
            })
                .then((response) => {
                    $('.bd-example-modal-xl').modal('hide');
                    var data = response.data;
                    table.ajax.reload();
                    createToast(data, data['title'], '{{ __('global.now') }}', data['data'] === 'success'? 'fas fa-check-circle text-success' :'fas fa-exclamation-circle text-danger')
                })
                .catch((error) => {

                });
        }

        function multiupdate() {
            var select = table.rows('.selected').data();
            var attackItems = [];
            select.each(function(e){
                attackItems.push(e.id)
            })
            var checkboxes = $('input:checkbox:checked');
            var checkboxeItems = [];
            checkboxes.each(function (key, value) {
                checkboxeItems.push(value.name)
            })
            axios.post('{{ route('tools.attackListItemMultiedit') }}', {
                'attack_list_id' : $('#attack_list_id').val(),
                'items' : attackItems,
                'checkboxes' : checkboxeItems,
                'type' : $('#multiedit_type').val(),
                'xStart' : $('#multiedit_xStart').val(),
                'yStart' : $('#multiedit_yStart').val(),
                'xTarget' : $('#multiedit_xTarget').val(),
                'yTarget' : $('#multiedit_yTarget').val(),
                'slowest_unit' : $('#multiedit_slowest_unit').val(),
                'note' : $('#multiedit_note').val(),
                'day' : $('#multiedit_day').val(),
                'time' : $('#multiedit_time').val(),
                'ms' : $('#multiedit_ms').val(),
                'time_type' : $('#multiedit_time_type').val(),
                'key' : '{{ $attackList->edit_key }}',
                'spear': $('#multiedit_spear').val() != 0 ? $('#multiedit_spear').val() : 0,
                'sword': $('#multiedit_sword').val() != 0 ? $('#multiedit_sword').val() : 0,
                'axe': $('#multiedit_axe').val() != 0 ? $('#multiedit_axe').val() : 0,
                'archer': $('#multiedit_archer').val() != 0 ? $('#multiedit_archer').val() : 0,
                'spy': $('#multiedit_spy').val() != 0 ? $('#multiedit_spy').val() : 0,
                'light': $('#multiedit_light').val() != 0 ? $('#multiedit_light').val() : 0,
                'marcher': $('#multiedit_marcher').val() != 0 ? $('#multiedit_marcher').val() : 0,
                'heavy': $('#multiedit_heavy').val() != 0 ? $('#multiedit_heavy').val() : 0,
                'ram': $('#multiedit_ram').val() != 0 ? $('#multiedit_ram').val() : 0,
                'catapult': $('#multiedit_catapult').val() != 0 ? $('#multiedit_catapult').val() : 0,
                'knight': $('#multiedit_knight').val() != 0 ? $('#multiedit_knight').val() : 0,
                'snob': $('#multiedit_snob').val() != 0 ? $('#multiedit_snob').val() : 0,
            })
                .then((response) => {
                    var data = response.data;
                    table.ajax.reload();
                    createToast(data, data['title'], '{{ __('global.now') }}', data['data'] === 'success'? 'fas fa-check-circle text-success' :'fas fa-exclamation-circle text-danger')
                })
                .catch((error) => {

                });
        }

        function importWB() {
                var importWB = $('#importWB');
                axios.post('{{ route('tools.attackPlannerMode', [$attackList->id, 'importWB', $attackList->edit_key]) }}', {
                    'import': importWB.val(),
                    'key': '{{$attackList->edit_key}}',
                })
                    .then((response) => {
                        importWB.val('');
                        table.ajax.reload();
                    })
                    .catch((error) => {

                    });
        }

        $(document).on('submit', '#importItemsForm', function (e) {
            e.preventDefault();
            importWB();
        });

        $(document).on('submit', '#createItemForm', function (e) {
            e.preventDefault();
            var start = $('#xStart').val() + '|' + $('#yStart').val();
            var target = $('#xTarget').val() + '|' + $('#yTarget').val();

            var error = 0;
            if (start == ''){
                error += 1;
            }
            if (target == ''){
                error += 1;
            }
            if (start == target){
                var data = []
                data['msg'] = '{{ __('tool.attackPlanner.errorKoord') }}';
                createToast(data, '{{ __('tool.attackPlanner.errorKoordTitle') }}', '{{ __('global.now') }}', 'fas fa-exclamation-circle text-danger')
                error += 1;
            }

            if (error == 0){
                store();
            }
        });

        $(document).on('submit', '#editItemForm', function (e) {
            e.preventDefault();
            var start = $('#edit_xStart').val() + '|' + $('#edit_yStart').val();
            var target = $('#edit_xTarget').val() + '|' + $('#edit_yTarget').val();

            var error = 0;
            if (start == ''){
                error += 1;
            }
            if (target == ''){
                error += 1;
            }
            if (start == target){
                var data = []
                data['msg'] = '{{ __('tool.attackPlanner.errorKoord') }}';
                createToast(data, '{{ __('tool.attackPlanner.errorKoordTitle') }}', '{{ __('global.now') }}', 'fas fa-exclamation-circle text-danger')
                error += 1;
            }

            if (error == 0){
                update();
            }
        });

        $(document).on('submit', '#multieditItemForm', function (e) {
            e.preventDefault();
            var start = $('#multiedit_xStart').val() + '|' + $('#multiedit_yStart').val();
            var target = $('#multiedit_xTarget').val() + '|' + $('#multiedit_yTarget').val();

            var error = 0;
            if ($('#multiedit_start_checkbox').is(':checked') || $('#multiedit_target_checkbox').is(':checked')) {
                if (start == '') {
                    error += 1;
                }
                if (target == '') {
                    error += 1;
                }
                if (start == target) {
                    var data = []
                    data['msg'] = '{{ __('tool.attackPlanner.errorKoord') }}';
                    createToast(data, '{{ __('tool.attackPlanner.errorKoordTitle') }}', '{{ __('global.now') }}', 'fas fa-exclamation-circle text-danger')
                    error += 1;
                }
            }

            if (error == 0){
                multiupdate();
            }
        });

        $(".time").on( "keydown", function (e) {
            keyArray[e.which] = true;
            if(keyArray[17] && keyArray[86]){
                var dataTarget = $(this).attr('data-target');
                var target = (dataTarget != null)?dataTarget:'';
                var inputTarget = $('#' + target + 'time');
                inputTarget.attr('type', 'text').select()
            }
        });

        $(".time").on( "keyup", function (e) {
            delete keyArray[e.which];
        });

        $(".time").bind('paste', function (e) {
            var dataTarget = $(this).attr('data-target');
            var target = (dataTarget != null)?dataTarget:'';
            var pastedData = e.originalEvent.clipboardData.getData('text');
            var time = pastedData.split(':');
            var output;
            if (time.length === 4){
                output = time[0] + ':' + time[1] + ':' + time[2] + '.' + time[3];
            }else {
                output = pastedData;
            }
            $('#' + target + 'time').val(output).attr('type', 'time')
        });

        function edit(id) {
            var data = table.row('#' + id).data();
            var rowData = data.DT_RowData;
            var type = $.inArray(rowData.type, {{ json_encode(\App\Util\Icon::attackPlannerTypeIcons()) }}) ? rowData.type : -1;
            $('#attack_list_item').val(data.id);
            $('#edit_type').val(type);
            $('#edit_xStart').val(rowData.xStart);
            $('#edit_yStart').val(rowData.yStart);
            $('#edit_xTarget').val(rowData.xTarget);
            $('#edit_yTarget').val(rowData.yTarget);
            $('#edit_day').val(rowData.day);
            $('#edit_time').val(rowData.time);
            $('#edit_slowest_unit').val(rowData.slowest_unit);
            $('#edit_spear').val(data.spear);
            $('#edit_sword').val(data.sword);
            $('#edit_axe').val(data.axe);
            $('#edit_archer').val(data.archer);
            $('#edit_spy').val(data.spy);
            $('#edit_light').val(data.light);
            $('#edit_marcher').val(data.marcher);
            $('#edit_heavy').val(data.heavy);
            $('#edit_ram').val(data.ram);
            $('#edit_catapult').val(data.catapult);
            $('#edit_knight').val(data.knight);
            $('#edit_snob').val(data.snob);
            $('#edit_note').val(data.note);
            $('#edit_unit_img').attr('src', slowest_unit_img(rowData.slowest_unit.toString()));
            $('#edit_type_img').attr('src', typ_img(type.toString()));
            village(rowData.xStart, rowData.yStart, 'Start', 'edit_');
            village(rowData.xTarget, rowData.yTarget, 'Target', 'edit_');
        }

        function village(x, y, input, target = null) {
            if (x != '' && y != '') {
                axios.get('{{ route('index') }}/api/{{ $worldData->server->code }}/{{ $worldData->name }}/villageCoords/' + x + '/' + y, {})
                    .then((response) => {
                        const data = response.data.data;
                        $('#' + target + 'village' + input).html(data['name'].trunc(25) + ' <b>' + x + '|' + y + '</b>  [' + data['continent'] + ']').attr('class', 'form-control-feedback ml-2 valid-feedback');
                        //$('#' + input.toLowerCase() + '_village_id').val(data['villageID']);
                        $('#' + target + 'x' + input).attr('class', 'form-control form-control-sm mx-auto col-5 koord is-valid').attr('style', 'background-position-y: 0.4em;');
                        $('#' + target + 'y' + input).attr('class', 'form-control form-control-sm mx-auto col-5 koord is-valid').attr('style', 'background-position-y: 0.4em;');
                    })
                    .catch((error) => {
                        $('#' + target + 'village' + input).html('{{ __('ui.villageNotExist') }}').attr('class', 'form-control-feedback ml-2 invalid-feedback');
                        //$('#' + input.toLowerCase() + '_village_id').val('');
                        $('#' + target + 'x' + input).attr('class', 'form-control form-control-sm mx-auto col-5 koord is-invalid').attr('style', 'background-position-y: 0.4em;');
                        $('#' + target + 'y' + input).attr('class', 'form-control form-control-sm mx-auto col-5 koord is-invalid').attr('style', 'background-position-y: 0.4em;');
                    });
            }
        }

        function exportWB() {
            axios.get('{{ route('tools.attackPlannerMode', [$attackList->id, 'exportWB', $attackList->edit_key]) }}', {
            })
                .then((response) => {
                    $('#exportWB').val(response.data);
                })
                .catch((error) => {

                });
        }

        function exportBB() {
            axios.get('{{ route('tools.attackPlannerMode', [$attackList->id, 'exportBB', $attackList->edit_key]) }}', {
            })
                .then((response) => {
                    $('#exportBB').val(response.data);
                })
                .catch((error) => {

                });
        }

        function exportIGM() {
            axios.get('{{ route('tools.attackPlannerMode', [$attackList->id, 'exportIGM', $attackList->edit_key]) }}', {
            })
                .then((response) => {
                    $('#exportIGM').val(response.data);
                })
                .catch((error) => {

                });
        }

        @endif

        @auth
            @if($attackList->user_id != Auth::user()->id)
                function changeFollow() {
                    var icon = $('#follow-icon');
                    axios.post('{{ route('tools.follow') }}',{
                        model: 'AttackPlanner_AttackList',
                        id: '{{ $attackList->id }}'
                    })
                        .then((response) => {
                            if(icon.hasClass('far')){
                                icon.removeClass('far text-muted').addClass('fas text-warning').attr('style','cursor:pointer; text-shadow: 0 0 15px #000;');
                            }else {
                                icon.removeClass('fas text-warning').addClass('far text-muted').attr('style', 'cursor:pointer;');
                            }
                        })
                        .catch((error) => {

                        });
                }
            @endif
        @endauth

        $(document).on('click', 'input[type="checkbox"][data-group]', function(event) {
            // The checkbox that was clicked
            var actor = $(this);
            // The status of that checkbox
            var checked = actor.prop('checked');
            // The group that checkbox is in
            var group = actor.data('group');
            // All checkboxes of that group
            var checkboxes = $('input[type="checkbox"][data-group="' + group + '"]');
            // All checkboxes excluding the one that was clicked
            var otherCheckboxes = checkboxes.not(actor);
            // Check those checkboxes
            otherCheckboxes.prop('checked', checked);
        });

        function sendattack(id){
            axios.post('{{ route('tools.attackListItemSendattack') }}', {
                'id': id,
                'key': '{{$attackList->show_key}}',
            })
                .then((response) => {
                    table.ajax.reload();
                })
                .catch((error) => {
                });
        }

        function copy(type) {
            /* Get the text field */
            var copyText = $("#" + type);

            /* Select the text field */
            copyText.select();

            /* Copy the text inside the text field */
            document.execCommand("copy");
        }

        function muteAudio() {
            if(muteaudio){
                $('#audioMuteIcon').removeClass('fa-volume-mute').addClass('fa-volume-up');
                muteaudio = false;
            }else{
                $('#audioMuteIcon').removeClass('fa-volume-up').addClass('fa-volume-mute');
                muteaudio = true;
            }
        }

        function countdown(){
            axios.post('{{ route('api.time') }}')
                .then((response) => {
                    now = parseInt(response.data['time']);
                    $('countdown').each(function () {
                        var date = $(this).attr('date');
                        startTimer(now, date, $(this));
                    })
                })
                .catch((error) => {
                    now = parseInt({{ \Carbon\Carbon::now()->timestamp }});
                    startTimer(now, date, $(this));
                });
        }

        function startTimer(now, arrive, display) {
            var duration = arrive - now;
            var timer = duration, days, hours, minutes, seconds;
            var timerPlay = false;
            if (duration < 0){
                display.parent().html("00:00:00").addClass("bg-danger text-white");
            }else {
                var interval = setInterval(function () {

                    days = Math.floor(timer / 86400);
                    hours = Math.floor((timer - days * 86400) / 3600);
                    minutes = Math.floor((timer - days * 86400 - hours * 3600) / 60);
                    seconds = timer - days * 86400 - hours * 3600 - minutes * 60;

                    days = days < 1 ? "" : days + " Tage ";
                    hours = hours < 10 ? "0" + hours : hours;
                    minutes = minutes < 10 ? "0" + minutes : minutes;
                    seconds = seconds < 10 ? "0" + seconds : seconds;
                    display.html(days + hours + ":" + minutes + ":" + seconds);

                    if (--timer < 0) {
                        display.parent().addClass("bg-danger text-white");
                        clearInterval(interval);
                    }
                    if (timer < audioTiming && !timerPlay) {
                        audio();
                        timerPlay = true;
                    }
                }, 1000);
            }
        }

        function audio(){
            if(!muteaudio){
                var $audio = $('audio');
                var audio = $audio[0];
                audio.volume = 0.2;
                audio.play();
                var audioTime = setInterval(function () {
                    audio.pause()
                    audio.currentTime = 0
                    clearInterval(audioTime)
                }, 2000)
            }
        }

        String.prototype.trunc = String.prototype.trunc ||
            function(n){
                return (this.length > n) ? this.substr(0, n-1) + '&hellip;' : this;
            };

        function slowest_unit_img(unit){
            switch (unit) {
                case '0': return '{{ \App\Util\Icon::icons(0) }}';
                case '1': return '{{ \App\Util\Icon::icons(1) }}';
                case '2': return '{{ \App\Util\Icon::icons(2) }}';
                case '3': return '{{ \App\Util\Icon::icons(3) }}';
                case '4': return '{{ \App\Util\Icon::icons(4) }}';
                case '5': return '{{ \App\Util\Icon::icons(5) }}';
                case '6': return '{{ \App\Util\Icon::icons(6) }}';
                case '7': return '{{ \App\Util\Icon::icons(7) }}';
                case '8': return '{{ \App\Util\Icon::icons(8) }}';
                case '9': return '{{ \App\Util\Icon::icons(9) }}';
                case '10': return '{{ \App\Util\Icon::icons(10) }}';
                case '11': return '{{ \App\Util\Icon::icons(11) }}';
            }
        }

        function typ_img(input){
            switch (input) {
                case '-1': return '{{ \App\Util\Icon::icons(-1) }}';
                case '8': return '{{ \App\Util\Icon::icons(8) }}';
                case '11': return '{{ \App\Util\Icon::icons(11) }}';
                case '14': return '{{ \App\Util\Icon::icons(14) }}';
                case '45': return '{{ \App\Util\Icon::icons(45) }}';
                case '0': return '{{ \App\Util\Icon::icons(0) }}';
                case '1': return '{{ \App\Util\Icon::icons(1) }}';
                case '7': return '{{ \App\Util\Icon::icons(7) }}';
                case '46': return '{{ \App\Util\Icon::icons(46) }}';
            }
        }

        function popover(){
            $(function () {
                $('[data-toggle="popover"]').popover({
                    html : true,
                    container: 'body'
                })
            });
        };

        $(function (e) {
            $('#title-input').on("keypress keyup blur",function (event) {
                if (event.keyCode == 13) {
                    titleSave();
                    event.preventDefault();
                }
            });

            $('.type').change(function (e) {
                var dataTarget = $(this).attr('data-target');
                var target = (dataTarget != null)?dataTarget:'';
                var img = $('#' + target + 'type_img');
                var input = $(this).val();
                img.attr('src', typ_img(input));
            });

            $('.slowest_unit').change(function (e) {
                var dataTarget = $(this).attr('data-target');
                var target = (dataTarget != null)?dataTarget:'';
                var img = $('#' + target + 'unit_img');
                var input = $(this).val();
                img.attr('src', slowest_unit_img(input));
            });

            $(".xStart").keyup(function () {
                var dataTarget = $(this).attr('data-target');
                var target = (dataTarget != null)?dataTarget:'';
                if (this.value.length == this.maxLength) {
                    $('#' + target + 'yStart').focus();
                }
            });

            $(".xStart").bind('paste', function(e) {
                var dataTarget = $(this).attr('data-target');
                var target = (dataTarget != null)?dataTarget:'';
                var pastedData = e.originalEvent.clipboardData.getData('text');
                var coords = pastedData.split("|");
                if (coords.length === 2) {
                    x = coords[0].substring(0, 3);
                    y = coords[1].substring(0, 3);
                    $('#' + target + 'xStart').val(coords[0].substring(0, 3));
                    $('#' + target + 'yStart').val(coords[1].substring(0, 3));
                    village(x, y, 'Start', target)
                }
            });

            $(".xTarget").keyup(function () {
                var dataTarget = $(this).attr('data-target');
                var target = (dataTarget != null)?dataTarget:'';
                if (this.value.length == this.maxLength) {
                    $('#' + target + 'yTarget').focus();
                }
            });

            $(".xTarget").bind('paste', function(e) {
                var dataTarget = $(this).attr('data-target');
                var target = (dataTarget != null)?dataTarget:'';
                var pastedData = e.originalEvent.clipboardData.getData('text');
                var coords = pastedData.split("|");
                if (coords.length === 2) {
                    x = coords[0].substring(0, 3);
                    y = coords[1].substring(0, 3);
                    $('#' + target + 'xTarget').val(coords[0].substring(0, 3));
                    $('#' + target + 'yTarget').val(coords[1].substring(0, 3));
                    village(x, y, 'Target', target)
                }
            });

            $('.koord').change(function (e) {
                var dataTarget = $(this).attr('data-target');
                var target = (dataTarget != null)?dataTarget:'';
                var input = this.id;
                var ex = input.split('_');
                if (ex.length > 1){
                    input = ex[1];
                }
                var type = input.substring(1, 2).toUpperCase() + input.substring(2);
                var x = $('#' + target + 'x' + type).val();
                var y = $('#' + target + 'y' + type).val();
                village(x, y, type, target)
            });
        })
    </script>
@endpush
