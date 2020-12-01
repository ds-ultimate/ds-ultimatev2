@extends('layouts.app')

@section('titel', __('tool.accMgrDB.title_show').': '.$template->name)

@push('style')
@endpush

@section('content')
    <div class="row justify-content-center">
        <!-- Titel für Tablet | PC -->
        <div class="col-12 p-lg-5 mx-auto my-1 text-center d-none d-lg-block">
            <div class="float-left mt-4">
                <a class="btn btn-primary" href="{{ route('tools.accMgrDB.index') }}">{{ __('global.back') }}</a>
            </div>
            <h1 class="font-weight-normal">{{ __('tool.accMgrDB.title_show') }}</h1>

            <div class="float-right mt-4">
                <input id="export_template_data" class="d-none mr-2" type="text" readonly="readonly" value="{{ $template->exportDS() }}">
                <a id="export_template" class="btn btn-primary"><i class="far fa-copy"></i> {{ __("tool.accMgrDB.export") }}</a>
            </div>
        </div>
        <!-- ENDE Titel für Tablet | PC -->
        <!-- Titel für Mobile Geräte -->
        <div class="p-lg-5 mx-auto my-1 text-center d-lg-none truncate">
            <h1 class="font-weight-normal">
                {{ __('tool.accMgrDB.title_show') }}
            </h1>
            <h4>
            </h4>
        </div>
        <!-- ENDE Titel für Mobile Geräte -->
    </div>
    <div class="col-12 mt-2">
        <div class="card">
            <div class="card-body">
                <h4 class="mb-4">
                    <div class="float-left">
                        {{ $template->name }}
                    </div>
                    <div class="rating float-right" data-rate="{{ $template->rating }}" @isset($ownVote) data-voted="{{ $ownVote->rating }}" @endisset data-totalvotes="{{ $template->totalVotes }}"></div>
                </h4>

                <table id="table_all" class="table table-borderless table-sm w-100">
                    <tbody>
                    <tr>
                        @foreach(\App\Util\BuildingUtils::$BUILDINGS as $name=>$info)
                            <th><img src="{{ \App\Util\BuildingUtils::getImage($name) }}" data-toggle="tooltip" title="{{ __("ui.buildings.$name") }}"></th>
                        @endforeach
                    </tr>
                    <tr>
                        @foreach(\App\Util\BuildingUtils::$BUILDINGS as $name=>$info)
                            <td>{{ $result[$name] ?? 0 }}</td>
                        @endforeach
                    </tr>
                    <tr>
                    </tr>
                    </tbody>
                </table>
                <table id="table_detailed" class="table table-hover table-sm w-100">
                    <tbody>
                        @foreach($buildings as $build)
                            {!! \App\Http\Controllers\Tools\AccMgrDB::createBuildingRow($build['name'], $build['diff'],
                                    $build['result'], $build['farm'], $build['points'], false) !!}
                        @endforeach
                    </tbody>
                </table>

                <div class="form-group">
                    <div class="form-check form-check-inline">
                        <input type="checkbox" id="remove_additional" name="remove_additional" class="form-check-input" {{ $template->remove_additional ? 'checked' : '' }} disabled>
                        <label for="remove_additional">{{ __('tool.accMgrDB.remAddit') }}</label>
                    </div>
                </div>
                @if($template->remove_additional)
                    <div class="form-group ml-4">
                        <div class="form-check form-check-inline">
                            <input type="checkbox" id="removeIgno_church" name="removeIgno_church" class="form-check-input" {{ $ignored['church'] ? 'checked' : '' }} disabled>
                            <label for="removeIgno_church">{{ __('tool.accMgrDB.remChurch') }}</label>
                        </div>
                    </div>
                    <div class="form-group ml-4">
                        <div class="form-check form-check-inline">
                            <input type="checkbox" id="removeIgno_church_f" name="removeIgno_church_f" class="form-check-input" {{ $ignored['church_f'] ? 'checked' : '' }} disabled>
                            <label for="removeIgno_church_f">{{ __('tool.accMgrDB.remFirstChurch') }}</label>
                        </div>
                    </div>
                    <div class="form-group ml-4">
                        <div class="form-check form-check-inline">
                            <input type="checkbox" id="removeIgno_watchtower" name="removeIgno_watchtower" class="form-check-input" {{ $ignored['watchtower'] ? 'checked' : '' }} disabled>
                            <label for="removeIgno_watchtower">{{ __('tool.accMgrDB.remWT') }}</label>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection

@push('js')
<script>
    $(function() {
        $('#export_template_data').removeClass("d-none").hide();
        $('#export_template').click(function () {
            $('#export_template_data').removeClass("d-none");
            copy('#export_template_data');
            createToast("{{ __("tool.accMgrDB.export_success") }}", "{{ __("tool.accMgrDB.export") }}", "now", "fas fa-check");
        });
    });

    $(document).ready(function() {
        $.each($(".rating").get(), function (k,v) {
            $(v).rating({
                totalStars: 5,
                readOnly: {{ Auth::check() ? 'false' : 'true' }},
                showYourVote: true,
                showLoginContent: true,
                starColor: {
                    standard: 'text-primary',
                    hover: 'text-secondary',
                    select: '#f7e755'
                },
            }, function (e) {
                axios.post('{{ route('tools.accMgrDB.rating_api', [$template->id]) }}', {
                    'rating' : e,
                    @isset($key)
                    'key' : '{{ $key }}'
                    @endisset
                })
                    .then((response) => {
                        console.log(response)
                    })
                    .catch((error) => {
                        alert('Fehler')
                    });
            });
        })
    });

    function copy(selector) {
        /* Get the text field */
        var copyText = $(selector);
        copyText.show();
        /* Select the text field */
        copyText.select();
        /* Copy the text inside the text field */
        document.execCommand("copy");
        copyText.hide();
    }
</script>
@endpush
