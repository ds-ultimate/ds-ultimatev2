@extends('layouts.app')

@section('titel', __('ui.titel.worldOverview'))

@section('content')
    <div class="row justify-content-center">
        <div class="col-12">
            <div class="col-md-5 p-lg-5 mx-auto my-1 text-center">
                <h1 class="font-weight-normal">{{ ucfirst(__('ui.titel.worldOverview')) }}</h1>
            </div>
        </div>
        <!-- Normale Welten -->
        <div class="col-12 col-lg-6 mt-2">
            <div class="card">
                <div class="card-body">
                    @if(isset($worldsActive['world']) && count($worldsActive['world']) > 0)
                    <h2 class="card-title">{{ __('ui.tabletitel.normalWorlds') }}:</h2>
                    <x-server_table :data="$worldsActive['world']"/>
                    @endif
                    @if (isset($worldsInactive['world']) && count($worldsInactive['world']) > 0)
                        <div class="w-100 text-center my-3">
                            <button class="btn btn-secondary btn-sm" data-toggle="collapse" data-target="#inactive1" aria-expanded="false" aria-controls="inactive1" type="button">
                                {{__('ui.showMoreWorlds')}}</button>
                        </div>
                        <div class="collapse inactive" id="inactive1">
                            <h2 class="card-title">{{ __('ui.tabletitel.normalWorlds').' '.__('ui.archive') }}:</h2>
                            <x-server_table :data="$worldsInactive['world']"/>
                        </div>
                        @endif
                </div>
            </div>
        </div>
        <!-- ENDE Normale Welten -->
        <!-- Spezial Welten -->
        <div class="col-12 col-lg-6 mt-2">
            <div class="card">
                <div class="card-body">
                    @if (isset($worldsActive['special']) && count($worldsActive['special']) > 0)
                    <h2 class="card-title">{{ __('ui.tabletitel.specialWorlds') }}:</h2>
                    <x-server_table :data="$worldsActive['special']"/>
                    @endif
                    @if (isset($worldsInactive['special']) && count($worldsInactive['special']) > 0)
                        <div class="w-100 text-center my-3">
                            <button class="btn btn-secondary btn-sm" data-toggle="collapse" data-target="#inactive2" aria-expanded="false" aria-controls="inactive2" type="button">
                                {{__('ui.showMoreWorlds')}}</button>
                        </div>
                        <div class="collapse inactive" id="inactive2">
                            <h2 class="card-title">{{ __('ui.tabletitel.specialWorlds').' '.__('ui.archive') }}:</h2>
                            <x-server_table :data="$worldsInactive['special']"/>
                        </div>
                    @endif
                </div>
            </div>
        </div>
        <!-- ENDE Spezial Welten -->
    </div>
@endsection

@push('js')
<script>
    $('.inactive').on('show.bs.collapse', function (e) {
        $('button[aria-controls=' + $(e.currentTarget).attr('id') + ']').html('{{__('ui.showLessWorlds')}}')
    })
    $('.inactive').on('hide.bs.collapse', function (e) {
        $('button[aria-controls=' + $(e.currentTarget).attr('id') + ']').html('{{__('ui.showMoreWorlds')}}')
    })
    $(document).on("click", '.btn[data-toggle="collapse"]', resize)
    $(window).on('resize', resize)
    
    function resize () {
        $('.world-table').each((_, elm) => {
            $('.server-truncate', elm).css('max-width', 'initial')
            var sum = 0, first = -1
            $('thead>tr>th', elm).each((_, elm) => {
                if(first == -1) {
                    first = $(elm).width()
                }
                sum += $(elm).width()
            })

            var ow = $(elm).parent().width()
            if(sum <= ow) return //is ok

            first = Math.max(first - (sum - ow), 100)
            $('.server-truncate', elm).css('max-width', first + "px")
        })
    }
</script>
@endpush
