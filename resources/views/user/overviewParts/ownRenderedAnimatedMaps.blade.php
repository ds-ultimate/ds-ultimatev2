{{--start own RenderedAnimatedMaps--}}
<div class="tab-pane fade {{ ($page == 'myRenderedAnimatedMap')? 'show active' : '' }}" id="myRenderedAnimatedMap" role="tabpanel" aria-labelledby="home-tab">
    <div class="row mt-2">
        <div class="col-4">
            @if(count($renderedAnimatedMaps) > 0)
            <div class="list-group" id="ownRenderedAnimatedMap" role="tablist">
                @foreach($renderedAnimatedMaps as $map)
                    <a class="list-group-item list-group-item-action {{ ($renderedAnimatedMaps->get(0)->id === $map->id)? 'active ': '' }}" id="renderedAnimatedMap-{{ $map->id }}" data-toggle="list" onclick="switchRenderedAnimatedMap('{{ $map->id }}')" href="#previewRenderedAnimatedMap" role="tab" aria-controls="home">
                        <b>{{ $map->world->display_name }}</b>
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

@push('js')
    <script>
        $(document).ready(function () {
            $('#deleteButtonRenderedAnimatedMap').on('confirmed.bs.confirmation', destroyRenderedAnimatedMap);
        })
        
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
            $('#editButtonRenderedAnimatedMap').attr('href', renderedAnimatedMapRoutes[id][1]);
            renderedAnimatedMapDelete = renderedAnimatedMapRoutes[id][2];
            renderedAnimatedMapId = id;
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
    </script>
@endpush
