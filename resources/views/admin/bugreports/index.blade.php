@extends('layouts.admin')

@section('content')
<div class="card">
    <div class="card-header">
        {{ $header }}
        
        @can($create['permission'])
            <div class="float-right">
                <a class="btn btn-success" href="{{ route($create['route']) }}">
                    {{ $create['title'] }}
                </a>
            </div>
        @endcan
    </div>
    
    <div id="dropdownSettingsWrapper" style="display: none">
        <div class="dropdown d-inline-flex">
            <button id="dropdown-filter-priority" type="button" class="btn dropdown-toggle ml-2" data-toggle="dropdown">
                <i class="fas fa-exclamation-circle"></i>
            </button>
            <div id="dropdown-filter-priority-div" class="dropdown-menu dropdown-menu-left" aria-labelledby="dropdown-filter-priority">
                <a class="dropdown-item filter-item filter-all">{{ __('admin.bugreport.filter.all') }}</a>
                <a class="dropdown-item filter-item" filter-target="prio[0]">{{ __('user.bugreport.prioritySelect.low') }}</a>
                <a class="dropdown-item filter-item" filter-target="prio[1]">{{ __('user.bugreport.prioritySelect.normal') }}</a>
                <a class="dropdown-item filter-item" filter-target="prio[2]">{{ __('user.bugreport.prioritySelect.high') }}</a>
                <a class="dropdown-item filter-item" filter-target="prio[3]">{{ __('user.bugreport.prioritySelect.critical') }}</a>
            </div>
        </div>

        <div class="dropdown d-inline-flex">
            <button id="dropdown-filter-status" type="button" class="btn dropdown-toggle ml-2" data-toggle="dropdown">
                <i class="fas fa-tasks"></i>
            </button>
            <div id="dropdown-filter-status-div" class="dropdown-menu dropdown-menu-left" aria-labelledby="dropdown-filter-status">
                <a class="dropdown-item filter-item filter-all">{{ __('admin.bugreport.filter.all') }}</a>
                <a class="dropdown-item filter-item" filter-target="status[0]">{{ __('admin.bugreport.statusSelect.open') }}</a>
                <a class="dropdown-item filter-item" filter-target="status[1]">{{ __('admin.bugreport.statusSelect.inprogress') }}</a>
                <a class="dropdown-item filter-item" filter-target="status[2]">{{ __('admin.bugreport.statusSelect.resolved') }}</a>
                <a class="dropdown-item filter-item" filter-target="status[3]">{{ __('admin.bugreport.statusSelect.close') }}</a>
            </div>
        </div>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table id="table_id" class="table table-bordered table-striped table-hover datatable w-100">
                <thead>
                    <tr>
                        @foreach($tableColumns as $col)
                            <th style="{{ $col['style'] }}">
                                {{ $col['title'] }}
                            </th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@push('js')
<script>
    $(function() {
        var dataTable = $('#table_id').DataTable({
            "columnDefs": [
                @foreach($tableColumns as $col)
                    {"targets": {{ $loop->index }}, "className": '{{ $col['class'] }}'},
                @endforeach
            ],
            "processing": true,
            "serverSide": true,
            "order": [[ 5, "asc" ]],
            "ajax": {
                "url": "{{ route($datatableRoute) }}",
                "data": function ( d ) {
                    $('.filter-item.active').each(function() {
                        d[$(this).attr("filter-target")] = true;
                    });
                }
            },
            "columns": [
                @foreach($tableColumns as $col)
                    { "data": "{{ $col['data'] }}"{!! $col['dataAdditional'] !!} },
                @endforeach
            ],
            responsive: true,
            {!! \App\Util\Datatable::language() !!}
        });
        
        $('#dropdownSettingsWrapper').children().appendTo("#table_id_filter");
        $('.filter-item').click(function (e) {
            e.stopPropagation();
            $(this).toggleClass('active');
            if($(this).hasClass("filter-all")) {
                if($(this).hasClass('active')) {
                    $(this).parent().children('.filter-item').addClass('active');
                } else {
                    $(this).parent().children('.filter-item').removeClass('active');
                }
            }
            dataTable.draw(false);
        });
    });
</script>
@endpush

@push('style')
<style>
    table.dataTable thead .sorting:before,table.dataTable thead .sorting:after,table.dataTable thead .sorting_asc:before,table.dataTable thead .sorting_asc:after,table.dataTable thead .sorting_desc:before,table.dataTable thead .sorting_desc:after,table.dataTable thead .sorting_asc_disabled:before,table.dataTable thead .sorting_asc_disabled:after,table.dataTable thead .sorting_desc_disabled:before,table.dataTable thead .sorting_desc_disabled:after{position:absolute;bottom:0.3em;display:block;opacity:0.3}
    table.dataTable thead .sorting_asc:before,table.dataTable thead .sorting_desc:after{opacity:1}table.dataTable thead .sorting_asc_disabled:before,table.dataTable thead .sorting_desc_disabled:after{opacity:0}
    table.dataTable tbody tr.selected a, table.dataTable tbody th.selected a, table.dataTable tbody td.selected a {color: #7d510f;}
    table.dataTable tbody tr.selected, table.dataTable tbody th.selected, table.dataTable tbody td.selected {color: #212529;}
    table.dataTable tbody>tr.selected, table.dataTable tbody>tr>.selected {background-color: rgba(237, 212, 146, 0.4);}
</style>
@endpush
