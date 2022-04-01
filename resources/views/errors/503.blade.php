<!doctype html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width; initial-scale = 1.0; maximum-scale=1.0; user-scalable=no" />
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <link href="{{ \App\Util\BasicFunctions::asset('css/app.css') }}" rel="stylesheet">
    <link rel="shortcut icon" type="image/x-icon" href="{{ asset('images/favicon.ico') }}">
    @if (config('app.debug') == false)
        <!-- Matomo -->
        <script type="text/javascript">
            var _paq = window._paq || [];
            /* tracker methods like "setCustomDimension" should be called before "trackPageView" */
            _paq.push(["setDocumentTitle", document.domain + "/" + document.title]);
            _paq.push(["setCookieDomain", "*.ds-ultimate.de"]);
            _paq.push(['trackPageView']);
            _paq.push(['enableLinkTracking']);
            (function() {
                var u="//matomo.ds-ultimate.de/";
                _paq.push(['setTrackerUrl', u+'matomo.php']);
                _paq.push(['setSiteId', '1']);
                var d=document, g=d.createElement('script'), s=d.getElementsByTagName('script')[0];
                g.type='text/javascript'; g.async=true; g.defer=true; g.src=u+'matomo.js'; s.parentNode.insertBefore(g,s);
            })();
        </script>
        <noscript><p><img src="//matomo.ds-ultimate.de/matomo.php?idsite=1&amp;rec=1" style="border:0;" alt="" /></p></noscript>
        <!-- End Matomo Code -->
    @endif
</head>
<body style="padding-right: 0px; min-height: 100%; margin-bottom: 80px">
<div class="flex-center position-ref full-height">
    @include('nav.down')
    <div class="container mb-5 pb-3">
        <div class="cointainer">
            <div class="row justify-content-center">
                <div class="col-12">
                    <div class="col-md-5 p-lg-5 mx-auto my-1 text-center">
                        <h1 class="font-weight-normal">Wartungsarbeiten</h1>
                    </div>
                </div>
                <div class="col-12">
                    <div class="card">
                        <div class="card-body text-center">
                            Wir führen derzeit Wartungsarbeiten durch.<br>
                            Nähere Infos erhaltet ihr auf unserem Discord unter <b>#update</b>:
                            <a href="https://discord.gg/JcDAmPm" target="_blank">Einladungs Link</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @include('footer.down')
</div>

<!-- jQuery first, then Popper.js, then Bootstrap JS -->
<script src="{{ \App\Util\BasicFunctions::asset('js/app.js') }}"></script>
</body>
</html>
