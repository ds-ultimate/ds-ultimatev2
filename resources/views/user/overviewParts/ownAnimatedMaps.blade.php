{{--start own AnimatedMaps--}}
<div class="tab-pane fade {{ ($page == 'myAnimatedMap')? 'show active' : '' }}" id="myAnimatedMap" role="tabpanel" aria-labelledby="home-tab">
    <div class="row mt-2">
        <div class="col-4">
            @if(count($animatedMaps) > 0)
            <div class="list-group" id="ownAnimatedMap" role="tablist">
                @foreach($animatedMaps as $map)
                    <a class="list-group-item list-group-item-action {{ ($animatedMaps->get(0)->id === $map->id)? 'active ': '' }}" id="animatedMap-{{ $map->id }}" data-toggle="list" onclick="switchAnimatedMap('{{ $map->id }}')" href="#previewAnimatedMap" role="tab" aria-controls="home">
                        <b>{{ $map->world->getDistplayName() }}</b>
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

@push('js')
    <script>
        $(document).ready(function () {
            $('#deleteButtonAnimatedMap').on('confirmed.bs.confirmation', destroyAnimatedMap);
        })

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
    </script>
@endpush
