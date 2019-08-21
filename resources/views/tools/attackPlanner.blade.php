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
                                                    <option value="0">Angriff</option>
                                                    <option value="1">Eroberung</option>
                                                    <option value="2">Fake</option>
                                                    <option value="3">Wallbrecher</option>
                                                </optgroup>
                                                <optgroup label="Defensiv">
                                                    <option value="4">Unterstützung</option>
                                                    <option value="5">Stand Unterstützung</option>
                                                    <option value="6">Schnelle Unterstützung</option>
                                                    <option value="7">Fake Unterstützung</option>
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
                                            <textarea id="#note" class="form-control form-control-sm" style="height: 80px"></textarea>
                                        </div>
                                    </div>
                                </div>
                                @csrf
                                <input id="attack_list_id" type="hidden" value="{{ $attackList->id }}">
                                <input id="start_village_id" type="hidden">
                                <input id="target_village_id" type="hidden">
                                <div class="col-12">
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
                                    <label class="control-label col-2">Bearbeitungslink</label>
                                    <div class="col-1">
                                        <button class="btn btn-primary btn-sm" onclick="copy('create')">Kopieren</button>
                                    </div>
                                    <div class="col-9">
                                        <input id="link-create" type="text" class="form-control-plaintext form-control-sm disabled" value="{{ route('attackPlannerMode', [$worldData->server->code, $worldData->name, $attackList->id, 'create', $attackList->edit_key]) }}" />
                                        <small class="form-control-feedback ml-2">Link um den Angriffsplan zu bearbeiten</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="form-group row">
                                    <label class="control-label col-2">Präsentationslink</label>
                                    <div class="col-1">
                                        <button class="btn btn-primary btn-sm" onclick="copy('show')">Kopieren</button>
                                    </div>
                                    <div class="col-9">
                                        <input id="link-show" type="text" class="form-control-plaintext form-control-sm disabled" value="{{ route('attackPlannerMode', [$worldData->server->code, $worldData->name, $attackList->id, 'show', $attackList->show_key]) }}" />
                                        <small class="form-control-feedback ml-2">Link um den Angriffsplan anzuschauen</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="tab-pane fade" id="import" role="tabpanel" aria-labelledby="import-tab">
                        <div class="row pt-3">
                            IMPORTS
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
                <div class="card-body">
                    <table id="data1" class="table table-bordered no-wrap">
                        <thead>
                            <tr>
                                <th>Typ</th>
                                <th>Startdorf</th>
                                <th>Angreifer</th>
                                <th>Zieldorf</th>
                                <th>Verteidiger</th>
                                <th>Einheit</th>
                                <th>Abschickzeit</th>
                                <th>Ankunft</th>
                                <th width="95px">Restzeit</th>
                                <th>&nbsp;</th>
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
                dom: 't',
                ordering: false,
                paging: false,
                processing: true,
                serverSide: true,
                ajax: '{!! route('attackPlannerItem.data', [ $attackList->id ]) !!}',
                columns: [
                    { data: 'type', name: 'type' },
                    { data: 'start_village_id', name: 'start_village_id' },
                    { data: 'attacker', name: 'attacker' },
                    { data: 'target_village_id', name: 'target_village_id' },
                    { data: 'defender', name: 'defender' },
                    { data: 'slowest_unit', name: 'slowest_unit'},
                    { data: 'send_time', name: 'send_time' },
                    { data: 'arrival_time', name: 'arrival_time' },
                    { data: 'time', name: 'time' },
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
                    countdown();
                },
                keys: true, //enable KeyTable extension
                {!! \App\Util\Datatable::language() !!}
            });

        function typ_img(input){
            switch (input) {
                case 0:
                    return '{{ \App\Util\Icon::icons(8) }}';
                case 1:
                    return '{{ \App\Util\Icon::icons(11) }}';
                case 2:
                    return '{{ \App\Util\Icon::icons(14) }}';
                case 3:
                    return '{{ \App\Util\Icon::icons(45) }}';
                case 4:
                    return '{{ \App\Util\Icon::icons(0) }}';
                case 5:
                    return '{{ \App\Util\Icon::icons(1) }}';
                case 6:
                    return '{{ \App\Util\Icon::icons(7) }}';
                case 7:
                    return '{{ \App\Util\Icon::icons(46) }}';
            }
        }

        function copy(type) {
            /* Get the text field */
            var copyText = $("#link-" + type);

            /* Select the text field */
            copyText.select();

            /* Copy the text inside the text field */
            document.execCommand("copy");
        }

        function countdown(){
            $('[data-countdown]').each(function() {
                var $this = $(this), finalDate = $(this).data('countdown');
                $this.countdown(finalDate, function(event) {
                    var format = '%H:%M:%S';
                    if(event.offset.totalDays > 0) {
                        if (event.offset.totalDays > 1) {
                            format = '%D Tage ' + format;
                        }else {
                            format = '%D Tag ' + format;
                        }
                    }
                    $this.html(event.strftime(format));
                }).on('finish.countdown', function (e) {
                    $this.addClass('bg-danger text-white')
                });
            });
        };

        String.prototype.trunc = String.prototype.trunc ||
            function(n){
                return (this.length > n) ? this.substr(0, n-1) + '&hellip;' : this;
            };

        function store(send, arrival) {
            axios.post('{{ route('attackPlannerItem.store') }}', {
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
                    console.log(error);
                });
        }

        function slowest_unit(unit, dis){
            switch (unit) {
                case '0':
                    return Math.round('{{ round((float)$unitConfig->spear->speed) }}' * dis);
                case '1':
                    return Math.round('{{ round((float)$unitConfig->sword->speed) }}' * dis);
                case '2':
                    return Math.round('{{ round((float)$unitConfig->axe->speed) }}' * dis);
                case '3':
                    return Math.round('{{ round((float)$unitConfig->archer->speed) }}' * dis);
                case '4':
                    return Math.round('{{ round((float)$unitConfig->spy->speed) }}' * dis);
                case '5':
                    return Math.round('{{ round((float)$unitConfig->light->speed) }}' * dis);
                case '6':
                    return Math.round('{{ round((float)$unitConfig->marcher->speed) }}' * dis);
                case '7':
                    return Math.round('{{ round((float)$unitConfig->heavy->speed) }}' * dis);
                case '8':
                    return Math.round('{{ round((float)$unitConfig->ram->speed) }}' * dis);
                case '9':
                    return Math.round('{{ round((float)$unitConfig->catapult->speed) }}' * dis);
                case '10':
                    return Math.round('{{ round((float)$unitConfig->knight->speed) }}' * dis);
                case '11':
                    return Math.round('{{ round((float)$unitConfig->snob->speed) }}' * dis);
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
                        $('#x' + input).attr('class', 'form-control form-control-sm mx-auto col-5 koord is-valid');
                        $('#y' + input).attr('class', 'form-control form-control-sm mx-auto col-5 koord is-valid');
                    })
                    .catch((error) =>{
                        $('#village' + input).html('{{ __('ui.villageNotExist') }}').attr('class', 'form-control-feedback ml-2 invalid-feedback');
                        $('#' + input.toLowerCase() + '_village_id').val('');
                        $('#x' + input).attr('class', 'form-control form-control-sm mx-auto col-5 koord is-invalid');
                        $('#y' + input).attr('class', 'form-control form-control-sm mx-auto col-5 koord is-invalid');
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
                    var dateUnixSend = new Date(day + ' ' + time).getTime() - (slowest_unit(slow, dis)*60*1000);
                    store(dateUnixSend, dateUnixArrival);
                }
            });

        })
    </script>
@endsection
