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
        @if($mode == 'create')
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
                                        <input type="date" class="form-control form-control-sm" />
                                        <small class="form-control-feedback">Ankunftstag des Angriffes</small>
                                    </div>
                                </div>
                            </div>
                            <!--/span-->
                            <div class="col-md-4">
                                <div class="form-group row">
                                    <label class="control-label col-4">Uhrzeit</label>
                                    <div class="col-8">
                                        <input id="settime" type="time" step="1" class="form-control form-control-sm" />
                                        <small class="form-control-feedback">Ankunftszeit des Angriffes</small>
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
                                        <select id="unit" class="form-control form-control-sm">
                                            <option value="spear">{{ __('ui.unit.spear') }}</option>
                                            <option value="sword">{{ __('ui.unit.sword') }}</option>
                                            <option value="axe">{{ __('ui.unit.axe') }}</option>
                                            @if ($config->game->archer == 1)
                                                <option value="archer">{{ __('ui.unit.archer') }}</option>
                                            @endif
                                            <option value="spy">{{ __('ui.unit.spy') }}</option>
                                            <option value="light">{{ __('ui.unit.light') }}</option>
                                            @if ($config->game->archer == 1)
                                                <option value="marcher">{{ __('ui.unit.marcher') }}</option>
                                            @endif
                                            <option value="heavy">{{ __('ui.unit.heavy') }}</option>
                                            <option value="ram">{{ __('ui.unit.ram') }}</option>
                                            <option value="catapult">{{ __('ui.unit.catapult') }}</option>
                                            @if ($config->game->knight == 1)
                                                <option value="knight">{{ __('ui.unit.knight') }}</option>
                                            @endif
                                            <option value="snob">{{ __('ui.unit.snob') }}</option>
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
                                        <textarea class="form-control form-control-sm" style="height: 80px"></textarea>
                                    </div>
                                </div>
                            </div>
                            <!--/span-->
                        </div>
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
                                        <input id="link-create" type="text" class="form-control-plaintext form-control-sm disabled" value="{{ route('attackPlannerMode', [$worldData->server->code, $worldData->name, $attackList->id, 'create', $attackList->create_key]) }}" />
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
                                <th>Restzeit</th>
                                <th>&nbsp;</th>
                            </tr>
                        </thead>
                        <tbody class="small">
                            <tr>
                                <td><img id="type_img_list" src="{{ asset('images/ds_images/unit/unit_ram.png') }}"></td>
                                <td>#Mable Pines <b>490|531</b> [K45]</td>
                                <td>PureArroganz</td>
                                <td>#Shmebulock. <b>437|487</b> [K44]</td>
                                <td>PureArroganz</td>
                                <td><img id="unit_img_list" src="{{ asset('images/ds_images/unit/unit_ram.png') }}"></td>
                                <td>{{ $now }}</td>
                                <td>{{ $now }}</td>
                                <td data-countdown="2019/08/17">
                                </td>
                                <td>
                                    <button class="btn btn-link dropdown text-black-50 p-0" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        <i class="fas fa-ellipsis-v"></i>
                                    </button>
                                    <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownMenuButton">
                                        <a class="dropdown-item" data-toggle="modal" data-target="#exampleModal">{{ __('global.edit') }}</a>
                                        <form action="" method="POST" onsubmit="" style="display: inline-block;">
                                            <input type="submit" class="dropdown-item" style="width: 158px" value="{{ trans('global.delete') }}">
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td><img id="type_img_list" src="{{ asset('images/ds_images/unit/unit_ram.png') }}"></td>
                                <td>#Mable Pines <b>490|531</b> [K45]</td>
                                <td>PureArroganz</td>
                                <td>#Shmebulock. <b>437|487</b> [K44]</td>
                                <td>PureArroganz</td>
                                <td><img id="unit_img_list" src="{{ asset('images/ds_images/unit/unit_ram.png') }}"></td>
                                <td>{{ $now }}</td>
                                <td>{{ $now }}</td>
                                <td data-countdown="2019/08/18 14:06:00">
                                </td>
                                <td>
                                    <button class="btn btn-link dropdown text-black-50 p-0" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        <i class="fas fa-ellipsis-v"></i>
                                    </button>
                                    <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownMenuButton">
                                        <a class="dropdown-item" data-toggle="modal" data-target="#exampleModal">{{ __('global.edit') }}</a>
                                        <form action="" method="POST" onsubmit="" style="display: inline-block;">
                                            <input type="submit" class="dropdown-item" style="width: 158px" value="{{ trans('global.delete') }}">
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td><img id="type_img_list" src="{{ asset('images/ds_images/unit/unit_ram.png') }}"></td>
                                <td>#Mable Pines <b>490|531</b> [K45]</td>
                                <td>PureArroganz</td>
                                <td>#Shmebulock. <b>437|487</b> [K44]</td>
                                <td>PureArroganz</td>
                                <td><img id="unit_img_list" src="{{ asset('images/ds_images/unit/unit_ram.png') }}"></td>
                                <td>{{ $now }}</td>
                                <td>{{ $now }}</td>
                                <td data-countdown="2019/08/19">
                                </td>
                                <td>
                                    <button class="btn btn-link dropdown text-black-50 p-0" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        <i class="fas fa-ellipsis-v"></i>
                                    </button>
                                    <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownMenuButton">
                                        <a class="dropdown-item" data-toggle="modal" data-target="#exampleModal">{{ __('global.edit') }}</a>
                                        <form action="" method="POST" onsubmit="" style="display: inline-block;">
                                            <input type="submit" class="dropdown-item" style="width: 158px" value="{{ trans('global.delete') }}">
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td><img id="type_img_list" src="{{ asset('images/ds_images/unit/unit_ram.png') }}"></td>
                                <td>#Mable Pines <b>490|531</b> [K45]</td>
                                <td>PureArroganz</td>
                                <td>#Shmebulock. <b>437|487</b> [K44]</td>
                                <td>PureArroganz</td>
                                <td><img id="unit_img_list" src="{{ asset('images/ds_images/unit/unit_ram.png') }}"></td>
                                <td>{{ $now }}</td>
                                <td>{{ $now }}</td>
                                <td data-countdown="2019/08/20">
                                </td>
                                <td>
                                    <button class="btn btn-link dropdown text-black-50 p-0" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        <i class="fas fa-ellipsis-v"></i>
                                    </button>
                                    <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownMenuButton">
                                        <a class="dropdown-item" data-toggle="modal" data-target="#exampleModal">{{ __('global.edit') }}</a>
                                        <form action="" method="POST" onsubmit="" style="display: inline-block;">
                                            <input type="submit" class="dropdown-item" style="width: 158px" value="{{ trans('global.delete') }}">
                                        </form>
                                    </div>
                                </td>
                            </tr>
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
        function typ_img(input){
            switch (input) {
                case 0:
                    return '{{ asset('images/ds_images/unit/unit_ram.png') }}';
                case 1:
                    return '{{ asset('images/ds_images/unit/unit_snob.png') }}';
                case 2:
                    return '{{ asset('images/ds_images/unit/fake.png') }}';
                case 3:
                    return '{{ asset('images/ds_images/unit/wall.png') }}';
                case 4:
                    return '{{ asset('images/ds_images/unit/unit_spear.png') }}';
                case 5:
                    return '{{ asset('images/ds_images/unit/unit_sword.png') }}';
                case 6:
                    return '{{ asset('images/ds_images/unit/unit_heavy.png') }}';
                case 7:
                    return '{{ asset('images/ds_images/unit/def_fake.png') }}';
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

        String.prototype.trunc = String.prototype.trunc ||
            function(n){
                return (this.length > n) ? this.substr(0, n-1) + '&hellip;' : this;
            };

        $(document).ready(function (e) {
            $('#type').change(function (e) {
                var img = $('#type_img');
                var input = parseInt($(this).val());
                img.attr('src', typ_img(input));
            });

            $('#unit').change(function (e) {
                var img = $('#unit_img');
                var input = $(this).val();

                img.attr('src', '{{ asset('images/ds_images/unit/') }}/unit_' + input + '.png');
            });

            $('#data1').DataTable({
                dom: 't',
                ordering: false,
                paging: false,
                //responsive: true,

                keys: true, //enable KeyTable extension
                {!! \App\Util\Datatable::language() !!}
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

            // $('.koord').change(function (e) {
            //     console.log(e.target.val());
            // });

            $('.koord').change(function (e) {
                var input = $('#' + this.id).parent().attr('id');
                var type = input.substring(0, 1).toUpperCase() + input.substring(1);
                var x = $('#x' + type).val();
                var y = $('#y' + type).val();
                if (x != '' && y != ''){
                    village(x, y, type)
                }
            });

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

            function village(x, y, input) {
                axios.get('{{ route('index') }}/api/{{ $worldData->server->code }}/{{ $worldData->name }}/villageCoords/'+ x + '/' + y, {

                })
                    .then((response) =>{
                        const data = response.data.data;
                        $('#village' + input).html(data['name'].trunc(25) + ' <b>' + x + '|' + y + '</b>  [' + data['continent'] + ']');
                    })
                    .catch((error) =>{
                        $('#village' + input).html('{{ __('ui.villageNotExist') }}');
                    });
            }

        })
    </script>
@endsection
