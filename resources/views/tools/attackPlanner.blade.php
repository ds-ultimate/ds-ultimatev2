@extends('layouts.app')

@section('titel', $worldData->displayName(),': '.__('tool.attackPlanner.title'))

@section('style')
    <link href="{{ asset('plugin/jquery-ui/jquery-ui.min.css') }}" rel="stylesheet">
    <style>
        table.dataTable thead .sorting:before,table.dataTable thead .sorting:after,table.dataTable thead .sorting_asc:before,table.dataTable thead .sorting_asc:after,table.dataTable thead .sorting_desc:before,table.dataTable thead .sorting_desc:after,table.dataTable thead .sorting_asc_disabled:before,table.dataTable thead .sorting_asc_disabled:after,table.dataTable thead .sorting_desc_disabled:before,table.dataTable thead .sorting_desc_disabled:after{position:absolute;bottom:0.3em;display:block;opacity:0.3}
        table.dataTable thead .sorting_asc:before,table.dataTable thead .sorting_desc:after{opacity:1}table.dataTable thead .sorting_asc_disabled:before,table.dataTable thead .sorting_desc_disabled:after{opacity:0}
        table.dataTable tbody tr.selected a, table.dataTable tbody th.selected a, table.dataTable tbody td.selected a {color: #7d510f;}
        table.dataTable tbody tr.selected, table.dataTable tbody th.selected, table.dataTable tbody td.selected {color: #212529;}
        table.dataTable tbody>tr.selected, table.dataTable tbody>tr>.selected {background-color: rgba(237, 212, 146, 0.4);}
        /*.even.selected td {*/
        /*    background-color: rgba(238, 223, 193, 0); !important; !* Add !important to make sure override datables base styles *!*/
        /*}*/

        /*.odd.selected td {*/
        /*    background-color: #edd492; !important; !* Add !important to make sure override datables base styles *!*/
        /*}*/
    </style>
@stop

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
                    <li class="nav-item">
                        <a class="nav-link active" id="create-tab" data-toggle="tab" href="#create" role="tab" aria-controls="create" aria-selected="true">{{ __('global.edit') }}</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="link-tab" data-toggle="tab" href="#link" role="tab" aria-controls="link" aria-selected="false">{{ __('tool.attackPlanner.links') }}</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="import-tab" data-toggle="tab" href="#import" role="tab" aria-controls="import" aria-selected="false">{{ __('tool.attackPlanner.importExport') }}</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="stats-tab" data-toggle="tab" href="#stats" role="tab" aria-controls="stats" aria-selected="false">{{ __('tool.attackPlanner.statistics') }}</a>
                    </li>
                </ul>
                <div class="card-body tab-content">
                    <div class="tab-pane fade show active" id="create" role="tabpanel" aria-labelledby="create-tab">
                        <form id="createItemForm">
                            <div class="row">
                                <div class="col-12 text-center">
                                    <b id="title-show" class="h3 card-title">{{ ($attackList->title === null)? __('ui.noTitle'): $attackList->title }}</b>
                                    <input id="title-input" onfocus="this.select();" class="form-control mb-3" style="display:none" name="title" type="text">
                                    <a id="title-edit" onclick="titleEdit()" style="cursor:pointer;"><i class="far fa-edit text-muted h5 ml-2"></i></a>
                                    <a id="title-save" onclick="titleSave()" style="cursor:pointer; display:none"><i class="far fa-save text-muted h5 ml-2"></i></a>
                                    <hr>
                                </div>
                                <div class="col-md-4">
                                    <div class="input-group input-group-sm mb-3">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">{{ __('tool.attackPlanner.type') }}</span>
                                            <span class="input-group-text"><img id="type_img" src="{{ \App\Util\Icon::icons(8) }}"></span>
                                        </div>
                                        <select id="type" class="custom-select type" data-toggle="tooltip" data-placement="top" title="{{ __('tool.attackPlanner.type_helper') }}">
                                            <optgroup label="{{ __('tool.attackPlanner.offensive') }}">
                                                <option value="8">{{ __('tool.attackPlanner.attack') }}</option>
                                                <option value="11">{{ __('tool.attackPlanner.conquest') }}</option>
                                                <option value="14">{{ __('tool.attackPlanner.fake') }}</option>
                                                <option value="45">{{ __('tool.attackPlanner.wallbreaker') }}</option>
                                            </optgroup>
                                            <optgroup label="{{ __('tool.attackPlanner.defensive') }}">
                                                <option value="0">{{ __('tool.attackPlanner.support') }}</option>
                                                <option value="1">{{ __('tool.attackPlanner.standSupport') }}</option>
                                                <option value="7">{{ __('tool.attackPlanner.fastSupport') }}</option>
                                                <option value="46">{{ __('tool.attackPlanner.fakeSupport') }}</option>
                                            </optgroup>
                                        </select>
                                    </div>
                                </div>
                                <!--/span-->
                                <div class="col-md-4">
                                    <div class="input-group input-group-sm mb-3">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">{{ __('tool.attackPlanner.startVillage') }}</span>
                                        </div>
                                        <input id="xStart" class="form-control mx-auto col-5 koord xStart" type="text" placeholder="500" maxlength="3" />
                                        <div class="input-group-append input-group-prepend">
                                            <span class="input-group-text">|</span>
                                        </div>
                                        <input id="yStart" class="form-control mx-auto col-5 koord yStart" type="text" placeholder="500" maxlength="3" />
                                    </div>
                                </div>
                                <!--/span-->
                                <div class="col-md-4">
                                    <div class="input-group input-group-sm mb-3">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">{{ __('tool.attackPlanner.targetVillage') }}</span>
                                        </div>
                                        <input id="xTarget" class="form-control mx-auto col-5 koord xTarget" type="text" placeholder="500" maxlength="3" />
                                        <div class="input-group-append input-group-prepend">
                                            <span class="input-group-text">|</span>
                                        </div>
                                        <input id="yTarget" class="form-control mx-auto col-5 koord yTarget" type="text" placeholder="500" maxlength="3" />
                                    </div>
                                </div>
                                <!--/span-->
                                <div class="col-md-4">
                                    <div class="input-group input-group-sm mb-3">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">{{ __('tool.attackPlanner.date') }}</span>
                                        </div>
                                        <input id="day" type="date" class="form-control form-control-sm day" value="{{ date('Y-m-d', time()) }}" data-toggle="tooltip" data-placement="top" title="{{ __('tool.attackPlanner.date_helper') }}" />
                                    </div>
                                </div>
                                <!--/span-->
                                <div class="col-md-4">
                                    <div class="input-group input-group-sm mb-3">
                                        <div class="input-group-prepend">
                                            <button id="time_title" type="button" class="btn input-group-text dropdown-toggle dropdown-toggle-split" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                {{ __('tool.attackPlanner.arrivalTime') }} <span class="sr-only">Toggle Dropdown</span>
                                            </button>
                                            <div class="dropdown-menu">
                                                <a class="dropdown-item" onclick="changeTime(0)">{{ __('tool.attackPlanner.arrivalTime') }}</a>
                                                <a class="dropdown-item" onclick="changeTime(1)">{{ __('tool.attackPlanner.sendTime') }}</a>
                                            </div>
                                        </div>
                                        <input id="time" type="time" step="0.001" class="form-control form-control-sm time" value="{{ date('H:i:s', time()+3600) }}" data-toggle="tooltip" data-placement="top" title="{{ __('tool.attackPlanner.time_helper') }}" />
                                        <input id="time_type" type="hidden" value="0">
                                    </div>
                                </div>
                                <!--/span-->
                                <div class="col-md-4">
                                    <div class="input-group input-group-sm mb-3">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">{{ __('global.unit') }}</span>
                                            <span class="input-group-text"><img id="unit_img" src="{{ \App\Util\Icon::icons(0) }}"></span>
                                        </div>
                                        <select id="slowest_unit" class="form-control form-control-sm slowest_unit" data-toggle="tooltip" data-placement="top" title="{{ __('tool.attackPlanner.unit_helper') }}">
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
                                            @if ($config->game->knight > 0)
                                                <option value="10">{{ __('ui.unit.knight') }}</option>
                                            @endif
                                            <option value="11">{{ __('ui.unit.snob') }}</option>
                                        </select>
                                    </div>
                                </div>
                                <!--/span-->
                                <div class="col-12">
                                    <div class="form-inline row">
                                        <div class="input-group col-2 input-group-sm mb-3">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text" id="inputGroup-sizing-sm"><img id="unit_spear" class="pr-2" src="{{ \App\Util\Icon::icons(0) }}"></span>
                                            </div>
                                            <input id="spear" name="spear" class="form-control form-control-sm col-9" type="number">
                                        </div>
                                        <div class="input-group col-2 input-group-sm mb-3">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text" id="inputGroup-sizing-sm"><img id="unit_sword" class="pr-2" src="{{ \App\Util\Icon::icons(1) }}"></span>
                                            </div>
                                            <input id="sword" name="sword" class="form-control form-control-sm col-9" type="number">
                                        </div>
                                        <div class="input-group col-2 input-group-sm mb-3">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text" id="inputGroup-sizing-sm"><img id="unit_axe" class="pr-2" src="{{ \App\Util\Icon::icons(2) }}"></span>
                                            </div>
                                            <input id="axe" name="axe" class="form-control form-control-sm col-9" type="number">
                                        </div>
                                        @if ($config->game->archer == 1)
                                            <div class="input-group col-2 input-group-sm mb-3">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text" id="inputGroup-sizing-sm"><img id="unit_archer" class="pr-2" src="{{ \App\Util\Icon::icons(3) }}"></span>
                                                </div>
                                                <input id="archer" name="archer" class="form-control form-control-sm col-9" type="number">
                                            </div>
                                        @endif
                                        <div class="input-group col-2 input-group-sm mb-3">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text" id="inputGroup-sizing-sm"><img id="unit_spy" class="pr-2" src="{{ \App\Util\Icon::icons(4) }}"></span>
                                            </div>
                                            <input id="spy" name="spy" class="form-control form-control-sm col-9" type="number">
                                        </div>
                                        <div class="input-group col-2 input-group-sm mb-3">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text" id="inputGroup-sizing-sm"><img id="unit_light" class="pr-2" src="{{ \App\Util\Icon::icons(5) }}"></span>
                                            </div>
                                            <input id="light" name="light" class="form-control form-control-sm col-9" type="number">
                                        </div>
                                        @if ($config->game->archer == 1)
                                            <div class="input-group col-2 input-group-sm mb-3">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text" id="inputGroup-sizing-sm"><img id="unit_marcher" class="pr-2" src="{{ \App\Util\Icon::icons(6) }}"></span>
                                                </div>
                                                <input id="marcher" name="marcher" class="form-control form-control-sm col-9" type="number">
                                            </div>
                                        @endif
                                        <div class="input-group col-2 input-group-sm mb-3">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text" id="inputGroup-sizing-sm"><img id="unit_heavy" class="pr-2" src="{{ \App\Util\Icon::icons(7) }}"></span>
                                            </div>
                                            <input id="heavy" name="heavy" class="form-control form-control-sm col-9" type="number">
                                        </div>
                                        <div class="input-group col-2 input-group-sm mb-3">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text" id="inputGroup-sizing-sm"><img id="unit_ram" class="pr-2" src="{{ \App\Util\Icon::icons(8) }}"></span>
                                            </div>
                                            <input id="ram" name="ram" class="form-control form-control-sm col-9" type="number">
                                        </div>
                                        <div class="input-group col-2 input-group-sm mb-3">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text" id="inputGroup-sizing-sm"><img id="unit_catapult" class="pr-2" src="{{ \App\Util\Icon::icons(9) }}"></span>
                                            </div>
                                            <input id="catapult" name="catapult" class="form-control form-control-sm col-9" type="number">
                                        </div>
                                        @if ($config->game->knight > 0)
                                            <div class="input-group col-2 input-group-sm mb-3">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text" id="inputGroup-sizing-sm"><img id="unit_knight" class="pr-2" src="{{ \App\Util\Icon::icons(10) }}"></span>
                                                </div>
                                                <input id="knight" name="knight" class="form-control form-control-sm col-9" type="number">
                                            </div>
                                        @endif
                                        <div class="input-group col-2 input-group-sm mb-3">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text" id="inputGroup-sizing-sm"><img id="unit_snob" class="pr-2" src="{{ \App\Util\Icon::icons(11) }}"></span>
                                            </div>
                                            <input id="snob" name="snob" class="form-control form-control-sm col-9" type="number">
                                        </div>
                                    </div>
                                </div>
                                <!--/span-->
                                <div class="col-md-12">
                                    <div class="form-group row">
                                        <label class="control-label col-3">Notizen</label>
                                        <div class="col-12">
                                            <textarea id="note" class="form-control form-control-sm"  rows="2"></textarea>
                                        </div>
                                    </div>
                                </div>
                                @csrf
                                <input id="attack_list_id" type="hidden" value="{{ $attackList->id }}">
                                <div class="col-12">
                                    <input type="button" class="btn bg-danger btn-sm float-left text-white link" onclick="destroyOutdated()" value="{{ __('global.delete').' '.__('tool.attackPlanner.outdated') }}">
                                    <input type="submit" class="btn btn-sm btn-success float-right" value="{{ __('global.save') }}">
                                </div>
                                <!--/span-->
                            </div>
                        </form>
                    </div>
                    <div class="tab-pane fade" id="link" role="tabpanel" aria-labelledby="link-tab">
                        <div class="row pt-3">
                            <div class="col-12">
                                <div class="form-group row">
                                    <label class="control-label col-md-2">{{ __('tool.attackPlanner.editLink') }}</label>
                                    <div class="col-md-1">
                                        <button class="btn btn-primary btn-sm" onclick="copy('link-edit')">{{ __('global.datatables.copy') }}</button>
                                    </div>
                                    <div class="col-md-9">
                                        <input id="link-edit" type="text" class="form-control-plaintext form-control-sm disabled" value="{{ route('tools.attackPlannerMode', [$attackList->id, 'edit', $attackList->edit_key]) }}" />
                                        <small class="form-control-feedback">{{ __('tool.attackPlanner.editLink_helper') }}</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="form-group row">
                                    <label class="control-label col-md-2">{{ __('tool.attackPlanner.showLink') }}</label>
                                    <div class="col-md-1">
                                        <button class="btn btn-primary btn-sm" onclick="copy('link-show')">{{ __('global.datatables.copy') }}</button>
                                    </div>
                                    <div class="col-md-9">
                                        <input id="link-show" type="text" class="form-control-plaintext form-control-sm disabled" value="{{ route('tools.attackPlannerMode', [$attackList->id, 'show', $attackList->show_key]) }}" />
                                        <small class="form-control-feedback">{{ __('tool.attackPlanner.showLink_helper') }}</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="tab-pane fade" id="import" role="tabpanel" aria-labelledby="import-tab">
                        <div class="row pt-3">
                            <div class="col-12">
                                <div class="form-group">
                                    <label class="control-label mr-3">{{ __('tool.attackPlanner.exportWB') }}</label> <button class="btn btn-primary btn-sm" onclick="copy('exportWB')">{{ __('global.datatables.copy') }}</button>
                                    <textarea id="exportWB" class="form-control form-control-sm" rows="1"></textarea>
                                    <small class="form-control-feedback">{{ __('tool.attackPlanner.exportWBDesc') }}</small>
                                </div>
                                <div class="form-group">
                                    <label class="control-label mr-3">{{ __('tool.attackPlanner.exportBB') }}</label> <button class="btn btn-primary btn-sm" onclick="copy('exportBB')">{{ __('global.datatables.copy') }}</button>
                                    <textarea id="exportBB" class="form-control form-control-sm" rows="1"></textarea>
                                    <small class="form-control-feedback">{{ __('tool.attackPlanner.exportBBDesc') }}</small>
                                </div>
                                <div class="form-group">
                                    <label class="control-label mr-3">{{ __('tool.attackPlanner.exportIGM') }}</label> <button class="btn btn-primary btn-sm" onclick="copy('exportIGM')">{{ __('global.datatables.copy') }}</button>
                                    <textarea id="exportIGM" class="form-control form-control-sm" rows="1"></textarea>
                                    <small class="form-control-feedback">{{ __('tool.attackPlanner.exportIGMDesc') }}</small>
                                </div>
                                <form id="importItemsForm">
                                    @csrf
                                    <div class="form-group">
                                        <label class="control-label mr-3">{{ __('tool.attackPlanner.import') }}</label>
                                        <textarea id="importWB" class="form-control form-control-sm" style="height: 80px"></textarea>
                                        <small class="form-control-feedback">{{ __('tool.attackPlanner.import_helper') }}</small>
                                    </div>
                                    <div class="col-12">
                                        <input type="submit" class="btn btn-sm btn-success float-right" value="{{ __('tool.attackPlanner.import') }}">
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    <div class="tab-pane fade" id="stats" role="tabpanel" aria-labelledby="stats-tab">
                        <div class="row pt-3">
                            <div class="col-1"></div>
                            <div class="col-4">
                                <h3>{{ __('ui.tabletitel.general') }}</h3>
                                <div class="form-group">
                                    {{ __('tool.attackPlanner.attackTotal') }}: <b id="attackTotal" class="float-right">{{ \App\Util\BasicFunctions::numberConv($stats['total']) }}</b>
                                </div>
                                <div class="form-group">
                                    {{ __('tool.attackPlanner.attackStart_village') }}: <b id="attackStart_village" class="float-right">{{ \App\Util\BasicFunctions::numberConv($stats['start_village']) }}</b>
                                </div>
                                <div class="form-group">
                                    {{ __('tool.attackPlanner.attackTarget_village') }}: <b id="attackTarget_village" class="float-right">{{ \App\Util\BasicFunctions::numberConv($stats['target_village']) }}</b>
                                </div>
                            </div>
                            <div class="col-1"></div>
                            <div class="col-2">
                                <h3>{{ __('global.units') }}</h3>
                                @if (isset($stats['slowest_unit']))
                                    @foreach ($stats['slowest_unit'] as $slowest_unit)
                                        <div class="form-group">
                                            <img src="{{ \App\Util\Icon::icons($slowest_unit['id']) }}">-{{ __('global.total') }} <b id="attackTotal" class="float-right">{{ \App\Util\BasicFunctions::numberConv($slowest_unit['count']) }}</b>
                                        </div>
                                    @endforeach
                                @endif
                            </div>
                            <div class="col-1"></div>
                            <div class="col-2">
                                <h3>{{ __('tool.attackPlanner.type') }}</h3>
                                @if (isset($stats['type']))
                                    @foreach ($stats['type'] as $type)
                                        <div class="form-group">
                                            <img src="{{ \App\Util\Icon::icons($type['id']) }}">-{{ __('global.total') }} <b id="attackTotal" class="float-right">{{ \App\Util\BasicFunctions::numberConv($type['count']) }}</b>
                                        </div>
                                    @endforeach
                                @endif
                            </div>
                            <div class="col-1"></div>
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
    <div class="modal fade bd-example-modal-xl" tabindex="-1" role="dialog" aria-labelledby="myExtraLargeModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{ __('global.edit') }}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="editItemForm">
                    <div class="modal-body">
                        <div class="row justify-content-md-center">
                            <div class="col-md-4">
                                <div class="input-group input-group-sm mb-3">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">Type</span>
                                        <span class="input-group-text"><img id="edit_type_img" src="{{ \App\Util\Icon::icons(8) }}"></span>
                                    </div>
                                    <select id="edit_type" class="custom-select type" data-toggle="tooltip" data-placement="top" title="{{ __('tool.attackPlanner.type_helper') }}" data-target="edit_">
                                        <option value="-1">{{ __('ui.old.nodata') }}</option>
                                        <optgroup label="{{ __('tool.attackPlanner.offensive') }}">
                                            <option value="8">{{ __('tool.attackPlanner.attack') }}</option>
                                            <option value="11">{{ __('tool.attackPlanner.conquest') }}</option>
                                            <option value="14">{{ __('tool.attackPlanner.fake') }}</option>
                                            <option value="45">{{ __('tool.attackPlanner.wallbreaker') }}</option>
                                        </optgroup>
                                        <optgroup label="{{ __('tool.attackPlanner.defensive') }}">
                                            <option value="0">{{ __('tool.attackPlanner.support') }}</option>
                                            <option value="1">{{ __('tool.attackPlanner.standSupport') }}</option>
                                            <option value="7">{{ __('tool.attackPlanner.fastSupport') }}</option>
                                            <option value="46">{{ __('tool.attackPlanner.fakeSupport') }}</option>
                                        </optgroup>
                                    </select>
                                </div>
                            </div>
                            <!--/span-->
                            <div class="col-md-4">
                                <div class="input-group input-group-sm mb-3">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">{{ __('tool.attackPlanner.startVillage') }}</span>
                                    </div>
                                    <input id="edit_xStart" data-target="edit_" class="form-control mx-auto col-5 koord xStart" type="text" placeholder="500" maxlength="3" />
                                    <div class="input-group-append input-group-prepend">
                                        <span class="input-group-text">|</span>
                                    </div>
                                    <input id="edit_yStart" data-target="edit_" class="form-control mx-auto col-5 koord yStart" type="text" placeholder="500" maxlength="3" />
                                </div>
                            </div>
                            <!--/span-->
                            <div class="col-md-4">
                                <div class="input-group input-group-sm mb-3">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">{{ __('tool.attackPlanner.targetVillage') }}</span>
                                    </div>
                                    <input id="edit_xTarget" data-target="edit_" class="form-control mx-auto col-5 koord xTarget" type="text" placeholder="500" maxlength="3" />
                                    <div class="input-group-append input-group-prepend">
                                        <span class="input-group-text">|</span>
                                    </div>
                                    <input id="edit_yTarget" data-target="edit_" class="form-control mx-auto col-5 koord yTarget" type="text" placeholder="500" maxlength="3" />
                                </div>
                            </div>
                            <!--/span-->
                            <div class="col-md-4">
                                <div class="input-group input-group-sm mb-3">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">{{ __('tool.attackPlanner.date') }}</span>
                                    </div>
                                    <input id="edit_day" data-target="edit_" type="date" class="form-control form-control-sm day" value="{{ date('Y-m-d', time()) }}" data-toggle="tooltip" data-placement="top" title="{{ __('tool.attackPlanner.date_helper') }}" />
                                </div>
                            </div>
                            <!--/span-->
                            <div class="col-md-4">
                                <div class="input-group input-group-sm mb-3">
                                    <div class="input-group-prepend">
                                        <button id="edit_time_title" type="button" class="btn input-group-text dropdown-toggle dropdown-toggle-split" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                            {{ __('tool.attackPlanner.arrivalTime') }} <span class="sr-only">Toggle Dropdown</span>
                                        </button>
                                        <div class="dropdown-menu">
                                            <a class="dropdown-item" onclick="changeTime(0,'edit_')">{{ __('tool.attackPlanner.arrivalTime') }}</a>
                                            <a class="dropdown-item" onclick="changeTime(1,'edit_')">{{ __('tool.attackPlanner.sendTime') }}</a>
                                        </div>
                                    </div>
                                    <input id="edit_time" data-target="edit_" type="time" step="0.001" class="form-control form-control-sm time" value="{{ date('H:i:s', time()+3600) }}" data-toggle="tooltip" data-placement="top" title="{{ __('tool.attackPlanner.date_helper') }}" />
                                    <input id="edit_time_type" data-target="edit_" type="hidden" value="0">
                                </div>
                            </div>
                            <!--/span-->
                            <div class="col-md-4">
                                <div class="input-group input-group-sm mb-3">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">{{ __('global.unit') }}</span>
                                        <span class="input-group-text"><img id="unit_img" src="{{ \App\Util\Icon::icons(0) }}"></span>
                                    </div>
                                    <select id="edit_slowest_unit" data-target="edit_" class="form-control form-control-sm slowest_unit" data-toggle="tooltip" data-placement="top" title="{{ __('tool.attackPlanner.unit_helper') }}">
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
                                        @if ($config->game->knight > 0)
                                            <option value="10">{{ __('ui.unit.knight') }}</option>
                                        @endif
                                        <option value="11">{{ __('ui.unit.snob') }}</option>
                                    </select>
                                </div>
                            </div>
                            <!--/span-->
                            <div class="col-12">
                                <div class="form-inline row">
                                    <div class="input-group col-2 input-group-sm mb-3">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text" id="inputGroup-sizing-sm"><img id="unit_spear" class="pr-2" src="{{ \App\Util\Icon::icons(0) }}"></span>
                                        </div>
                                        <input id="edit_spear" name="spear" class="form-control form-control-sm col-9" type="number">
                                    </div>
                                    <div class="input-group col-2 input-group-sm mb-3">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text" id="inputGroup-sizing-sm"><img id="unit_sword" class="pr-2" src="{{ \App\Util\Icon::icons(1) }}"></span>
                                        </div>
                                        <input id="edit_sword" name="sword" class="form-control form-control-sm col-9" type="number">
                                    </div>
                                    <div class="input-group col-2 input-group-sm mb-3">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text" id="inputGroup-sizing-sm"><img id="unit_axe" class="pr-2" src="{{ \App\Util\Icon::icons(2) }}"></span>
                                        </div>
                                        <input id="edit_axe" name="axe" class="form-control form-control-sm col-9" type="number">
                                    </div>
                                    @if ($config->game->archer == 1)
                                        <div class="input-group col-2 input-group-sm mb-3">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text" id="inputGroup-sizing-sm"><img id="unit_archer" class="pr-2" src="{{ \App\Util\Icon::icons(3) }}"></span>
                                            </div>
                                            <input id="edit_archer" name="archer" class="form-control form-control-sm col-9" type="number">
                                        </div>
                                    @endif
                                    <div class="input-group col-2 input-group-sm mb-3">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text" id="inputGroup-sizing-sm"><img id="unit_spy" class="pr-2" src="{{ \App\Util\Icon::icons(4) }}"></span>
                                        </div>
                                        <input id="edit_spy" name="spy" class="form-control form-control-sm col-9" type="number">
                                    </div>
                                    <div class="input-group col-2 input-group-sm mb-3">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text" id="inputGroup-sizing-sm"><img id="unit_light" class="pr-2" src="{{ \App\Util\Icon::icons(5) }}"></span>
                                        </div>
                                        <input id="edit_light" name="light" class="form-control form-control-sm col-9" type="number">
                                    </div>
                                    @if ($config->game->archer == 1)
                                        <div class="input-group col-2 input-group-sm mb-3">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text" id="inputGroup-sizing-sm"><img id="unit_marcher" class="pr-2" src="{{ \App\Util\Icon::icons(6) }}"></span>
                                            </div>
                                            <input id="edit_marcher" name="marcher" class="form-control form-control-sm col-9" type="number">
                                        </div>
                                    @endif
                                    <div class="input-group col-2 input-group-sm mb-3">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text" id="inputGroup-sizing-sm"><img id="unit_heavy" class="pr-2" src="{{ \App\Util\Icon::icons(7) }}"></span>
                                        </div>
                                        <input id="edit_heavy" name="heavy" class="form-control form-control-sm col-9" type="number">
                                    </div>
                                    <div class="input-group col-2 input-group-sm mb-3">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text" id="inputGroup-sizing-sm"><img id="unit_ram" class="pr-2" src="{{ \App\Util\Icon::icons(8) }}"></span>
                                        </div>
                                        <input id="edit_ram" name="ram" class="form-control form-control-sm col-9" type="number">
                                    </div>
                                    <div class="input-group col-2 input-group-sm mb-3">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text" id="inputGroup-sizing-sm"><img id="unit_catapult" class="pr-2" src="{{ \App\Util\Icon::icons(9) }}"></span>
                                        </div>
                                        <input id="edit_catapult" name="catapult" class="form-control form-control-sm col-9" type="number">
                                    </div>
                                    @if ($config->game->knight > 0)
                                        <div class="input-group col-2 input-group-sm mb-3">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text" id="inputGroup-sizing-sm"><img id="unit_knight" class="pr-2" src="{{ \App\Util\Icon::icons(10) }}"></span>
                                            </div>
                                            <input id="edit_knight" name="knight" class="form-control form-control-sm col-9" type="number">
                                        </div>
                                    @endif
                                    <div class="input-group col-2 input-group-sm mb-3">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text" id="inputGroup-sizing-sm"><img id="unit_snob" class="pr-2" src="{{ \App\Util\Icon::icons(11) }}"></span>
                                        </div>
                                        <input id="edit_snob" name="snob" class="form-control form-control-sm col-9" type="number">
                                    </div>
                                </div>
                            </div>
                            <!--/span-->
                            <div class="col-md-12">
                                <div class="form-group row">
                                    <label class="control-label col-3">Notizen</label>
                                    <div class="col-12">
                                        <textarea id="edit_note" class="form-control form-control-sm"  rows="2"></textarea>
                                    </div>
                                </div>
                            </div>
                            <input id="attack_list_item" type="hidden">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-danger" data-dismiss="modal">{{ __('global.close') }}</button>
                        <button type="submit" class="btn btn-success">{{ __('global.save') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- ENDE Modal -->
@endsection

@section('js')
    <audio controls class="d-none">
        <source src="{{ asset('sounds/attackplanner/420661__kinoton__alarm-siren-fast-oscillations.wav') }}" type="audio/mpeg">
        Your browser does not support the audio element.
    </audio>
    <script type="text/javascript" src="{{ asset('plugin/jquery.countdown/jquery.countdown.min.js') }}"></script>
    <script>
        var muteaudio = false;
        var keyArray = {};
        var audioTiming = 0;
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
                order:[[6, 'desc']],
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
                        'targets': [1,3,9,10,11,@if($mode == 'edit') 12 @endif]
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
                            '<label id="audioTimingText" for="customRange2">{!! str_replace('%S%', '<input id="audioTimingInput" class="form-control form-control-sm mx-1" style="width: 50px;" type="text" value="0">', __('tool.attackPlanner.audioTiming')) !!}</label>' +
                            '<input type="range" class="custom-range" min="0" max="60" id="audioTiming" value="0">' +
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

                    table.ajax.reload();

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
                    table.ajax.reload();

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
                alert('{{ __('tool.attackPlanner.errorKoord') }}');
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
                alert('{{ __('tool.attackPlanner.errorKoord') }}');
                error += 1;
            }

            if (error == 0){
                update();
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
            $('#attack_list_item').val(data.id);
            $('#edit_type').val(rowData.type);
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
            $('#edit_unit_img').attr('src', slowest_unit_img(rowData.slowest_unit));
            $('#edit_type_img').attr('src', typ_img(rowData.type));
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
                    $('#exportWB').html(response.data);
                })
                .catch((error) => {

                });
        }

        function exportBB() {
            axios.get('{{ route('tools.attackPlannerMode', [$attackList->id, 'exportBB', $attackList->edit_key]) }}', {
            })
                .then((response) => {
                    $('#exportBB').html(response.data);
                })
                .catch((error) => {

                });
        }

        function exportIGM() {
            axios.get('{{ route('tools.attackPlannerMode', [$attackList->id, 'exportIGM', $attackList->edit_key]) }}', {
            })
                .then((response) => {
                    $('#exportIGM').html(response.data);
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
@endsection
