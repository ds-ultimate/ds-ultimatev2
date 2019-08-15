@extends('layouts.admin')
@section('content')
@can('world_create')
    <div style="margin-bottom: 10px;" class="row">
        <div class="col-lg-12">
            <a class="btn btn-success" href="{{ route("admin.worlds.create") }}">
                {{ trans('global.add') }} {{ trans('cruds.world.title_singular') }}
            </a>
        </div>
    </div>
@endcan
<div class="card">
    <div class="card-header">
        {{ trans('cruds.world.title_singular') }} {{ trans('global.list') }}
    </div>

    <div class="card-body">
        <div class="table-responsive">
            <table class=" table table-bordered table-striped table-hover datatable">
                <thead>
                    <tr>
                        <th width="10">

                        </th>
                        <th>
                            {{ trans('cruds.world.fields.id') }}
                        </th>
                        <th>
                            {{ trans('cruds.world.fields.server') }}
                        </th>
                        <th>
                            {{ trans('cruds.world.fields.name') }}
                        </th>
                        <th>
                            {{ trans('cruds.world.fields.ally') }}
                        </th>
                        <th>
                            {{ trans('cruds.world.fields.player') }}
                        </th>
                        <th>
                            {{ trans('cruds.world.fields.village') }}
                        </th>
                        <th>
                            {{ trans('cruds.world.fields.url') }}
                        </th>
                        <th>
                            {{ trans('cruds.world.fields.active') }}
                        </th>
                        <th>
                            {{ trans('cruds.world.fields.update') }}
                        </th>
                        <th>
                            {{ trans('cruds.world.fields.clean') }}
                        </th>
                        <th>
                            &nbsp;
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($worlds as $key => $world)
                        <tr data-entry-id="{{ $world->id }}">
                            <td>

                            </td>
                            <td>
                                {{ $world->id ?? '' }}
                            </td>
                            <td>
                                <span class="flag-icon flag-icon-{{ $world->server->flag ?? '' }}"></span> {{ $world->server->code ?? '' }}
                            </td>
                            <td>
                                {{ $world->name ?? '' }}
                            </td>
                            <td>
                                {{ \App\Util\BasicFunctions::numberConv($world->ally_count) ?? '' }}
                            </td>
                            <td>
                                {{ \App\Util\BasicFunctions::numberConv($world->player_count) ?? '' }}
                            </td>
                            <td>
                                {{ \App\Util\BasicFunctions::numberConv($world->village_count) ?? '' }}
                            </td>
                            <td>
                                <a href="{{ $world->url ?? '' }}" target="_blank">{{ $world->url ?? '' }}</a>
                            </td>
                            <td>
                                {!! ($world->active == 1)? '<span class="fas fa-check" style="color: green"></span>' : '<span class="fas fa-times" style="color: red"></span>' !!}
                            </td>
                            <td>
                                {{ $world->worldUpdated_at->diffForHumans() }}
                            </td>
                            <td>
                                {{ $world->worldCleaned_at->diffForHumans() }}
                            </td>
                            <td>
                                @can('world_show')
                                    <a class="btn btn-xs btn-primary" href="{{ route('admin.worlds.show', $world->id) }}">
                                        {{ trans('global.view') }}
                                    </a>
                                @endcan
                                @can('world_edit')
                                    <a class="btn btn-xs btn-info" href="{{ route('admin.worlds.edit', $world->id) }}">
                                        {{ trans('global.edit') }}
                                    </a>
                                @endcan
                                @can('world_delete')
                                    <form action="{{ route('admin.worlds.destroy', $world->id) }}" method="POST" onsubmit="return confirm('{{ trans('global.areYouSure') }}');" style="display: inline-block;">
                                        <input type="hidden" name="_method" value="DELETE">
                                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                        <input type="submit" class="btn btn-xs btn-danger" value="{{ trans('global.delete') }}">
                                    </form>
                                @endcan
                            </td>

                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
@section('scripts')
@parent
<script>
    $(function () {
  let deleteButtonTrans = '{{ trans('global.datatables.delete') }}'
  let deleteButton = {
    text: deleteButtonTrans,
    url: "{{ route('admin.worlds.massDestroy') }}",
    className: 'btn-danger',
    action: function (e, dt, node, config) {
      var ids = $.map(dt.rows({ selected: true }).nodes(), function (entry) {
          return $(entry).data('entry-id')
      });

      if (ids.length === 0) {
        alert('{{ trans('global.datatables.zero_selected') }}')

        return
      }

      if (confirm('{{ trans('global.areYouSure') }}')) {
        $.ajax({
          headers: {'x-csrf-token': _token},
          method: 'POST',
          url: config.url,
          data: { ids: ids, _method: 'DELETE' }})
          .done(function () { location.reload() })
      }
    }
  }
  let dtButtons = $.extend(true, [], $.fn.dataTable.defaults.buttons)
@can('world_delete')
  dtButtons.push(deleteButton)
@endcan

  $('.datatable:not(.ajaxTable)').DataTable({ buttons: dtButtons })
})

</script>
@endsection
