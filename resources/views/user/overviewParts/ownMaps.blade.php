{{--start own Map--}}
<div class="tab-pane fade {{ ($page == 'myMap')? 'show active' : '' }}" id="myMap" role="tabpanel" aria-labelledby="home-tab">
    <div class="row mt-2">
        <div class="col-4">
            @if(count($maps) > 0)
            <div class="list-group" id="ownMaps" role="tablist">
                @foreach($maps as $map)
                    <a class="list-group-item list-group-item-action {{ ($maps->get(0)->id === $map->id)? 'active ': '' }}" id="map-{{ $map->id }}" data-toggle="list" onclick="switchMap('{{ $map->id }}', '{{ $map->edit_key }}', '{{ $map->show_key }}')" href="#previewMap" role="tab" aria-controls="home">
                        <b>{{ $map->world->display_name }}</b>
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
                <a id="editButtonMap" href="{{ route('tools.map.mode', [$maps->get(0)->id, 'edit', $maps->get(0)->edit_key]) }}" class="btn btn-success mb-2 w-100">{{ __('global.edit') }}</a>
                <a id="deleteButtonMap" data-toggle="confirmation" data-content="{{ __('user.confirm.destroy.mapContent') }}" class="btn btn-danger mb-2 w-100">{{ __('global.delete') }}</a>
                <label class="mt-3">{{ __('tool.map.editLink') }}:</label>
                <div class="input-group mb-2">
                    <input id="editLinkMap" type="text" class="form-control" value="{{ route('tools.map.mode', [$maps->get(0)->id, 'edit', $maps->get(0)->edit_key]) }}">
                    <div class="input-group-append">
                        <span class="input-group-text" style="cursor:pointer" id="basic-addon2" onclick="copy('editLinkMap')"><i class="far fa-copy"></i></span>
                    </div>
                </div>
                <label class="mt-3">{{ __('tool.map.showLink') }}:</label>
                <div class="input-group mb-2">
                    <input id="showLinkMap" type="text" class="form-control" value="{{ route('tools.map.mode', [$maps->get(0)->id, 'show', $maps->get(0)->show_key]) }}">
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

@push('js')
    <script>
        $(document).ready(function () {
            $('#deleteButtonMap').on('confirmed.bs.confirmation', destroyMap);
        })

        @if (count($maps) > 0)
        var mapId = '{{ $maps->get(0)->id }}';;
        var mapKey = '{{ $maps->get(0)->edit_key }}';
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
    </script>
@endpush
