@props(['data'])

<table class="table table-hover table-striped nowrap w-100 world-table">
    <thead>
    <tr>
        <th>{{ ucfirst(__('ui.table.world')) }}</th>
        <th>{{ ucfirst(__('ui.table.player')) }}</th>
        <th>{{ ucfirst(__('ui.table.ally')) }}</th>
        <th>{{ ucfirst(__('ui.table.village')) }}</th>
    </tr>
    </thead>
    <tbody>
    @foreach($data as $world)
        <tr>
            <td class="server-truncate"><span class="flag-icon flag-icon-{{ $world->server->flag }}"></span> {!! \App\Util\BasicFunctions::linkWorld($world, $world->display_name) !!}
                <small class="text-muted">({{ $world->server->code.$world->name }})</small>
                @auth
                    @can('world_access')
                        {!! \App\Util\BasicFunctions::worldStatus($world->active) !!}
                    @endcan
                @endauth
            </td>
            <td>{!! \App\Util\BasicFunctions::linkWorldPlayer($world, \App\Util\BasicFunctions::numberConv($world->player_count)) !!}</td>
            <td>{!! \App\Util\BasicFunctions::linkWorldAlly($world, \App\Util\BasicFunctions::numberConv($world->ally_count)) !!}</td>
            <td>{{ \App\Util\BasicFunctions::numberConv($world->village_count) }}</td>
        </tr>
    @endforeach
    </tbody>
</table>
