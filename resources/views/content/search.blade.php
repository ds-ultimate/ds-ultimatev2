@extends('layouts.temp')

@section('content')
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/v/bs4-4.1.1/dt-1.10.18/datatables.min.css"/>
    <link href="https://cdn.datatables.net/responsive/2.2.3/css/responsive.bootstrap.min.css" rel="stylesheet">
    <div class="row justify-content-center">
        <div class="col-10">
            <div class="col-12">
                <ul id = "lang_menu">
                    <li class = "language{{ App::isLocale('de') ? ' active' : '' }}"><a href="{{ route('locale', 'de') }}">Deutsch</a></li>
                    <li class = "language{{ App::isLocale('en') ? ' active' : '' }}"><a href="{{ route('locale', 'en') }}">English</a></li>
                </ul>
            </div>
            <table id="table_id" class="table table-hover table-sm w-100">
                <thead>
                <tr class="d-none d-md-table-row">
                    <th colspan="6">{{ ucfirst(__('Allgemein')) }}</th>
                    <th colspan="4">{{ ucfirst(__('Besiegte Gegner')) }}</th>
                </tr>
                <tr>
                    <th>{{ ucfirst(__('Rang')) }}</th>
                    <th>{{ ucfirst(__('Name')) }}</th>
                    <th>{{ ucfirst(__('Stamm')) }}</th>
                    <th>{{ ucfirst(__('Punkte')) }}</th>
                    <th>{{ ucfirst(__('Dörfer')) }}</th>
                    <th>{{ ucfirst(__('Punkte pro Dorf')) }}</th>
                    <th>{{ ucfirst(__('Insgesamt')) }}</th>
                    <th>{{ ucfirst(__('Angreifer')) }}</th>
                    <th>{{ ucfirst(__('Verteidiger')) }}</th>
                    <th>{{ ucfirst(__('Unterstützer')) }}</th>
                </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
        </div>
    </div>
    <script src="https://cdn.datatables.net/responsive/2.2.3/js/dataTables.responsive.min.js"></script>
    <script src="//cdnjs.cloudflare.com/ajax/libs/numeral.js/2.0.6/numeral.min.js"></script>
    <script>

        $(document).ready( function () {
            $.extend( $.fn.dataTable.defaults, {
                responsive: true
            } );

            $('#table_id').DataTable({
                "processing": true,
                "serverSide": true,
                "ajax": "{{ route('api.searchPlayer', [$search]) }}",
                "columns": [
                    { "data": "world" },
                    { "data": "id"},
                    { "data": "name"},
                    { "data": "points"},
                ],
                responsive: true,
                {!! \App\Util\Datatable::language() !!}
            });
        } );
    </script>
@endsection
