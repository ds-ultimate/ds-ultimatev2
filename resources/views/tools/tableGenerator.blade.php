@extends('layouts.app')

@section('titel', __('tool.tableGenerator.title'))

@section('content')
    <div class="row justify-content-center">
        <!-- Titel für Tablet | PC -->
        <div class="col-12 p-lg-5 mx-auto my-1 text-center d-none d-lg-block">
            <h1 class="font-weight-normal">{{ ucfirst(__('tool.tableGenerator.title')) }}</h1>
        </div>
        <!-- ENDE Titel für Tablet | PC -->
        <!-- Titel für Mobile Geräte -->
        <div class="p-lg-5 mx-auto my-1 text-center d-lg-none truncate">
            <h1 class="font-weight-normal">
                {{ ucfirst(__('tool.tableGenerator.title')).' ' }}
            </h1>
        </div>
        <!-- ENDE Titel für Mobile Geräte -->
        <!-- Village Card -->
        <div class="col-12 col-md-6 mt-2">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">{{ __('ui.tabletitel.general') }}:</h4>
                    <form id="table-form">
                        <div class="form-group row">
                            <label for="inputEmail3" class="col-sm-4 col-form-label"><b>{{ __('admin.worlds.update') }}</b></label>
                            <div class="col-sm-8">
                                <label class="col-form-label" for="autoSizingCheck">
                                    {{ $worldData->worldUpdated_at . ' (' . $worldData->worldUpdated_at->diffForHumans() . ')' }}
                                </label>
                            </div>
                        </div>
                        <hr>
                        <fieldset class="form-group">
                            <div class="row">
                                <legend class="col-form-label col-sm-4 pt-0"><b>{{ __('tool.attackPlanner.type') }}</b></legend>
                                <div class="col-sm-8">
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="typRadio" id="typRadio1" value="playerByAlly" data-input="ally" checked>
                                        <label class="form-check-label" for="typRadio1">
                                            {{ __('tool.tableGenerator.playerByAlly') }}
                                        </label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="typRadio" id="typRadio2" value="villageByPlayer" data-input="player">
                                        <label class="form-check-label" for="typRadio2">
                                            {{ __('tool.tableGenerator.villageByPlayer') }}
                                        </label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="typRadio" id="typRadio3" value="villageByAlly" data-input="ally">
                                        <label class="form-check-label" for="typRadio3">
                                            {{ __('tool.tableGenerator.villageByAlly') }}
                                        </label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="typRadio" id="typRadio4" value="villageAndPlayerByAlly" data-input="ally">
                                        <label class="form-check-label" for="typRadio4">
                                            {{ __('tool.tableGenerator.villageAndPlayerByAlly') }}
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </fieldset>
                        <hr>
                        <fieldset class="form-group">
                            <div class="row">
                                <legend class="col-form-label col-sm-4 pt-0"><b>{{__('ui.sorting')}}</b></legend>
                                <div class="col-sm-8">
                                    <select id="sorting" name="sorting" class="form-control">
                                        <option value="points" selected>{{ ucfirst(__('ui.table.points')) }}</option>
                                        <option value="name">{{ ucfirst(__('ui.table.name')) }}</option>
                                    </select>
                                </div>
                            </div>
                        </fieldset>
                        <hr>
                        <fieldset class="form-group">
                            <div class="row">
                                <legend class="col-form-label col-sm-4 pt-0"><b>{{__('ui.columns')}}</b></legend>
                                <div class="col-sm-8">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="number" id="number" value="number">
                                        <label class="form-check-label" for="number">
                                            {{__('ui.numberLines')}}
                                        </label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="name" id="name" value="name" checked disabled>
                                        <label class="form-check-label" for="name">
                                            {{ ucfirst(__('ui.table.name')) }}
                                        </label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="points" id="points" value="points">
                                        <label class="form-check-label" for="points">
                                            {{__('ui.showPoints')}}
                                        </label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="showPointDiff" id="showPointDiff" value="showPointDiff">
                                        <label class="form-check-label" for="showPointDiff">
                                            {{__('ui.showPointDiff')}}
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </fieldset>
                        <hr>
                        <div class="form-group row">
                            <label for="inputState" class="col-sm-4 col-form-label"><b>{{__('ui.additionalColumns')}}</b></label>
                            <div class="col-sm-8">
                                <select id="columns" name="columns" class="form-control">
                                    <option value="0" selected>0</option>
                                    <option value="1">1</option>
                                    <option value="2">2</option>
                                    <option value="3">3</option>
                                    <option value="4">4</option>
                                    <option value="5">5</option>
                                </select>
                            </div>
                        </div>
                        <hr>
                        <div class="form-group row">
                            <label class="col-sm-4 col-form-label" for="player"><b>{{__('ui.selection')}}</b></label>
                            <div class="col-sm-8">
                                <select id="ally" name="selectType" class="form-control mr-1 data-input-map select2-container--classic select2-single" style="min-width: 200px">
                                    <option></option>
                                </select>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <!-- ENDE Village Card -->
        <!-- Unit Card -->
        <div class="col-12 col-md-6 mt-2">
            <div id="output-card" class="card">
                <div id="output-body" class="card-body">
                    <textarea id="tableOutput" class="form-control h-100"></textarea>
                </div>
            </div>
        </div>
        <!-- ENDE Unit Card -->
    </div>
@endsection

@push('js')
    <script src="{{ asset('plugin/select2/select2.full.min.js') }}"></script>
    <script>
        $("#ally").select2({
            ajax: {
                url: '{{ route('index') }}/api/{{ $worldData->server->code }}/{{ $worldData->name }}/select2Ally',
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
            placeholder: '{{ ucfirst(__('tool.map.allySelectPlaceholder')) }}',
            theme: "bootstrap4"
        });

        $(document).on('change', 'input', function () {
            if($(this).attr('type') != 'radio'){
                $('#table-form').submit();
            }
        });

        $(document).on('change', "select", function () {
            $('#table-form').submit();
        })

        $(document).on("change", 'input[type=radio]', function (e) {
            var data = $(this).data('input');
            var select = $("select[name='selectType']");
            if($(this).val() == 'villageByAlly'){
                $('#sorting').prop("disabled", true)
            }else{
                $('#sorting').prop("disabled", false)
            }
            if($(this).val() == 'playerByAlly'){
                $('#showPointDiff').prop("disabled", false)
            }else{
                $('#showPointDiff').prop("disabled", true)
            }
            select.attr('id', data).val(null);
            $('#tableOutput').val('');
            select.select2("destroy");
            select.select2({
                ajax: {
                    url: '{{ route('index') }}/api/{{ $worldData->server->code }}/{{ $worldData->name }}/select2' + data.substr(0,1).toUpperCase()+data.substr(1),
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
                placeholder: (data === 'player')?'{{ ucfirst(__('tool.map.playerSelectPlaceholder')) }}':'{{ ucfirst(__('tool.map.allySelectPlaceholder')) }}',
                theme: "bootstrap4"
            });
        });

        $(document).on('submit', '#table-form', function (e) {
            e.preventDefault();
            if ($('select[name=selectType]').val().length > 0){
                axios.post('{{ route('tools.tableGeneratorData') }}', {
                    'world': '{{ $worldData->id }}',
                    'type': $('input[name=typRadio]:checked').val(),
                    'sorting': $('select[name=sorting]').val(),
                    'number': $('#number').is(":checked")? true : false,
                    'name': $('#name').is(":checked")? true : false,
                    'points': $('#points').is(":checked")? true : false,
                    'history': $('#history').is(":checked")? true : false,
                    'showPointDiff': $('#showPointDiff').is(":checked")? true : false,
                    'columns': $('#columns').val(),
                    'selectType': $('select[name=selectType]').val(),
                })
                    .then((response) => {
                        var data = response.data;
                        if (data.length > 1){
                            var output = '{!! __('tool.tableGenerator.maxSing') !!}';
                            $.each(data, function (index, value) {
                                output += "<div class=\"input-group mb-2\">\n" +
                                    "<input id=\"copyInput"+ index +"\" name=\"copyInput\" type=\"text\" class=\"form-control\" value=\""+ value +"\" aria-describedby=\"basic-addon2\">\n" +
                                    "<div class=\"input-group-append\">\n" +
                                    "<span class=\"input-group-text\" style=\"cursor:pointer\" id=\"basic-addon2\" onclick=\"copy('copyInput"+ index +"')\"><i class=\"far fa-copy\"></i></span>\n" +
                                    "</div>\n" +
                                    "</div>";
                            });
                            $('#output-card').attr('style', '');
                            $('#output-body').html(output);
                        }else if(data.length != 0){
                            $('#output-card').attr('style', 'height: 500px');
                            $('#output-body').html("<textarea id=\"tableOutput\" class=\"form-control h-100\">" + data[0] + "</textarea>");
                        }else{
                            $('#output-body').html("{{ __('ui.old.nodata') }}");
                        }
                    })
                    .catch((error) => {
                        $('#output-body').html("{{ __('ui.mistake') }}");
                    });
            }else{
                $('#output-card').attr('style', 'height: 500px');
                $('#output-body').html("<textarea id=\"tableOutput\" class=\"form-control h-100\"></textarea>");
            }

        });

        function copy(type) {
            /* Get the text field */
            var copyText = $("#" + type);
            /* Select the text field */
            copyText.select();
            /* Copy the text inside the text field */
            document.execCommand("copy");
        }

    </script>
@endpush
