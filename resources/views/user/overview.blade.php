@extends('layouts.temp')

@section('content')
    <div class="row justify-content-center">
        <!-- Titel für Tablet | PC -->
        <div class="p-lg-5 mx-auto my-1 text-center d-none d-lg-block">
            <h1 class="font-weight-normal">{{ ucfirst(__('ui.tabletitel.overview')).' von '.Auth::user()->name }}</h1>
        </div>
        <!-- ENDE Titel für Tablet | PC -->
        <!-- Titel für Mobile Geräte -->
        <div class="p-lg-5 mx-auto my-1 text-center d-lg-none truncate">
            <h1 class="font-weight-normal">
                {{ ucfirst(__('ui.tabletitel.overview')).' von ' }}
            </h1>
            <h4>
                {{ Auth::getName() }}
            </h4>
        </div>
        <!-- ENDE Titel für Tablet | PC -->
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <ul class="nav nav-tabs" id="myTab" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" id="home-tab" data-toggle="tab" href="#myMap" role="tab" aria-controls="home" aria-selected="true">{{ __('ui.own.maps') }}</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="profile-tab" data-toggle="tab" href="#myAttackplanner" role="tab" aria-controls="profile" aria-selected="false">{{ __('ui.own.attackplanner') }}</a>
                        </li>
                    </ul>
                    <div class="tab-content" id="myTabContent">
                        <div class="tab-pane fade show active" id="myMap" role="tabpanel" aria-labelledby="home-tab">
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
                                            {{ __('ui.own.noMap') }}
                                        @endif
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="tab-content" id="nav-tabContent">
                                        <div class="tab-pane fade show active" id="previewMap" role="tabpanel" aria-labelledby="list-home-list">
                                            <img id="imgMap" src="{{ route('api.map.show.sized', [$maps->get(0)->id, $maps->get(0)->show_key, 500, 500, 'png']) }}">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-2">
                                    <a id="editButtonMap" href="{{ route('tools.mapToolMode', [$maps->get(0)->id, 'edit', $maps->get(0)->edit_key]) }}" class="btn btn-success mb-2 w-100">{{ __('global.edit') }}</a>
                                    <a id="deleteButtonMap" href="" class="btn btn-danger mb-2 w-100">{{ __('global.delete') }}</a>
                                    <label class="mt-3">{{ __('ui.tool.map.editLink') }}:</label>
                                    <div class="input-group mb-2">
                                        <input id="editLinkMap" type="text" class="form-control" value="{{ route('tools.mapToolMode', [$maps->get(0)->id, 'edit', $maps->get(0)->edit_key]) }}" aria-label="Recipient's username" aria-describedby="basic-addon2">
                                        <div class="input-group-append">
                                            <span class="input-group-text" style="cursor:pointer" id="basic-addon2" onclick="copy('editLinkMap')"><i class="far fa-copy"></i></span>
                                        </div>
                                    </div>
                                    <label class="mt-3">{{ __('ui.tool.map.showLink') }}:</label>
                                    <div class="input-group mb-2">
                                        <input id="showLinkMap" type="text" class="form-control" value="{{ route('tools.mapToolMode', [$maps->get(0)->id, 'show', $maps->get(0)->show_key]) }}" aria-label="Recipient's username" aria-describedby="basic-addon2">
                                        <div class="input-group-append">
                                            <span class="input-group-text" style="cursor:pointer" id="basic-addon2" onclick="copy('showLinkMap')"><i class="far fa-copy"></i></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane fade" id="myAttackplanner" role="tabpanel" aria-labelledby="profile-tab">
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
                                            {{ __('ui.own.noAttackplanner') }}
                                        @endif
                                    </div>
                                </div>
                                <div class="col-2">
                                    <a id="editButtonAttackPlanner" href="{{ route('tools.attackPlannerMode', [$attackLists->get(0)->id, 'edit', $attackLists->get(0)->edit_key]) }}" class="btn btn-success mb-2 w-100">{{ __('global.edit') }}</a>
                                    <a id="deleteButtonAttackPlanner" href="" class="btn btn-danger mb-2 w-100">{{ __('global.delete') }}</a>
                                    <label class="mt-3">{{ __('ui.tool.map.editLink') }}:</label>
                                    <div class="input-group mb-2">
                                        <input id="editLinkAttackPlanner" type="text" class="form-control" value="{{ route('tools.attackPlannerMode', [$attackLists->get(0)->id, 'edit', $attackLists->get(0)->edit_key]) }}" aria-label="Recipient's username" aria-describedby="basic-addon2">
                                        <div class="input-group-append">
                                            <span class="input-group-text" style="cursor:pointer" id="basic-addon2" onclick="copy('editLinkAttackPlanner')"><i class="far fa-copy"></i></span>
                                        </div>
                                    </div>
                                    <label class="mt-3">{{ __('ui.tool.map.showLink') }}:</label>
                                    <div class="input-group mb-2">
                                        <input id="showLinkAttackPlanner" type="text" class="form-control" value="{{ route('tools.attackPlannerMode', [$attackLists->get(0)->id, 'show', $attackLists->get(0)->show_key]) }}" aria-label="Recipient's username" aria-describedby="basic-addon2">
                                        <div class="input-group-append">
                                            <span class="input-group-text" style="cursor:pointer" id="basic-addon2" onclick="copy('showLinkAttackPlanner')"><i class="far fa-copy"></i></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('js')
    <script>
        function switchMap(id, edit_key, show_key) {
            $('#imgMap').attr('src', '{{ route('index') }}/api/map/' + id + '/' + show_key + '/500-500.png');
            $('#editButtonMap').attr('href', '{{ route('index') }}/tools/map/' + id + '/edit/' + edit_key);
            $('#editLinkMap').val('{{ route('index') }}/tools/map/' + id + '/edit/' + edit_key);
            $('#showLinkMap').val('{{ route('index') }}/tools/map/' + id + '/show/' + show_key);
        }

        function switchAttackPlanner(id, edit_key, show_key) {
            $('#editButtonAttackPlanner').attr('href', '{{ route('index') }}/tools/attackPlanner/' + id + '/edit/' + edit_key);
            $('#editLinkAttackPlanner').val('{{ route('index') }}/tools/attackPlanner/' + id + '/edit/' + edit_key);
            $('#showLinkAttackPlanner').val('{{ route('index') }}/tools/attackPlanner/' + id + '/show/' + show_key);
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
