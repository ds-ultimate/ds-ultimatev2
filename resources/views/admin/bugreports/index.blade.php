@extends('layouts.admin')

@section('content')
<div class="card">
    <div class="card-header">
        {{ __('admin.bugreport.title') }}
        
        <div class="float-right position-relative">
            <a class="btn btn-success" href="{{ route("admin.bugreports.create") }}">
                {{ __('admin.bugreport.new') }}
            </a>
            
            <button id="dropdown-filter" type="button" class="btn dropdown-toggle" data-toggle="dropdown">
                <i class="fas fa-filter"></i>
            </button>
            <div class="dropdown-menu dropdown-menu-left" aria-labelledby="dropdown-filter">
                <a class="dropdown-item" href="{{ route("admin.bugreports.index") }}">{{ __('admin.bugreport.filter.all') }}</a>
                <a class="dropdown-item" href="{{ route("admin.bugreports.new") }}">{{ __('admin.bugreport.filter.new') }}</a>
                
                <div class="dropdown-divider"></div>
                <h5 class="dropdown-header">{{ __('user.bugreport.priority') }}</h5>
                <a class="dropdown-item" href="{{ route("admin.bugreports.priority",[0]) }}">{{ __('user.bugreport.prioritySelect.low') }}</a>
                <a class="dropdown-item" href="{{ route("admin.bugreports.priority",[1]) }}">{{ __('user.bugreport.prioritySelect.normal') }}</a>
                <a class="dropdown-item" href="{{ route("admin.bugreports.priority",[2]) }}">{{ __('user.bugreport.prioritySelect.high') }}</a>
                <a class="dropdown-item" href="{{ route("admin.bugreports.priority",[3]) }}">{{ __('user.bugreport.prioritySelect.critical') }}</a>
                
                <div class="dropdown-divider"></div>
                <h5 class="dropdown-header">{{ __('admin.bugreport.status') }}</h5>
                <a class="dropdown-item" href="{{ route("admin.bugreports.status",[0]) }}">{{ __('admin.bugreport.statusSelect.open') }}</a>
                <a class="dropdown-item" href="{{ route("admin.bugreports.status",[1]) }}">{{ __('admin.bugreport.statusSelect.inprogress') }}</a>
                <a class="dropdown-item" href="{{ route("admin.bugreports.status",[2]) }}">{{ __('admin.bugreport.statusSelect.resolved') }}</a>
                <a class="dropdown-item" href="{{ route("admin.bugreports.status",[3]) }}">{{ __('admin.bugreport.statusSelect.close') }}</a>
            </div>
        </div>
    </div>
    
    {{-- //TODO this table --}}
    <div class="card-body">
        <div class="table-responsive">
            <table class=" table table-bordered table-striped table-hover datatable w-100">
                <thead>
                    <tr>
                        <th>
                            {{ __('user.bugreport.priority') }}
                        </th>
                        <th nowrap>
                            {{ __('user.bugreport.form_title') }}
                        </th>
                        <th>
                            {{ __('user.bugreport.name') }}
                        </th>
                        <th>
                            {{ __('admin.bugreport.status') }}
                        </th>
                        <th>
                            {{ __('admin.bugreport.comment.title') }}
                        </th>
                        <th>
                            {{ __('admin.bugreport.created_at') }}
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
                                <h4>{!! $bugreport->getPriorityBadge() ?? '' !!}</h4>
                            </td>
                            <td nowrap>
                                @if ($bugreport->firstSeen === null)
                                    <b>{{ $bugreport->title }}</b> <i class="badge badge-primary">{{ __('admin.bugreport.new') }}</i>
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
                                        {{ __('global.view') }}
                                    </a>
                                @endcan
                                @can('bugreport_edit')
                                    <a class="btn btn-xs btn-info" href="{{ route('admin.bugreports.edit', $bugreport->id) }}">
                                        {{ __('global.edit') }}
                                    </a>
                                @endcan
                                @can('bugreport_delete')
                                    <form action="{{ route('admin.bugreports.destroy', $bugreport->id) }}" method="POST" onsubmit="return confirm('{{ __('global.areYouSure') }}');" style="display: inline-block;">
                                        @method('DELETE')
                                        @csrf
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
