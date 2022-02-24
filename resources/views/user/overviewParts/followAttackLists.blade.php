{{--start follow AttackList--}}
<div class="tab-pane fade {{ ($page == 'followAttackplanner')? 'show active' : '' }}" id="followAttackplanner" role="tabpanel" aria-labelledby="profile-tab">
    <div class="row mt-2">
        <div class="col-10">
            <div class="list-group" id="followAttackList" role="tablist">
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
                @if (count($attackListsFollow) > 0)
                    @foreach($attackListsFollow as $attackList)
                        <a class="list-group-item list-group-item-action {{ ($attackListsFollow->get(0)->id === $attackList->id)? 'active ': '' }}" id="attackList-{{ $attackList->id }}" onclick="switchAttackPlanner('{{ $attackList->id }}', null, '{{ $attackList->show_key }}', true)" data-toggle="list" role="tab" aria-controls="home">
                            <div class="row">
                                <div class="col-2">
                                    <b>{{ $attackList->world->display_name }}</b>
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
                @else
                    {{ __('ui.old.nodata') }}
                @endif
            </div>
        </div>
        <div class="col-2">
            @if (count($attackListsFollow) > 0)
            <a id="showButtonAttackPlannerFollow" href="{{ route('tools.attackPlannerMode', [$attackListsFollow->get(0)->id, 'show', $attackListsFollow->get(0)->show_key]) }}" class="btn btn-primary mb-2 w-100">{{ __('tool.attackPlanner.show') }}</a>
            <label class="mt-3">{{ __('tool.map.showLink') }}:</label>
            <div class="input-group mb-2">
                <input id="showLinkAttackPlannerFollow" type="text" class="form-control" value="{{ route('tools.attackPlannerMode', [$attackListsFollow->get(0)->id, 'show', $attackListsFollow->get(0)->show_key]) }}">
                <div class="input-group-append">
                    <span class="input-group-text" style="cursor:pointer" id="basic-addon2" onclick="copy('showLinkAttackPlannerFollow')"><i class="far fa-copy"></i></span>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
{{--end follow AttackList--}}
