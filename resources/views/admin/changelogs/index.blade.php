@extends('layouts.admin')
@section('content')
@can('changelog_create')
    <div style="margin-bottom: 10px;" class="row">
        <div class="col-lg-12">
            <a class="btn btn-success" href="{{ route("admin.changelogs.create") }}">
                {{ trans('global.add') }} {{ trans('cruds.changelog.title_singular') }}
            </a>
        </div>
    </div>
@endcan
<div class="card">
    <div class="card-header">
        {{ trans('cruds.changelog.title_singular') }} {{ trans('global.list') }}
    </div>

    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-striped table-hover datatable text-truncate w-100">
                <thead>
                    <tr>
                        <th width="10">

                        </th>
                        <th>
                            {{ trans('cruds.changelog.fields.id') }}
                        </th>
                        <th>
                            {{ trans('cruds.changelog.fields.version') }}
                        </th>
                        <th>
                            {{ trans('cruds.changelog.fields.title') }}
                        </th>
                        <th>
                            {{ trans('cruds.changelog.fields.repository_html_url') }}
                        </th>
                        <th>
                            {{ trans('cruds.changelog.fields.icon') }}
                        </th>
                        <th>
                            {{ trans('cruds.changelog.fields.color') }}
                        </th>
                        <th>
                            {{ trans('cruds.changelog.fields.update') }}
                        </th>
                        <th>
                            &nbsp;
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($changelogs as $key => $changelog)
                        <tr data-entry-id="{{ $changelog->id }}">
                            <td>

                            </td>
                            <td>
                                {{ $changelog->id ?? '' }}
                            </td>
                            <td>
                                {{ $changelog->version ?? '' }}
                            </td>
                            <td>
                                {{ $changelog->title ?? '' }}
                            </td>
                            <td>
                                <a href="{{ $changelog->repository_html_url ?? '' }}">{{ $changelog->repository_html_url ?? '' }}</a>
                            </td>
                            <td class="py-0">
                                <h2 class="mb-0"><i class="{{ $changelog->icon ?? '' }}"></i></h2>
                            </td>
                            <td class="py-0">
                                <label class="form-check-label" for="inlineRadio1" style="width: 20px; height: 20px; background-color: {{ $changelog->color }}"></label>
                            </td>
                            <td>
                                {{ $changelog->updated_at->diffForHumans() }}
                            </td>
                            <td>
                                @can('changelog_show')
                                    <a class="btn btn-xs btn-primary" href="{{ route('admin.changelogs.show', $changelog->id) }}">
                                        {{ trans('global.view') }}
                                    </a>
                                @endcan
                                @can('changelog_edit')
                                    <a class="btn btn-xs btn-info" href="{{ route('admin.changelogs.edit', $changelog->id) }}">
                                        {{ trans('global.edit') }}
                                    </a>
                                @endcan
                                @can('changelog_delete')
                                    <form action="{{ route('admin.changelogs.destroy', $changelog->id) }}" method="POST" onsubmit="return confirm('{{ trans('global.areYouSure') }}');" style="display: inline-block;">
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
    url: "{{ route('admin.changelogs.massDestroy') }}",
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
@can('changelog_delete')
  dtButtons.push(deleteButton)
@endcan

  $('.datatable:not(.ajaxTable)').DataTable({
    buttons: dtButtons,
    columnDefs: [ {
        targets: [3],
        render: function ( data, type, row ) {
            console.log(type);
            return data.length > 20 ?
                data.substr( 0, 20 ) +'…' :
                data;
        }
    }],
  })
})

</script>
@endsection
