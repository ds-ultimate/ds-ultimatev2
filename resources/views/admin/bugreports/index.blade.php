@extends('layouts.admin')
@section('content')
@can('bugreport_create')
    <div style="margin-bottom: 10px;" class="row">
        <div class="col-3">
            <a class="btn btn-success" href="{{ route("admin.bugreports.create") }}">
                {{ trans('global.add') }} {{ trans('cruds.bugreport.title_singular') }}
            </a>
            <a class="btn btn-danger" href="{{ route("admin.bugreports.index") }}">
                {{ trans('global.all') }}
            </a>
            <a class="btn btn-primary" href="{{ route("admin.bugreports.new") }}">
                {{ trans('cruds.bugreport.new') }}
            </a>
        </div>
        <div class="col">
            <a class="btn btn-info" href="{{ route("admin.bugreports.priority",[0]) }}">
                {{ trans('global.show') }} {{ trans('user.bugreport.prioritySelect.low') }}
            </a>
            <a class="btn btn-primary" href="{{ route("admin.bugreports.priority",[1]) }}">
                {{ trans('global.show') }} {{ trans('user.bugreport.prioritySelect.normal') }}
            </a>
            <a class="btn btn-warning" href="{{ route("admin.bugreports.priority",[2]) }}">
                {{ trans('global.show') }} {{ trans('user.bugreport.prioritySelect.high') }}
            </a>
            <a class="btn btn-danger" href="{{ route("admin.bugreports.priority",[3]) }}">
                {{ trans('global.show') }} {{ trans('user.bugreport.prioritySelect.critical') }}
            </a>
        </div>
        <div class="col">
            <a class="btn btn-dark" href="{{ route("admin.bugreports.status",[0]) }}">
                {{ trans('global.show') }} {{ trans('cruds.bugreport.statusSelect.open') }}
            </a>
            <a class="btn btn-primary" href="{{ route("admin.bugreports.status",[1]) }}">
                {{ trans('global.show') }} {{ trans('cruds.bugreport.statusSelect.inprogress') }}
            </a>
            <a class="btn btn-light" href="{{ route("admin.bugreports.status",[2]) }}">
                {{ trans('global.show') }} {{ trans('cruds.bugreport.statusSelect.resolved') }}
            </a>
            <a class="btn btn-success" href="{{ route("admin.bugreports.status",[3]) }}">
                {{ trans('global.show') }} {{ trans('cruds.bugreport.statusSelect.close') }}
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
            <table class=" table table-bordered table-striped table-hover datatable w-100">
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
                            {{ trans('cruds.bugreport.fields.comment') }}
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
                                {{ $bugreport->comments->count() }}
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
    $(function() {
        let copyButtonTrans = '{{ trans('global.datatables.copy') }}'
        let csvButtonTrans = '{{ trans('global.datatables.csv') }}'
        let excelButtonTrans = '{{ trans('global.datatables.excel') }}'
        let pdfButtonTrans = '{{ trans('global.datatables.pdf') }}'
        let printButtonTrans = '{{ trans('global.datatables.print') }}'
        let colvisButtonTrans = '{{ trans('global.datatables.colvis') }}'

        let languages = {
            'de': 'https://cdn.datatables.net/plug-ins/1.10.19/i18n/German.json',
            'en': 'https://cdn.datatables.net/plug-ins/1.10.19/i18n/English.json'
        };

        $.extend(true, $.fn.dataTable.Buttons.defaults.dom.button, { className: 'btn' })
        $.extend(true, $.fn.dataTable.defaults, {
            language: {
                url: languages.{{ app()->getLocale() }}
            },
            columnDefs: [{
                orderable: false,
                className: 'select-checkbox',
                targets: 0
            }, {
                orderable: false,
                searchable: false,
                targets: -1
            }],
            select: {
                style:    'multi+shift',
                selector: 'td:first-child'
            },
            order: [],
            scrollX: true,
            pageLength: 100,
            dom: 'lBfrtip<"actions">',
            buttons: [
                {
                    extend: 'copy',
                    className: 'btn-default',
                    text: copyButtonTrans,
                    exportOptions: {
                        columns: ':visible'
                    }
                },
                {
                    extend: 'csv',
                    className: 'btn-default',
                    text: csvButtonTrans,
                    exportOptions: {
                        columns: ':visible'
                    }
                },
                {
                    extend: 'excel',
                    className: 'btn-default',
                    text: excelButtonTrans,
                    exportOptions: {
                        columns: ':visible'
                    }
                },
                {
                    extend: 'pdf',
                    className: 'btn-default',
                    text: pdfButtonTrans,
                    exportOptions: {
                        columns: ':visible'
                    }
                },
                {
                    extend: 'print',
                    className: 'btn-default',
                    text: printButtonTrans,
                    exportOptions: {
                        columns: ':visible'
                    }
                },
                {
                    extend: 'colvis',
                    className: 'btn-default',
                    text: colvisButtonTrans,
                    exportOptions: {
                        columns: ':visible'
                    }
                }
            ]
        });

        $.fn.dataTable.ext.classes.sPageButton = '';
    });

</script>
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
