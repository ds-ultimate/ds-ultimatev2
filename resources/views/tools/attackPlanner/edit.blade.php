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
                                    <span class="input-group-text"><img id="edit_unit_img" src="{{ \App\Util\Icon::icons(0) }}"></span>
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
