<!doctype html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, minimum-scale=1, maximum-scale=2.0, user-scalable=yes" />
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }} @yield('titel')</title>

    <!-- Fonts -->
    <!-- <link href="https://fonts.googleapis.com/css?family=Raleway:100,600" rel="stylesheet" type="text/css"> -->
    @darkmode
        <link href="{{ asset('css/dark.css') }}" rel="stylesheet">
        <link href="{{ asset('plugin/jquery-ui/dark/jquery-ui.min.css') }}" rel="stylesheet">
        <meta name="color-scheme" content="dark">
        <meta name="theme-color" content="#202327">
    @else
        <link href="{{ asset('css/app.css') }}" rel="stylesheet">
        <link href="{{ asset('plugin/jquery-ui/light/jquery-ui.min.css') }}" rel="stylesheet">
        <meta name="color-scheme" content="light">
        <meta name="theme-color" content="#edd492">
    @enddarkmode
    <link href="{{ asset('css/datatables.min.css') }}" rel="stylesheet">
    <link rel="shortcut icon" type="image/x-icon" href="{{ asset('images/favicon.ico') }}">
    <link href="{{ asset('css/fontawesome.css') }}" rel="stylesheet" />
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
    @stack('style')
</head>
<body>
<div class="flex-center main-container">
    @include('nav.standart')
    <div class="container">
        <div id="toast-content" style="position: fixed; top: 60px; right: 10px; z-index: 100;">

        </div>
        @yield('content')
    </div>
    @include('footer.standart')
</div>
@include('cookie-consent::index')
<script src="{{ asset('js/app.js') }}"></script>
<script src="{{ asset('plugin/jquery-ui/jquery-ui.min.js') }}"></script>
<script src="{{ asset('js/datatables.min.js') }}"></script>
@stack('js')
</body>
</html>
