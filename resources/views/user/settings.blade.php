@extends('layouts.app')

@section('titel', ucfirst(__('ui.titel.settings')).' von '.Auth::user()->name)

@push('style')
    <link href="{{ \App\Util\BasicFunctions::asset('plugin/bootstrap-colorpicker/bootstrap-colorpicker.min.css') }}" rel="stylesheet">
@endpush

@section('content')
    <div class="row justify-content-center">
        <!-- Titel für Tablet | PC -->
        <div class="p-lg-5 mx-auto my-1 text-center d-none d-lg-block">
            <h1 class="font-weight-normal">{{ ucfirst(__('ui.personalSettings.title')).' von '.Auth::user()->name }}</h1>
        </div>
        <!-- ENDE Titel für Tablet | PC -->
        <!-- Titel für Mobile Geräte -->
        <div class="p-lg-5 mx-auto my-1 text-center d-lg-none truncate">
            <h1 class="font-weight-normal">
                {{ ucfirst(__('ui.titel.settings')).' von ' }}
            </h1>
            <h4>
                {{ Auth::user()->name }}
            </h4>
        </div>
        <!-- ENDE Titel für Tablet | PC -->
    </div>
    <div class="row justify-content-center">
        <div class="col-12 col-lg-3 mt-2">
            <div class="card">
                <div class="card-header">
                    {{ __('ui.personalSettings.title') }}
                </div>
                <div class="card-body">
                    <div class="nav flex-column nav-pills" id="settings-tab" role="tablist" aria-orientation="vertical">
                        <a class="nav-link {{ ($page == 'settings-profile')? 'active' : '' }}" id="settings-profile-tab" data-toggle="pill" href="#settings-profile" role="tab" aria-controls="settings-profile" aria-selected="true">{{ __('ui.personalSettings.profile') }}</a>
                        <a class="nav-link {{ ($page == 'settings-account')? 'active' : '' }}" id="settings-account-tab" data-toggle="pill" href="#settings-account" role="tab" aria-controls="settings-account" aria-selected="false">{{ __('ui.personalSettings.account') }}</a>
                        @can('discord_bot_beta')
                        <a class="nav-link {{ ($page == 'settings-connection')? 'active' : '' }}" id="settings-connection-tab" data-toggle="pill" href="#settings-connection" role="tab" aria-controls="settings-connection" aria-selected="false">{{ __('ui.personalSettings.connection') }}</a>
                        @endcan
                        <a class="nav-link {{ ($page == 'settings-map')? 'active' : '' }}" id="settings-map-tab" data-toggle="pill" href="#settings-map" role="tab" aria-controls="settings-map" aria-selected="false">{{ __('ui.personalSettings.map') }}</a>
                        <a class="nav-link {{ ($page == 'settings-attplanner-upload')? 'active' : '' }}" id="settings-attplanner-upload-tab" data-toggle="pill" href="#settings-attplanner-upload" role="tab" aria-controls="settings-attplanner-upload" aria-selected="false">{{ __('ui.personalSettings.attplannerUpload.title') }}</a>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12 col-lg-9 mt-2">
            <div class="card">
                <div class="card-header">
                    <h5 id="settings-card-title" class="card-title my-1">{{ __('ui.personalSettings.title') }}</h5>
                </div>
                <div id="user-overview" class="card-body">
                    <div class="tab-content" id="settings-tabContent">
                        <!-- START settings-profile -->
                        <div class="tab-pane fade {{ ($page == 'settings-profile')? 'show active' : '' }}" id="settings-profile" role="tabpanel" aria-labelledby="settings-profile-tab" data-title="{{ __('ui.personalSettings.profile') }}">
                            <form id="settings-account-form">
                                <div class="form-group">
                                    <label for="name">{{ __('user.name') }}</label>
                                    <div class="input-group">
                                        <input type="text" class="form-control" id="name" value="{{ Auth::user()->name }}">
                                        <div id="name-errors" class="text-danger"></div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="email">{{ __('user.mailAddress') }}</label>
                                    <div class="input-group">
                                        <input type="email" class="form-control" id="email" placeholder="{{ Auth::user()->email }}" aria-describedby="basic-addon2" readonly>
                                        <div class="input-group-append">
                                            <span class="input-group-text text-danger" id="basic-addon2">{{ __('ui.personalSettings.workInProgress') }}</span>
                                        </div>
                                        <div id="email-errors" class="text-danger"></div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <input class="btn btn-success float-right" type="submit" value="{{ __('global.save') }}">
                                </div>
                            </form>
                        </div>
                        <!-- ENDE settings-profile -->
                        <!-- START settings-account -->
                        <div class="tab-pane fade {{ ($page == 'settings-account')? 'show active' : '' }}" id="settings-account" role="tabpanel" aria-labelledby="settings-account-tab" data-title="{{ __('ui.personalSettings.account') }}">
                            <p class="col-12 text-center">
                                {!!  __('ui.personalSettings.account_help') !!}
                            </p>
                            @if (session('status'))
                                <div class="col-12 text-center mb-3">
                                    <span class="invalid-feedback d-block" role="alert">
                                        <strong>{{ session('status') }}</strong>
                                    </span>
                                </div>
                            @endif
                            <table class="table">
                                @foreach(\App\Http\Controllers\User\LoginController::getDriver() as $driver)
                                    <tr>
                                        <td><i class="{{ $driver['icon'] }} h1 m-2" style="color:{{ $driver['color'] }}"></i></td>
                                        @if(Auth::user()->profile->checkOauth($driver['name']))
                                            <td><b>{{ __('ui.personalSettings.connectionVerified') }}</b></td>
                                            <td><a class="btn btn-danger m-2 float-right" href="{{ route('user.socialiteDestroy', $driver['name']) }}">{{ __('global.delete') }}</a></td>
                                        @else
                                            <td></td>
                                            <td><a class="btn btn-primary m-2 float-right" href="{{ route('loginRedirect', $driver['name']) }}">{{ __('global.add') }}</a></td>
                                        @endif
                                    </tr>
                                @endforeach
                            </table>
                        </div>
                        <!-- ENDE settings-account -->
                        @can('discord_bot_beta')
                        <!-- START settings-connection -->
                        <div class="tab-pane fade {{ ($page == 'settings-connection')? 'show active' : '' }}" id="settings-connection" role="tabpanel" aria-labelledby="settings-connection-tab" data-title="{{ __('ui.personalSettings.connection') }}">
                            <p class="col-12 text-center">
                                {!!  __('ui.personalSettings.connection_help') !!}
                            </p>
                            <form id="connectionForm">
                                <div class="form-group row">
                                    <div class="col-sm-2">
                                        <label class="form-check-label" for="server">{{ __('ui.server.title') }}:</label>
                                        <select id="server" name="server" class="form-control mr-1 data-input-map select2-container--classic select2-single">
                                            <option></option>
                                            @foreach(\App\Server::all() as $serverSelect)
                                                <option value="{{ $serverSelect->id }}" title="{{ $serverSelect->flag }}">{{ strtoupper($serverSelect->code) }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-sm-4">
                                        <label class="form-check-label" for="world">{{ __('ui.table.world') }}:</label>
                                        <select id="world" name="world" class="form-control mr-1 data-input-map select2-container--classic select2-single">
                                            <option></option>
                                        </select>
                                    </div>
                                    <div class="col-sm-4">
                                        <label class="form-check-label" for="player">{{ __('ui.table.player') }}:</label>
                                        <select id="player" name="player" class="form-control mr-1 data-input-map select2-container--classic select2-single">
                                            <option></option>
                                        </select>
                                    </div>
                                    <div class="col-sm-2">
                                        <label class="form-check-label"> </label>
                                        <button type="submit" class="btn btn-primary position-absolute" style="bottom: 0;">{{ __('global.add') }}</button>
                                    </div>
                                </div>
                            </form>
                            <div class="table-responsive">
                                <table id="connectionTable" class="table table-striped table-hover table-sm w-100">
                                    <thead>
                                    <tr>
                                        <th style="max-width: 50px; min-width: 50px">{{ ucfirst(__('ui.server.title')) }}</th>
                                        <th style="max-width: 50px; min-width: 50px">{{ ucfirst(__('ui.table.world')) }}</th>
                                        <th style="max-width: 50px; min-width: 50px">{{ ucfirst(__('ui.table.player')) }}</th>
                                        <th>{{ ucfirst(__('ui.table.village')) }}</th>
                                        <th>&nbsp;</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <!-- ENDE settings-connection -->
                        @endcan
                        <!-- START settings-map -->
                        <div class="tab-pane fade {{ ($page == 'settings-map')? 'show active' : '' }}" id="settings-map" role="tabpanel" aria-labelledby="settings-map-tab" data-title="{{ __('ui.personalSettings.map') }}">
                        <form id="mapSettingsForm">
                            <div id='default-background-div' class='col-12 input-group mb-2 mr-sm-2'>
                                <div class='colour-picker-map input-group-prepend'>
                                    <span class='input-group-text colorpicker-input-addon'><i></i></span>
                                    <input name='default[background]' id="bg-colour" type='hidden' value='{{ Auth::user()->profile->getBackgroundColour() }}'/>
                                </div>
                                <label class='form-control'>{{ __('tool.map.defaultBackground') }}</label>
                            </div>
                            <div class="form-inline mb-2">
                                <div class="form-check col-lg-auto ml-auto">
                                    <input id="checkbox-show-player-hid" name="showPlayerHere" type="hidden" value="1" />
                                    <input id="checkbox-show-player" name="showPlayer" type="checkbox" class="form-check-input map-settings-input" @checked(Auth::user()->profile->playerEnabled()) />
                                    <label class="form-check-label" for="checkbox-show-player">{{ __('tool.map.showPlayer') }}</label>
                                </div>
                                <div id='default-player-div' class='col-lg-9 input-group'>
                                    <div class='colour-picker-map input-group-prepend'>
                                        <span class='input-group-text colorpicker-input-addon'><i></i></span>
                                        <input name='default[player]' id="player-colour" type='hidden' value='{{ Auth::user()->profile->getDefPlayerColour() }}'/>
                                    </div>
                                    <label class='form-control'>{{ __('tool.map.defaultPlayer') }}</label>
                                </div>
                            </div>
                            <div class="form-inline mb-2">
                                <div class="form-check col-lg-auto ml-auto">
                                    <input id="checkbox-show-barbarian-hid" name="showBarbarianHere" type="hidden" value="1" />
                                    <input id="checkbox-show-barbarian" name="showBarbarian" type="checkbox" class="form-check-input map-settings-input" @checked(Auth::user()->profile->barbarianEnabled()) />
                                    <label class="form-check-label" for="checkbox-show-barbarian">{{ __('tool.map.showBarbarian') }}</label>
                                </div>
                                <div id='default-barbarian-div' class='col-lg-9 input-group'>
                                    <div class='colour-picker-map input-group-prepend'>
                                        <span class='input-group-text colorpicker-input-addon'><i></i></span>
                                        <input name='default[barbarian]' type='hidden' id="barbarian-colour" value='{{ Auth::user()->profile->getDefBarbarianColour() }}'/>
                                    </div>
                                    <label class='form-control'>{{ __('tool.map.defaultBarbarian') }}</label>
                                </div>
                            </div>
                            <div class="form-inline mb-2">
                                <div class="col-lg-6 input-group">
                                    <label for="map-zoom-value" class="col-lg-4">{{ __('tool.map.zoom') }}</label>
                                    <select class="form-control col-lg-2 map-settings-input" id="map-zoom-value" name="zoomValue">
                                        <option value="1000" @selected(($mapDimensions['w'] == 1000)) >0</option>
                                        <option value="599" @selected(($mapDimensions['w'] == 599)) >1</option>
                                        <option value="359" @selected(($mapDimensions['w'] == 359)) >2</option>
                                        <option value="215" @selected(($mapDimensions['w'] == 215)) >3</option>
                                        <option value="129" @selected(($mapDimensions['w'] == 129)) >4</option>
                                        <option value="77" @selected(($mapDimensions['w'] == 77)) >5</option>
                                        <option value="46" @selected(($mapDimensions['w'] == 46)) >6</option>
                                        <option value="28" @selected(($mapDimensions['w'] == 28)) >7</option>
                                        <option value="16" @selected(($mapDimensions['w'] == 16)) >8</option>
                                        <option value="10" @selected(($mapDimensions['w'] == 10)) >9</option>
                                    </select>
                                </div>
                                <div id="center-pos-div" class="input-group col-lg-6 mb-2">
                                    <label for="center-pos-x" class="col-lg-4">{{ __('tool.map.center') }}</label>
                                    <input id="center-pos-x" name="centerX" class="form-control mr-1 map-settings-input" placeholder="500" type="text" value="{{ $mapDimensions['cx'] }}"/>|
                                    <input id="center-pos-y" name="centerY" class="form-control ml-1 map-settings-input" placeholder="500" type="text" value="{{ $mapDimensions['cy'] }}"/>
                                </div>
                            </div>
                            <div class="form-inline mb-2 col-lg-6">
                                <label for="markerFactor" class="col-lg-auto">{{ ucfirst(__('tool.map.markerFactor')) }}</label>
                                <input type="range" class="custom-range w-auto flex-lg-fill map-settings-input" min="0" max="0.4" step="0.01" id="markerFactor" value="{{ Auth::user()->profile->map_makerFactor }}" name="markerFactor">
                                <div id="markerFactorText" class="ml-4">{{ intval(Auth::user()->profile->map_markerFactor*100) }}%</div>
                            </div>
                            <div class="form-group float-right">
                                <input type="submit" class="btn btn-sm btn-success" value='{{ ucfirst(__('global.save')) }}'>
                            </div>
                        </form>
                        </div>
                        <!-- ENDE settings-map -->
                        <!-- START settings-attackPlanner-upload -->
                        <div class="tab-pane fade {{ ($page == 'settings-attplanner-upload')? 'show active' : '' }}" id="settings-attplanner-upload" role="tabpanel" aria-labelledby="settings-map-tab" data-title="{{ __('ui.personalSettings.attplannerUpload.title') }}">
                            <div class="col-12 col-md-6">
                                <form id="attackPlannerFileUpload">
                                <label class="form-label" for="attackPlannerUpload">{{ __('ui.personalSettings.attplannerUpload.description') }}</label>
                                <input type="file" class="form-control" name="file" />
                                <div class="form-group mt-3">
                                    <label for="name">{{ __('user.name') }}</label>
                                    <div class="input-group">
                                        <input type="text" class="form-control" name="name" value="">
                                        <div id="name-errors" class="text-danger"></div>
                                    </div>
                                </div>
                                <input type="submit" class="btn btn-sm btn-success mt-2" value="{{ __('global.save') }}">
                                </form>
                            </div>
                            <div class="col-12 mt-4">
                                <h4>{{ __('ui.personalSettings.attplannerUpload.tableTitle') }}</h4>
                                <table class="table">
                                @foreach(Auth::user()->profile->customSounds as $sound)
                                    <tr data-target="{{ $sound->id }}">
                                        <td>
                                            <h4>
                                                <div class="sound-edit-name">
                                                    <a class="text-success" style="cursor: pointer;"><i class="fas fa-edit"></i></a>
                                                    <div class="name-show-wrapper d-inline">{{ $sound->name }}</div>
                                                </div>
                                                <div class="d-none name-edit-wrapper"><input type="text" class="form-control w-auto d-inline">
                                                    <a class="text-success" style="cursor: pointer;"><i class="far fa-save"></i></a>
                                                </div>
                                            </h4>
                                        </td>
                                        <td>
                                            <h4 class="mb-0">
                                                <a class="text-success sound-play" style="cursor: pointer;">
                                                    <i class="fas fa-play"></i>
                                                </a>
                                                <a class="text-danger sound-delete" style="cursor: pointer;">
                                                    <i class="fas fa-times"></i>
                                                </a>
                                            </h4>
                                        </td>
                                    </tr>
                                @endforeach
                                </table>
                            </div>
                        </div>
                        <!-- ENDE settings-attackPlanner-upload -->
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('js')
    <script src="{{ \App\Util\BasicFunctions::asset('plugin/select2/select2.full.min.js') }}"></script>
    <script src="{{ \App\Util\BasicFunctions::asset('plugin/bootstrap-datepicker/bootstrap-datepicker.min.js') }}"></script>
    <script src="{{ \App\Util\BasicFunctions::asset('plugin/bootstrap-colorpicker/bootstrap-colorpicker.min.js') }}"></script>
    <script>
        var worldTable = $("#world");
        var playerTable = $("#player");
        var connectionTable = $('#connectionTable').DataTable({
            "processing": true,
            "serverSide": true,
            "ajax": "{{ route('user.DsConnection') }}",
            "columns": [
                { "data": "server" },
                { "data": "world" },
                { "data": "player" },
                { "data": "key" },
                { "data": "action" },
            ],
            responsive: true,
            {!! \App\Util\Datatable::language() !!}
        });

        $(function () {
            $("#date_picker").datepicker({
                language: 'all',
                format: 'dd.mm.yyyy',
                weekStart: 1,
            })

            $("#server").select2({
                templateResult: formatState,
                theme: "bootstrap4",
                placeholder: "{{ __('ui.server.title') }}"
            });

            worldTable.select2({
                theme: "bootstrap4",
                placeholder: "{{ __('ui.table.world') }}",
            });

            playerTable.select2({
                theme: "bootstrap4",
                placeholder: "{{ __('ui.table.player') }}",
            });

            $('#server').on('select2:select', function (e) {
                var url = "{{ route('api.activeWorldByServer', ['%server%']) }}";
                axios.get(url.replace('%server%', $('#server').val()))
                    .then(function (response) {
                        worldTable.empty().trigger("change");
                        var option1 = new Option('{{ __('ui.table.world') }}', 0, false, false);
                        worldTable.append(option1).trigger('change');
                        var array = response.data;
                        array.forEach(function (data) {
                            var option = new Option(data.text, data.id, false, false);
                            worldTable.val(null).trigger('change');
                            worldTable.append(option).trigger('change');
                        })
                    })
                    .catch((error) => {
                    });
            });

            $('#world').on('select2:select', function (e) {
                var dataUrl = "{{ route('api.select2Player', ['%world%']) }}";
                playerTable.val(null).trigger('change');
                playerTable.select2({
                    ajax: {
                        url: dataUrl.replace("%world%", $('#world').val()),
                        data: function (params) {
                            var query = {
                                search: params.term,
                                page: params.page || 1
                            }

                            // Query parameters will be ?search=[term]&type=public
                            return query;
                        }
                    },
                    allowClear: true,
                    placeholder: '{{ ucfirst(__('tool.map.playerSelectPlaceholder')) }}',
                    theme: "bootstrap4"
                });
            });
            $('#server').trigger('select2:select', null);

            function formatState (state) {
                if (!state.id) {
                    return state.text;
                }
                var baseUrl = "/user/pages/images/flags";
                var $state = $(
                    '<span><span class="flag-icon flag-icon-' + state.title + '"></span> ' + state.text + '</span>'
                );
                return $state;
            };

            $.fn.datepicker.dates['all'] = {
                days: ["{{ __('datepicker.Sunday') }}", "{{ __('datepicker.Monday') }}", "{{ __('datepicker.Tuesday') }}", "{{ __('datepicker.Wednesday') }}", "{{ __('datepicker.Thursday') }}", "{{ __('datepicker.Friday') }}", "{{ __('datepicker.Saturday') }}"],
                daysShort: ["{{ __('datepicker.Sun') }}", "{{ __('datepicker.Mon') }}", "{{ __('datepicker.Tue') }}", "{{ __('datepicker.Wed') }}", "{{ __('datepicker.Thu') }}", "{{ __('datepicker.Fri') }}", "{{ __('datepicker.Sat') }}"],
                daysMin: ["{{ __('datepicker.Su') }}", "{{ __('datepicker.Mo') }}", "{{ __('datepicker.Tu') }}", "{{ __('datepicker.We') }}", "{{ __('datepicker.Th') }}", "{{ __('datepicker.Fr') }}", "{{ __('datepicker.Sa') }}"],
                months: ["{{ __('datepicker.January') }}", "{{ __('datepicker.February') }}", "{{ __('datepicker.March') }}", "{{ __('datepicker.April') }}", "{{ __('datepicker.May') }}", "{{ __('datepicker.June') }}", "{{ __('datepicker.July') }}", "{{ __('datepicker.August') }}", "{{ __('datepicker.September') }}", "{{ __('datepicker.October') }}", "{{ __('datepicker.November') }}", "{{ __('datepicker.December') }}"],
                monthsShort: ["{{ __('datepicker.Jan') }}", "{{ __('datepicker.Feb') }}", "{{ __('datepicker.Mar') }}", "{{ __('datepicker.Apr') }}", "{{ __('datepicker.May') }}", "{{ __('datepicker.Jun') }}", "{{ __('datepicker.Jul') }}", "{{ __('datepicker.Aug') }}", "{{ __('datepicker.Sep') }}", "{{ __('datepicker.Oct') }}", "{{ __('datepicker.Nov') }}", "{{ __('datepicker.Dec') }}"],
                today: "{{ __('datepicker.Today') }}",
                monthsTitle: "{{ __('datepicker.months') }}",
                clear: "{{ __('datepicker.Clear') }}",
            };

            $('.nav-link', $('#user-overview')).on("click", function (e) {
                var href = $(this).attr("href");
                history.pushState(null, null, href.replace('#', '/user/settings/'));
                e.preventDefault();
            });
        });

        $('a[data-toggle="pill"]').on('shown.bs.tab', function (e) {
            var loc = window.location.href
            var url = loc.substring(0, loc.lastIndexOf('/') + 1) + $(this).attr('href').substring(1)
            $('#settings-card-title').html($($(this).attr('href')).data('title'))
            
            window.history.pushState(null, $($(this).attr('href')).data('title'), url)
        });

        $(document).on('submit', '#connectionForm', function (e) {
            e.preventDefault();
            axios.post('{{ route('user.addConnection') }}', {
                'world': $('#world').val(),
                'player': $('#player').val(),
            })
                .then((response) => {

                    connectionTable.ajax.reload();
                })
                .catch((error) => {
                    if(error.response.status == 422) {
                        var errors = error.response.data.errors;
                        $.each(errors, function(i, item) {
                            var data = item[0].charAt(0).toUpperCase()+item[0].substr(1)
                            createToast(data, "{{ __('ui.personalSettings.connection') }}", "{{ __('global.now') }}");
                        });
                    }
                });
        })

        function checkConnection(id) {
            axios.post('{{ route('user.DsConnection') }}',{
                'id': id,
            })
                .then((response) => {
                    var data = response.data;
                    createToast(data['msg'], "{{ __('ui.personalSettings.connection') }}", "{{ __('global.now') }}");
                    connectionTable.ajax.reload();
                })
                .catch((error) => {
                });
        }

        function destroyConnection(id, key) {
            axios.post('{{ route('user.destroyConnection') }}',{
                'id': id,
                'key': key,
            })
                .then((response) => {
                    var data = response.data;
                    createToast(data['msg'], "{{ __('ui.personalSettings.connection') }}", "{{ __('global.now') }}");
                    connectionTable.ajax.reload();
                })
                .catch((error) => {
                });
        }

        $(document).on('submit', '#settings-account-form', function (e) {
            e.preventDefault();
            axios.post('{{ route('user.saveSettingsAccount') }}',{
                'name': $('#name').val(),
                'birthday': $('#date_picker').val(),
            })
                .then((response) => {
                    var data = response.data;
                    createToast(data['msg'], "{{ __('ui.personalSettings.connection') }}", "{{ __('global.now') }}");
                })
                .catch((error) => {
                    if(error.response.status == 422) {
                        var errors = error.response.data.errors;
                        $.each(errors, function(i, item) {
                            var data = item[0].charAt(0).toUpperCase()+item[0].substr(1)
                            createToast(data, "{{ __('ui.personalSettings.connection') }}", "{{ __('global.now') }}");
                        });
                    }
                });
        })

        function copy(type) {
            /* Get the text field */
            var copyText = $("#" + type);
            /* Select the text field */
            copyText.select();
            /* Copy the text inside the text field */
            document.execCommand("copy");
        }
    </script>
    <script>
        /* Map settings */
        $(function() {
            $('.colour-picker-map')
                .colorpicker({
                    useHashPrefix: false,
                    template: '<div class="colorpicker">' +
                        '<div class="colorpicker-saturation"><i class="colorpicker-guide"></i></div>' +
                        '<div class="colorpicker-hue"><i class="colorpicker-guide"></i></div>' +
                        '<div class="colorpicker-bar"><input class="color-io" type="text"></div>' +
                        '</div>',
                    extensions: [{
                        name: 'swatches',
                        options: {
                            colors: {
                                'c11': '#ffffff', 'c12': '#eeece1', 'c13': '#d99694', 'c14': '#c0504d', 'c15': '#f79646', 'c16': '#ffff00', 'c17': '#9bbb59',
                                'c21': '#4bacc6', 'c22': '#548dd4', 'c23': '#1f497d', 'c24': '#8064a2', 'c25': '#f926e5', 'c26': '#7f6000', 'c27': '#000000',
                            },
                            namesAsValues: false
                        }
                    }]
                })
                .on('colorpickerChange', function(e) {
                    var popID = $("span", e.colorpicker.element).attr("aria-describedby");
                    var io = $(".color-io", $("#" + popID));

                    if (e.value === io.val() || !e.color || !e.color.isValid()) {
                        // do not replace the input value if the color is invalid or equals
                        return;
                    }

                    io.val(e.color.string());
                    // initialize the input on colorpicker creation
                })
                .on("colorpickerShow", function(e) {
                    var popID = $("span", e.colorpicker.element).attr("aria-describedby");
                    var io = $(".color-io", $("#" + popID));
                    io.val(e.color.string());

                    io.on('change keyup', function () {
                        e.colorpicker.setValue(io.val());
                    });
                });

            $('.colour-picker-map').on('colorpickerHide', storeMapSettings);
            $('.map-settings-input').change(storeMapSettings);
            $('#markerFactor').on("input", function(slideEvt) {
                $("#markerFactorText").text(parseInt(slideEvt.target.value*100) + "%");
            });
            $('#mapSettingsForm').on('submit', function (e) {
                e.preventDefault();
                storeMapSettings();
            });
        });

        var storingMap = false;
        var storeMapNeeded = false;
        function storeMapSettings() {
            if(storingMap) {
                storeMapNeeded = true;
                return;
            }
            storingMap = true;
            axios.post('{{ route('user.saveMapSettings') }}', $('#mapSettingsForm').serialize())
                .then((response) => {
                    setTimeout(function() {
                        if(storeMapNeeded) {
                            storeMapNeeded = false
                            store();
                        }
                    }, 400);
                    storingMap = false;

                    var data = response.data;
                    createToast(data['msg'], "{{ __('ui.personalSettings.map') }}", "{{ __('global.now') }}");
                })
                .catch((error) => {
                    storingMap = false;
                    if(error.response.status == 422) {
                        var errors = error.response.data.errors;
                        $.each(errors, function(i, item) {
                            var data = item[0].charAt(0).toUpperCase()+item[0].substr(1)
                            createToast(data, "{{ __('ui.personalSettings.map') }}", "{{ __('global.now') }}");
                        });
                    }
                });
        }
    </script>
    <audio id="audio-elm">
        <source type="audio/mpeg">
        Your browser does not support the audio element.
    </audio>
    <script>
        $(document).on('submit', '#attackPlannerFileUpload', function (e) {
            e.preventDefault();
            let selectedFile = $('#attackPlannerFileUpload input[name=file]')[0].files[0]
            let formData = new FormData();
            formData.append("file", selectedFile);
            formData.append("name", $('#attackPlannerFileUpload input[name=name]').val());

            axios.post('{{ route("tools.attackPlannerSound.upload") }}', formData, {
                headers: {
                  "Content-Type": "multipart/form-data",
                }
            })
                .then((response) => {
                    window.location.reload();
                })
                .catch((error) => {
                    if(error.response.status == 422) {
                        var errors = error.response.data.errors;
                        $.each(errors, function(i, item) {
                            var data = item[0].charAt(0).toUpperCase()+item[0].substr(1)
                            createToast(data, "{{ __('ui.personalSettings.map') }}", "{{ __('global.now') }}");
                        });
                    }
                });
        });
        
        $(function() {
            $('#attackPlannerFileUpload input[name=file]').on('change', function() {
                var fName = $(this).val().split('\\').pop()
                fName = fName.substring(0, fName.lastIndexOf("."))
                $('#attackPlannerFileUpload input[name=name]').val(fName)
            })
            $('.sound-edit-name').on('click', function() {
                $(this).addClass("d-none")
                var inpWrapper = $('.name-edit-wrapper', $(this).parent())
                inpWrapper.removeClass('d-none')
                $('input', inpWrapper).val($('.name-show-wrapper', $(this)).text())
            })
            $('.name-edit-wrapper .fa-save').on('click', function() {
                var name = $('input', $(this).closest('.name-edit-wrapper')).val()
                var data = {
                    name: name
                }
                axios.post('{{ route("tools.attackPlannerSound.editName", ["%SOUND_ID%"]) }}'
                        .replace('%SOUND_ID%', $(this).closest('tr').attr("data-target")), data)
                    .then((response) => {
                        $(this).closest('.name-edit-wrapper').addClass("d-none")
                        var showWrapper = $('.sound-edit-name', $(this).closest('tr'))
                        showWrapper.removeClass('d-none')
                        $('.name-show-wrapper', showWrapper).html(name)
                    })
                    .catch((error) => {
                        if(error.response.status == 422) {
                            var errors = error.response.data.errors;
                            $.each(errors, function(i, item) {
                                var data = item[0].charAt(0).toUpperCase()+item[0].substr(1)
                                createToast(data, "{{ __('ui.personalSettings.map') }}", "{{ __('global.now') }}");
                            });
                        }
                    });
            })
            $('.sound-play').on('click', function() {
                var audio = $('#audio-elm')[0]
                var selected = '{{ route("tools.attackPlannerSound.fetch", ["%SOUND_ID%"]) }}'
                        .replace('%SOUND_ID%', $(this).closest('tr').attr("data-target"))
                if($('#audio-elm source')[0].src != selected) {
                    //changed source
                    $('#audio-elm source')[0].src = selected
                    audio.load()
                }
                audio.volume = 0.5
                audio.play()
                setTimeout(function () {
                    audio.pause()
                    audio.currentTime = 0
                }, 2000)
            })
            $('.sound-delete').on('click', function() {
                axios.post('{{ route("tools.attackPlannerSound.delete", ["%SOUND_ID%"]) }}'
                        .replace('%SOUND_ID%', $(this).closest('tr').attr("data-target")), [])
                .then((response) => {
                    $(this).closest('tr').remove()
                })
            })
        })
    </script>
@endpush
