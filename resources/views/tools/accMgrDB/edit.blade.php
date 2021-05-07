@extends('layouts.app')

@section('titel', __('tool.accMgrDB.title_edit').': '.$formEntries[0]['value'])

@push('style')
@endpush

@section('content')
    <div class="row justify-content-center">
        <!-- Titel für Tablet | PC -->
        <div class="col-12 p-lg-5 mx-auto my-1 text-center d-none d-lg-block">
            <div class="float-left mt-4">
                <a class="btn btn-primary" href="{{ route('tools.accMgrDB.index') }}">{{ __('global.back') }}</a>
            </div>
            <h1 class="font-weight-normal d-inline">{{ __('tool.accMgrDB.title_edit') }}</h1>

            <div class="float-right mt-4">
                <input id="export_template_data" class="d-none mr-2" type="text" readonly="readonly" value="{{ $export ?? "" }}">
                <a id="export_template" class="btn btn-primary{{ $export == null?" d-none":""}}"><i class="far fa-copy"></i> {{ __("tool.accMgrDB.export") }}</a>
            </div>
        </div>
        <!-- ENDE Titel für Tablet | PC -->
        <!-- Titel für Mobile Geräte -->
        <div class="mx-auto my-1 text-center d-lg-none truncate">
            <h1 class="font-weight-normal">
                {{ __('tool.accMgrDB.title_edit') }}
            </h1>
            <h4>
            </h4>
        </div>
        <!-- ENDE Titel für Mobile Geräte -->
    </div>
    <div class="col-12 mt-2">
        <div class="card">
            <div class="card-body">
                @if($id >= 0)
                <div class="col-12">
                    <div class="form-group row">
                        <label class="control-label col-6 col-md-3 col-lg-2">{{ ucfirst(__('tool.accMgrDB.showLink')) }}</label>
                        <div class="col-6 col-md-1">
                            <a class="float-right btn btn-primary btn-sm" onclick="copy('#link-show', false)">{{ ucfirst(__('tool.accMgrDB.copy')) }}</a>
                        </div>
                        <div class="col-md-8 col-lg-9">
                            <input id="link-show" type="text" class="form-control-plaintext form-control-sm disabled" value="{{ route('tools.accMgrDB.show_key', [$id, $showKey]) }}" />
                            <small class="form-control-feedback">{{ ucfirst(__('tool.accMgrDB.showLinkDesc')) }}</small>
                        </div>
                    </div>
                </div>
                @endif
                <form id="form_save_template" action="{{ route("tools.accMgrDB.save") }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method("POST")
                    <input id="id_input" type="hidden" name="id" value="{{ $id }}">
                    <x-edit_element :formEntry="$formEntries[0]"/>
                    <x-edit_element :formEntry="$formEntries[1]"/>

                    <table id="table_all" class="table table-borderless table-sm w-100"><tbody>
                        <tr>
                            @foreach(\App\Util\BuildingUtils::$BUILDINGS as $name=>$info)
                                <th><img src="{{ \App\Util\BuildingUtils::getImage($name) }}" data-toggle="tooltip" title="{{ __("ui.buildings.$name") }}"></th>
                            @endforeach
                        </tr>
                        <tr id="building_all_row">
                            {!! \App\Http\Controllers\Tools\AccMgrDB::createAllBuildingsTable($result) !!}
                        </tr>
                    </tbody></table>
                    <table id="table_detailed" class="table table-hover table-sm w-100">
                        <tbody class="droptrue-list">
                            @foreach($buildings as $build)
                                {!! \App\Http\Controllers\Tools\AccMgrDB::createBuildingRow($build['name'], $build['diff'], $build['result'], $build['farm'], $build['points']) !!}
                            @endforeach
                        </tbody>
                    </table>

                    @for($i = 2; $i < count($formEntries); $i++)
                        <x-edit_element :formEntry="$formEntries[$i]"/>
                    @endfor
                    <div>
                        <input class="btn btn-danger" type="submit" value="{{ __('global.save') }}">
                    </div>
                </form>
                <form id="add-building-form" class="form-inline w-100 mt-3">
                    <div>{{ __('tool.accMgrDB.building') }}</div>
                    <select name="name" class="form-control select2-sel">
                        @foreach(\App\Util\BuildingUtils::$BUILDINGS as $name=>$options)
                            <option value="{{ $name }}">{{ __("ui.buildings.$name") }}</option>
                        @endforeach
                    </select>
                    <input name="amount" type="number" class="form-control form-control-sm" min="1" max="30" value="1">
                    <input type="submit" class="btn btn-primary ml-auto float-right" value="{{ __('global.add') }}">
                </form>
            </div>
        </div>
    </div>
@endsection

@push('style')
    <link href="{{ asset('plugin/select2/select2.min.css') }}" rel="stylesheet" />
    <link href="{{ asset('plugin/select2/select2-bootstrap4.min.css') }}" rel="stylesheet" />
    <style>
        #add-building-form > * {
            margin-right : 1rem;
        }
    </style>
@endpush

@push('js')
<script src="{{ asset('plugin/select2/select2.full.min.js') }}"></script>
<script>
    var currentLevel = {
        @foreach(\App\Util\BuildingUtils::$BUILDINGS as $name=>$info)
            '{{$name}}': {{ $result[$name] ?? 0 }},
        @endforeach
    };

    $(function() {
        $.each($('.droptrue-list').get(), function (k,v) {
            sortable.Sortable.create(v, {
                group: 'lists',
                animation: 300,
                handle: '.handle', // handle's class
                @if($id != -1)
                onEnd: function (e) {
                    save(false);
                },
                @endif
            });
        });

        $("input[name=remove_additional]").change(function() {
            if(this.checked) {
                $("input[name^='removeIgno_']").parent().show();
            } else {
                $("input[name^='removeIgno_']").parent().hide();
            }
        });
        $('input[name=remove_additional]').trigger('change');
        hotbarEvents();

        $(window).bind('beforeunload', function(){
            if ($('#id_input').val() == -1){
                return '{{ __('tool.accMgrDB.leaveMessage') }}';
            }
        });

        $('#add-building-form').submit(function(e) {
            e.preventDefault();
            var data = new FormData(this);
            $('#table_detailed tbody').append('<tr build_name="' +
                    data.get('name') + '" amount="' + data.get('amount') + '"></tr>');
            save(false);
        });

        $('#form_save_template').submit(function(e) {
            e.preventDefault();
            save(true);
        });

        $('#export_template_data').removeClass('d-none').hide();
        $('#export_template').click(function () {
            copy('#export_template_data');
            createToast("{{ __('tool.accMgrDB.export_success') }}", "{{ __('tool.accMgrDB.export') }}", 'now', 'fas fa-check');
        });

        addEventsForRemoveButton();

        $('.select2-sel').select2();
        
        $('#public').change(saveFalse);
        $('#remove_additional').change(saveFalse);
        $('#removeIgno_church').change(saveFalse);
        $('#removeIgno_church_f').change(saveFalse);
        $('#removeIgno_watchtower').change(saveFalse);
        
        var timeTitle = -1;
        $('#name').on('input', function(e) {
            if(timeTitle != -1) {
                clearTimeout(timeTitle);
            }
            timeTitle = setTimeout(function() {
                saveFalse(e);
                timeTitle = -1;
            }, 500);
        })
    })
    
    
    function hotbarEvents() {
        $('.buildHotbar').click(function() {
            $('#table_detailed tbody').append('<tr build_name="' +
                    this.id.replace('building_', '') + '" amount="1"></tr>');
            save(false);
        });
    }
    
    function saveFalse(e) {
        save(false);
    }

    function addEventsForRemoveButton() {
        $('.build-remove').click(function (e) {
            $(this).parent().parent().remove();
            save(false);
        });
    }

    function copy(selector, hideShow=true) {
        /* Get the text field */
        var copyText = $(selector);
        if(hideShow) copyText.show();
        /* Select the text field */
        copyText.select();
        /* Copy the text inside the text field */
        document.execCommand('copy');
        if(hideShow) copyText.hide();
    }
    
    var storing = false;
    var storeNeeded = false;
    var forceNext = false;
    function save(forceSave) {
        if(storing) {
            storeNeeded = true;
            forceNext = forceNext || forceSave;
            return;
        }
        storing = true;
        
        var data = $('#form_save_template').serialize();
        var raw = $('#table_detailed tbody tr');

        var buildings = [
            {
                'name': 'forceSave',
                'value': (forceSave) ? 1:0,
            }
        ];
        for(var i = 0; i < raw.length; i++) {
            buildings[buildings.length] = {
                'name': 'buildings['+i+'][build_name]',
                'value': $(raw[i]).attr('build_name'),
            }
            buildings[buildings.length] = {
                'name': 'buildings['+i+'][amount]',
                'value': $(raw[i]).attr('amount'),
            }
        }
        data += '&' + $.param(buildings);
        
        axios.post($('#form_save_template').attr('action'), data)
            .then((response) => {
                var dat = response.data;
                var oldId = $('#id_input').val();
                currentLevel = dat.buildings;
                $('#table_detailed tbody').html(dat.html);
                $('#id_input').val(dat.id);
                $('#export_template').removeClass('d-none');
                $('#export_template_data').val(dat.export);
                $('#building_all_row').html(dat.table_html)
                addEventsForRemoveButton();
                hotbarEvents();

                if(data.saved) {
                    createToast("{{ __('tool.accMgrDB.save_success') }}", "{{ __('global.save') }}", 'now', 'fas fa-check');
                }
                
                if(forceSave) {
                    window.location.href = "{{ route('tools.accMgrDB.index') }}";
                }
                
                setTimeout(function() {
                    storing = false;
                    if(storeNeeded) {
                        storeNeeded = false;
                        var nextForc = forceNext;
                        forceNext = false;
                        save(nextForc);
                    }
                }, 400);
            })
            .catch((error) => {
                createToast("{{ __("tool.accMgrDB.save_error") }}", "{{ __('global.save') }}", 'now', 'fas fa-exclamation-circle text-danger');
                setTimeout(function() {
                    storing = false;
                    if(storeNeeded) {
                        storeNeeded = false;
                        var nextForc = forceNext;
                        forceNext = false;
                        save(nextForc);
                    }
                }, 400);
            });
    }
</script>
@endpush
