@extends('layouts.app')

@section('titel', __('tool.accMgrDB.title'))

@section('content')
    <div class="row justify-content-center">
        <!-- Titel für Tablet | PC -->
        <div class="col-12 p-lg-5 mx-auto my-1 text-center d-none d-lg-block">
            <h1 class="font-weight-normal">
                {{ __('tool.accMgrDB.title') }}
                @isset($worldData)
                    [{{ $worldData->displayName() }}]
                @endisset
            </h1>
        </div>
        <!-- ENDE Titel für Tablet | PC -->
        <!-- Titel für Mobile Geräte -->
        <div class="p-lg-5 mx-auto my-1 text-center d-lg-none truncate">
            <h1 class="font-weight-normal">
                {{ __('tool.accMgrDB.title') }}
            </h1>
            <h4>
                @isset($worldData)
                    {{ $worldData->displayName() }}
                @endisset
            </h4>
        </div>
        <!-- ENDE Titel für Mobile Geräte -->
    </div>
    <div class="col-12 mt-2">
        <div id="dropdownSettingsWrapper" style="display: none">
            @auth
                <a id="createNew" href="{{ route('tools.accMgrDB.create') }}" class="btn btn-success ml-2">
                    <i class="fas fa-plus-circle"></i>
                </a>
                <div class="btn-group">
                    <button id="import-toggle" type="button" class="btn btn-success dropdown-toggle ml-2" data-toggle="dropdown">
                        <i class="fas fa-file-import"></i>
                    </button>
                    <div id="import-toggle-div" class="dropdown-menu dropdown-menu-left p-3" aria-labelledby="import-toggle">
                        <form id="form_import" action="{{ route("tools.accMgrDB.import") }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            @method("POST")
                            <div class="form-group">
                                <h4><label for="import_data">{{ __('tool.accMgrDB.import_label') }}</label></h4>
                                <input type="text" id="import_data" name="data"  class="form-control" value="" required>
                            </div>
                            <div>
                                <input class="btn btn-danger" type="submit" value="{{ __('tool.accMgrDB.import') }}">
                            </div>
                        </form>
                    </div>
                </div>
            @endauth

            <div class="btn-group">
                <button id="filter-toggle" type="button" class="btn btn-success dropdown-toggle ml-2" data-toggle="dropdown">
                    <i class="fas fa-filter"></i>
                </button>
                <div id="filter-toggle-div" class="dropdown-menu dropdown-menu-right p-3" style="right: auto !important" aria-labelledby="filter-toggle">
                    <a id="filter-watchtower" class="dropdown-item db-filter{{ ($hasSetting["watchtower"] ? " active" : "") }}">{{ __('tool.accMgrDB.filter.watchtower') }}</a>
                    <a id="filter-church" class="dropdown-item db-filter{{ ($hasSetting["church"] ? " active" : "") }}">{{ __('tool.accMgrDB.filter.church') }}</a>
                    <a id="filter-statue" class="dropdown-item db-filter{{ ($hasSetting["statue"] ? " active" : "") }}">{{ __('tool.accMgrDB.filter.statue') }}</a>
                </div>
            </div>
        </div>
        <div class="card">
            <div class="card-body">
                <table id="table_id" class="table table-hover table-sm w-100">
                    <thead>
                    <tr>
                        <th>{{ ucfirst(__('tool.accMgrDB.table.name')) }}</th>
                        <th>{{ ucfirst(__('tool.accMgrDB.table.rating')) }}</th>
                        <th>{{ ucfirst(__('tool.accMgrDB.table.type')) }}</th>
                        <th>{{ ucfirst(__('tool.accMgrDB.table.creator')) }}</th>
                        <th style="width:180px;"></th>
                        <th></th>
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
    var dataTable;
    $(document).ready( function () {
        dataTable = $('#table_id').DataTable({
            "columnDefs": [
                {"targets": [1], "orderSequence": ["desc", "asc"]},
            ],
            "processing": true,
            "serverSide": true,
            "order": [[ 0, "desc" ]],
            "ajax": {
                url: "{{ route('tools.accMgrDB.index_api') }}",
                data: function(d) {
                    return $.extend({}, d, {
                        "watchtower": ($("#filter-watchtower").hasClass('active') ? 1 : 0),
                        "church": ($("#filter-church").hasClass('active') ? 1 : 0),
                        "statue": ($("#filter-statue").hasClass('active') ? 1 : 0),
                    });
                }
                
            },
            "columns": [
                { "data": "name" },
                { "data": "rating", "searchable": false},
                { "data": "type", "orderable": false, "searchable": false},
                { "data": "user_id", "searchable": false},
                { "data": "actions", "searchable": false},
                { "data": "public", "searchable": false},
            ],
            responsive: true,
            {!! \App\Util\Datatable::language() !!}
        });

        $('#dropdownSettingsWrapper').children().appendTo("#table_id_filter");
        $('#form_import').submit(function (e) {
            e.preventDefault();

            axios.post(this.action, $(this).serialize())
                .then((response) => {
                    var dat = response.data;
                    if(dat.success) {
                        window.location.href = dat.url;
                    } else {
                        createToast(dat.error, "{{ __('tool.accMgrDB.import') }}", "now", "fas fa-times");
                    }
                })
                .catch((error) => {

                });
        });
        
        $('.db-filter').click(function (e) {
            e.stopPropagation();
            $(this).toggleClass('active');
            dataTable.draw(false);
        });
    });

</script>
@endpush
