@extends('layouts.app')

@section('titel', $worldData->displayName(),': '.__('tool.attackPlanner.title'))

@section('style')
    <link href="{{ asset('plugin/jquery-ui/jquery-ui.min.css') }}" rel="stylesheet">
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
                                    <div class="form-group row">
                                        <label class="control-label col-3">{{ __('tool.attackPlanner.type') }}</label>
                                        <div class="col-1">
                                            <img id="type_img" src="{{ \App\Util\Icon::icons(8) }}">
                                        </div>
                                        <div class="col-8">
                                            <select id="type" class="form-control form-control-sm type">
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
                                            <small class="form-control-feedback">{{ __('tool.attackPlanner.type_helper') }}</small>
                                        </div>
                                    </div>
                                </div>
                                <!--/span-->
                                <div class="col-md-4">
                                    <div class="form-group row">
                                        <label class="control-label col-4">{{ __('tool.attackPlanner.startVillage') }}</label>
                                        <div id="start" class="form-inline col-8">
                                            <input id="xStart" class="form-control form-control-sm mx-auto col-5 koord xStart" type="text" placeholder="500" maxlength="3" />
                                            |
                                            <input id="yStart" class="form-control form-control-sm mx-auto col-5 koord yStart" type="text" placeholder="500" maxlength="3" />
                                            <small id="villageStart" class="form-control-feedback ml-2">{{ __('tool.attackPlanner.startVillage_helper') }}</small>
                                        </div>
                                    </div>
                                </div>
                                <!--/span-->
                                <div class="col-md-4">
                                    <div class="form-group row">
                                        <label class="control-label col-4">{{ __('tool.attackPlanner.targetVillage') }}</label>
                                        <div id="target" class="form-inline col-8">
                                            <input id="xTarget" class="form-control form-control-sm mx-auto col-5 koord xTarget" type="text" placeholder="500" maxlength="3" />
                                            |
                                            <input id="yTarget" class="form-control form-control-sm mx-auto col-5 koord yTarget" type="text" placeholder="500" maxlength="3" />
                                            <small id="villageTarget" class="form-control-feedback ml-2">{{ __('tool.attackPlanner.targetVillage_helper') }}</small>
                                        </div>
                                    </div>
                                </div>
                                <!--/span-->
                                <div class="col-md-4">
                                    <div class="form-group row">
                                        <label class="control-label col-3">{{ __('tool.attackPlanner.date') }}</label>
                                        <div class="col-9">
                                            <input id="day" type="date" class="form-control form-control-sm day" value="{{ date('Y-m-d', time()) }}" />
                                            <small id="day_feedback" class="form-control-feedback">{{ __('tool.attackPlanner.date_helper') }}</small>
                                        </div>
                                    </div>
                                </div>
                                <!--/span-->
                                <div class="col-md-4">
                                    <div class="form-group row">
                                        <label class="control-label col-4">{{ __('tool.attackPlanner.time') }}</label>
                                        <div class="col-8">
                                            <input id="time" type="time" step="1" class="form-control form-control-sm time" value="{{ date('H:i:s', time()+3600) }}" />
                                            <small id="time_feedback" class="form-control-feedback">{{ __('tool.attackPlanner.time_helper') }}</small>
                                        </div>
                                    </div>
                                </div>
                                <!--/span-->
                                <div class="col-md-4">
                                    <div class="form-group row">
                                        <label class="control-label col-3">{{ __('global.unit') }}</label>
                                        <div class="col-1">
                                            <img id="unit_img" src="{{ \App\Util\Icon::icons(0) }}">
                                        </div>
                                        <div class="col-8">
                                            <select id="slowest_unit" class="form-control form-control-sm slowest_unit">
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
                                            <small class="form-control-feedback">{{ __('tool.attackPlanner.unit_helper') }}</small>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="form-inline">
                                        <div class="form-group col-2 pb-3">
                                            <img id="unit_spear" class="pr-2" src="{{ \App\Util\Icon::icons(0) }}">
                                            <input id="spear" name="spear" class="form-control form-control-sm col-9" type="number">
                                        </div>
                                        <div class="form-group col-2 pb-3">
                                            <img id="unit_sword" class="pr-2" src="{{ \App\Util\Icon::icons(1) }}">
                                            <input id="sword" name="sword" class="form-control form-control-sm col-9" type="number">
                                        </div>
                                        <div class="form-group col-2 pb-3">
                                            <img id="unit_axe" class="pr-2" src="{{ \App\Util\Icon::icons(2) }}">
                                            <input id="axe" name="axe" class="form-control form-control-sm col-9" type="number">
                                        </div>
                                        @if ($config->game->archer == 1)
                                        <div class="form-group col-2 pb-3">
                                            <img id="unit_archer" class="pr-2" src="{{ \App\Util\Icon::icons(3) }}">
                                            <input id="archer" name="archer" class="form-control form-control-sm col-9" type="number">
                                        </div>
                                        @endif
                                        <div class="form-group col-2 pb-3">
                                            <img id="unit_spy" class="pr-2" src="{{ \App\Util\Icon::icons(4) }}">
                                            <input id="spy" name="spy" class="form-control form-control-sm col-9" type="number">
                                        </div>
                                        <div class="form-group col-2 pb-3">
                                            <img id="unit_light" class="pr-2" src="{{ \App\Util\Icon::icons(5) }}">
                                            <input id="light" name="light" class="form-control form-control-sm col-9" type="number">
                                        </div>
                                        @if ($config->game->archer == 1)
                                        <div class="form-group col-2 pb-3">
                                            <img id="unit_marcher" class="pr-2" src="{{ \App\Util\Icon::icons(6) }}">
                                            <input id="marcher" name="marcher" class="form-control form-control-sm col-9" type="number">
                                        </div>
                                        @endif
                                        <div class="form-group col-2 pb-3">
                                            <img id="unit_heavy" class="pr-2" src="{{ \App\Util\Icon::icons(7) }}">
                                            <input id="heavy" name="heavy" class="form-control form-control-sm col-9" type="number">
                                        </div>
                                        <div class="form-group col-2 pb-3">
                                            <img id="unit_ram" class="pr-2" src="{{ \App\Util\Icon::icons(8) }}">
                                            <input id="ram" name="ram" class="form-control form-control-sm col-9" type="number">
                                        </div>
                                        <div class="form-group col-2 pb-3">
                                            <img id="unit_catapult" class="pr-2" src="{{ \App\Util\Icon::icons(9) }}">
                                            <input id="catapult" name="catapult" class="form-control form-control-sm col-9" type="number">
                                        </div>
                                        @if ($config->game->knight > 0)
                                        <div class="form-group col-2 pb-3">
                                            <img id="unit_knight" class="pr-2" src="{{ \App\Util\Icon::icons(10) }}">
                                            <input id="knight" name="knight" class="form-control form-control-sm col-9" type="number">
                                        </div>
                                        @endif
                                        <div class="form-group col-2 pb-3">
                                            <img id="unit_snob" class="pr-2" src="{{ \App\Util\Icon::icons(11) }}">
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
                                <div class="form-group row">
                                    <label class="control-label col-3">{{ __('tool.attackPlanner.type') }}</label>
                                    <div class="col-1">
                                        <img id="edit_type_img" src="{{ \App\Util\Icon::icons(8) }}">
                                    </div>
                                    <div class="col-8">
                                        <select id="edit_type" class="form-control form-control-sm type" data-target="edit_">
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
                                        <small class="form-control-feedback">{{ __('tool.attackPlanner.type_helper') }}</small>
                                    </div>
                                </div>
                            </div>
                            <!--/span-->
                            <div class="col-md-4">
                                <div class="form-group row">
                                    <label class="control-label col-4">{{ __('tool.attackPlanner.startVillage') }}</label>
                                    <div id="start" class="form-inline col-8">
                                        <input id="edit_xStart" class="form-control form-control-sm mx-auto col-5 koord xStart" data-target="edit_" type="text" placeholder="500" maxlength="3" />
                                        |
                                        <input id="edit_yStart" class="form-control form-control-sm mx-auto col-5 koord yStart" data-target="edit_" type="text" placeholder="500" maxlength="3" />
                                        <small id="edit_villageStart" class="form-control-feedback ml-2">{{ __('tool.attackPlanner.startVillage_helper') }}</small>
                                    </div>
                                </div>
                            </div>
                            <!--/span-->
                            <div class="col-md-4">
                                <div class="form-group row">
                                    <label class="control-label col-4">{{ __('tool.attackPlanner.targetVillage') }}</label>
                                    <div id="target" class="form-inline col-8">
                                        <input id="edit_xTarget" class="form-control form-control-sm mx-auto col-5 koord xTarget" data-target="edit_" type="text" placeholder="500" maxlength="3" />
                                        |
                                        <input id="edit_yTarget" class="form-control form-control-sm mx-auto col-5 koord yTarget" data-target="edit_" type="text" placeholder="500" maxlength="3" />
                                        <small id="edit_villageTarget" class="form-control-feedback ml-2">{{ __('tool.attackPlanner.targetVillage_helper') }}</small>
                                    </div>
                                </div>
                            </div>
                            <!--/span-->
                            <div class="col-md-4">
                                <div class="form-group row">
                                    <label class="control-label col-3">{{ __('tool.attackPlanner.date') }}</label>
                                    <div class="col-9">
                                        <input id="edit_day" type="date" class="form-control form-control-sm day" value="{{ date('Y-m-d', time()) }}" data-target="edit_" />
                                        <small id="edit_day_feedback" class="form-control-feedback">{{ __('tool.attackPlanner.date_helper') }}</small>
                                    </div>
                                </div>
                            </div>
                            <!--/span-->
                            <div class="col-md-4">
                                <div class="form-group row">
                                    <label class="control-label col-4">{{ __('tool.attackPlanner.time') }}</label>
                                    <div class="col-8">
                                        <input id="edit_time" type="time" step="1" class="form-control form-control-sm time" value="{{ date('H:i:s', time()+3600) }}" data-target="edit_" />
                                        <small id="edit_time_feedback" class="form-control-feedback">{{ __('tool.attackPlanner.time_helper') }}</small>
                                    </div>
                                </div>
                            </div>
                            <!--/span-->
                            <div class="col-md-4">
                                <div class="form-group row">
                                    <label class="control-label col-3">{{ __('global.unit') }}</label>
                                    <div class="col-1">
                                        <img id="edit_unit_img" src="{{ \App\Util\Icon::icons(0) }}">
                                    </div>
                                    <div class="col-8">
                                        <select id="edit_slowest_unit" class="form-control form-control-sm slowest_unit" data-target="edit_">
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
                                        <small class="form-control-feedback">{{ __('tool.attackPlanner.unit_helper') }}</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="form-inline">
                                    <div class="form-group col-2 pb-3">
                                        <img id="unit_spear" class="pr-2" src="{{ \App\Util\Icon::icons(0) }}">
                                        <input id="edit_spear" name="edit_spear" class="form-control form-control-sm col-9" type="number">
                                    </div>
                                    <div class="form-group col-2 pb-3">
                                        <img id="unit_sword" class="pr-2" src="{{ \App\Util\Icon::icons(1) }}">
                                        <input id="edit_sword" name="edit_sword" class="form-control form-control-sm col-9" type="number">
                                    </div>
                                    <div class="form-group col-2 pb-3">
                                        <img id="unit_axe" class="pr-2" src="{{ \App\Util\Icon::icons(2) }}">
                                        <input id="edit_axe" name="edit_axe" class="form-control form-control-sm col-9" type="number">
                                    </div>
                                    @if ($config->game->archer == 1)
                                        <div class="form-group col-2 pb-3">
                                            <img id="unit_archer" class="pr-2" src="{{ \App\Util\Icon::icons(3) }}">
                                            <input id="edit_archer" name="edit_archer" class="form-control form-control-sm col-9" type="number">
                                        </div>
                                    @endif
                                    <div class="form-group col-2 pb-3">
                                        <img id="unit_spy" class="pr-2" src="{{ \App\Util\Icon::icons(4) }}">
                                        <input id="edit_spy" name="edit_spy" class="form-control form-control-sm col-9" type="number">
                                    </div>
                                    <div class="form-group col-2 pb-3">
                                        <img id="unit_light" class="pr-2" src="{{ \App\Util\Icon::icons(5) }}">
                                        <input id="edit_light" name="edit_light" class="form-control form-control-sm col-9" type="number">
                                    </div>
                                    @if ($config->game->archer == 1)
                                        <div class="form-group col-2 pb-3">
                                            <img id="unit_marcher" class="pr-2" src="{{ \App\Util\Icon::icons(6) }}">
                                            <input id="edit_marcher" name="edit_marcher" class="form-control form-control-sm col-9" type="number">
                                        </div>
                                    @endif
                                    <div class="form-group col-2 pb-3">
                                        <img id="unit_heavy" class="pr-2" src="{{ \App\Util\Icon::icons(7) }}">
                                        <input id="edit_heavy" name="edit_heavy" class="form-control form-control-sm col-9" type="number">
                                    </div>
                                    <div class="form-group col-2 pb-3">
                                        <img id="unit_ram" class="pr-2" src="{{ \App\Util\Icon::icons(8) }}">
                                        <input id="edit_ram" name="edit_ram" class="form-control form-control-sm col-9" type="number">
                                    </div>
                                    <div class="form-group col-2 pb-3">
                                        <img id="unit_catapult" class="pr-2" src="{{ \App\Util\Icon::icons(9) }}">
                                        <input id="edit_catapult" name="edit_catapult" class="form-control form-control-sm col-9" type="number">
                                    </div>
                                    @if ($config->game->knight > 0)
                                        <div class="form-group col-2 pb-3">
                                            <img id="unit_knight" class="pr-2" src="{{ \App\Util\Icon::icons(10) }}">
                                            <input id="edit_knight" name="edit_knight" class="form-control form-control-sm col-9" type="number">
                                        </div>
                                    @endif
                                    <div class="form-group col-2 pb-3">
                                        <img id="unit_snob" class="pr-2" src="{{ \App\Util\Icon::icons(11) }}">
                                        <input id="edit_snob" name="edit_snob" class="form-control form-control-sm col-9" type="number">
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
    <script type="text/javascript" src="{{ asset('plugin/jquery.countdown/jquery.countdown.min.js') }}"></script>
    <script>
        var table =
            $('#data1').DataTable({
                ordering: false,
                processing: true,
                serverSide: true,
                pageLength: 25,
                searching: false,
                ajax: '{!! route('tools.attackListItem.data', [ $attackList->id , $attackList->show_key]) !!}',
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
                    { data: 'info', name: 'info' },
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
                    @if($mode == 'edit')
                    exportWB();
                    exportBB();
                    exportIGM();
                    @endif
                    countdown();
                    popover();
                    @auth
                        @if($attackList->user_id != Auth::user()->id)
                            @if($attackList->follows()->where('user_id', Auth::user()->id)->count() > 0)
                            $('#data1_wrapper .row div:eq(2)').html('<div class="float-right"><i id="follow-icon" style="cursor:pointer; text-shadow: 0 0 15px #000;" onclick="changeFollow()" class="fas fa-star h4 text-warning"></i></div>');
                            @else
                            $('#data1_wrapper .row div:eq(2)').html('<div class="float-right"><i id="follow-icon" style="cursor:pointer" onclick="changeFollow()" class="far text-muted fa-star h4 text-muted"></i></div>');
                            @endif
                        @endif
                    @endauth
                },
                {!! \App\Util\Datatable::language() !!}
            });

        @if($mode == 'edit')
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

        function countdown(){
            $('[data-countdown]').each(function() {
                var finalDate = $(this).data('countdown');
                $(this).countdown(finalDate, {
                    precision:  500
                })
                    .on('update.countdown', function(event) {
                        var format = '%H:%M:%S';
                        if(event.offset.totalDays > 0) {
                            if (event.offset.totalDays > 1) {
                                format = '%D {{ __('tool.distCalc.days') }} ' + format;
                            }else {
                                format = '%D {{ __('tool.distCalc.days') }} ' + format;
                            }
                        }
                        $(this).html(event.strftime(format));
                    })
                    .on('finish.countdown', function (e) {
                        $(this).addClass('bg-danger text-white').html('00:00:00')
                    });
            });
        };

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

            $('.koord').on("keypress keyup blur",function (event) {
                $(this).val($(this).val().replace(/[^\d].+/, ""));
                if ((event.which < 48 || event.which > 57)) {
                    event.preventDefault();
                }
                if (event.keyCode == 13) {
                    calc();
                }
            });

            $(".xStart").keyup(function () {
                var dataTarget = $(this).attr('data-target');
                var target = (dataTarget != null)?dataTarget:'';
                if (this.value.length == this.maxLength) {
                    $(this).next('#' + target + 'yStart').focus();
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
                    $(this).next('#' + target + 'yTarget').focus();
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
                var input = $('#' + this.id).parent().attr('id');
                var type = input.substring(0, 1).toUpperCase() + input.substring(1);
                var x = $('#' + target + 'x' + type).val();
                var y = $('#' + target + 'y' + type).val();
                village(x, y, type, target)
            });

        })
    </script>
@endsection
