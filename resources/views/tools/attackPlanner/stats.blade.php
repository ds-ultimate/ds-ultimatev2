<div class="tab-pane fade {{ ($active)? 'show active':'' }}" id="{{ $key }}" role="tabpanel" aria-labelledby="{{ $key }}-tab">
    <div class="row pt-3">
        <div class="col-1"></div>
        <div class="col-4">
            <h3>{{ __('ui.tabletitel.general') }}</h3>
            <div class="form-group">
                {{ __('tool.attackPlanner.attackTotal') }}: <b id="attackTotal" class="float-right">{{ \App\Util\BasicFunctions::numberConv($stats['total']) }}</b>
            </div>
            <div class="form-group">
                {{ __('tool.attackPlanner.attackStart_village') }}: <b id="attackStart_village" class="float-right">{{ \App\Util\BasicFunctions::numberConv($stats['start_village']) }}</b>
            </div>
            <div class="form-group">
                {{ __('tool.attackPlanner.attackTarget_village') }}: <b id="attackTarget_village" class="float-right">{{ \App\Util\BasicFunctions::numberConv($stats['target_village']) }}</b>
            </div>
        </div>
        <div class="col-1"></div>
        <div class="col-2">
            <h3>{{ __('global.units') }}</h3>
            @if (isset($stats['slowest_unit']))
                @foreach ($stats['slowest_unit'] as $slowest_unit)
                    <div class="form-group">
                        <img src="{{ \App\Util\Icon::icons($slowest_unit['id']) }}">-{{ __('global.total') }} <b id="attackTotal" class="float-right">{{ \App\Util\BasicFunctions::numberConv($slowest_unit['count']) }}</b>
                    </div>
                @endforeach
            @endif
        </div>
        <div class="col-1"></div>
        <div class="col-2">
            <h3>{{ __('tool.attackPlanner.type') }}</h3>
            @if (isset($stats['type']))
                @foreach ($stats['type'] as $type)
                    <div class="form-group">
                        <img src="{{ \App\Util\Icon::icons($type['id']) }}">-{{ __('global.total') }} <b id="attackTotal" class="float-right">{{ \App\Util\BasicFunctions::numberConv($type['count']) }}</b>
                    </div>
                @endforeach
            @endif
        </div>
        <div class="col-1"></div>
    </div>
</div>
