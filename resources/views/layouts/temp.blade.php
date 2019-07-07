<!doctype html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width; initial-scale = 1.0; maximum-scale=1.0; user-scalable=no" />

    <title>{{ config('app.name', 'Laravel') }} @yield('titel')</title>

    <!-- Fonts -->
    <!-- <link href="https://fonts.googleapis.com/css?family=Raleway:100,600" rel="stylesheet" type="text/css"> -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <link href="{{ asset('plugin/Datatables/DataTables-1.10.18/css/dataTables.bootstrap4.min.css') }}" rel="stylesheet">
    <link href="{{ asset('plugin/Datatables/Responsive-2.2.2/css/responsive.bootstrap4.css') }}" rel="stylesheet">
</head>
<body style="padding-right: 0px">
<div class="flex-center position-ref full-height">
    @include('nav.standart')
    <div class="container mb-5 pb-3">
        @yield('content')
    </div>
    @include('footer.standart')
</div>

<!-- jQuery first, then Popper.js, then Bootstrap JS -->
<script src="{{ asset('js/jquery.min.js') }}"></script>
<script src="{{ asset('js/bootstrap.bundle.min.js') }}"></script>
<script src="{{ asset('plugin/Datatables/DataTables-1.10.18/js/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('plugin/Datatables/DataTables-1.10.18/js/dataTables.bootstrap4.js') }}"></script>
<script src="{{ asset('plugin/Datatables/Responsive-2.2.2/js/dataTables.responsive.min.js') }}"></script>
<script src="{{ asset('js/numeral.min.js') }}"></script>
@yield('js')
</body>
</html>
