@extends('layouts.app')

@section('titel', $worldData->getDistplayName(),': '.__('tool.animHistMap.title'))

@section('content')
    <div class="row justify-content-center">
        <!-- Titel für Tablet | PC -->
        <div class="col-12 p-lg-5 mx-auto my-1 text-center d-none d-lg-block">
            <h1 class="font-weight-normal">{{ $wantedJob->getTitle().' ['.$worldData->getDistplayName().']' }}</h1>
        </div>
        <!-- ENDE Titel für Tablet | PC -->
        <!-- Titel für Mobile Geräte -->
        <div class="p-lg-5 mx-auto my-1 text-center d-lg-none truncate">
            <h1 class="font-weight-normal">
                {{ $wantedJob->getTitle() }}
            </h1>
            <h4>
                {{ '['.$worldData->getDistplayName().']' }}
            </h4>
        </div>
        <!-- ENDE Titel für Mobile Geräte -->
        <div class="col-12 mt-2">
            <div class="card">
                <div class="card-body">
                    <div class="progress position-relative" style="height: 40px">
                        <div id="progress-div" class="progress-bar" role="progressbar" aria-valuenow="40"
                            aria-valuemin="0" aria-valuemax="100" style="width: 0%">
                        </div>
                        <div id="progress-text" class="progress-text">
                            ----
                        </div>
                        <div class="progress-bg2 bg-success progress-bar-striped progress-bar-animated"></div>
                    </div>
                </div>
                <div id="rerun-div" class="mt-3 card-body">
                    <a id="rerunRender" class="btn btn-warning float-right" href="{{ route('tools.animHistMap.renderRerun', [$wantedJob->id, $wantedJob->edit_key]) }}">{{ __('tool.animHistMap.rerun') }}</a>
                </div>
                <div id="download-div" class="card-body" style="display: none">
                    <a id="mp4Download" href="" target="_blank">{{ __('tool.animHistMap.download.mp4') }}<br></a>
                    <a id="zipDownload" href="" target="_blank">{{ __('tool.animHistMap.download.zip') }}<br></a>
                    <a id="gifDownload" href="" target="_blank">{{ __('tool.animHistMap.download.gif') }}</a>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('js')
<script>
    var reloadTimeout;
    $(function () {
        reloadTimeout = setInterval(reloadStatus, 1000);
    });


    function reloadStatus() {
        $.ajax({
            type: "GET",
            url: "{{ route('tools.animHistMap.apiRenderStatus', [$wantedJob->id, $wantedJob->edit_key]) }}?" + Math.floor(Math.random() * 9000000 + 1000000),
            success: function(data){
                $('#progress-text').html(data.text);
                if(data.finished == 1) {
                    clearInterval(reloadTimeout);
                    $('#progress-div')[0].style.width = 100 + "%";
                    $('#download-div').show();
                    $('#mp4Download')[0].href = data.downloadMP4;
                    $('#gifDownload')[0].href = data.downloadGIF;
                    if(typeof(data.downloadZIP) !== "undefined") {
                        $('#zipDownload')[0].href = data.downloadZIP;
                        $('#zipDownload').removeClass("d-none")
                    } else {
                        $('#zipDownload').addClass("d-none")
                    }
                } else {
                    var w = data.cur / data.max
                    $('#progress-div')[0].style.width = Math.round(w * 100) + "%";
                }
            },
        });
    };
</script>
@endpush
