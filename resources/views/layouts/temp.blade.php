<!doctype html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width; initial-scale = 1.0; maximum-scale=1.0; user-scalable=no" />
    <meta name="google-site-verification" content="VHh31G0jFa1hVoy54n4ZeGupk8sU4kOxPeYGgOEblkA" />

    <title>{{ config('app.name', 'Laravel') }} @yield('titel')</title>

    <!-- Fonts -->
    <!-- <link href="https://fonts.googleapis.com/css?family=Raleway:100,600" rel="stylesheet" type="text/css"> -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <link href="{{ asset('plugin/Datatables/DataTables-1.10.18/css/dataTables.bootstrap4.min.css') }}" rel="stylesheet">
    <link href="{{ asset('plugin/Datatables/Responsive-2.2.2/css/responsive.bootstrap4.css') }}" rel="stylesheet">
    <link rel="shortcut icon" type="image/x-icon" href="{{ asset('images/favicon.ico') }}">
    <link rel="stylesheet" href="https://kit-free.fontawesome.com/releases/latest/css/free.min.css" media="all">
    <link rel="stylesheet" href="https://kit-free.fontawesome.com/releases/latest/css/free-v4-shims.min.css" media="all">
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
    @yield('style')
</head>
<body style="padding-right: 0px; min-height: 100%; margin-bottom: 80px">
<div class="flex-center position-ref full-height">
    @include('nav.standart')
    <div class="container mb-5 pb-3">
        @yield('content')
    </div>
    @include('footer.standart')
</div>

<!-- jQuery first, then Popper.js, then Bootstrap JS -->
<script src="https://kit.fontawesome.com/d27d915b5f.js"></script>
<script src="{{ asset('js/jquery.min.js') }}"></script>
<script src="{{ asset('plugin/jquery-ui/jquery-ui.min.js') }}"></script>
<script src="{{ asset('js/bootstrap.bundle.min.js') }}"></script>
<script src="{{ asset('plugin/Datatables/DataTables-1.10.18/js/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('plugin/Datatables/DataTables-1.10.18/js/dataTables.bootstrap4.js') }}"></script>
<script src="{{ asset('plugin/Datatables/Responsive-2.2.2/js/dataTables.responsive.min.js') }}"></script>
<script src="{{ asset('js/numeral.min.js') }}"></script>
<style>
    .cookie-consent {
        color: white;
        background: red;
        padding: 15px;
        position: absolute;
        top: 50%;
        left: 50%;
        -ms-transform: translateX(-50%) translateY(-50%);
        -webkit-transform: translate(-50%,-50%);
        transform: translate(-50%,-50%);
    }
</style>
@yield('js')
</body>
</html>
