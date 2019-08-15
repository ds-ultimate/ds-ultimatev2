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
            <table class=" table table-bordered table-striped table-hover datatable">
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
                            {{ trans('cruds.changelog.fields.content') }}
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
                                {!! $changelog->content ?? '' !!}
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

  $('.datatable:not(.ajaxTable)').DataTable({ buttons: dtButtons })
})

</script>
@endsection
