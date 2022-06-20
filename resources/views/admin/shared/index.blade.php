@extends('layouts.admin')

@section('content')
<div class="card">
    <div class="card-header">
        {{ $header }}

        @isset($create)
        @can($create['permission'])
            <div class="float-right">
                <a class="btn btn-success" href="{{ route($create['route']) }}">
                    {{ $create['title'] }}
                </a>
            </div>
        @endcan
        @endisset
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
                <tbody class="sortable">
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@push('js')
<script>
    var table = $('#table_id').DataTable({
        "columnDefs": [
            @foreach($tableColumns as $col)
                {"targets": {{ $loop->index }}, "className": '{{ $col['class'] }}'},
            @endforeach
        ],
        "processing": true,
        "serverSide": true,
        "ajax": "{{ route($datatableRoute) }}",
        "columns": [
            @foreach($tableColumns as $col)
                { "data": "{{ $col['data'] }}"{!! $col['dataAdditional'] !!} },
            @endforeach
        ],
        responsive: true,
        {!! \App\Util\Datatable::language() !!}
    });
    
    @forceSet($handle)
    @if($handle)
    $.each($('.sortable').get(), function (k,v) {
        var s = sortable.Sortable.create(v, {
            animation: 300,
            handle: '.handle', // handle's class
            onEnd: function (e) {
                axios.post('{{ route($datatableRoute . '.reorder') }}', {
                    'order' : s.toArray(),
                })
                    .then((response) => {
                    })
                    .catch((error) => {
                    });
            },
        });
    });
    @endif
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
