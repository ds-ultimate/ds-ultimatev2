@extends('layouts.app')

@section('titel', $worldData->display_name,': '.__('tool.map.title'))

@push('style')
    <link href="{{ asset('plugin/bootstrap-colorpicker/bootstrap-colorpicker.min.css') }}" rel="stylesheet">
    <style>
        #map-popup {
            position: absolute;
            background-color: #ffffff90;
            padding: 5px;
            pointer-events: none;
        }
        #map-popup a {
            pointer-events: auto;
        }
    </style>
@endpush

@php
    if ($mode == 'edit'){
        $tabList = [
            'edit' => ['name' => __('tool.map.edit'), 'active' => true],
            'drawing' => ['name' => __('tool.map.drawing'), 'active' => false],
            'link' => ['name' => __('tool.map.links'), 'active' => false],
            'settings' => ['name' => __('tool.map.settings'), 'active' => false],
            'legend' => ['name' => __('tool.map.legend'), 'active' => false],
            ];
    }else{
        $tabList = [
            'legend' => ['name' => __('tool.map.legend'), 'active' => true],
            ];
    }
@endphp
@section('content')
    <div class="row justify-content-center">
        <!-- Titel für Tablet | PC -->
        <div class="col-12 p-lg-5 mx-auto my-1 text-center d-none d-lg-block">
            @auth
            <div class="col-2 position-absolute dropdown">
                <button class="btn btn-secondary dropdown-toggle" type="button" id="ownedMaps" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    {{ ucfirst(__('tool.map.fastSwitch')) }}
                </button>
                <div class="dropdown-menu" aria-labelledby="ownedMaps">
                    @foreach($ownMaps as $map)
                        <a class="dropdown-item" href="{{
                            route('tools.map.mode', [$map->id, 'edit', $map->edit_key])
                            }}">{{ $map->getTitle().' ['.$map->world->display_name.']' }}</a>
                    @endforeach
                </div>
            </div>
            @endauth
            <h1 class="font-weight-normal">{{ $wantedMap->getTitle().' ['.$worldData->display_name.']' }}</h1>
        </div>
        <!-- ENDE Titel für Tablet | PC -->
        <!-- Titel für Mobile Geräte -->
        <div class="p-lg-5 mx-auto my-1 text-center d-lg-none truncate">
            <h1 class="font-weight-normal">
                {{ $wantedMap->getTitle() }}
            </h1>
            <h4>
                {{ '['.$worldData->display_name.']' }}
            </h4>
        </div>
        <!-- ENDE Titel für Mobile Geräte -->
        <div class="col-12">
            @if($wantedMap->title === null && $mode == 'edit')
                <div class="card mt-2 p-3">
                    {{ __('tool.map.withoutTitle') }}
                </div>
            @endif
            @if($wantedMap->isCached() && $mode == 'edit')
                <div class="card mt-2 p-3">
                    {{ __('tool.map.cached') }}
                </div>
            @endif
            <div class="card mt-2">
                <form id="mapEditForm" action="{{ route('tools.map.mode', [$wantedMap->id, 'saveEdit', $wantedMap->edit_key]) }}" method="post">
                    @csrf
                    <ul class="nav nav-tabs" id="myTab" role="tablist">
                        @foreach($tabList as $key => $tab)
                            <li class="nav-item">
                                <a class="nav-link {{ ($tab['active'])?'active':'' }}" id="{{ $key }}-tab" data-toggle="tab" href="#{{ $key }}" role="tab" aria-controls="{{ $key }}" aria-selected="true">{{ $tab['name'] }}</a>
                            </li>
                        @endforeach
                    </ul>
                    <div class="card-body tab-content">
                        @foreach($tabList as $key => $tab)
                            @include('tools.map.'.$key, ['active' => $tab['active'], 'mapType' => "map"])
                        @endforeach
                    </div>
                </form>
            </div>
        </div>
        <div class="col-12 mt-2">
            <div class="card">
                @auth
                    @if($wantedMap->user_id != Auth::user()->id)
                        @if($wantedMap->follows()->where('user_id', Auth::user()->id)->count() > 0)
                            <div class="float-right position-absolute" style="right: 10px; top: 10px"><i id="follow-icon" style="cursor:pointer; text-shadow: 0 0 15px #000;" onclick="changeFollow()" class="fas fa-star h4 text-warning"></i></div>
                        @else
                            <div class="float-right position-absolute" style="right: 10px; top: 10px"><i id="follow-icon" style="cursor:pointer" onclick="changeFollow()" class="far text-muted fa-star h4 text-muted"></i></div>
                        @endif
                    @endif
                @endauth
                <ul class="nav nav-tabs" id="mapshowtabs" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link active map-show-tab" id="size-1-tab" data-toggle="tab" href="#size-1" role="tab" aria-controls="size-1" aria-selected="true">{{ '1000x1000' }}</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link map-show-tab" id="size-2-tab" data-toggle="tab" role="tab" href="#size-2" aria-controls="size-2" aria-selected="false">{{ '700x700' }}</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link map-show-tab" id="size-3-tab" data-toggle="tab" role="tab" href="#size-3" aria-controls="size-3" aria-selected="false">{{ '500x500' }}</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link map-show-tab" id="size-4-tab" data-toggle="tab" role="tab" href="#size-4" aria-controls="size-4" aria-selected="false">{{ '200x200' }}</a>
                    </li>
                </ul>
                <div class="card-body tab-content">
                    <div class="tab-pane fade show active map-show-content text-center" id="size-1" role="tabpanel" aria-labelledby="size-1-tab"></div>
                    <div class="tab-pane fade map-show-content text-center" id="size-2" role="tabpanel" aria-labelledby="size-2-tab"></div>
                    <div class="tab-pane fade map-show-content text-center" id="size-3" role="tabpanel" aria-labelledby="size-3-tab"></div>
                    <div class="tab-pane fade map-show-content text-center" id="size-4" role="tabpanel" aria-labelledby="size-4-tab"></div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('js')
@if($mode == 'edit')
<script src="{{ asset('plugin/bootstrap-colorpicker/bootstrap-colorpicker.min.js') }}"></script>
<script>
    $(function () {
        @if(! $wantedMap->isCached())
            $('#mapEditForm').on('submit', function (e) {
                e.preventDefault();
                store();
            });

            $('.colour-picker-map').on('colorpickerHide', store);
        @endif
    });

    @if(! $wantedMap->isCached())
        function addStoreNewElements(context) {
            $('.colour-picker-map', context).on('colorpickerHide', store);
        }

        var storing = false;
        var storeNeeded = false;
        function store() {
            if(storing) {
                storeNeeded = true;
                return;
            }
            storing = true;
            axios.post('{{ route('tools.map.mode', [$wantedMap->id, 'save', $wantedMap->edit_key]) }}', $('#mapEditForm').serialize())
                .then((response) => {
                    mapDimensions = [
                        response.data.xs,
                        response.data.ys,
                        response.data.w,
                        response.data.h,
                    ];

                    setTimeout(function() {
                        if(storeNeeded) {
                            storeNeeded = false
                            store();
                        }
                    }, 400);
                    storing = false;
                    reloadMap();
                    reloadDrawerBackground();
                })
                .catch((error) => {

                });
        }
    @endif

    var reloading = false;
    function reloadMap() {
        if(reloading) {
            reloadNeeded = true;
            return;
        }
        reloading = true;
        var elm = $('.active.map-show-content')[0];
        elm.style.widht = elm.clientWidth + "px";
        elm.style.height = elm.clientHeight + "px";
        $('.map-show-content').empty();
        $('.active.map-show-tab').trigger('click');
    }
</script>
@endif
<script>
    function copy(type) {
        /* Get the text field */
        var copyText = $("#link-" + type);
        /* Select the text field */
        copyText.select();
        /* Copy the text inside the text field */
        document.execCommand("copy");
    }
    
    var sizeRoutes = {
        "size-1": [
            "{{ route('api.map.show.sized', [$wantedMap->id, $wantedMap->show_key, '1000', '1000', 'base64']) }}",
            "{{ route('api.map.show.sized', [$wantedMap->id, $wantedMap->show_key, '1000', '1000', 'png']) }}",
            1000, 1000
        ],
        "size-2": [
            "{{ route('api.map.show.sized', [$wantedMap->id, $wantedMap->show_key, '700', '700', 'base64']) }}",
            "{{ route('api.map.show.sized', [$wantedMap->id, $wantedMap->show_key, '700', '700', 'png']) }}",
            700, 700
        ],
        "size-3": [
            "{{ route('api.map.show.sized', [$wantedMap->id, $wantedMap->show_key, '500', '500', 'base64']) }}",
            "{{ route('api.map.show.sized', [$wantedMap->id, $wantedMap->show_key, '500', '500', 'png']) }}",
            500, 500
        ],
        "size-4": [
            "{{ route('api.map.show.sized', [$wantedMap->id, $wantedMap->show_key, '200', '200', 'base64']) }}",
            "{{ route('api.map.show.sized', [$wantedMap->id, $wantedMap->show_key, '200', '200', 'png']) }}",
            200, 200
        ],
    };

    //define here since we are refering to it
    var reloadNeeded = false;

    $('.map-show-tab').click(function (e) {
        var targetID = this.attributes['aria-controls'].nodeValue;
        if($('#'+targetID)[0].innerHTML.length > 0) return;

        $.ajax({
            type: "GET",
            url: sizeRoutes[targetID][0] + "?" + Math.floor(Math.random() * 9000000 + 1000000),
            success: function(data){
                $('#'+targetID).html(
                    '<div class="form-group row">' +
                        '<label class="control-label col-6 col-md-3 col-lg-2">{{ ucfirst(__('tool.map.forumLink')) }}</label>' +
                        '<div class="col-6 col-md-1">' +
                            '<a class="float-right btn btn-primary btn-sm" onclick="copy(\''+targetID+'\')">{{ ucfirst(__('tool.map.copy')) }}</a>' +
                        '</div>' +
                        '<div class="col-md-8 col-lg-9">' +
                            '<input id="link-'+targetID+'" type="text" class="border form-control-plaintext form-control-sm disabled" value="[url={{ route('tools.map.mode', [$wantedMap->id, 'show', $wantedMap->show_key]) }}][img]'+sizeRoutes[targetID][1]+'[/img][/url]" />' +
                            '<small class="form-control-feedback">{{ ucfirst(__('tool.map.forumLinkDesc')) }}</small>' +
                        '</div>' +
                    '</div>' +
                    '<img id="'+targetID+'-img" class="p-0" src="' + data + '" />'
                );

                $('#'+targetID+'-img').click(function(e) {
                    mapClicked(e, this, targetID, sizeRoutes[targetID][2], sizeRoutes[targetID][3]);
                });

                setTimeout(function() {
                    var elm = $('.active.map-show-content')[0];
                    elm.style.widht = "";
                    elm.style.height = "";
                    @if($mode == 'edit')
                        reloading = false;
                        if(reloadNeeded) {
                            reloadNeeded = false;
                            reloadMap();
                        }
                    @endif
                }, 500);
            },
        });
    });

    var mapDimensions = [
        {{$mapDimensions['xs']}},
        {{$mapDimensions['ys']}},
        {{$mapDimensions['w']}},
        {{$mapDimensions['h']}},
    ];

    function mapClicked(e, that, targetID, xSize, ySize) {
        var xPerc = (e.pageX - $(that).offset().left) / xSize;
        var yPerc = (e.pageY - $(that).offset().top) / ySize;

        var mapX = Math.floor( mapDimensions[0] + mapDimensions[2]*xPerc );
        var mapY = Math.floor( mapDimensions[1] + mapDimensions[3]*yPerc );


        if($('#map-popup')[0]) {
            $('#map-popup').remove();
        }

        axios.get('{{ route('index') }}/api/{{ $worldData->server->code }}/{{ $worldData->name }}/villageCoords/'+ mapX + '/' + mapY, {
        })
            .then((response) => {
                const data = response.data.data;
                var xRel = e.pageX - $($('#size-1')[0].parentElement.parentElement).offset().left;
                var yRel = e.pageY - $($('#size-1')[0].parentElement.parentElement).offset().top;

                var popupHTML = '<div id="map-popup">'+
                    '{{ ucfirst(__('ui.table.name')) }}: <a href="'+data.selfLink+'" target="_blank">'+data.name+'</a><br>'+
                    '{{ ucfirst(__('ui.table.points')) }}: '+data.points+'<br>'+
                    '{{ ucfirst(__('ui.table.coordinates')) }}: '+data.coordinates+'<br>';

                if(data.owner != 0) {
                    popupHTML += '{{ ucfirst(__('ui.table.owner')) }}: <a href="'+data.ownerLink+'" target="_blank">'+data.ownerName+'</a><br>';
                } else {
                    popupHTML += '{{ ucfirst(__('ui.table.owner')) }}: '+data.ownerName+'<br>';
                }
                if(data.ownerAlly != 0) {
                    popupHTML += '{{ ucfirst(__('ui.table.ally')) }}: <a href="'+data.ownerAllyLink+'" target="_blank">'+data.ownerAllyName+
                        '['+data.ownerAllyTag+']</a><br>';
                } else {
                    popupHTML += '{{ ucfirst(__('ui.table.ally')) }}: '+data.ownerAllyName+'<br>';
                }
                popupHTML += "{{ ucfirst(__('ui.table.conquer')) }}: "+data.conquer+"<br></div>";
                $('#'+targetID).append(popupHTML);

                $('#map-popup')[0].style.left = xRel+"px";
                $('#map-popup')[0].style.top = yRel+"px";
            })
            .catch((error) => {
            });
    }

    $(function () {
        $('.active.map-show-tab').trigger('click');
    });

    @auth
        @if($wantedMap->user_id != Auth::user()->id)
            function changeFollow() {
                var icon = $('#follow-icon');
                axios.post('{{ route('tools.follow') }}',{
                    model: 'Map_Map',
                    id: '{{ $wantedMap->id }}'
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
</script>
@endpush
