@extends('layouts.temp')

@section('titel', ucfirst(__('ui.tabletitel.overview')).' von '.Auth::user()->name)

@section('content')
    <div class="row justify-content-center">
        <!-- Titel fÃ¼r Tablet | PC -->
        <div class="p-lg-5 mx-auto my-1 text-center d-none d-lg-block">
            <h1 class="font-weight-normal">{{ ucfirst(__('ui.tabletitel.overview')).' von '.Auth::user()->name }}</h1>
        </div>
        <!-- ENDE Titel fÃ¼r Tablet | PC -->
        <!-- Titel fÃ¼r Mobile GerÃ¤te -->
        <div class="p-lg-5 mx-auto my-1 text-center d-lg-none truncate">
            <h1 class="font-weight-normal">
                {{ ucfirst(__('ui.tabletitel.overview')).' von ' }}
            </h1>
            <h4>
                {{ Auth::user()->name }}
            </h4>
        </div>
        <!-- ENDE Titel fÃ¼r Tablet | PC -->
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <ul class="nav nav-tabs" id="myTab" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link {{ ($page == 'myMap')? 'active' : '' }}" id="myMap-tab" data-toggle="tab" href="#myMap" role="tab" aria-controls="home" aria-selected="true">{{ __('ui.own.maps') }}</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ ($page == 'myAttackplanner')? 'active' : '' }}" id="myAttackplanner-tab" data-toggle="tab" href="#myAttackplanner" role="tab" aria-controls="myAttackplanner" aria-selected="false">{{ __('ui.own.attackplanner') }}</a>
                        </li>
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
                                    <div class="list-group" id="ownMaps" role="tablist">
                                        @if (count($maps) > 0)
                                            @foreach($maps as $map)
                                                <a class="list-group-item list-group-item-action {{ ($maps->get(0)->id === $map->id)? 'active ': '' }}" id="{{ $map->id }}" data-toggle="list" onclick="switchMap('{{ $map->id }}', '{{ $map->edit_key }}', '{{ $map->show_key }}')" href="#previewMap" role="tab" aria-controls="home">
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
                                    @if (count($maps) > 0)
                                    <div class="tab-content" id="nav-tabContent">
                                        <div class="tab-pane fade show active" id="previewMap" role="tabpanel" aria-labelledby="list-home-list">
                                            <img alt="map" id="imgMap" src="{{ route('api.map.show.sized', [$maps->get(0)->id, $maps->get(0)->show_key, 500, 500, 'png']) }}">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-2">
                                    <a id="editButtonMap" href="{{ route('tools.mapToolMode', [$maps->get(0)->id, 'edit', $maps->get(0)->edit_key]) }}" class="btn btn-success mb-2 w-100">{{ __('global.edit') }}</a>
                                    {{--<a id="deleteButtonMap" onclick="destroyMap()" class="btn btn-danger mb-2 w-100">{{ __('global.delete') }}</a>--}}
                                    <label class="mt-3">{{ __('tool.map.editLink') }}:</label>
                                    <div class="input-group mb-2">
                                        <input id="editLinkMap" type="text" class="form-control" value="{{ route('tools.mapToolMode', [$maps->get(0)->id, 'edit', $maps->get(0)->edit_key]) }}" aria-label="Recipient's username" aria-describedby="basic-addon2">
                                        <div class="input-group-append">
                                            <span class="input-group-text" style="cursor:pointer" id="basic-addon2" onclick="copy('editLinkMap')"><i class="far fa-copy"></i></span>
                                        </div>
                                    </div>
                                    <label class="mt-3">{{ __('tool.map.showLink') }}:</label>
                                    <div class="input-group mb-2">
                                        <input id="showLinkMap" type="text" class="form-control" value="{{ route('tools.mapToolMode', [$maps->get(0)->id, 'show', $maps->get(0)->show_key]) }}" aria-label="Recipient's username" aria-describedby="basic-addon2">
                                        <div class="input-group-append">
                                            <span class="input-group-text" style="cursor:pointer" id="basic-addon2" onclick="copy('showLinkMap')"><i class="far fa-copy"></i></span>
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
                                        @if (count($attackLists) > 0)
                                            @foreach($attackLists as $attackList)
                                                <a class="list-group-item list-group-item-action {{ ($attackLists->get(0)->id === $attackList->id)? 'active ': '' }}" id="{{ $attackList->id }}" onclick="switchAttackPlanner('{{ $attackList->id }}', '{{ $attackList->edit_key }}', '{{ $attackList->show_key }}')" data-toggle="list" role="tab" aria-controls="home">
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
                                    @if (count($attackLists) > 0)
                                        <a id="editButtonAttackPlanner" href="{{ route('tools.attackPlannerMode', [$attackLists->get(0)->id, 'edit', $attackLists->get(0)->edit_key]) }}" class="btn btn-success mb-2 w-100">{{ __('global.edit') }}</a>
                                        {{--<a id="deleteButtonAttackPlanner" onclick="destroyAttackPlanner()" class="btn btn-danger mb-2 w-100">{{ __('global.delete') }}</a>--}}
                                        <label class="mt-3">{{ __('tool.map.editLink') }}:</label>
                                        <div class="input-group mb-2">
                                            <input id="editLinkAttackPlanner" type="text" class="form-control" value="{{ route('tools.attackPlannerMode', [$attackLists->get(0)->id, 'edit', $attackLists->get(0)->edit_key]) }}" aria-label="Recipient's username" aria-describedby="basic-addon2">
                                            <div class="input-group-append">
                                                <span class="input-group-text" style="cursor:pointer" id="basic-addon2" onclick="copy('editLinkAttackPlanner')"><i class="far fa-copy"></i></span>
                                            </div>
                                        </div>
                                        <label class="mt-3">{{ __('tool.map.showLink') }}:</label>
                                        <div class="input-group mb-2">
                                            <input id="showLinkAttackPlanner" type="text" class="form-control" value="{{ route('tools.attackPlannerMode', [$attackLists->get(0)->id, 'show', $attackLists->get(0)->show_key]) }}" aria-label="Recipient's username" aria-describedby="basic-addon2">
                                            <div class="input-group-append">
                                                <span class="input-group-text" style="cursor:pointer" id="basic-addon2" onclick="copy('showLinkAttackPlanner')"><i class="far fa-copy"></i></span>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                        {{--end own AttackList--}}
                        {{--start follow Map--}}
                        <div class="tab-pane fade {{ ($page == 'followMap')? 'show active' : '' }}" id="followMap" role="tabpanel" aria-labelledby="home-tab">
                            <div class="row mt-2">
                                <div class="col-4">
                                    <div class="list-group" id="ownMaps" role="tablist">
                                        @if (count($mapsFollow) > 0)
                                            @foreach($mapsFollow as $map)
                                                <a class="list-group-item list-group-item-action {{ ($mapsFollow->get(0)->id === $map->id)? 'active ': '' }}" id="{{ $map->id }}" data-toggle="list" onclick="switchMap('{{ $map->id }}', null, '{{ $map->show_key }}', true)" href="#previewMap" role="tab" aria-controls="home">
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
                                </div>
                                <div class="col-2">
                                    <a id="showButtonMapFollow" href="{{ route('tools.mapToolMode', [$mapsFollow->get(0)->id, 'show', $mapsFollow->get(0)->show_key]) }}" class="btn btn-primary mb-2 w-100">{{ __('tool.map.show') }}</a>
                                    <label class="mt-3">{{ __('tool.map.showLink') }}:</label>
                                    <div class="input-group mb-2">
                                        <input id="showLinkMapFollow" type="text" class="form-control" value="{{ route('tools.mapToolMode', [$mapsFollow->get(0)->id, 'show', $mapsFollow->get(0)->show_key]) }}" aria-label="Recipient's username" aria-describedby="basic-addon2">
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
                                        @if (count($attackListsFollow) > 0)
                                            @foreach($attackListsFollow as $attackList)
                                                <a class="list-group-item list-group-item-action {{ ($attackListsFollow->get(0)->id === $attackList->id)? 'active ': '' }}" id="{{ $attackList->id }}" onclick="switchAttackPlanner('{{ $attackList->id }}', null, '{{ $attackList->show_key }}', true)" data-toggle="list" role="tab" aria-controls="home">
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
                                            <input id="showLinkAttackPlannerFollow" type="text" class="form-control" value="{{ route('tools.attackPlannerMode', [$attackListsFollow->get(0)->id, 'show', $attackListsFollow->get(0)->show_key]) }}" aria-label="Recipient's username" aria-describedby="basic-addon2">
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

@section('js')
    <script>
        $(document).ready(function () {
            $('.nav-link').on("click", function (e) {
                var href = $(this).attr("href");
                history.pushState(null, null, href.replace('#', '/user/overview/'));
                e.preventDefault();
            });
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

        function destroyMap() {
            console.log(mapId + '____' + mapKey);
        }

        function destroyAttackPlanner() {
            console.log(attackPlannerId + '____' + attackPlannerKey);
        }

        function copy(type) {
            /* Get the text field */
            var copyText = $("#" + type);
            /* Select the text field */
            copyText.select();
            /* Copy the text inside the text field */
            document.execCommand("copy");
        }
    </script>
@stop
