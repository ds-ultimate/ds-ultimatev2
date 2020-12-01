@extends('layouts.app')

@section('titel', "Collection")

@section('content')
    <div class="row justify-content-center">
        <!-- Titel für Tablet | PC -->
        <div class="col-12 p-lg-5 mx-auto my-1 text-center d-none d-lg-block">
            <h1 class="font-weight-normal">Datensammlung: </h1>
        </div>
        <div class="card mb-4">
            <div class="card-body">
		<div class="table-responsive">
                <h2>Auswertung (alles auf HG 0 umgerechnet in s):</h2>
                <table class="table" id="data-table">
                    <colgroup>
                        <col style="width: 50px">
                        @foreach ($buildingTimes as $name => $value)
                            <col style="width: 100px">
                        @endforeach
                    </colgroup>
                    <thead>
                    <tr>
                        <th style="text-align: right">Gebäude</th>
                        @foreach ($buildingTimes as $name => $value)
                            <th>{{ $name }}{{-- ucfirst(__("ui.buildings.$name")) --}}</th>
                        @endforeach
                    </tr>
                    <tr>
                        <th>Stufe</th>
                        <th colspan="30"></th>
                    </tr>
                    </thead>
                    <tbody>
                    @for($i = 0; $i < 30; $i++)
                        <tr>
                            <th>{{ $i }}</th>
                            @foreach ($buildingTimes as $name => $value)
                                <td>{{ ((isset($value[$i]))?($value[$i]):("???")) }}</td>
                            @endforeach
                        </tr>
                    @endfor
                    </tbody>
                </table>
		</div>
            </div>
        </div>
        <div class="card mb-4">
            <div class="card-body">
                <h2>Letze daten:</h2>
                <table class="table" id="latest-table">
                    <colgroup>
                        <col style="width: 100px">
                        <col style="width: 120px">
                        <col style="width: 50px">
                        <col style="width: 80px">
                        <col style="width: 80px">
                        <col style="width: 60px">
                        <col style="width: 60px">
                        <col style="width: 60px">
                        <col style="width: 100px">
                    </colgroup>
                    <thead>
                    <tr>
                        <th>Welt</th>
                        <th>Gebäude</th>
                        <th>Stufe</th>
                        <th>Bauzeit</th>
                        <th>HG Stufe</th>
                        <th>Holz</th>
                        <th>Lehm</th>
                        <th>Eisen</th>
                        <th>Bauernhof</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach ($latestData as $data)
                        <tr>
                            <th>{{ $data->world->displayName() }} ({{ $data->world->server->code . $data->world->name }})</th>
                            <td>{{ ucfirst(__("ui.buildings." . $data->building)) }}</td>
                            <td>{{ $data->level }}</td>
                            <td>{{ $data->buildtime }}</td>
                            <td>{{ $data->mainLevel }}</td>
                            <td>{{ $data->wood }}</td>
                            <td>{{ $data->clay }}</td>
                            <td>{{ $data->iron }}</td>
                            <td>{{ $data->pop }}</td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection

@push('js')
<script>
    var url = "https://mkich.ds-ultimate.de/tools/datacollectionHQ/post/";
    
    $(function () {
        $('#select-world').change(function() {
            $('#collect-form')[0].action = url + $('#select-world').val() + "?debug=1";
        });
        
        $('#select-world').trigger("change");
    });
</script>
@endpush

@push('style')
<style>
    #latest-table thead th { text-align: center }
    #latest-table thead th:first-child { text-align: left }
    #latest-table tbody td { text-align: center }
    #latest-table tbody td:first-child { text-align: left }
    
    #data-table thead th { text-align: center }
    #data-table thead th:first-child { text-align: left }
    #data-table tbody td { text-align: center }
    #data-table tbody td:first-child { text-align: left }
</style>
@endpush