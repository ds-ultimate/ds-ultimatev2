@extends('layouts.temp')

@section('titel', ucfirst(__('ui.titel.settings')).' von '.Auth::user()->name)

@section('style')
    <link href="{{ asset('plugin/select2/select2.min.css') }}" rel="stylesheet" />
    <link href="{{ asset('plugin/select2/select2-bootstrap4.min.css') }}" rel="stylesheet" />
@stop

@section('content')
    <div id="toast-content" style="position: absolute; top: 60px; right: 10px; z-index: 100;">

    </div>
    <div class="row justify-content-center">
        <!-- Titel für Tablet | PC -->
        <div class="p-lg-5 mx-auto my-1 text-center d-none d-lg-block">
            <h1 class="font-weight-normal">{{ ucfirst(__('ui.titel.settings')).' von '.Auth::user()->name }}</h1>
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
            <div class="col-3">
                <div class="card">
                    <div class="card-header">
                        {{ __('ui.personalSettings.title') }}
                    </div>
                    <div class="card-body">
                        <div class="nav flex-column nav-pills" id="settings-tab" role="tablist" aria-orientation="vertical">
                            <a class="nav-link {{ ($page == 'settings-profile')? 'active' : '' }}" id="settings-profile-tab" data-toggle="pill" href="#settings-profile" role="tab" aria-controls="settings-profile" aria-selected="true">{{ __('ui.personalSettings.profile') }}</a>
                            <a class="nav-link {{ ($page == 'settings-account')? 'active' : '' }}" id="settings-account-tab" data-toggle="pill" href="#settings-account" role="tab" aria-controls="settings-account" aria-selected="false">{{ __('ui.personalSettings.account') }}</a>
                            <a class="nav-link {{ ($page == 'settings-connection')? 'active' : '' }}" id="settings-connection-tab" data-toggle="pill" href="#settings-connection" role="tab" aria-controls="settings-connection" aria-selected="false">{{ __('ui.personalSettings.connection') }}</a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-9">
                <div class="card">
                    <div class="card-header">
                        <h5 id="settings-card-title" class="card-title">{{ __('ui.personalSettings') }}</h5>
                    </div>
                    <div class="card-body">
                        <div class="tab-content" id="settings-tabContent">
                            <div class="tab-pane fade {{ ($page == 'settings-profile')? 'show active' : '' }}" id="settings-profile" role="tabpanel" aria-labelledby="settings-profile-tab" data-title="{{ __('ui.personalSettings.profile') }}">
                                {{ __('ui.personalSettings.profile') }}
                                <div class="text-center">
                                    <div class="btn-group position-absolute p-1" role="group">
                                        <button id="btnGroupDrop1" type="button" class="btn btn-dark dropdown-toggle btn-sm" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                            <i class="fas fa-user-edit"></i>
                                        </button>
                                        <div class="dropdown-menu" aria-labelledby="btnGroupDrop1">
                                            <a class="dropdown-item" href="#">
                                                {{ __('ui.personalSettings.uploadeImage') }}
                                                <input id="imgUpload" type="file" name="file" style="position: absolute;font-size: 20px;opacity: 0;right: 0;top: 0; width: 265px"/>
                                            </a>
                                            <a class="dropdown-item" href="#" onclick="destroy()">
                                                {{ __('ui.personalSettings.deleteImage') }}
                                            </a>
                                        </div>
                                    </div>
                                    <img id="avatarImage" src="{{ Auth::user()->avatarPath() }}" class="rounded img-thumbnail" alt="" style="max-width: 200px; max-height: 200px">
                                    <div id="avatar-errors" class="text-danger"></div>
                                </div>
                                <form>
                                    <div class="form-group">
                                        <label for="name">{{ __('user.name') }}</label>
                                        <div class="input-group">
                                            <input type="text" class="form-control" id="name" placeholder="{{ Auth::user()->name }}" readonly>
                                            <div class="input-group-append">
                                                <span class="input-group-text text-danger" id="basic-addon2">{{ __('ui.personalSettings.workInProgress') }}</span>
                                            </div>
                                            <div id="name-errors" class="text-danger"></div>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label for="name">{{ __('user.mailAddress') }}</label>
                                        <div class="input-group">
                                            <input type="email" class="form-control" id="email" placeholder="{{ Auth::user()->email }}" aria-describedby="basic-addon2" readonly>
                                            <div class="input-group-append">
                                                <span class="input-group-text text-danger" id="basic-addon2">{{ __('ui.personalSettings.workInProgress') }}</span>
                                            </div>
                                            <div id="email-errors" class="text-danger"></div>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label for="birthday">{{ __('ui.personalSettings.birthday') }}</label>
                                        <input id="date_picker" name="birthday" class="form-control" type="text">
                                        <div id="birthday-errors" class="text-danger"></div>
                                    </div>
                                </form>
                            </div>
                            <div class="tab-pane fade {{ ($page == 'settings-account')? 'show active' : '' }}" id="settings-account" role="tabpanel" aria-labelledby="settings-account-tab" data-title="{{ __('ui.personalSettings.account') }}">
                                <form id="settings-account-form">
                                    <div class="form-group">
                                        <label for="skype">{{ __('ui.personalSettings.skype') }} <i class="fab fa-skype h3" style="color: #00AFF0;"></i></label>
                                        <input type="text" class="form-control" id="skype_name" placeholder="SkypeName" value="{{ Auth::user()->profile->skype }}">
                                        <div id="skype-errors" class="text-danger"></div>
                                    </div>
                                    <div class="form-group">
                                        <label for="discord">{{ __('ui.personalSettings.discord') }} <i class="fab fa-discord h3" style="color: #738ADB;"></i></label>
                                        <input type="text" class="form-control" id="discord_name" placeholder="DiscordName#1234" value="{{ Auth::user()->profile->discord }}">
                                        <div id="discord-errors" class="text-danger"></div>
                                    </div>
                                    <button type="submit" class="btn btn-primary float-right">{{ __('global.save') }}</button>
                                </form>
                            </div>
                            <div class="tab-pane fade {{ ($page == 'settings-connection')? 'show active' : '' }}" id="settings-connection" role="tabpanel" aria-labelledby="settings-connection-tab" data-title="{{ __('ui.personalSettings.connection') }}">
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
                                            <th style="max-width: 50px; min-width: 50px">{{ ucfirst(__('key')) }}</th>
                                            <th>&nbsp;</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
    </div>
@endsection

@section('js')
    <script src="{{ asset('plugin/select2/select2.full.min.js') }}"></script>
    <script src="{{ asset('plugin/bootstrap-datepicker/bootstrap-datepicker.min.js') }}"></script>
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

        $(document).ready(function () {
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
                axios.get('{{ route('index') }}/api/' + e.params.data.text.toLowerCase() + '/activeWorlds')
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
                        console.log(error);
                    });
            });

            $('#world').on('select2:select', function (e) {
                var server = $('#server').find(':selected');
                var world = $('#world').find(':selected');
                playerTable.val(null).trigger('change');
                playerTable.select2({
                    ajax: {
                        url: '{{ route('index') }}/api/' + server[0].text.toLowerCase() + '/' + world[0].value + '/searchPlayer',
                        data: function (params) {
                            var query = {
                                search: params.term,
                                type: 'public'
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

        $('.nav-link').on("click", function (e) {
            var href = $(this).attr("href");
            history.pushState(null, null, href.replace('#', '/user/settings/'));
            e.preventDefault();
        });

        })

        $('a[data-toggle="pill"]').on('shown.bs.tab', function (e) {
            $('#settings-card-title').html($($(this).attr('href')).data('title'))
        });

        $(document).on('change','#imgUpload' , function(){
            const file = this.files[0];

            var formData = new FormData();
            formData.append("file", file);
            axios.post('{{ route('user.uploadeImage') }}', formData, {
                headers: {
                    'Content-Type': 'multipart/form-data'
                }
            })
                .then((response) => {
                    $('#avatarImage').attr('src', '/storage/app/' + response.data.img);
                    $('#avatar-errors').html('');
                })
                .catch((error) => {
                    var errors = error.response.data.errors['file'];
                    $('#avatar-errors').html(errors[0].replace('file', '{{ __('global.file') }}'));
                });
        });

        function destroy() {
            axios.post('{{ route('user.destroyImage') }}')
                .then((response) => {
                    $('#avatarImage').attr('src', '{{ asset('images/default/user.png') }}')
                })
                .catch((error) => {
                });
        }

        $(document).on('submit', '#connectionForm', function (e) {
            e.preventDefault();
            axios.post('{{ route('user.addConnection') }}', {
                'server': $('#server').val(),
                'world': $('#world').val(),
                'player': $('#player').val(),
            })
                .then((response) => {
                    connectionTable.ajax.reload();
                })
                .catch((error) => {
                });
        })

        function checkConnection(id) {
            axios.post('{{ route('user.DsConnection') }}',{
                'id': id,
            })
                .then((response) => {
                    var data = response.data;
                    createToast(data);
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
                    createToast(data);
                    connectionTable.ajax.reload();
                })
                .catch((error) => {
                });
        }

        function createToast(data) {
            var int = Math.floor((Math.random() * 1000) + 1);
            $('#toast-content').append('<div class="toast toast'+int+'" role="alert" aria-live="assertive" aria-atomic="true" data-delay="5000">\n' +
                '            <div class="toast-header">\n' +
                '                <div class="mr-2"><i class="fas fa-sync"></i></div>\n' +
                '                <strong class="mr-auto">{{ __('ui.personalSettings.connection') }}</strong>\n' +
                '                <small class="text-muted">{{__('global.now')}}</small>\n' +
                '                <button type="button" class="ml-2 mb-1 close" data-dismiss="toast" aria-label="Close">\n' +
                '                    <span aria-hidden="true">&times;</span>\n' +
                '                </button>\n' +
                '            </div>\n' +
                '            <div class="toast-body">\n' +
                data['msg'] +
                '            </div>\n' +
                '        </div>');
            $('.toast'+int).toast('show');
        }

        $(document).on('submit', '#settings-account-form', function (e) {
            e.preventDefault();
            axios.post('{{ route('user.saveSettingsAccount') }}',{
                'discord': $('#discord_name').val(),
                'skype': $('#skype_name').val(),
            })
                .then((response) => {
                    var data = response.data;
                    createToast(data);
                    connectionTable.ajax.reload();
                })
                .catch((error) => {
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
@stop
