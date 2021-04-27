@extends('layouts.app')

@section('titel', ucfirst(__('user.title.overview')).' '.Auth::user()->name)

@section('content')
    <div class="row justify-content-center">
        <!-- Titel für Tablet | PC -->
        <div class="p-lg-5 mx-auto my-1 text-center d-none d-lg-block">
            <h1 class="font-weight-normal">{{ ucfirst(__('user.title.overview')).' '.Auth::user()->name }}</h1>
        </div>
        <!-- ENDE Titel für Tablet | PC -->
        <!-- Titel für Mobile Geräte -->
        <div class="p-lg-5 mx-auto my-1 text-center d-lg-none truncate">
            <h1 class="font-weight-normal">
                {{ ucfirst(__('user.title.overview')) }}
            </h1>
            <h4>
                {{ Auth::user()->name }}
            </h4>
        </div>
        <!-- ENDE Titel für Geräte | PC -->
        <div class="col-12">
            <div class="card">
                <div id="user-overview" class="card-body">
                    <ul class="nav nav-tabs" id="myTab" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link {{ ($page == 'myMap')? 'active' : '' }}" id="myMap-tab" data-toggle="tab" href="#myMap" role="tab" aria-controls="home" aria-selected="true">{{ __('ui.own.maps') }}</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ ($page == 'myAttackplanner')? 'active' : '' }}" id="myAttackplanner-tab" data-toggle="tab" href="#myAttackplanner" role="tab" aria-controls="myAttackplanner" aria-selected="false">{{ __('ui.own.attackplanner') }}</a>
                        </li>
                        @can('anim_hist_map_beta')
                        <li class="nav-item">
                            <a class="nav-link {{ ($page == 'myAnimatedMap')? 'active' : '' }}" id="myAnimatedMap-tab" data-toggle="tab" href="#myAnimatedMap" role="tab" aria-controls="myAnimatedMap" aria-selected="false">{{ __('ui.own.animatedMap') }}</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ ($page == 'myRenderedAnimatedMap')? 'active' : '' }}" id="myRenderedAnimatedMap-tab" data-toggle="tab" href="#myRenderedAnimatedMap" role="tab" aria-controls="myRenderedAnimatedMap" aria-selected="false">{{ __('ui.own.renderedAnimatedMap') }}</a>
                        </li>
                        @endcan
                        <li class="nav-item">
                            <a class="nav-link {{ ($page == 'followMap')? 'active' : '' }}" id="followMap-tab" data-toggle="tab" href="#followMap" role="tab" aria-controls="followMap" aria-selected="false">{{ __('ui.follow.map') }}</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ ($page == 'followAttackplanner')? 'active' : '' }}" id="followAttackplanner-tab" data-toggle="tab" href="#followAttackplanner" role="tab" aria-controls="followAttackplanner" aria-selected="false">{{ __('ui.follow.attackplanner') }}</a>
                        </li>
                    </ul>
                    <div class="tab-content" id="myTabContent">
                        {{--start own Map--}}
                        <div class="tab-pane fade {{ ($page == 'myMap')? 'show active' : '' }}" id="myMap" role="tabpanel" aria-labelledby="home-tab">
                            <div class="row mt-2">
                                <div class="col-4">
                                    @if(count($animatedMaps) > 0)
                                    <div class="list-group" id="ownMaps" role="tablist">
                                        @foreach($maps as $map)
                                            <a class="list-group-item list-group-item-action {{ ($maps->get(0)->id === $map->id)? 'active ': '' }}" id="map-{{ $map->id }}" data-toggle="list" onclick="switchMap('{{ $map->id }}', '{{ $map->edit_key }}', '{{ $map->show_key }}')" href="#previewMap" role="tab" aria-controls="home">
                                                <b>{{ $map->world->displayName() }}</b>
                                                <span class="float-right">{{ ($map->title === null)? __('ui.noTitle'): $map->title }}</span>
                                            </a>
                                        @endforeach
                                    </div>
                                    @else
                                    <div id="mapNoData">
                                        {{ __('ui.old.nodata') }}
                                    </div>
                                    @endif
                                </div>
                                <div class="col-6">
                                    @if (count($maps) > 0)
                                    <div class="tab-content" id="map-own-nav-tabContent">
                                        <div class="tab-pane fade show active" id="previewMap" role="tabpanel" aria-labelledby="list-home-list">
                                            <img alt="map" id="imgMap" src="{{ route('api.map.show.sized', [$maps->get(0)->id, $maps->get(0)->show_key, 500, 500, 'png']) }}">
                                        </div>
                                    </div>
                                    @endif
                                </div>
                                <div class="col-2">
                                    @if (count($maps) > 0)
                                    <div id="map-own-side-panel">
                                        <a id="editButtonMap" href="{{ route('tools.mapToolMode', [$maps->get(0)->id, 'edit', $maps->get(0)->edit_key]) }}" class="btn btn-success mb-2 w-100">{{ __('global.edit') }}</a>
                                        <a id="deleteButtonMap" data-toggle="confirmation" data-content="{{ __('user.confirm.destroy.mapContent') }}" class="btn btn-danger mb-2 w-100">{{ __('global.delete') }}</a>
                                        <label class="mt-3">{{ __('tool.map.editLink') }}:</label>
                                        <div class="input-group mb-2">
                                            <input id="editLinkMap" type="text" class="form-control" value="{{ route('tools.mapToolMode', [$maps->get(0)->id, 'edit', $maps->get(0)->edit_key]) }}">
                                            <div class="input-group-append">
                                                <span class="input-group-text" style="cursor:pointer" id="basic-addon2" onclick="copy('editLinkMap')"><i class="far fa-copy"></i></span>
                                            </div>
                                        </div>
                                        <label class="mt-3">{{ __('tool.map.showLink') }}:</label>
                                        <div class="input-group mb-2">
                                            <input id="showLinkMap" type="text" class="form-control" value="{{ route('tools.mapToolMode', [$maps->get(0)->id, 'show', $maps->get(0)->show_key]) }}">
                                            <div class="input-group-append">
                                                <span class="input-group-text" style="cursor:pointer" id="basic-addon2" onclick="copy('showLinkMap')"><i class="far fa-copy"></i></span>
                                            </div>
                                        </div>
                                    </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                        {{--end own Map--}}
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
                                                        <b>{{ $attackList->world->displayName() }}</b>
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
                        @can('anim_hist_map_beta')
                        {{--start own AnimatedMaps--}}
                        <div class="tab-pane fade {{ ($page == 'myAnimatedMap')? 'show active' : '' }}" id="myAnimatedMap" role="tabpanel" aria-labelledby="home-tab">
                            <div class="row mt-2">
                                <div class="col-4">
                                    @if(count($animatedMaps) > 0)
                                    <div class="list-group" id="ownAnimatedMap" role="tablist">
                                        @foreach($animatedMaps as $map)
                                            <a class="list-group-item list-group-item-action {{ ($animatedMaps->get(0)->id === $map->id)? 'active ': '' }}" id="animatedMap-{{ $map->id }}" data-toggle="list" onclick="switchAnimatedMap('{{ $map->id }}')" href="#previewAnimatedMap" role="tab" aria-controls="home">
                                                <b>{{ $map->world->displayName() }}</b>
                                                <span class="float-right">{{ ($map->title === null)? __('ui.noTitle'): $map->title }}</span>
                                            </a>
                                        @endforeach
                                    </div>
                                    @endif
                                    <div id="animatedMapNoData"{!! (count($animatedMaps)>0)?(' style="display: none"'):('') !!}>
                                        {{ __('ui.old.nodata') }}
                                    </div>
                                </div>
                                <div class="col-6">
                                    @if (count($animatedMaps) > 0)
                                    <div class="tab-content" id="animatedMap-own-nav-tabContent">
                                        <div class="tab-pane fade show active" id="previewAnimatedMap" role="tabpanel" aria-labelledby="list-home-list">
                                            <img width="500px" alt="map" id="imgAnimatedMap" src="{{ $animatedMaps->get(0)->preview() }}">
                                        </div>
                                    </div>
                                    @endif
                                </div>
                                <div class="col-2">
                                    @if (count($animatedMaps) > 0)
                                    <div id="animatedMap-own-side-panel">
                                        <a id="editButtonAnimatedMap" href="{{ route('tools.animHistMap.mode', [$animatedMaps->get(0)->id, 'edit', $animatedMaps->get(0)->edit_key]) }}" class="btn btn-success mb-2 w-100">{{ __('global.edit') }}</a>
                                        <a id="deleteButtonAnimatedMap" data-toggle="confirmation" data-content="{{ __('user.confirm.destroy.animatedMapContent') }}" class="btn btn-danger mb-2 w-100">{{ __('global.delete') }}</a>
                                        <label class="mt-3">{{ __('tool.animHistMap.editLink') }}:</label>
                                        <div class="input-group mb-2">
                                            <input id="editLinkAnimatedMap" type="text" class="form-control" value="{{ route('tools.animHistMap.mode', [$animatedMaps->get(0)->id, 'edit', $animatedMaps->get(0)->edit_key]) }}">
                                            <div class="input-group-append">
                                                <span class="input-group-text" style="cursor:pointer" id="basic-addon2" onclick="copy('editLinkAnimatedMap')"><i class="far fa-copy"></i></span>
                                            </div>
                                        </div>
                                        <label class="mt-3">{{ __('tool.animHistMap.showLink') }}:</label>
                                        <div class="input-group mb-2">
                                            <input id="showLinkAnimatedMap" type="text" class="form-control" value="{{ route('tools.animHistMap.mode', [$animatedMaps->get(0)->id, 'show', $animatedMaps->get(0)->show_key]) }}">
                                            <div class="input-group-append">
                                                <span class="input-group-text" style="cursor:pointer" id="basic-addon2" onclick="copy('showLinkAnimatedMap')"><i class="far fa-copy"></i></span>
                                            </div>
                                        </div>
                                    </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                        {{--end own AnimatedMaps--}}
                        {{--start own RenderedAnimatedMaps--}}
                        <div class="tab-pane fade {{ ($page == 'myRenderedAnimatedMap')? 'show active' : '' }}" id="myRenderedAnimatedMap" role="tabpanel" aria-labelledby="home-tab">
                            <div class="row mt-2">
                                <div class="col-4">
                                    @if(count($renderedAnimatedMaps) > 0)
                                    <div class="list-group" id="ownRenderedAnimatedMap" role="tablist">
                                        @foreach($renderedAnimatedMaps as $map)
                                            <a class="list-group-item list-group-item-action {{ ($renderedAnimatedMaps->get(0)->id === $map->id)? 'active ': '' }}" id="renderedAnimatedMap-{{ $map->id }}" data-toggle="list" onclick="switchRenderedAnimatedMap('{{ $map->id }}')" href="#previewRenderedAnimatedMap" role="tab" aria-controls="home">
                                                <b>{{ $map->world->displayName() }}</b>
                                                <span class="float-right">{{ ($map->title === null)? __('ui.noTitle'): $map->title }}</span>
                                            </a>
                                        @endforeach
                                    </div>
                                    @endif
                                    <div id="renderedAnimatedMapNoData"{!! (count($renderedAnimatedMaps)>0)?(' style="display: none"'):('') !!}>
                                        {{ __('ui.old.nodata') }}
                                    </div>
                                </div>
                                <div class="col-6">
                                    @if (count($renderedAnimatedMaps) > 0)
                                    <div class="tab-content" id="renderedAnimatedMap-own-nav-tabContent">
                                        <div class="tab-pane fade show active" id="previewRenderedAnimatedMap" role="tabpanel" aria-labelledby="list-home-list">
                                            <img width="500px" alt="map" id="imgRenderedAnimatedMap" src="{{ $renderedAnimatedMaps->get(0)->preview() }}">
                                        </div>
                                    </div>
                                    @endif
                                </div>
                                <div class="col-2">
                                    @if (count($renderedAnimatedMaps) > 0)
                                    <div id="renderedAnimatedMap-own-side-panel">
                                        <a id="editButtonRenderedAnimatedMap" href="{{ route("tools.animHistMap.renderStatus", [$renderedAnimatedMaps->get(0)->id, $renderedAnimatedMaps->get(0)->edit_key]) }}" class="btn btn-success mb-2 w-100">{{ __('global.edit') }}</a>
                                        <a id="deleteButtonRenderedAnimatedMap" data-toggle="confirmation" data-content="{{ __('user.confirm.destroy.renderedAnimatedMapContent') }}" class="btn btn-danger mb-2 w-100">{{ __('global.delete') }}</a>
                                    </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                        {{--end own RenderedAnimatedMaps--}}
                        @endcan
                        {{--start follow Map--}}
                        <div class="tab-pane fade {{ ($page == 'followMap')? 'show active' : '' }}" id="followMap" role="tabpanel" aria-labelledby="home-tab">
                            <div class="row mt-2">
                                <div class="col-4">
                                    <div class="list-group" id="ownAttacks" role="tablist">
                                        @if (count($mapsFollow) > 0)
                                            @foreach($mapsFollow as $map)
                                                <a class="list-group-item list-group-item-action {{ ($mapsFollow->get(0)->id === $map->id)? 'active ': '' }}" id="map-{{ $map->id }}" data-toggle="list" onclick="switchMap('{{ $map->id }}', null, '{{ $map->show_key }}', true)" href="#previewMap" role="tab" aria-controls="home">
                                                    <b>{{ $map->world->displayName() }}</b>
                                                    <span class="float-right">{{ ($map->title === null)? __('ui.noTitle'): $map->title }}</span>
                                                </a>
                                            @endforeach
                                        @else
                                            {{ __('ui.old.nodata') }}
                                        @endif
                                    </div>
                                </div>
                                <div class="col-6">
                                    @if (count($mapsFollow) > 0)
                                    <div class="tab-content" id="nav-tabContent">
                                        <div class="tab-pane fade show active" id="previewMap" role="tabpanel" aria-labelledby="list-home-list">
                                            <img alt="map" id="imgMapFollow" src="{{ route('api.map.show.sized', [$mapsFollow->get(0)->id, $mapsFollow->get(0)->show_key, 500, 500, 'png']) }}">
                                        </div>
                                    </div>
                                    @endif
                                </div>
                                <div class="col-2">
                                    @if (count($mapsFollow) > 0)
                                    <a id="showButtonMapFollow" href="{{ route('tools.mapToolMode', [$mapsFollow->get(0)->id, 'show', $mapsFollow->get(0)->show_key]) }}" class="btn btn-primary mb-2 w-100">{{ __('tool.map.show') }}</a>
                                    <label class="mt-3">{{ __('tool.map.showLink') }}:</label>
                                    <div class="input-group mb-2">
                                        <input id="showLinkMapFollow" type="text" class="form-control" value="{{ route('tools.mapToolMode', [$mapsFollow->get(0)->id, 'show', $mapsFollow->get(0)->show_key]) }}">
                                        <div class="input-group-append">
                                            <span class="input-group-text" style="cursor:pointer" id="basic-addon2" onclick="copy('showLinkMapFollow')"><i class="far fa-copy"></i></span>
                                        </div>
                                    </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                        {{--end follow Map--}}
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
                                                            <b>{{ $attackList->world->displayName() }}</b>
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
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('js')
    <script src="{{ asset('plugin/bootstrap-confirmation/bootstrap-confirmation.min.js') }}"></script>
    <script>
        $(document).ready(function () {
            $('.nav-link', $('#user-overview')).on("click", function (e) {
                var href = $(this).attr("href");
                history.pushState(null, null, href.replace('#', '/user/overview/'));
                e.preventDefault();
            });

            $('[data-toggle=confirmation]').confirmation({
                rootSelector: '[data-toggle=confirmation]',
                popout: true,
                title: "{{ __('user.confirm.destroy.title') }}",
                btnOkLabel: "{{ __('user.confirm.destroy.ok') }}",
                btnOkClass: 'btn-danger',
                btnCancelLabel: "{{ __('user.confirm.destroy.cancel') }}",
                btnCancelClass: 'btn-info',
            });
            $('#deleteButtonMap').on('confirmed.bs.confirmation', destroyMap);
            $('#deleteButtonAttackPlanner').on('confirmed.bs.confirmation', destroyAttackPlanner);
            @can('anim_hist_map_beta')
            $('#deleteButtonAnimatedMap').on('confirmed.bs.confirmation', destroyAnimatedMap);
            $('#deleteButtonRenderedAnimatedMap').on('confirmed.bs.confirmation', destroyRenderedAnimatedMap);
            @endcan
        })

        @if (count($maps) > 0)
        var mapId = '{{ $maps->get(0)->id }}';;
        var mapKey = '{{ $maps->get(0)->edit_key }}';
        @endif
        @if (count($attackLists) > 0)
        var attackPlannerId = '{{ $attackLists->get(0)->id }}';
        var attackPlannerKey = '{{ $attackLists->get(0)->edit_key }}';
        @endif

        function switchMap(id, edit_key, show_key, follow=false) {
            if (follow){
                $('#imgMapFollow').attr('src', '{{ route('index') }}/api/map/' + id + '/' + show_key + '/500-500.png');
                $('#showButtonMapFollow').attr('href', '{{ route('index') }}/tools/map/' + id + '/show/' + show_key);
                $('#showLinkMapFollow').val('{{ route('index') }}/tools/map/' + id + '/show/' + show_key);
            } else {
                $('#imgMap').attr('src', '{{ route('index') }}/api/map/' + id + '/' + show_key + '/500-500.png');
                $('#editButtonMap').attr('href', '{{ route('index') }}/tools/map/' + id + '/edit/' + edit_key);
                $('#editLinkMap').val('{{ route('index') }}/tools/map/' + id + '/edit/' + edit_key);
                $('#showLinkMap').val('{{ route('index') }}/tools/map/' + id + '/show/' + show_key);
                mapId = id;
                mapKey = edit_key;
            }
        }

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

        @can('anim_hist_map_beta')
        var animatedMapRoutes = {
            @foreach($animatedMaps as $map)
                {{ $map->id }}: [
                    "{{ $map->preview() }}",
                    "{{ route("tools.animHistMap.mode", [$map->id, 'edit', $map->edit_key]) }}",
                    "{{ route("tools.animHistMap.mode", [$map->id, 'show', $map->show_key]) }}",
                    "{{ route("tools.animHistMap.destroyAnimHistMapMap", [$map->id, $map->edit_key]) }}"
                ],
            @endforeach
        };
        
        @if (count($animatedMaps) > 0)
        var animatedMapDelete = '{{ route("tools.animHistMap.destroyAnimHistMapMap", [$animatedMaps->get(0)->id, $animatedMaps->get(0)->edit_key]) }}';
        var animatedMapId = '{{ $animatedMaps->get(0)->id }}';
        @endif
        
        function switchAnimatedMap(id) {
            $('#imgAnimatedMap').attr('src', animatedMapRoutes[id][0]);
            $('#editButtonAnimatedMap').attr('href', animatedMapRoutes[id][1]);
            $('#editLinkAnimatedMap').val(animatedMapRoutes[id][1]);
            $('#showLinkAnimatedMap').val(animatedMapRoutes[id][2]);
            animatedMapDelete = animatedMapRoutes[id][3];
            animatedMapId = id;
        }
        
        var renderedAnimatedMapRoutes = {
            @foreach($renderedAnimatedMaps as $map)
                {{ $map->id }}: [
                    "{{ $map->preview() }}",
                    "{{ route("tools.animHistMap.renderStatus", [$map->id, $map->edit_key]) }}",
                    "{{ route("tools.animHistMap.destroyAnimHistMapJob", [$map->id, $map->edit_key]) }}"
                ],
            @endforeach
        };
        
        @if (count($renderedAnimatedMaps) > 0)
        var renderedAnimatedMapDelete = '{{ route("tools.animHistMap.destroyAnimHistMapJob", [$renderedAnimatedMaps->get(0)->id, $renderedAnimatedMaps->get(0)->edit_key]) }}';
        var renderedAnimatedMapId = '{{ $renderedAnimatedMaps->get(0)->id }}';
        @endif
        
        function switchRenderedAnimatedMap(id) {
            $('#imgRenderedAnimatedMap').attr('src', renderedAnimatedMapRoutes[id][0]);
            $('#editButtonAnimatedMap').attr('href', renderedAnimatedMapRoutes[id][1]);
            renderedAnimatedMapDelete = renderedAnimatedMapRoutes[id][2];
            renderedAnimatedMapId = id;
        }
        @endcan

        function destroyMap() {
            axios.delete('{{ route('index') }}/tools/map/' + mapId + '/' + mapKey)
                .then((response) => {
                    var data = response.data;
                    createToast(data['msg'], "{{ __('tool.map.title') }}", "{{ __('global.now') }}");

                    $('#map-' + mapId).remove();
                    $('#ownMaps').children(':first').click();
                    if($('#ownMaps').children()[0] == undefined) {
                        $('#mapNoData').show();
                        $('#map-own-nav-tabContent').hide();
                        $('#map-own-side-panel').hide();
                    }
                })
                .catch((error) => {
                });
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
        
        @can('anim_hist_map_beta')
        function destroyAnimatedMap() {
            axios.delete(animatedMapDelete)
                .then((response) => {
                    var data = response.data;
                    createToast(data['msg'], "{{ __('tool.animHistMap.title') }}", "{{ __('global.now') }}");

                    $('#animatedMap-' + animatedMapId).remove();
                    $('#ownAnimatedMap').children().eq(0).click();
                    if($('#ownAnimatedMap').children()[0] == undefined) {
                        $('#animatedMapNoData').show();
                        $('#animatedMap-own-side-panel').hide();
                        $('#animatedMap-own-nav-tabContent').hide();
                    }
                })
                .catch((error) => {
                });
        }
        
        function destroyRenderedAnimatedMap() {
            axios.delete(renderedAnimatedMapDelete)
                .then((response) => {
                    var data = response.data;
                    createToast(data['msg'], "{{ __('tool.animHistMap.title') }}", "{{ __('global.now') }}");

                    $('#renderedAnimatedMap-' + renderedAnimatedMapId).remove();
                    $('#ownRenderedAnimatedMap').children().eq(0).click();
                    if($('#ownRenderedAnimatedMap').children()[0] == undefined) {
                        $('#renderedAnimatedMapNoData').show();
                        $('#renderedAnimatedMap-own-side-panel').hide();
                        $('#renderedAnimatedMap-own-nav-tabContent').hide();
                    }
                })
                .catch((error) => {
                });
        }
        @endcan

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
