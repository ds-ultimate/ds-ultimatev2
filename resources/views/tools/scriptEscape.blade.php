@extends('layouts.app')

@section('titel', __('tool.tableGenerator.title'))

@section('content')
    <div class="row justify-content-center">
        <!-- Titel für Tablet | PC -->
        <div class="col-12 p-lg-5 mx-auto my-1 text-center d-none d-lg-block">
            <h1 class="font-weight-normal">{{ ucfirst(__('tool.scriptEscape.title')) }}</h1>
        </div>
        <!-- ENDE Titel für Tablet | PC -->
        <!-- Titel für Mobile Geräte -->
        <div class="p-lg-5 mx-auto my-1 text-center d-lg-none truncate">
            <h1 class="font-weight-normal">
                {{ ucfirst(__('tool.scriptEscape.title')) }}
            </h1>
        </div>
        <!-- ENDE Titel für Mobile Geräte -->
        <!-- Input Card -->
        <div class="col-12 col-md-6 mt-2">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">{{ __('tool.scriptEscape.input') }}:</h4>
                    <textarea id="scriptEscapeToolInput" class="form-control" style="min-height: 800px"></textarea>
                </div>
            </div>
        </div>
        <!-- ENDE Input Card -->
        <!-- Output Card -->
        <div class="col-12 col-md-6 mt-2">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">{{ __('tool.scriptEscape.output') }}:</h4>
                    <textarea id="scriptEscapeToolOutput" class="form-control" style="min-height: 800px"></textarea>
                </div>
            </div>
        </div>
        <!-- ENDE Output Card -->
    </div>
@endsection

@push('js')
    <script>
        function updateInputs() {
            var input = $("#scriptEscapeToolInput").val()
            var result = escapeSpecialCharsToUnicode(input)
            $("#scriptEscapeToolOutput").val(result)
        }
        
        $("#scriptEscapeToolInput").on("input", updateInputs)
        updateInputs()

        function escapeSpecialCharsToUnicode(jsCode) {
            let result = "";

            for (const char of jsCode) {
                const codePoint = char.codePointAt(0);

                if (codePoint <= 0x7F) {
                    // ASCII character
                    result += char;
                } else if (codePoint <= 0xFFFF) {
                    // BMP character
                    result += "\\u" + codePoint.toString(16).padStart(4, "0");
                } else {
                // Astral character → surrogate pair
                    const high = Math.floor((codePoint - 0x10000) / 0x400) + 0xD800;
                    const low = ((codePoint - 0x10000) % 0x400) + 0xDC00;
                    result +=
                        "\\u" + high.toString(16).padStart(4, "0") +
                        "\\u" + low.toString(16).padStart(4, "0");
                }
            }

            return result;
        }
    </script>
@endpush
