<!doctype html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale = 1.0, maximum-scale=1.0, user-scalable=no" />
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }} @yield('titel')</title>

    <!-- Fonts -->
    <!-- <link href="https://fonts.googleapis.com/css?family=Raleway:100,600" rel="stylesheet" type="text/css"> -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <link href="{{ asset('css/admin_sidebar.css') }}" rel="stylesheet">
    <link href="{{ asset('css/datatables.min.css') }}" rel="stylesheet">
    <link rel="shortcut icon" type="image/x-icon" href="{{ asset('images/favicon.ico') }}">
    <link href="{{ asset('css/fontawesome.css') }}" rel="stylesheet" />
    @stack('style')
</head>

<body style="padding-right: 0px; min-height: 100%; margin-bottom: 80px">
    @include('admin.shared.menue')
    <div class="admin-content-wrapper">
        @include('admin.shared.header')
        <div class="p-3">
            <div id="toast-content" style="position: absolute; top: 60px; right: 10px; z-index: 100;">

            </div>
            @yield('content')
        </div>
    </div>
    @include('footer.standart')
    
    <script src="{{ asset('js/app.js') }}"></script>
    <script src="{{ asset('plugin/jquery-ui/jquery-ui.min.js') }}"></script>
    <script src="{{ asset('js/datatables.min.js') }}"></script>
    @stack('js')
</body>
</html>
