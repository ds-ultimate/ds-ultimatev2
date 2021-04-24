@extends('layouts.app')

@section('titel', $worldData->displayName(),': '.__('tool.animHistMap.title'))

@section('content')
    <div class="row justify-content-center">
        <!-- Titel für Tablet | PC -->
        <div class="col-12 p-lg-5 mx-auto my-1 text-center d-none d-lg-block">
            <h1 class="font-weight-normal">{{ $wantedJob->getTitle().' ['.$worldData->displayName().']' }}</h1>
        </div>
        <!-- ENDE Titel für Tablet | PC -->
        <!-- Titel für Mobile Geräte -->
        <div class="p-lg-5 mx-auto my-1 text-center d-lg-none truncate">
            <h1 class="font-weight-normal">
                {{ $wantedJob->getTitle() }}
            </h1>
            <h4>
                {{ '['.$worldData->displayName().']' }}
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
                    <a id="mp4Download" href="" target="_blank">{{ __('tool.animHistMap.download.mp4') }}</a><br>
                    <a id="zipDownload" href="" target="_blank">{{ __('tool.animHistMap.download.zip') }}</a><br>
                    <a id="gifDownload" href="" target="_blank">{{ __('tool.animHistMap.download.gif') }}</a>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('style')
<style>
    .progress-text {
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        position: absolute;
        font-size: 2.25rem;
        text-align: center;
        display: inline-block;
        line-height: initial;
        mix-blend-mode: difference;
        color: white;
        z-index: 2;
    }
    
    .progress-bar {
        background-color: black;
    }
    
    .progress-bg2 {
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        position: absolute;
        mix-blend-mode: screen;
        z-index: 3;
    }
</style>
@endpush

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
                console.log(data);
                $('#progress-text').html(data.text);
                if(data.finished == 1) {
                    clearInterval(reloadTimeout);
                    $('#progress-div')[0].style.width = 100 + "%";
                    $('#download-div').show();
                    $('#mp4Download')[0].href = data.downloadMP4;
                    $('#zipDownload')[0].href = data.downloadZIP;
                    $('#gifDownload')[0].href = data.downloadGIF;
                } else {
                    var w = data.cur / data.max
                    $('#progress-div')[0].style.width = Math.round(w * 100) + "%";
                }
            },
        });
    };
</script>
@endpush
