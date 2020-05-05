@extends('layouts.admin')

@section('content')
<div class="card">
    <div class="card-header">
        {{ __('admin.news.title') }}
        
        @can('news_create')
            <div class="float-right">
                <a class="btn btn-success" href="{{ route("admin.news.create") }}">
                    {{ __('admin.news.create') }}
                </a>
            </div>
        @endcan
    </div>
    
    {{-- //TODO this table --}}
    <div class="card-body">
        <div class="table-responsive">
            <table class=" table table-bordered table-striped table-hover datatable w-100">
                <thead>
                    <tr>
                        <th>
                            {{ __('admin.news.id') }}
                        </th>
                        <th>
                            {{ __('admin.news.content') }} DE
                        </th>
                        <th>
                            {{ __('admin.news.content') }} EN
                        </th>
                        <th>
                            {{ __('admin.news.update') }}
                        </th>
                        <th>
                            &nbsp;
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($news as $key => $news)
                        <tr data-entry-id="{{ $news->id }}">
                            <td>
                                {{ $news->id ?? '' }}
                            </td>
                            <td>
                                {!! $news->content_de ?? '' !!}
                            </td>
                            <td>
                                {!! $news->content_en ?? '' !!}
                            </td>
                            <td>
                                {{ $news->updated_at }}
                            </td>
                            <td>
                                @can('news_show')
                                    <a class="btn btn-xs btn-primary" href="{{ route('admin.news.show', $news->id) }}">
                                        {{ trans('global.view') }}
                                    </a>
                                @endcan
                                @can('news_edit')
                                    <a class="btn btn-xs btn-info" href="{{ route('admin.news.edit', $news->id) }}">
                                        {{ trans('global.edit') }}
                                    </a>
                                @endcan
                                @can('news_delete')
                                    <form action="{{ route('admin.news.destroy', $news->id) }}" method="POST" onsubmit="return confirm('{{ trans('global.areYouSure') }}');" style="display: inline-block;">
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
@endsection
