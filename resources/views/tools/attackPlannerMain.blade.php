@extends('layouts.app')

@section('titel', $worldData->getDistplayName(),': '.__('tool.attackPlanner.title'))

@push('style')
    <style>
        @media print {
            #data1 a {
                color: #000;
            }
        }
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
                    {{ __('tool.attackPlanner.fastSwitch') }}
                </button>
                <div class="dropdown-menu" aria-labelledby="ownedPlanners">
                    @foreach($ownPlanners as $planner)
                        <a class="dropdown-item" href="{{
                            route('tools.attackPlannerMode', [$planner->id, 'edit', $planner->edit_key])
                            }}">{{ $planner->getTitle().' ['.$planner->world->getDistplayName().']' }}</a>
                    @endforeach
                </div>
            </div>
            @endauth
            <h1 class="font-weight-normal">{{ $attackList->getTitle().' ['.$worldData->getDistplayName().']' }}</h1>
        </div>
        <!-- ENDE Titel für Tablet | PC -->
        <!-- Titel für Mobile Geräte -->
        <div class="p-lg-5 mx-auto my-1 text-center d-lg-none truncate">
            <h1 class="font-weight-normal">
                {{ $attackList->getTitle().' ' }}
            </h1>
            <h4>
                {{ '['.$worldData->getDistplayName().']' }}
            </h4>
        </div>
        <!-- ENDE Titel für Mobile Geräte -->
        @if($mode == 'edit')
        <!-- Village Card -->
        <div class="col-12 d-print-none">
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
            <div class="card mb-2 p-3 d-print-none">
                <b>{{ __('tool.attackPlanner.warnSending') }}</b>
            </div>
            <div class="card">
                <div class="d-none">
                    <div id="datatablesHeader1" class="form-inline d-print-none">
                        <div>
                            <label id="audioTimingText" for="audioTiming">{!! str_replace('%S%', '<input id="audioTimingInput" class="form-control form-control-sm mx-1 text-right" style="width: 50px;" type="text" value="">', __('tool.attackPlanner.audioTiming')) !!}</label>
                            <input type="range" class="custom-range" min="0" max="300" id="audioTiming" value="60">
                        </div>
                        <div class="pl-3">
                            <select id="audioTypeSelection">
                            @foreach(App\Http\Controllers\Tools\AttackPlannerSoundController::getAlarmData() as $name => $url)
                                <option value="{{ asset($url) }}">{{ $name }}</option>
                            @endforeach
                            </select>
                        </div>
                        <div class="audioVolumeContainer pl-3 position-relative">
                            <h5><a class="btn @toDarkmode(btn-outline-dark) float-right" onclick="muteAudio()" role="button">
                                <i id="audioMuteIcon" class="fas fa-volume-up"></i>
                            </a></h5>
                            <div class="tooltip-audio popover fade bs-popover-right nowrap show" style="left: 100%">
                                <div class="arrow" style="top: 5px;"></div>
                                <h3 class="popover-header"></h3>
                                <div class="popover-body">
                                    <div id="audioVolumeLabel" class="d-inline pr-2" style="vertical-align:top"></div>
                                    <input type="range" class="custom-range w-auto" min="0" max="1" step="0.01" id="audioVolume" value="0.2">
                                </div>
                            </div>
                        </div>
                        <style>.tooltip-audio {display: none}.audioVolumeContainer:hover .tooltip-audio {display: block}</style>
                        <div class="pl-3">
                            <h5><a class="btn @toDarkmode(btn-outline-dark) float-right" onclick="audio()" role="button">
                                <i id="audioPlayIcon" class="fas fa-play"></i>
                            </a></h5>
                        </div>
                        @auth
                        <div class="pl-3">
                            <h5><a class="btn @toDarkmode(btn-outline-dark) float-right" href="{{ route("user.settings", ["settings-attplanner-upload"]) }}" target="_blank" role="button">
                                <i class="fas fa-upload"></i>
                            </a></h5>
                        </div>
                        @if($attackList->user_id != Auth::user()->id)
                            @if($attackList->follows()->where('user_id', Auth::user()->id)->count() > 0)
                                <div class="col-1">
                                    <h5><i id="follow-icon" style="cursor:pointer; text-shadow: 0 0 15px #000;" onclick="changeFollow()" class="fas fa-star h4 text-warning mt-2"></i></h5>
                                </div>
                            @else
                                <div class="col-1">
                                    <h5><i id="follow-icon" style="cursor:pointer" onclick="changeFollow()" class="far fa-star h4 text-muted mt-2"></i></h5>
                                </div>
                            @endif
                        @endif
                        @endauth
                    </div>
                    <div id="datatablesHeader2" data-toggle="hover" title="{{ __('tool.attackPlanner.uvModeDesc') }}">
                        <input type="checkbox" id="checkAsUV" class="mr-1" @checked($attackList->uvMode) >
                        <label for="checkAsUV">{{ __('tool.attackPlanner.uvMode') }}</label>
                    </div>
                </div>
                <div class="card-body table-responsive">
                    <table id="data1" class="table table-bordered table-striped nowrap w-100">
                        <thead>
                            <tr>
                                @if($mode == 'edit')
                                <th style="min-width: 25px"><input type="checkbox" class="selectAll"/></th>
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
                                <th style="min-width: 50px">
                                    <h4 class="mb-0 text-center" style="line-height: 1;">
                                        <a class="text-danger confirm-massDestroy" data-toggle="confirmation" data-content="{{ __('tool.attackPlanner.confirm.massDelete') }}" style="cursor: pointer"><i class="fas fa-times"></i></a>
                                    </h4>
                                </th>
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
    @if($mode == 'edit')
        <!-- START Modal -->
        @include('tools.attackPlanner.edit')
        <!-- ENDE Modal -->
    @endif
@endsection

@push('js')
    <script src="{{ \App\Util\BasicFunctions::asset('plugin/bootstrap-confirmation/bootstrap-confirmation.min.js') }}"></script>
    <audio id="audio-elm">
        <source type="audio/mpeg">
        Your browser does not support the audio element.
    </audio>
    <script>
        var firstDraw = true;
        var muteaudio = false;
        var audioTiming;
        var maxAudioTiming = $('#audioTiming')[0].max - 0;
        var table = $('#data1').DataTable({
            ordering: true,
            processing: true,
            serverSide: true,
            pageLength: 25,
            searching: false,
            @if($mode == 'edit')
            select: {
                style: 'multi+shift'
            },
            @endif
            order:[[{{ ($mode == 'edit')?'7':'6' }}, 'desc']],
            ajax: '{!! route('tools.attackListItem.data', [ $attackList->id , $attackList->show_key]) !!}',
            columns: [
                @if($mode == 'edit')
                { data: 'select', name: 'select'},
                @endif
                { data: 'start_village_id', name: 'start_village_id'},
                { data: 'attacker', name: 'attacker'},
                { data: 'target_village_id', name: 'target_village_id'},
                { data: 'defender', name: 'defender'},
                { data: 'slowest_unit', name: 'slowest_unit'},
                { data: 'type', name: 'type'},
                { data: 'send_time', name: 'send_time', orderSequence:["desc", "asc"]},
                { data: 'arrival_time', name: 'arrival_time'},
                { data: 'time', name: 'send_time', orderSequence:["desc", "asc"]},
                { data: 'info', name: 'info'},
                { data: 'action', name: 'action'},
                @if($mode == 'edit')
                { data: 'delete', name: 'delete' },
                @endif
            ],
            columnDefs: [
                {
                    'orderable': false,
                    @if($mode == 'edit')
                    'targets': [10,11,12]
                    @else
                    'targets': [9,10]
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
                countdown();
                popover();
                if(firstDraw) {
                    @if($mode == 'edit')
                    if(typeof updateExports !== 'undefined')
                        updateExports();
                    @endif
                    $('#data1_wrapper div.row:first-child').addClass("justify-content-between")
                    $('#data1_wrapper div.row:first-child > div').removeClass("col-md-6 col-sm-12")
                    $('#data1_wrapper div.row:first-child > div:eq(1)').append($('#datatablesHeader1'))
                    $('#data1_wrapper div.row:first-child').append($('#datatablesHeader2'))
                    loadAudioUIState();
                    $('#audioTiming').trigger('input')
                    $('#audioVolume').on('input', () => {$('#audioVolumeLabel').text(($('#audioVolume').val()-0).toFixed(2))})
                    $('#audioVolume').trigger('input')
                    $('#audioVolume').on('input', saveAudioUIState)
                    $('#audioTypeSelection').on('input', saveAudioUIState)
                    $('#audioTiming').on('input', saveAudioUIState)
                    $('#audioTimingInput').on('keyup', saveAudioUIState)

                    $('#checkAsUV').on('change', function() {
                        axios.post("{{ route('tools.attackPlannerModePost', [$attackList->id, 'saveAsUV', $attackList->show_key]) }}", {
                            'value': $('#checkAsUV').is(':checked'),
                        })
                            .then((response) => {
                                reloadData(false);
                            });
                    });
                    firstDraw = false
                }
            },
            stateSave: true,
            customName: "attackListItemData",
            {!! \App\Util\Datatable::language() !!}
        });

        @if($mode == 'edit')
            $("#data1 .selectAll").on("click", function() {
                if ($(this).prop('checked')) {
                    table.rows().select()
                }
                else {
                    table.rows().deselect()
                }
            })
            $("#data1 .selectAll").prop('checked', false)

            $("#data1 .confirm-massDestroy").on('confirmed.bs.confirmation', function() {
                var ids = []
                table.rows({ selected: true }).data().each(function(e) {
                    ids.push(e.id)
                })
                if(ids.length < 1) return

                axios.delete("{{ route('tools.attackListItem.massDestroy') }}", {
                    data: {
                        "id": "{{ $attackList->id }}",
                        "key": "{{ $attackList->edit_key }}",
                        "ids": ids,
                    }
                })
                    .then((response) => {
                        reloadData(true)
                    })
            })
        @endif

        function reloadData(upExp) {
            table.ajax.reload(null, false);
            @if($mode == 'edit')
            if(upExp && typeof updateExports !== 'undefined')
                updateExports();
            @endif
        }

        $(document).on('input', '#audioTiming', function () {
            var value = this.value;
            $('#audioTimingInput').val(value > maxAudioTiming ? maxAudioTiming : value);
            audioTiming = value;
        }).on('keyup', '#audioTimingInput', function (e) {
            var value = this.value;
            $('#audioTimingInput').val(value > maxAudioTiming ? maxAudioTiming : value);
            $('#audioTiming').val(value > maxAudioTiming ? maxAudioTiming : value);
            audioTiming = value;
        })

        @if($mode == 'edit')
        $(document).ready(function () {
            $('[data-toggle="tooltip"]').tooltip({classes: {"ui-tooltip": "ui-corner-all"}});
        });

        function destroy(id) {
            var url = "{{ route('tools.attackListItem.destroy', ['itemID']) }}";
            axios.delete(url.replaceAll('itemID', id), {
                data: {
                    "key": "{{ $attackList->edit_key }}",
                }
            })
                .then((response) => {
                    reloadData(true);
                });
        }

        function destroyAll() {
            axios.post("{{ route('tools.attackPlannerModePost', [$attackList->id, "clear", $attackList->edit_key]) }}")
                .then((response) => {
                    reloadData(true);
                });
        }

        $(function() {
            $('[data-toggle=confirmation]').confirmation({
                rootSelector: '[data-toggle=confirmation]',
                popout: true,
                title: "{{ __('user.confirm.destroy.title') }}",
                btnOkLabel: "{{ __('user.confirm.destroy.ok') }}",
                btnOkClass: 'btn btn-danger',
                btnCancelLabel: "{{ __('user.confirm.destroy.cancel') }}",
                btnCancelClass: 'btn btn-info',
            });
            $('.confirm-deleteAll').on('confirmed.bs.confirmation', destroyAll);
        });

        function destroyOutdated() {
            axios.post("{{ route('tools.attackPlannerModePost', [$attackList->id, 'destroyOutdated', $attackList->edit_key]) }}")
                .then((response) => {
                    reloadData(true);
                });
        }

        function destroySent() {
            axios.post("{{ route('tools.attackPlannerModePost', [$attackList->id, 'destroySent', $attackList->edit_key]) }}")
                .then((response) => {
                    reloadData(true);
                });
        }

        var keyArray = {};
        $(".time").on("keydown", function (e) {
            keyArray[e.which] = true;
            if(keyArray[17] && keyArray[86]){
                $(this).attr('type', 'text').select()
            }
        });

        $(".time").on("keyup", function (e) {
            delete keyArray[e.which];
        });

        $(".time").bind('paste', function (e) {
            var pastedData = e.originalEvent.clipboardData.getData('text');
            var time = pastedData.split(':');
            var output;
            if (time.length === 4){
                output = time[0] + ':' + time[1] + ':' + time[2] + '.' + time[3];
            }else {
                output = pastedData;
            }
            $(this).val(output).attr('type', 'time')
        });

        var editData = null
        function edit(id) {
            var context = $('#editItemForm');
            var data = table.row('#' + id).data();
            var rowData = data.DT_RowData;
            editData = data;
            var type = $.inArray(rowData.type, {{ json_encode(\App\Tool\AttackPlanner\AttackListItem::attackPlannerTypeIcons()) }}) ? rowData.type : -1;

            $('input[name="attack_list_item"]', context).val(data.id);
            $('select[name="type"]', context).val(type);
            $('input[name="xStart"]', context).val(rowData.xStart);
            $('input[name="yStart"]', context).val(rowData.yStart);
            $('input[name="xTarget"]', context).val(rowData.xTarget);
            $('input[name="yTarget"]', context).val(rowData.yTarget);
            $('select[name="slowest_unit"]', context).val(rowData.slowest_unit);
            $('input[name="spear"]', context).val(data.spear);
            $('input[name="sword"]', context).val(data.sword);
            $('input[name="axe"]', context).val(data.axe);
            $('input[name="archer"]', context).val(data.archer);
            $('input[name="spy"]', context).val(data.spy);
            $('input[name="light"]', context).val(data.light);
            $('input[name="marcher"]', context).val(data.marcher);
            $('input[name="heavy"]', context).val(data.heavy);
            $('input[name="ram"]', context).val(data.ram);
            $('input[name="catapult"]', context).val(data.catapult);
            $('input[name="knight"]', context).val(data.knight);
            $('input[name="snob"]', context).val(data.snob);
            $('select[name="support_boost"]', context).val(+(data.support_boost));
            $('select[name="tribe_skill"]', context).val(+(data.tribe_skill));
            $('textarea[name="note"]', context).val(data.note);

            $('.attack-type').trigger("change");
            $('.slowest-unit').trigger("change");
            $('.time-switcher[value=0]', context).click();
            checkVillage(rowData.xStart, rowData.yStart, $('input[name="xStart"]', context).parent());
            checkVillage(rowData.xTarget, rowData.yTarget, $('input[name="xTarget"]', context).parent());

            if(typeof editSetAutoTime !== 'undefined'){
                editSetAutoTime();
            }
            if(typeof editUpdateTime !== 'undefined'){
                editUpdateTime(rowData.day, rowData.time);
            }
        }

        $(document).on('hidden.bs.modal', '.edit-modal', function() {
            editData = null;
        })

        function checkVillage(x, y, parent) {
            if (x != '' && y != '') {
                var url = '{{ route('api.villageByCoord', [$worldData->id, '%xCoord%', '%yCoord%']) }}';
                axios.get(url.replaceAll('%xCoord%', x).replaceAll('%yCoord%', y), {})
                    .then((response) => {
                        $(".coord-input", parent).removeClass("is-invalid");
                        $(".coord-input", parent).addClass("is-valid");
                    })
                    .catch((error) => {
                        $(".coord-input", parent).removeClass("is-valid");
                        $(".coord-input", parent).addClass("is-invalid");
                    });
            }
        }

        function validatePreSend(par) {
            var sX = $('input[name="xStart"]', par).val();
            var sY = $('input[name="yStart"]', par).val();
            var tX = $('input[name="xTarget"]', par).val();
            var tY = $('input[name="yTarget"]', par).val();

            var error = 0;
            if (sX == '' || sY == '' || tX == '' || tY == ''){
                var data = []
                data['msg'] = '{{ __('tool.attackPlanner.errorKoordEmpty') }}';
                createToast(data['msg'], '{{ __('tool.attackPlanner.errorKoordTitle') }}', '{{ __('global.now') }}', 'fas fa-exclamation-circle text-danger')
                error += 1;
            }
            if (sX == tX && sY == tY){
                var data = []
                data['msg'] = '{{ __('tool.attackPlanner.errorKoord') }}';
                createToast(data['msg'], '{{ __('tool.attackPlanner.errorKoordTitle') }}', '{{ __('global.now') }}', 'fas fa-exclamation-circle text-danger')
                error += 1;
            }
            return error == 0;
        }

        @isIos
        function ios_time_prepare(raw_data) {
            var splitted = raw_data.split("&")
            var filtered_data = splitted.filter(d => !d.startsWith("ios_time"))
            var ios_data = splitted.filter(d => d.startsWith("ios_time"))
            var h = 0, m = 0, s = 0, ms = 0
            ios_data.forEach(d => {
                var tmp = d.split("=")
                if(tmp[0] == "ios_time_hour") h = +tmp[1]
                if(tmp[0] == "ios_time_minute") m = +tmp[1]
                if(tmp[0] == "ios_time_second") s = +tmp[1]
                if(tmp[0] == "ios_time_millisecond") ms = +tmp[1]
            })
            var time_str = "time=" + (h<10?"0"+h:""+h) + ":" + (m<10?"0"+m:""+m) + ":" + (s<10?"0"+s:""+s) + "." + (ms<100?(ms<10?"00"+ms:"0"+ms):""+ms)
            filtered_data.push(time_str)
            return filtered_data.join("&");
        }
        @endif
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
                    reloadData(true);
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
            muteaudio = !muteaudio
            updateAudioUI()
            saveAudioUIState();
        }

        function updateAudioUI() {
            if(muteaudio){
                $('#audioMuteIcon').removeClass('fa-volume-up').addClass('fa-volume-mute')
                $('.audioVolumeContainer .tooltip-audio').addClass("d-none")
            } else{
                $('#audioMuteIcon').removeClass('fa-volume-mute').addClass('fa-volume-up')
                $('.audioVolumeContainer .tooltip-audio').removeClass("d-none")
            }
        }

        function saveAudioUIState() {
            var data = {
                delay: $('#audioTiming').val(),
                sound: $('#audioTypeSelection').val(),
                volume: $('#audioVolume').val(),
                muted: muteaudio,
            }
            localStorage.setItem('attackPlannerSound', JSON.stringify(data))
        }

        function loadAudioUIState() {
            var data = JSON.parse(localStorage.getItem('attackPlannerSound'))
            if(data) {
                if(typeof(data.delay) !== "undefined") $('#audioTiming').val(data.delay)
                if(typeof(data.sound) !== "undefined") $('#audioTypeSelection').val(data.sound)
                if(typeof(data.volume) !== "undefined") $('#audioVolume').val(data.volume)
                if(typeof(data.muted) !== "undefined") muteaudio = data.muted
            }
            updateAudioUI()
        }

        function countdown(){
            countdownUpdateElements = [];
            update_delay();
            $('countdown').each(function () {
                var date = $(this).attr('date');
                startTimer(date, $(this));
            });
        }

        var timeDiff = null;
        function update_delay() {
            if(timeDiff == null) {
                var localTime = new Date().getTime() / 1000;
                var serverPage = parseInt({{ \Carbon\Carbon::now()->timestamp }}{{ \Carbon\Carbon::now()->milli }}) / 1000;
                timeDiff = serverPage - localTime;
                //console.log("Server delay", timeDiff);
            }

            axios.post('{{ route('api.time') }}')
                .then((response) => {
                    var localTime = new Date().getTime() / 1000;
                    now = parseInt(response.data['time']);
                    now+= parseInt(response.data['millis']) / 1000;
                    timeDiff = now - localTime;
                    //console.log("Ajax delay", timeDiff);
                })
                .catch((error) => {
                });
        }

        function startTimer(arrive, display) {
            var duration = arrive - new Date().getTime() / 1000 - timeDiff;
            if (duration < 0){
                display.parent().html("00:00:00").addClass("bg-danger text-white");
            }else {
                countdownUpdateElements.push({
                    "arrive": arrive,
                    "display": display,
                    "timerPlay": false
                })
            }
        }

        var countdownUpdateElements = [];

        function update_allCountdowns() {
            var days, hours, minutes, seconds;
            let mostRecent = 999999999;
            countdownUpdateElements.forEach(function(elm) {
                duration = elm.arrive - new Date().getTime() / 1000 - timeDiff;
                days = Math.floor(duration / 86400);
                hours = Math.floor((duration - days * 86400) / 3600);
                minutes = Math.floor((duration - days * 86400 - hours * 3600) / 60);
                seconds = Math.floor(duration - days * 86400 - hours * 3600 - minutes * 60);

                days = days < 1 ? "" : (days < 2 ? "1 Tag " : days + " Tage ");
                hours = hours < 10 ? "0" + hours : hours;
                minutes = minutes < 10 ? "0" + minutes : minutes;
                seconds = seconds < 10 ? "0" + seconds : seconds;
                elm.display.html(days + hours + ":" + minutes + ":" + seconds);

                if(mostRecent>duration){
                    mostRecent=duration;
                    document.title = days + hours + ":" + minutes + ":" + seconds;
                }

                if (Math.floor(duration) <= 0) {
                    elm.display.html("00:00:00");
                    elm.display.parent().addClass("bg-danger text-white");
                    countdownUpdateElements = countdownUpdateElements.filter(function(elmInner) {return elm != elmInner});
                }
                if (duration < audioTiming && !elm.timerPlay) {
                    audio();
                    elm.timerPlay = true;
                }
            })
        }

        var intervalUpdateCntdown = setInterval(update_allCountdowns, 1000);
        var intervalUpdateDelay = setInterval(update_delay, 300000);

        function audio(){
            if(!muteaudio){
                var audio = $('#audio-elm')[0];
                if($('#audio-elm source')[0].src != $('#audioTypeSelection').val()) {
                    //changed source
                    $('#audio-elm source')[0].src = $('#audioTypeSelection').val()
                    audio.load();
                }
                audio.volume = $('#audioVolume').val();
                audio.play();
                setTimeout(function () {
                    audio.pause();
                    audio.currentTime = 0;
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
            @foreach(\App\Tool\AttackPlanner\AttackListItem::attackPlannerTypeIcons() as $idx)
                case '{{ $idx }}': return '{{ \App\Util\Icon::icons($idx) }}';
            @endforeach
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

        $(function () {
            $('#title-input').on("keypress keyup blur",function (event) {
                if (event.keyCode == 13) {
                    event.preventDefault();
                    titleSave();
                }
            });

            $('.time-switcher').click(function(e) {
                var time_id = $(this).attr("value");
                $(".time-title", $(this).parent().parent()).html($(this).html());
                $(".time-type", $(this).parent().parent()).val(time_id);

                if($(this).parents('#editItemForm').length > 0 && editData != null && typeof editUpdateTime !== 'undefined'){
                    if(time_id == 0) {
                        editUpdateTime(editData.DT_RowData.day, editData.DT_RowData.time);
                    } else if(time_id == 1) {
                        editUpdateTime(editData.DT_RowData.sday, editData.DT_RowData.stime);
                    }
                }
            });

            $('.attack-type').change(function (e) {
                var img = $(".type-img", $(this).parent().parent());
                var input = $(this).val();
                img.attr('src', typ_img(input));
            });

            $('.slowest-unit').change(function (e) {
                var img = $(".unit-img", $(this).parent().parent());
                var input = $(this).val();
                img.attr('src', slowest_unit_img(input));
            });

            $(".coord-input").on("input", function (e) {
                if (this.value.length == this.maxLength) {
                    var next = $(this).nextAll(".coord-input:first");
                    if(next) {
                        next.focus();
                    }
                }
                var inputs = $(this).parent().children(".coord-input");
                var x = $(inputs[0]).val();
                var y = $(inputs[1]).val();
                checkVillage(x, y, $(this).parent())
            });

            $(".coord-input").on('paste', function(e) {
                var pastedData = e.originalEvent.clipboardData.getData('text');
                var match = pastedData.match(/(\d{1,3})\|(\d{1,3})/);
                if(match !== null) {
                    e.preventDefault();
                    x = match[1];
                    y = match[2];

                    var inputs = $(this).parent().children(".coord-input");
                    $(inputs[0]).val(x);
                    $(inputs[1]).val(y);

                    checkVillage(x, y, $(this).parent())
                }
            });

            $('.attack-type').trigger("change");
            $('.slowest-unit').trigger("change");
        })
    </script>
@endpush
