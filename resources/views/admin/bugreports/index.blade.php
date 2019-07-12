@extends('layouts.admin')
@section('content')
@can('bugreport_create')
    <div style="margin-bottom: 10px;" class="row">
        <div class="col-lg-12">
            <a class="btn btn-success" href="{{ route("admin.bugreports.create") }}">
                {{ trans('global.add') }} {{ trans('cruds.bugreport.title_singular') }}
            </a>
        </div>
    </div>
@endcan
<div class="card">
    <div class="card-header">
        {{ trans('cruds.bugreport.title_singular') }} {{ trans('global.list') }}
    </div>

    <div class="card-body">
        <div class="table-responsive">
            <table class=" table table-bordered table-striped table-hover datatable">
                <thead>
                    <tr>
                        <th width="10">

                        </th>
                        <th>
                            {{ trans('cruds.bugreport.fields.priority') }}
                        </th>
                        <th nowrap>
                            {{ trans('cruds.bugreport.fields.title') }}
                        </th>
                        <th>
                            {{ trans('cruds.bugreport.fields.name') }}
                        </th>
                        <th>
                            {{ trans('cruds.bugreport.fields.status') }}
                        </th>
                        <th>
                            {{ trans('cruds.bugreport.fields.created') }}
                        </th>
                        <th>
                            &nbsp;
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($bugreports as $key => $bugreport)
                        <tr data-entry-id="{{ $bugreport->id }}">
                            <td>

                            </td>
                            <td>
                                <h4>{!! $bugreport->getPriorityBadge() ?? '' !!}</h4>
                            </td>
                            <td nowrap>
                                @if ($bugreport->firstSeen === null)
                                    <b>{{ $bugreport->title }}</b> <i class="badge badge-primary">{{ trans('cruds.bugreport.new') }}</i>
                                @else
                                    {{ $bugreport->title }}
                                @endif
                            </td>
                            <td>
                                {!! $bugreport->name !!}
                            </td>
                            <td>
                                <h4>{!! $bugreport->getStatusBadge() !!}</h4>
                            </td>
                            <td>
                                {{ $bugreport->created_at->diffForHumans() }}
                            </td>
                            <td>
                                @can('bugreport_show')
                                    <a class="btn btn-xs btn-primary" href="{{ route('admin.bugreports.show', $bugreport->id) }}">
                                        {{ trans('global.view') }}
                                    </a>
                                @endcan
                                @can('bugreport_edit')
                                    <a class="btn btn-xs btn-info" href="{{ route('admin.bugreports.edit', $bugreport->id) }}">
                                        {{ trans('global.edit') }}
                                    </a>
                                @endcan
                                @can('bugreport_delete')
                                    <form action="{{ route('admin.bugreports.destroy', $bugreport->id) }}" method="POST" onsubmit="return confirm('{{ trans('global.areYouSure') }}');" style="display: inline-block;">
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
    url: "{{ route('admin.bugreports.massDestroy') }}",
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
@can('user_delete')
  dtButtons.push(deleteButton)
@endcan

  $('.datatable:not(.ajaxTable)').DataTable({ buttons: dtButtons })
})

</script>
@endsection
