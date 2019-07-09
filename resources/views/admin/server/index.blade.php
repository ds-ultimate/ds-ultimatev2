@extends('layouts.admin')
@section('content')
@can('server_create')
    <div style="margin-bottom: 10px;" class="row">
        <div class="col-lg-12">
            <a class="btn btn-success" href="{{ route("admin.server.create") }}">
                {{ trans('global.add') }} {{ trans('cruds.server.title_singular') }}
            </a>
        </div>
    </div>
@endcan
<div class="card">
    <div class="card-header">
        {{ trans('cruds.server.title_singular') }} {{ trans('global.list') }}
    </div>

    <div class="card-body">
        <div class="table-responsive">
            <table class=" table table-bordered table-striped table-hover datatable">
                <thead>
                    <tr>
                        <th width="10">

                        </th>
                        <th>
                            {{ trans('cruds.server.fields.id') }}
                        </th>
                        <th>
                            {{ trans('cruds.server.fields.code') }}
                        </th>
                        <th>
                            {{ trans('cruds.server.fields.flag') }}
                        </th>
                        <th>
                            {{ trans('cruds.server.fields.url') }}
                        </th>
                        <th style="max-width: 50px">
                            {{ trans('cruds.server.fields.active') }}
                        </th>
                        <th>
                            &nbsp;
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($servers as $key => $server)
                        <tr data-entry-id="{{ $server->id }}">
                            <td>

                            </td>
                            <td>
                                {{ $server->id ?? '' }}
                            </td>
                            <td>
                                {{ $server->code ?? '' }}
                            </td>
                            <td>
                                <span class="flag-icon flag-icon-{{ $server->flag ?? '' }}"></span> [{{ $server->flag }}]
                            </td>
                            <td>
                                {{ $server->url ?? '' }}
                            </td>
                            <td>
                                {!! ($server->active == 1)? '<span class="fas fa-check" style="color: green"></span>' : '<span class="fas fa-times" style="color: red"></span>' !!}
                            </td>
                            <td>
                                @can('server_show')
                                    <a class="btn btn-xs btn-primary" href="{{ route('admin.server.show', $server->id) }}">
                                        {{ trans('global.view') }}
                                    </a>
                                @endcan
                                @can('server_edit')
                                    <a class="btn btn-xs btn-info" href="{{ route('admin.server.edit', $server->id) }}">
                                        {{ trans('global.edit') }}
                                    </a>
                                @endcan
                                @can('server_delete')
                                    <form action="{{ route('admin.server.destroy', $server->id) }}" method="POST" onsubmit="return confirm('{{ trans('global.areYouSure') }}');" style="display: inline-block;">
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
    url: "{{ route('admin.server.massDestroy') }}",
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
@can('server_delete')
  dtButtons.push(deleteButton)
@endcan

  $('.datatable:not(.ajaxTable)').DataTable({ buttons: dtButtons })
})

</script>
@endsection
