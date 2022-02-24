{{--start follow Map--}}
<div class="tab-pane fade {{ ($page == 'followMap')? 'show active' : '' }}" id="followMap" role="tabpanel" aria-labelledby="home-tab">
    <div class="row mt-2">
        <div class="col-4">
            <div class="list-group" id="ownAttacks" role="tablist">
                @if (count($mapsFollow) > 0)
                    @foreach($mapsFollow as $map)
                        <a class="list-group-item list-group-item-action {{ ($mapsFollow->get(0)->id === $map->id)? 'active ': '' }}" id="map-{{ $map->id }}" data-toggle="list" onclick="switchMap('{{ $map->id }}', null, '{{ $map->show_key }}', true)" href="#previewMap" role="tab" aria-controls="home">
                            <b>{{ $map->world->display_name }}</b>
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
            <a id="showButtonMapFollow" href="{{ route('tools.map.mode', [$mapsFollow->get(0)->id, 'show', $mapsFollow->get(0)->show_key]) }}" class="btn btn-primary mb-2 w-100">{{ __('tool.map.show') }}</a>
            <label class="mt-3">{{ __('tool.map.showLink') }}:</label>
            <div class="input-group mb-2">
                <input id="showLinkMapFollow" type="text" class="form-control" value="{{ route('tools.map.mode', [$mapsFollow->get(0)->id, 'show', $mapsFollow->get(0)->show_key]) }}">
                <div class="input-group-append">
                    <span class="input-group-text" style="cursor:pointer" id="basic-addon2" onclick="copy('showLinkMapFollow')"><i class="far fa-copy"></i></span>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
{{--end follow Map--}}
