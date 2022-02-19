<div class="tab-pane fade {{ ($active)? 'show active':'' }}" id="{{ $key }}" role="tabpanel" aria-labelledby="{{ $key }}-tab">
    <form id="multieditItemForm">
        <div class="row">
            <div class="col-md-4">
                <div class="input-group input-group-sm mb-3">
                    <div class="input-group-prepend">
                        <div class="input-group-text">
                            <input name="checkboxes[type]" type="checkbox">
                        </div>
                        <span class="input-group-text">Type</span>
                        <span class="input-group-text"><img class="type-img" src="{{ \App\Util\Icon::icons(8) }}"></span>
                    </div>
                    <select name="type" class="custom-select attack-type" data-toggle="tooltip" data-placement="top" title="{{ __('tool.attackPlanner.type_helper') }}">
                        <option value="-1">{{ __('ui.old.nodata') }}</option>
                        <optgroup label="{{ __('tool.attackPlanner.offensive') }}">
                            <option value="8" selected>{{ __('tool.attackPlanner.attack') }}</option>
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
                        <div class="input-group-text">
                            <input name="checkboxes[start]" type="checkbox" >
                        </div>
                        <span class="input-group-text">{{ __('tool.attackPlanner.startVillage') }}</span>
                    </div>
                    <input name="xStart" class="form-control mx-auto col-5 coord-input" type="text" inputmode="numeric" placeholder="500" maxlength="3" />
                    <div class="input-group-append input-group-prepend">
                        <span class="input-group-text">|</span>
                    </div>
                    <input name="yStart" class="form-control mx-auto col-5 coord-input" type="text" inputmode="numeric" placeholder="500" maxlength="3" />
                </div>
            </div>
            <!--/span-->
            <div class="col-md-4">
                <div class="input-group input-group-sm mb-3">
                    <div class="input-group-prepend">
                        <div class="input-group-text">
                            <input name="checkboxes[target]" type="checkbox">
                        </div>
                        <span class="input-group-text">{{ __('tool.attackPlanner.targetVillage') }}</span>
                    </div>
                    <input name="xTarget" class="form-control mx-auto col-5 coord-input" type="text" inputmode="numeric" placeholder="500" maxlength="3" />
                    <div class="input-group-append input-group-prepend">
                        <span class="input-group-text">|</span>
                    </div>
                    <input name="yTarget" class="form-control mx-auto col-5 coord-input" type="text" inputmode="numeric" placeholder="500" maxlength="3" />
                </div>
            </div>
            <!--/span-->
            <div class="col-md-4">
                <div class="input-group input-group-sm mb-3">
                    <div class="input-group-prepend">
                        <div class="input-group-text">
                            <input class="multiedit-date-check" type="checkbox" data-group="groupDate">
                        </div>
                        <span class="input-group-text">{{ __('tool.attackPlanner.date') }}</span>
                    </div>
                    <input name="day" type="date" class="form-control form-control-sm day" value="{{ date('Y-m-d', time()) }}" data-toggle="tooltip" data-placement="top" title="{{ __('tool.attackPlanner.date_helper') }}" />
                </div>
            </div>
            <!--/span-->
            <div class="col-md-4">
                <div class="input-group input-group-sm mb-3">
                    <div class="input-group-prepend">
                        <div class="input-group-text">
                            <input class="multiedit-date-check" name="checkboxes[date]" type="checkbox" data-group="groupDate">
                        </div>
                        <button type="button" class="btn input-group-text dropdown-toggle dropdown-toggle-split time-title" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            {{ __('tool.attackPlanner.arrivalTime') }} <span class="sr-only">Toggle Dropdown</span>
                        </button>
                        <div class="dropdown-menu">
                            <a class="dropdown-item time-switcher" value="0">{{ __('tool.attackPlanner.arrivalTime') }}</a>
                            <a class="dropdown-item time-switcher" value="1">{{ __('tool.attackPlanner.sendTime') }}</a>
                        </div>
                        <input name="time_type" type="hidden" class="time-type" value="0">
                    </div>
                    <input name="time" type="time" step="0.001" class="form-control form-control-sm time" value="{{ date('H:i:s', time()+3600) }}" data-toggle="tooltip" data-placement="top" title="{{ __('tool.attackPlanner.date_helper') }}" />
                </div>
            </div>
            <!--/span-->
            <div class="col-md-4">
                <div class="input-group input-group-sm mb-3">
                    <div class="input-group-prepend">
                        <div class="input-group-text">
                            <input name="checkboxes[slowest_unit]" type="checkbox">
                        </div>
                        <span class="input-group-text">{{ __('global.unit') }}</span>
                        <span class="input-group-text"><img class="unit-img" src="{{ \App\Util\Icon::icons(0) }}"></span>
                    </div>
                    <select name="slowest_unit" class="form-control form-control-sm slowest-unit" data-toggle="tooltip" data-placement="top" title="{{ __('tool.attackPlanner.unit_helper') }}">
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
                            <div class="input-group-text">
                                <input name="checkboxes[spear]" type="checkbox">
                            </div>
                            <span class="input-group-text inputGroup-sizing-sm"><img class="pr-2" src="{{ \App\Util\Icon::icons(0) }}"></span>
                        </div>
                        <input name="spear" class="form-control form-control-sm col-9" type="number">
                    </div>
                    <div class="input-group col-2 input-group-sm mb-3">
                        <div class="input-group-prepend">
                            <div class="input-group-text">
                                <input name="checkboxes[sword]" type="checkbox">
                            </div>
                            <span class="input-group-text inputGroup-sizing-sm"><img class="pr-2" src="{{ \App\Util\Icon::icons(1) }}"></span>
                        </div>
                        <input name="sword" class="form-control form-control-sm col-9" type="number">
                    </div>
                    <div class="input-group col-2 input-group-sm mb-3">
                        <div class="input-group-prepend">
                            <div class="input-group-text">
                                <input name="checkboxes[axe]" type="checkbox">
                            </div>
                            <span class="input-group-text inputGroup-sizing-sm"><img class="pr-2" src="{{ \App\Util\Icon::icons(2) }}"></span>
                        </div>
                        <input name="axe" class="form-control form-control-sm col-9" type="number">
                    </div>
                    @if ($config->game->archer == 1)
                        <div class="input-group col-2 input-group-sm mb-3">
                            <div class="input-group-prepend">
                                <div class="input-group-text">
                                    <input name="checkboxes[archer]" type="checkbox">
                                </div>
                                <span class="input-group-text inputGroup-sizing-sm"><img class="pr-2" src="{{ \App\Util\Icon::icons(3) }}"></span>
                            </div>
                            <input name="archer" class="form-control form-control-sm col-9" type="number">
                        </div>
                    @endif
                    <div class="input-group col-2 input-group-sm mb-3">
                        <div class="input-group-prepend">
                            <div class="input-group-text">
                                <input name="checkboxes[spy]" type="checkbox">
                            </div>
                            <span class="input-group-text inputGroup-sizing-sm"><img class="pr-2" src="{{ \App\Util\Icon::icons(4) }}"></span>
                        </div>
                        <input name="spy" class="form-control form-control-sm col-9" type="number">
                    </div>
                    <div class="input-group col-2 input-group-sm mb-3">
                        <div class="input-group-prepend">
                            <div class="input-group-text">
                                <input name="checkboxes[light]" type="checkbox">
                            </div>
                            <span class="input-group-text inputGroup-sizing-sm"><img class="pr-2" src="{{ \App\Util\Icon::icons(5) }}"></span>
                        </div>
                        <input name="light" class="form-control form-control-sm col-9" type="number">
                    </div>
                    @if ($config->game->archer == 1)
                        <div class="input-group col-2 input-group-sm mb-3">
                            <div class="input-group-prepend">
                                <div class="input-group-text">
                                    <input name="checkboxes[marcher]" type="checkbox">
                                </div>
                                <span class="input-group-text inputGroup-sizing-sm"><img class="pr-2" src="{{ \App\Util\Icon::icons(6) }}"></span>
                            </div>
                            <input name="marcher" class="form-control form-control-sm col-9" type="number">
                        </div>
                    @endif
                    <div class="input-group col-2 input-group-sm mb-3">
                        <div class="input-group-prepend">
                            <div class="input-group-text">
                                <input name="checkboxes[heavy]" type="checkbox">
                            </div>
                            <span class="input-group-text inputGroup-sizing-sm"><img class="pr-2" src="{{ \App\Util\Icon::icons(7) }}"></span>
                        </div>
                        <input name="heavy" class="form-control form-control-sm col-9" type="number">
                    </div>
                    <div class="input-group col-2 input-group-sm mb-3">
                        <div class="input-group-prepend">
                            <div class="input-group-text">
                                <input name="checkboxes[ram]" type="checkbox">
                            </div>
                            <span class="input-group-text inputGroup-sizing-sm"><img class="pr-2" src="{{ \App\Util\Icon::icons(8) }}"></span>
                        </div>
                        <input name="ram" class="form-control form-control-sm col-9" type="number">
                    </div>
                    <div class="input-group col-2 input-group-sm mb-3">
                        <div class="input-group-prepend">
                            <div class="input-group-text">
                                <input name="checkboxes[catapult]" type="checkbox">
                            </div>
                            <span class="input-group-text inputGroup-sizing-sm"><img class="pr-2" src="{{ \App\Util\Icon::icons(9) }}"></span>
                        </div>
                        <input name="catapult" class="form-control form-control-sm col-9" type="number">
                    </div>
                    @if ($config->game->knight > 0)
                        <div class="input-group col-2 input-group-sm mb-3">
                            <div class="input-group-prepend">
                                <div class="input-group-text">
                                    <input name="checkboxes[knight]" type="checkbox">
                                </div>
                                <span class="input-group-text inputGroup-sizing-sm"><img class="pr-2" src="{{ \App\Util\Icon::icons(10) }}"></span>
                            </div>
                            <input name="knight" class="form-control form-control-sm col-9" type="number">
                        </div>
                    @endif
                    <div class="input-group col-2 input-group-sm mb-3">
                        <div class="input-group-prepend">
                            <div class="input-group-text">
                                <input name="checkboxes[snob]" type="checkbox">
                            </div>
                            <span class="input-group-text inputGroup-sizing-sm"><img class="pr-2" src="{{ \App\Util\Icon::icons(11) }}"></span>
                        </div>
                        <input name="snob" class="form-control form-control-sm col-9" type="number">
                    </div>
                </div>
            </div>
            <!--/span-->
            <div class="col-md-12">
                <div class="form-group row">
                    <label class="control-label col-3">
                        Notizen
                        <input name="checkboxes[note]" class="mr-3" type="checkbox">
                    </label>
                    <div class="col-12">
                        <textarea name="note" class="form-control form-control-sm" rows="2"></textarea>
                    </div>
                </div>
            </div>
            <input name="id" type="hidden" value="{{ $attackList->id }}">
            <input name="key" type="hidden" value="{{ $attackList->edit_key }}">
            <div class="col-12">
                <input type="button" class="btn bg-danger btn-sm float-left text-white link" onclick="destroyOutdated()" value="{{ __('tool.attackPlanner.deleteOutdated') }}">
                <input type="button" class="confirm-deleteAll btn bg-danger btn-sm float-left text-white link ml-4" data-toggle="confirmation" data-content="{{ __('tool.attackPlanner.confirm.clear') }}" value="{{ __('tool.attackPlanner.deleteAll') }}">
                <input type="submit" class="btn btn-sm btn-success float-right" value="{{ __('global.save') }}">
            </div>
        </div>
    </form>
</div>

@push('js')
<script>
    $(function() {
        $('.multiedit-date-check').change(function() {
            var check = this.checked;
            $('.multiedit-date-check').each(function(elm) {
                elm.checked = check;
            })
        });
        
        $('#multieditItemForm').on('submit', function (e) {
            e.preventDefault();
            if (multieditValidatePreSend(this)) {
                var select = table.rows('.selected').data();
                var postData = $('#multieditItemForm').serialize();
                var i = 0;
                select.each(function(e){
                    postData += "&" + encodeURIComponent("items[" + i + "]") + "=" + encodeURIComponent(e.id);
                    i++;
                })

                var id = $('#attack_list_item').val();
                axios.post('{{ route('tools.attackListItemMultiedit') }}', postData)
                    .then((response) => {
                        var data = response.data;
                        table.ajax.reload();
                        createToast(data['msg'], data['title'], '{{ __('global.now') }}', data['data'] === 'success'? 'fas fa-check-circle text-success' :'fas fa-exclamation-circle text-danger')
                    })
                    .catch((error) => {

                    });
            }
        });
    })
        
    function multieditValidatePreSend(par) {
        var sX = $('input[name="xStart"]', par).val();
        var sY = $('input[name="yStart"]', par).val();
        var tX = $('input[name="xTarget"]', par).val();
        var tY = $('input[name="yTarget"]', par).val();
        var sA = $('input[name="checkboxes[start]"]', par).is(':checked');
        var tA = $('input[name="checkboxes[target]"]', par).is(':checked');

        var error = 0;
        if (sA && (sX == '' || sY == '') || tA && (tX == '' || tY == '')){
            var data = []
            data['msg'] = '{{ __('tool.attackPlanner.errorKoordEmpty') }}';
            createToast(data['msg'], '{{ __('tool.attackPlanner.errorKoordTitle') }}', '{{ __('global.now') }}', 'fas fa-exclamation-circle text-danger')
            error += 1;
        }
        if (sA && tA && sX == tX && sY == tY){
            var data = []
            data['msg'] = '{{ __('tool.attackPlanner.errorKoord') }}';
            createToast(data['msg'], '{{ __('tool.attackPlanner.errorKoordTitle') }}', '{{ __('global.now') }}', 'fas fa-exclamation-circle text-danger')
            error += 1;
        }
        return error == 0;
    }
</script>
@endpush