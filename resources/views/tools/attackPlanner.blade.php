@extends('layouts.temp')

@section('titel', $worldData->displayName(),': '.__('ui.tool.attackPlanner.title'))

@section('style')
    <link href="{{ asset('plugin/jquery-ui/jquery-ui.min.css') }}" rel="stylesheet">
@stop

@section('content')
    <div class="row justify-content-center">
        <!-- Titel für Tablet | PC -->
        <div class="col-12 p-lg-5 mx-auto my-1 text-center d-none d-lg-block">
            <h1 class="font-weight-normal">{{ ucfirst(__('ui.tool.attackPlanner.title')).' ['.$worldData->displayName().']' }}</h1>
        </div>
        <!-- ENDE Titel für Tablet | PC -->
        <!-- Titel für Mobile Geräte -->
        <div class="p-lg-5 mx-auto my-1 text-center d-lg-none truncate">
            <h1 class="font-weight-normal">
                {{ ucfirst(__('ui.tool.attackPlanner.title')).' ' }}
            </h1>
            <h4>
                {{ '['.$worldData->displayName().']' }}
            </h4>
        </div>
        <!-- ENDE Titel für Mobile Geräte -->
        @if($mode == 'edit')
        <!-- Village Card -->
        <div class="col-12 mt-2">
            <div class="card">
                <ul class="nav nav-tabs" id="myTab" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link active" id="create-tab" data-toggle="tab" href="#create" role="tab" aria-controls="create" aria-selected="true">Bearbeiten</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="link-tab" data-toggle="tab" href="#link" role="tab" aria-controls="link" aria-selected="false">Links</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="import-tab" data-toggle="tab" href="#import" role="tab" aria-controls="import" aria-selected="false">Import/Export</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="stats-tab" data-toggle="tab" href="#stats" role="tab" aria-controls="stats" aria-selected="false">Statistik</a>
                    </li>
                </ul>
                <div class="card-body tab-content">
                    <div class="tab-pane fade show active" id="create" role="tabpanel" aria-labelledby="create-tab">
                        <form id="createItemForm">
                            <div class="row pt-3">
                                <div class="col-md-4">
                                    <div class="form-group row">
                                        <label class="control-label col-3">Typ</label>
                                        <div class="col-1">
                                            <img id="type_img" src="{{ asset('images/ds_images/unit/unit_ram.png') }}">
                                        </div>
                                        <div class="col-8">
                                            <select id="type" class="form-control form-control-sm">
                                                <optgroup label="Offensiv">
                                                    <option value="8">Angriff</option>
                                                    <option value="11">Eroberung</option>
                                                    <option value="14">Fake</option>
                                                    <option value="45">Wallbrecher</option>
                                                </optgroup>
                                                <optgroup label="Defensiv">
                                                    <option value="0">Unterstützung</option>
                                                    <option value="1">Stand Unterstützung</option>
                                                    <option value="7">Schnelle Unterstützung</option>
                                                    <option value="46">Fake Unterstützung</option>
                                                </optgroup>
                                            </select>
                                            <small class="form-control-feedback">Typ des Angriffes</small>
                                        </div>
                                    </div>
                                </div>
                                <!--/span-->
                                <div class="col-md-4">
                                    <div class="form-group row">
                                        <label class="control-label col-4">Startdorf</label>
                                        <div id="start" class="form-inline col-8">
                                            <input id="xStart" class="form-control form-control-sm mx-auto col-5 koord" type="text" placeholder="500" maxlength="3" />
                                            |
                                            <input id="yStart" class="form-control form-control-sm mx-auto col-5 koord" type="text" placeholder="500" maxlength="3" />
                                            <small id="villageStart" class="form-control-feedback ml-2">Koordinaten des Startdorfes</small>
                                        </div>
                                    </div>
                                </div>
                                <!--/span-->
                                <div class="col-md-4">
                                    <div class="form-group row">
                                        <label class="control-label col-4">Zieldorf</label>
                                        <div id="target" class="form-inline col-8">
                                            <input id="xTarget" class="form-control form-control-sm mx-auto col-5 koord" type="text" placeholder="500" maxlength="3" />
                                            |
                                            <input id="yTarget" class="form-control form-control-sm mx-auto col-5 koord" type="text" placeholder="500" maxlength="3" />
                                            <small id="villageTarget" class="form-control-feedback ml-2">Koordinaten des Zieldorfes</small>
                                        </div>
                                    </div>
                                </div>
                                <!--/span-->
                                <div class="col-md-4">
                                    <div class="form-group row">
                                        <label class="control-label col-3">Datum</label>
                                        <div class="col-9">
                                            <input id="day" type="date" class="form-control form-control-sm" />
                                            <small id="day_feedback" class="form-control-feedback">Ankunftstag des Angriffes</small>
                                        </div>
                                    </div>
                                </div>
                                <!--/span-->
                                <div class="col-md-4">
                                    <div class="form-group row">
                                        <label class="control-label col-4">Uhrzeit</label>
                                        <div class="col-8">
                                            <input id="time" type="time" step="1" class="form-control form-control-sm" />
                                            <small id="time_feedback" class="form-control-feedback">Ankunftszeit des Angriffes</small>
                                        </div>
                                    </div>
                                </div>
                                <!--/span-->
                                <div class="col-md-4">
                                    <div class="form-group row">
                                        <label class="control-label col-3">Einheit</label>
                                        <div class="col-1">
                                            <img id="unit_img" src="{{ asset('images/ds_images/unit/unit_spear.png') }}">
                                        </div>
                                        <div class="col-8">
                                            <select id="slowest_unit" class="form-control form-control-sm">
                                                <option value="0">{{ __('ui.unit.spear') }}</option>
                                                <option value="1">{{ __('ui.unit.sword') }}</option>
                                                <option value="2">{{ __('ui.unit.axe') }}</option>
                                                @if ($config->game->archer == 1)
                                                    <option value="3">{{ __('ui.unit.archer') }}</option>
                                                @endif
                                                <option value="4">{{ __('ui.unit.spy') }}</option>
                                                <option value="5">{{ __('ui.unit.light') }}</option>
                                                @if ($config->game->archer == 1)
                                                    <option value="6">{{ __('ui.unit.marcher') }}</option>
                                                @endif
                                                <option value="7">{{ __('ui.unit.heavy') }}</option>
                                                <option value="8">{{ __('ui.unit.ram') }}</option>
                                                <option value="9">{{ __('ui.unit.catapult') }}</option>
                                                @if ($config->game->knight == 1)
                                                    <option value="10">{{ __('ui.unit.knight') }}</option>
                                                @endif
                                                <option value="11">{{ __('ui.unit.snob') }}</option>
                                            </select>
                                            <small class="form-control-feedback">Langsamste Einheit</small>
                                        </div>
                                    </div>
                                </div>
                                <!--/span-->
                                <div class="col-md-12">
                                    <div class="form-group row">
                                        <label class="control-label col-3">Notizen</label>
                                        <div class="col-12">
                                            <textarea id="note" class="form-control form-control-sm" style="height: 80px"></textarea>
                                        </div>
                                    </div>
                                </div>
                                @csrf
                                <input id="attack_list_id" type="hidden" value="{{ $attackList->id }}">
                                <input id="start_village_id" type="hidden">
                                <input id="target_village_id" type="hidden">
                                <div class="col-12">
                                    <input type="button" class="btn bg-danger btn-sm float-left text-white link" onclick="destroyOutdated()" value="{{ __('global.delete').' '.__('ui.tool.attackPlanner.outdated') }}">
                                    <input type="submit" class="btn btn-sm btn-success float-right">
                                </div>
                                <!--/span-->
                            </div>
                        </form>
                    </div>
                    <div class="tab-pane fade" id="link" role="tabpanel" aria-labelledby="link-tab">
                        <div class="row pt-3">
                            <div class="col-12">
                                <div class="form-group row">
                                    <label class="control-label col-md-2">Bearbeitungslink</label>
                                    <div class="col-md-1">
                                        <button class="btn btn-primary btn-sm" onclick="copy('link-edit')">Kopieren</button>
                                    </div>
                                    <div class="col-md-9">
                                        <input id="link-edit" type="text" class="form-control-plaintext form-control-sm disabled" value="{{ route('attackPlannerMode', [$attackList->id, 'edit', $attackList->edit_key]) }}" />
                                        <small class="form-control-feedback">Link um den Angriffsplan zu bearbeiten</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="form-group row">
                                    <label class="control-label col-md-2">Präsentationslink</label>
                                    <div class="col-md-1">
                                        <button class="btn btn-primary btn-sm" onclick="copy('link-show')">Kopieren</button>
                                    </div>
                                    <div class="col-md-9">
                                        <input id="link-show" type="text" class="form-control-plaintext form-control-sm disabled" value="{{ route('attackPlannerMode', [$attackList->id, 'show', $attackList->show_key]) }}" />
                                        <small class="form-control-feedback">Link um den Angriffsplan anzuschauen</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="tab-pane fade" id="import" role="tabpanel" aria-labelledby="import-tab">
                        <div class="row pt-3">
                            <div class="col-12">
                                <div class="form-group">
                                    <label class="control-label mr-3">Export</label> <button class="btn btn-primary btn-sm" onclick="copy('importWB')">Kopieren</button>
                                    <textarea id="exportWB" class="form-control form-control-sm" style="height: 80px"></textarea>
                                    <small class="form-control-feedback">Export für die Workbench</small>
                                </div>
                                <form id="importItemsForm">
                                    @csrf
                                    <div class="form-group">
                                        <label class="control-label mr-3">Import</label>
                                        <textarea id="importWB" class="form-control form-control-sm" style="height: 80px"></textarea>
                                        <small class="form-control-feedback">Importiere Angriffe aus deiner Workbench</small>
                                    </div>
                                    <div class="col-12">
                                        <input type="submit" class="btn btn-sm btn-success float-right" value="Import">
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    <div class="tab-pane fade" id="stats" role="tabpanel" aria-labelledby="stats-tab">
                        <div class="row pt-3">
                            STATS
                        </div>
                    </div>
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
                                <th>Startdorf</th>
                                <th>Angreifer</th>
                                <th>Zieldorf</th>
                                <th>Verteidiger</th>
                                <th>Einheit</th>
                                <th>Typ</th>
                                <th>Abschickzeit</th>
                                <th>Ankunft</th>
                                <th width="95px">Restzeit</th>
                                <th style="min-width: 25px">&nbsp;</th>
                                @if($mode == 'edit')
                                    <th style="min-width: 25px">&nbsp;</th>
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
@endsection

@section('js')
    <script type="text/javascript" src="{{ asset('plugin/jquery.countdown/jquery.countdown.min.js') }}"></script>
    <script src="https://unpkg.com/axios/dist/axios.min.js"></script>
    <script>
        var table =
            $('#data1').DataTable({
                ordering: false,
                processing: true,
                serverSide: true,
                pageLength: 25,
                searching: false,
                ajax: '{!! route('attackListItem.data', [ $attackList->id ]) !!}',
                columns: [
                    { data: 'start_village', name: 'start_village', render: function (val) {return val.trunc(25)}},
                    { data: 'attacker', name: 'attacker' },
                    { data: 'target_village', name: 'target_village', render: function (val) {return val.trunc(25)}},
                    { data: 'defender', name: 'defender' },
                    { data: 'slowest_unit', name: 'slowest_unit'},
                    { data: 'type', name: 'type' },
                    { data: 'send_time', name: 'send_time' },
                    { data: 'arrival_time', name: 'arrival_time' },
                    { data: 'time', name: 'time' },
                    { data: 'action', name: 'action' },
                    @if($mode == 'edit')
                    { data: 'delete', name: 'action' },
                    @endif
                ],
                columnDefs: [
                    {
                        'targets': 8,
                        'createdCell':  function (td, cellData, rowData, row, col) {
                            $(td).attr('data-countdown', cellData);
                        }
                    }
                ],
                "drawCallback": function(settings, json) {
                    exportWB();
                    countdown();
                },
                {!! \App\Util\Datatable::language() !!}
            });

        function typ_img(input){
            switch (input) {
                case 8: return '{{ \App\Util\Icon::icons(8) }}';
                case 11: return '{{ \App\Util\Icon::icons(11) }}';
                case 14: return '{{ \App\Util\Icon::icons(14) }}';
                case 45: return '{{ \App\Util\Icon::icons(45) }}';
                case 0: return '{{ \App\Util\Icon::icons(0) }}';
                case 1: return '{{ \App\Util\Icon::icons(1) }}';
                case 7: return '{{ \App\Util\Icon::icons(7) }}';
                case 46: return '{{ \App\Util\Icon::icons(46) }}';
            }
        }

        @if($mode == 'edit')
        function destroy(id,key) {
            $.ajax(
                {
                    url: "{{ config('app.url') }}/attackListItem/"+id,
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
                    url: '{{ route('attackPlannerMode', [$attackList->id, 'destroyOutdated', $attackList->edit_key]) }}',
                    type: 'GET',
                    dataType: "JSON",
                    success: function ()
                    {
                        table.ajax.reload();
                    }
                });
        }

        function store(send, arrival) {
            axios.post('{{ route('attackListItem.store') }}', {
                'attack_list_id' : $('#attack_list_id').val(),
                'type' : $('#type option:selected' ).val(),
                'start_village_id' : $('#start_village_id').val(),
                'target_village_id' : $('#target_village_id').val(),
                'slowest_unit' : $('#slowest_unit option:selected').val(),
                'note' : $('#note').val(),
                'send_time' : send,
                'arrival_time' : arrival,
            })
                .then((response) => {

                    table.ajax.reload();

                })
                .catch((error) => {

                });
        }

        function importWB() {
                var importWB = $('#importWB');
                axios.post('{{ route('attackPlannerMode', [$attackList->id, 'importWB', $attackList->edit_key]) }}', {
                    'import': importWB.val(),
                    'key': '{{$attackList->edit_key}}',
                    "_token": '{{ csrf_token() }}',
                })
                    .then((response) => {
                        importWB.val('');
                        table.ajax.reload();
                    })
                    .catch((error) => {
                        console.log(importWB.html());
                    });
        }

        $(document).on('submit', '#importItemsForm', function (e) {
            e.preventDefault();
            importWB();
        });

        @endif

        function exportWB() {
            axios.get('{{ route('attackPlannerMode', [$attackList->id, 'exportWB', $attackList->show_key]) }}', {
              })
                .then((response) => {
                    $('#exportWB').html(response.data);
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

        function countdown(){
            $('[data-countdown]').each(function() {
                var $this = $(this), finalDate = $(this).data('countdown');
                $this.countdown(finalDate, {
                    precision:  500
                })
                    .on('update.countdown', function(event) {
                        var format = '%H:%M:%S';
                        if(event.offset.totalDays > 0) {
                            if (event.offset.totalDays > 1) {
                                format = '%D Tage ' + format;
                            }else {
                                format = '%D Tag ' + format;
                            }
                        }
                        $this.html(event.strftime(format));
                    })
                    .on('finish.countdown', function (e) {
                        $this.addClass('bg-danger text-white').html('00:00:00')
                    });
            });
        };

        String.prototype.trunc = String.prototype.trunc ||
            function(n){
                return (this.length > n) ? this.substr(0, n-1) + '&hellip;' : this;
            };


        function slowest_unit(unit, dis){
            switch (unit) {
                case '0':
                    return Math.round('{{ round((float)$unitConfig->spear->speed * 60) }}' * dis);
                case '1':
                    return Math.round('{{ round((float)$unitConfig->sword->speed * 60) }}' * dis);
                case '2':
                    return Math.round('{{ round((float)$unitConfig->axe->speed * 60) }}' * dis);
                case '3':
                    return Math.round('{{ round((float)$unitConfig->archer->speed * 60) }}' * dis);
                case '4':
                    return Math.round('{{ round((float)$unitConfig->spy->speed * 60) }}' * dis);
                case '5':
                    return Math.round('{{ round((float)$unitConfig->light->speed * 60) }}' * dis);
                case '6':
                    return Math.round('{{ round((float)$unitConfig->marcher->speed * 60) }}' * dis);
                case '7':
                    return Math.round('{{ round((float)$unitConfig->heavy->speed * 60) }}' * dis);
                case '8':
                    return Math.round('{{ round((float)$unitConfig->ram->speed * 60) }}' * dis);
                case '9':
                    return Math.round('{{ round((float)$unitConfig->catapult->speed * 60) }}' * dis);
                case '10':
                    return Math.round('{{ round((float)$unitConfig->knight->speed * 60) }}' * dis);
                case '11':
                    return Math.round('{{ round((float)$unitConfig->snob->speed * 60) }}' * dis);
            }
        }

        function slowest_unit_img(unit){
            switch (unit) {
                case '0':
                    return '{{ \App\Util\Icon::icons(0) }}';
                case '1':
                    return '{{ \App\Util\Icon::icons(1) }}';
                case '2':
                    return '{{ \App\Util\Icon::icons(2) }}';
                case '3':
                    return '{{ \App\Util\Icon::icons(3) }}';
                case '4':
                    return '{{ \App\Util\Icon::icons(4) }}';
                case '5':
                    return '{{ \App\Util\Icon::icons(5) }}';
                case '6':
                    return '{{ \App\Util\Icon::icons(6) }}';
                case '7':
                    return '{{ \App\Util\Icon::icons(7) }}';
                case '8':
                    return '{{ \App\Util\Icon::icons(8) }}';
                case '9':
                    return '{{ \App\Util\Icon::icons(9) }}';
                case '10':
                    return '{{ \App\Util\Icon::icons(10) }}';
                case '11':
                    return '{{ \App\Util\Icon::icons(11) }}';
            }
        }

        $(document).ready(function (e) {
            $('#type').change(function (e) {
                var img = $('#type_img');
                var input = parseInt($(this).val());
                img.attr('src', typ_img(input));
            });

            $('#slowest_unit').change(function (e) {
                var img = $('#unit_img');
                var input = $(this).val();

                img.attr('src', slowest_unit_img(input));
            });

            $('.koord').on("keypress keyup blur",function (event) {
                $(this).val($(this).val().replace(/[^\d].+/, ""));
                if ((event.which < 48 || event.which > 57)) {
                    event.preventDefault();
                }
                if (event.keyCode == 13) {
                    calc();
                }
            });

            $("#xStart").keyup(function () {
                if (this.value.length == this.maxLength) {
                    $(this).next('#yStart').focus();
                }
            });

            $("#xStart").bind('paste', function(e) {
                var pastedData = e.originalEvent.clipboardData.getData('text');
                var coords = pastedData.split("|");
                $("#yStart").val(coords[1].substring(0, 3));
            });

            $("#xTarget").keyup(function () {
                if (this.value.length == this.maxLength) {
                    $(this).next('#yTarget').focus();
                }
            });

            $("#xTarget").bind('paste', function(e) {
                var pastedData = e.originalEvent.clipboardData.getData('text');
                var coords = pastedData.split("|");
                $("#yTarget").val(coords[1].substring(0, 3));
            });

            $('.koord').change(function (e) {
                var input = $('#' + this.id).parent().attr('id');
                var type = input.substring(0, 1).toUpperCase() + input.substring(1);
                var x = $('#x' + type).val();
                var y = $('#y' + type).val();
                if (x != '' && y != ''){
                    village(x, y, type)
                }
            });

            function village(x, y, input) {
                axios.get('{{ route('index') }}/api/{{ $worldData->server->code }}/{{ $worldData->name }}/villageCoords/'+ x + '/' + y, {

                })
                    .then((response) =>{
                        const data = response.data.data;
                        $('#village' + input).html(data['name'].trunc(25) + ' <b>' + x + '|' + y + '</b>  [' + data['continent'] + ']').attr('class', 'form-control-feedback ml-2 valid-feedback');
                        $('#' + input.toLowerCase() + '_village_id').val(data['villageID']);
                        $('#x' + input).attr('class', 'form-control form-control-sm mx-auto col-5 koord is-valid').attr('style', 'background-position-y: 0.4em;');
                        $('#y' + input).attr('class', 'form-control form-control-sm mx-auto col-5 koord is-valid').attr('style', 'background-position-y: 0.4em;');
                    })
                    .catch((error) =>{
                        $('#village' + input).html('{{ __('ui.villageNotExist') }}').attr('class', 'form-control-feedback ml-2 invalid-feedback');
                        $('#' + input.toLowerCase() + '_village_id').val('');
                        $('#x' + input).attr('class', 'form-control form-control-sm mx-auto col-5 koord is-invalid').attr('style', 'background-position-y: 0.4em;');
                        $('#y' + input).attr('class', 'form-control form-control-sm mx-auto col-5 koord is-invalid').attr('style', 'background-position-y: 0.4em;');
                    });
            }

            $(document).on('submit', '#createItemForm', function (e) {
                e.preventDefault();
                var start = $('#start_village_id').val();
                var target = $('#target_village_id').val();
                var day = $('#day').val();
                var time = $('#time').val();

                var xStart = $('#xStart');
                var yStart = $('#yStart');
                var xTarget = $('#xTarget');
                var yTarget = $('#yTarget');

                $('#day').attr('class', 'form-control form-control-sm');
                $('#time').attr('class', 'form-control form-control-sm');

                var error = 0;

                if (day == ''){
                    $('#day').attr('class', 'form-control form-control-sm is-invalid');
                    error += 1;
                }
                if (time == ''){
                    $('#time').attr('class', 'form-control form-control-sm is-invalid');
                    error += 1;
                }
                if (start == ''){
                    error += 1;
                }
                if (target == ''){
                    error += 1;
                }
                if (start == target){
                    alert('Start- und Zieldorf haben die gleichen Koordinaten!');
                    error += 1;
                }

                if (error == 0){
                    var dis = Math.sqrt(Math.pow(xStart.val() - xTarget.val(), 2) + Math.pow(yStart.val() - yTarget.val(), 2));
                    var slow = $('#slowest_unit').val();
                    var dateUnixArrival = new Date(day + ' ' + time).getTime();
                    var dateUnixSend = new Date(day + ' ' + time).getTime() - (slowest_unit(slow, dis)*1000);
                    store(dateUnixSend, dateUnixArrival);
                }
            });

        })
    </script>
@endsection
