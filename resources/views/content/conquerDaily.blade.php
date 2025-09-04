@extends('layouts.app')

@section('titel', $worldData->getDistplayName().': '.__('ui.conquer.daily'))

@section('content')
<div class="row justify-content-center">
    <div class="col-12">
        <!-- Titel für Tablet | PC -->
        <div class="p-lg-5 mx-auto my-1 text-center d-none d-lg-block">
            <h1 class="font-weight-normal">{{ $worldData->getDistplayName().': '.__('ui.conquer.daily') }}</h1>
        </div>
        <!-- ENDE Titel für Tablet | PC -->
        <!-- Titel für Mobile Geräte -->
        <div class="p-lg-5 mx-auto my-1 text-center d-lg-none truncate">
            <h1 class="font-weight-normal">
                {{ $worldData->getDistplayName().': ' }}
            </h1>
            <h4>
                {{ __('ui.conquer.daily') }}
            </h4>
        </div>
        <!-- ENDE Titel für Tablet | PC -->
    </div>
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <div class="row">
                    <div class="m-auto">
                        <div class="form-inline">
                            <label class="control-label pr-3">{{ __('ui.table.date') }}: </label>
                            <input id="date_picker" class="form-control form-control-sm col-8" type="text" value="{{ Illuminate\Support\Carbon::now()->toDateString() }}" max="{{ Illuminate\Support\Carbon::now()->toDateString() }}" min="{{ Illuminate\Support\Carbon::now()->subDays(config('dsUltimate.db_save_day'))->toDateString() }}" readonly>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Datachart Eroberungen -->
    <div class="col-12 col-lg-6 mt-2">
        <div class="card">
            <div class="card-body cust-responsive">
                <h2 class="card-title">{{ __('ui.tabletitel.player') }}</h2>
                <table id="table_player" class="table table-hover table-sm w-100">
                    <thead>
                    <tr>
                        <th></th>
                        <th>{{ ucfirst(__('ui.table.name')) }}</th>
                        <th>{{ ucfirst(__('ui.table.ally')) }}</th>
                        <th>{{ ucfirst(__('global.total')) }}</th>
                    </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="col-12 col-lg-6 mt-2">
        <div class="card">
            <div class="card-body cust-responsive">
                <h2 class="card-title">{{ __('ui.tabletitel.allys') }}</h2>
                <table id="table_ally" class="table table-hover table-sm w-100">
                    <thead>
                    <tr>
                        <th></th>
                        <th>{{ ucfirst(__('ui.table.ally')) }}</th>
                        <th>{{ ucfirst(__('ui.table.tag')) }}</th>
                        <th>{{ ucfirst(__('global.total')) }}</th>
                    </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <!-- ENDE Datachart Eroberungen -->
</div>
{{--    @dd(\Illuminate\Support\Carbon::today()->toDateString())--}}
@endsection

@push('js')
    <script src="{{ \App\Util\BasicFunctions::asset('plugin/bootstrap-datepicker/bootstrap-datepicker.min.js') }}"></script>
    <script>
        var dataTablePlayer;
        var dataTableAlly;

        $(document).ready( function () {
            dataTablePlayer = $('#table_player').DataTable({
                "processing": true,
                "serverSide": true,
                "ordering": false,
                "searching": false,
                "info": false,
                "ajax": "{{ route('api.conquerDaily', [$worldData->id, 'player']) }}/{{ \Illuminate\Support\Carbon::today()->toDateString() }}",
                "columns": [
                    { "data": "DT_RowIndex"},
                    { "data": "name"},
                    { "data": "ally"},
                    { "data": "total"},
                ],
                "columnDefs": [
                    {"targets": [0, 1, 2, 3], "className": 'dt-left'},
                ],
                stateSave: true,
                customName: "conquerDailyPlayer",
                {!! \App\Util\Datatable::language() !!}
            });

            dataTableAlly = $('#table_ally').DataTable({
                "processing": true,
                "serverSide": true,
                "ordering": false,
                "searching": false,
                "info": false,
                "ajax": "{{ route('api.conquerDaily', [$worldData->id, 'ally']) }}/{{ \Illuminate\Support\Carbon::today()->toDateString() }}",
                "columns": [
                    { "data": "DT_RowIndex"},
                    { "data": "name"},
                    { "data": "tag"},
                    { "data": "total"},
                ],
                "columnDefs": [
                    {"targets": [0, 1, 2, 3], "className": 'dt-left'},
                ],
                stateSave: true,
                customName: "conquerDailyAlly",
                {!! \App\Util\Datatable::language() !!}
            });

            $.fn.datepicker.dates['all'] = {
                days: ["{{ __('datepicker.Sunday') }}", "{{ __('datepicker.Monday') }}", "{{ __('datepicker.Tuesday') }}", "{{ __('datepicker.Wednesday') }}", "{{ __('datepicker.Thursday') }}", "{{ __('datepicker.Friday') }}", "{{ __('datepicker.Saturday') }}"],
                daysShort: ["{{ __('datepicker.Sun') }}", "{{ __('datepicker.Mon') }}", "{{ __('datepicker.Tue') }}", "{{ __('datepicker.Wed') }}", "{{ __('datepicker.Thu') }}", "{{ __('datepicker.Fri') }}", "{{ __('datepicker.Sat') }}"],
                daysMin: ["{{ __('datepicker.Su') }}", "{{ __('datepicker.Mo') }}", "{{ __('datepicker.Tu') }}", "{{ __('datepicker.We') }}", "{{ __('datepicker.Th') }}", "{{ __('datepicker.Fr') }}", "{{ __('datepicker.Sa') }}"],
                months: ["{{ __('datepicker.January') }}", "{{ __('datepicker.February') }}", "{{ __('datepicker.March') }}", "{{ __('datepicker.April') }}", "{{ __('datepicker.May') }}", "{{ __('datepicker.June') }}", "{{ __('datepicker.July') }}", "{{ __('datepicker.August') }}", "{{ __('datepicker.September') }}", "{{ __('datepicker.October') }}", "{{ __('datepicker.November') }}", "{{ __('datepicker.December') }}"],
                monthsShort: ["{{ __('datepicker.Jan') }}", "{{ __('datepicker.Feb') }}", "{{ __('datepicker.Mar') }}", "{{ __('datepicker.Apr') }}", "{{ __('datepicker.May') }}", "{{ __('datepicker.Jun') }}", "{{ __('datepicker.Jul') }}", "{{ __('datepicker.Aug') }}", "{{ __('datepicker.Sep') }}", "{{ __('datepicker.Oct') }}", "{{ __('datepicker.Nov') }}", "{{ __('datepicker.Dec') }}"],
                today: "{{ __('datepicker.Today') }}",
                monthsTitle: "{{ __('datepicker.months') }}",
                clear: "{{ __('datepicker.Clear') }}",
            };

            $("#date_picker").datepicker({
                language: 'all',
                format:'yyyy-mm-dd',
                endDate:'{{ \Illuminate\Support\Carbon::now()->toDateString() }}',
                startDate:'{{ $fistconquer != null ? \Carbon\Carbon::createFromTimestamp($fistconquer->timestamp)->toDateString() : \Illuminate\Support\Carbon::now()->toDateString() }}',
                weekStart:1,
            })

            $(document).on('change', '#date_picker', function (e) {
                dataTablePlayer.ajax.url('{{ route('api.conquerDaily', [$worldData->id, 'player']) }}/' + $(this).val()).load();
                dataTableAlly.ajax.url('{{ route('api.conquerDaily', [$worldData->id, 'ally']) }}/' + $(this).val()).load();
            });
        });
    </script>
@endpush
