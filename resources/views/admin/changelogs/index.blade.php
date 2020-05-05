@extends('layouts.admin')

@section('content')
<div class="card">
    <div class="card-header">
        {{ __('admin.changelogs.title') }}
        
        @can('changelog_create')
            <div class="float-right position-relative">
                <a class="btn btn-success" href="{{ route("admin.changelogs.create") }}">
                    {{ __('admin.changelogs.create') }}
                </a>
            </div>
        @endcan
    </div>

    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-striped table-hover datatable text-truncate w-100">
                <thead>
                    <tr>
                        <th>
                            {{ __('admin.changelogs.id') }}
                        </th>
                        <th>
                            {{ __('admin.changelogs.version') }}
                        </th>
                        <th>
                            {{ __('admin.changelogs.form_title') }}
                        </th>
                        <th>
                            {{ __('admin.changelogs.url') }}
                        </th>
                        <th>
                            {{ __('admin.changelogs.icon') }}
                        </th>
                        <th>
                            {{ __('admin.changelogs.color') }}
                        </th>
                        <th>
                            {{ __('admin.changelogs.created') }}
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
                                        {{ __('global.view') }}
                                    </a>
                                @endcan
                                @can('changelog_edit')
                                    <a class="btn btn-xs btn-info" href="{{ route('admin.changelogs.edit', $changelog->id) }}">
                                        {{ __('global.edit') }}
                                    </a>
                                @endcan
                                @can('changelog_delete')
                                    <form action="{{ route('admin.changelogs.destroy', $changelog->id) }}" method="POST" onsubmit="return confirm('{{ __('global.areYouSure') }}');" style="display: inline-block;">
                                        <input type="hidden" name="_method" value="DELETE">
                                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                        <input type="submit" class="btn btn-xs btn-danger" value="{{ __('global.delete') }}">
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
@endsection
