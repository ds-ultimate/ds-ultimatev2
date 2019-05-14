@extends('layouts.temp')

@section('content')
    <div class="row justify-content-center">
        <div class="col-10">
            <div class="col-12">
                <ul id = "lang_menu">
                    <li class = "language{{ App::isLocale('de') ? ' active' : '' }}"><a href="{{ route('locale', 'de') }}">Deutsch</a></li>
                    <li class = "language{{ App::isLocale('en') ? ' active' : '' }}"><a href="{{ route('locale', 'en') }}">English</a></li>
                </ul>
            </div>
                <table id="table_id">
                    <thead>
                    <tr>
                        <th>{{ ucfirst(__('Rang')) }}</th>
                        <th>{{ ucfirst(__('Name')) }}</th>
                        <th>{{ ucfirst(__('Stamm')) }}</th>
                        <th>{{ ucfirst(__('Punkte')) }}</th>
                        <th>{{ ucfirst(__('Mitglieder')) }}</th>
                        <th>{{ ucfirst(__('Dörfer')) }}</th>
                        <th>{{ ucfirst(__('Punkte pro Spieler')) }}</th>
                        <th>{{ ucfirst(__('Insgesamt')) }}</th>
                        <th>{{ ucfirst(__('Angreifer')) }}</th>
                        <th>{{ ucfirst(__('Verteidiger')) }}</th>
                        <th>{{ ucfirst(__('Unterstützer')) }}</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($allyArray as $ally)
                        <tr>
                            <th>{{ $ally->rank }}</th>
                            <td>{{ \App\Util\BasicFunctions::outputName($ally->name) }}</td>
                            <td>{{ \App\Util\BasicFunctions::outputName($ally->tag) }}</td>
                            <td>{{ \App\Util\BasicFunctions::numberConv($ally->points) }}</td>
                            <td>{{ \App\Util\BasicFunctions::numberConv($ally->member_count) }}</td>
                            <td>{{ \App\Util\BasicFunctions::numberConv($ally->village_count) }}</td>
                            <td>{{ \App\Util\BasicFunctions::numberConv($ally->points/$ally->member_count) }}</td>
                            <td>{{ \App\Util\BasicFunctions::numberConv($ally->offBash) }}</td>
                            <td>{{ \App\Util\BasicFunctions::numberConv($ally->deffBash) }}</td>
                            <td>{{ \App\Util\BasicFunctions::numberConv($ally->gesBash - $ally->offBash - $ally->deffBash) }}</td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
        </div>
    </div>
<script>
    $(document).ready( function () {
        $('#table_id').DataTable();
    } );
</script>
@endsection
