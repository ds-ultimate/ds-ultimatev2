{{--start own AttackList--}}
<div class="tab-pane fade {{ ($page == 'myAttackplanner')? 'show active' : '' }}" id="myAttackplanner" role="tabpanel" aria-labelledby="profile-tab">
    <div class="row mt-2">
        <div class="col-10">
            <div class="list-group" id="ownAttackList" role="tablist">
                <a class="list-group-item list-group-item-action disabled" data-toggle="list" role="tab" aria-controls="home">
                    <div class="row">
                        <div class="col-2">
                            <b>{{ __('ui.server.worlds') }}</b>
                        </div>
                        <div class="col-6">
                            <span>Title</span>
                        </div>
                        <div class="col-2">
                            <span class=" float-right">{{ __('datatable.oPaginate_sNext') }}</span>
                        </div>
                        <div class="col-1">
                            <small class=" float-right">Ausstehend</small>
                        </div>
                        <div class="col-1">
                            <small class=" float-right">Abgelaufen</small>
                        </div>
                    </div>
                </a>
                @foreach($attackLists as $attackList)
                    <a class="list-group-item list-group-item-action {{ ($attackLists->get(0)->id === $attackList->id)? 'active ': '' }}" id="attackList-{{ $attackList->id }}" onclick="switchAttackPlanner('{{ $attackList->id }}', '{{ $attackList->edit_key }}', '{{ $attackList->show_key }}')" data-toggle="list" role="tab" aria-controls="home">
                        <div class="row">
                            <div class="col-2">
                                <b>{{ $attackList->world->getDistplayName() }}</b>
                            </div>
                            <div class="col-6">
                                <span>{{ ($attackList->title === null)? __('ui.noTitle'): $attackList->title }}</span>
                            </div>
                            <div class="col-2">
                                <span class="badge badge-info badge-pill float-right text-white">{{ $attackList->nextAttack() }}</span>
                            </div>
                            <div class="col-1">
                                <span class="badge badge-success badge-pill float-right">{{ $attackList->attackCount() }}</span>
                            </div>
                            <div class="col-1">
                                <span class="badge badge-danger badge-pill float-right">{{ $attackList->outdatedCount() }}</span>
                            </div>
                        </div>
                    </a>
                @endforeach
            </div>
            <div id="attackListNoData"{!! (count($attackLists)>0)?(' style="display: none"'):('') !!}>
                {{ __('ui.old.nodata') }}
            </div>
        </div>
        <div class="col-2">
            @if (count($attackLists) > 0)
            <div id="attackPlan-own-side-panel">
                <a id="editButtonAttackPlanner" href="{{ route('tools.attackPlannerMode', [$attackLists->get(0)->id, 'edit', $attackLists->get(0)->edit_key]) }}" class="btn btn-success mb-2 w-100">{{ __('global.edit') }}</a>
                <a id="deleteButtonAttackPlanner" data-toggle="confirmation" data-content="{{ __('user.confirm.destroy.attackPlanContent') }}"  class="btn btn-danger mb-2 w-100">{{ __('global.delete') }}</a>
                <label class="mt-3">{{ __('tool.map.editLink') }}:</label>
                <div class="input-group mb-2">
                    <input id="editLinkAttackPlanner" type="text" class="form-control" value="{{ route('tools.attackPlannerMode', [$attackLists->get(0)->id, 'edit', $attackLists->get(0)->edit_key]) }}">
                    <div class="input-group-append">
                        <span class="input-group-text" style="cursor:pointer" id="basic-addon2" onclick="copy('editLinkAttackPlanner')"><i class="far fa-copy"></i></span>
                    </div>
                </div>
                <label class="mt-3">{{ __('tool.map.showLink') }}:</label>
                <div class="input-group mb-2">
                    <input id="showLinkAttackPlanner" type="text" class="form-control" value="{{ route('tools.attackPlannerMode', [$attackLists->get(0)->id, 'show', $attackLists->get(0)->show_key]) }}">
                    <div class="input-group-append">
                        <span class="input-group-text" style="cursor:pointer" id="basic-addon2" onclick="copy('showLinkAttackPlanner')"><i class="far fa-copy"></i></span>
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
{{--end own AttackList--}}

@push('js')
    <script>
        $(document).ready(function () {
            $('#deleteButtonAttackPlanner').on('confirmed.bs.confirmation', destroyAttackPlanner);
        })

        @if (count($attackLists) > 0)
        var attackPlannerId = '{{ $attackLists->get(0)->id }}';
        var attackPlannerKey = '{{ $attackLists->get(0)->edit_key }}';
        @endif

        function switchAttackPlanner(id, edit_key, show_key, follow=false) {
            if (follow){
                $('#showButtonAttackPlannerFollow').attr('href', '{{ route('index') }}/tools/attackPlanner/' + id + '/show/' + show_key);
                $('#showLinkAttackPlannerFollow').val('{{ route('index') }}/tools/attackPlanner/' + id + '/show/' + show_key);
            } else {
                $('#editButtonAttackPlanner').attr('href', '{{ route('index') }}/tools/attackPlanner/' + id + '/edit/' + edit_key);
                $('#editLinkAttackPlanner').val('{{ route('index') }}/tools/attackPlanner/' + id + '/edit/' + edit_key);
                $('#showLinkAttackPlanner').val('{{ route('index') }}/tools/attackPlanner/' + id + '/show/' + show_key);
                attackPlannerId = id;
                attackPlannerKey = edit_key;
            }
        }

        function destroyAttackPlanner() {
            axios.delete('{{ route('index') }}/tools/attackPlanner/' + attackPlannerId + '/' + attackPlannerKey)
                .then((response) => {
                    var data = response.data;
                    createToast(data['msg'], "{{ __('tool.attackPlanner.title') }}", "{{ __('global.now') }}");

                    $('#attackList-' + attackPlannerId).remove();
                    $('#ownAttackList').children().eq(1).click();
                    if($('#ownAttackList').children()[1] == undefined) {
                        $('#attackListNoData').show();
                        $('#attackPlan-own-side-panel').hide();
                    }
                })
                .catch((error) => {
                });
        }
    </script>
@endpush
