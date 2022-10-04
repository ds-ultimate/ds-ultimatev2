<!doctype html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale = 1.0, maximum-scale=1.0, user-scalable=no" />
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'DS-Ultimate') }} @yield('titel')</title>

    <!-- Fonts -->
    <!-- <link href="https://fonts.googleapis.com/css?family=Raleway:100,600" rel="stylesheet" type="text/css"> -->
    <link href="{{ \App\Util\BasicFunctions::asset('css/app.css') }}" rel="stylesheet">
    <link href="{{ \App\Util\BasicFunctions::asset('css/admin_sidebar.css') }}" rel="stylesheet">
    <link href="{{ \App\Util\BasicFunctions::asset('css/datatables.min.css') }}" rel="stylesheet">
    <link rel="shortcut icon" type="image/x-icon" href="{{ asset('images/favicon.ico') }}">
    <link href="{{ \App\Util\BasicFunctions::asset('css/fontawesome.css') }}" rel="stylesheet" />
    @stack('style')
</head>

<body>
<div class="flex-center main-container">
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
</div>

<script src="{{ \App\Util\BasicFunctions::asset('js/app.js') }}"></script>
<script src="{{ \App\Util\BasicFunctions::asset('plugin/jquery-ui/jquery-ui.min.js') }}"></script>
<script src="{{ \App\Util\BasicFunctions::asset('js/datatables.min.js') }}"></script>
<script src="{{ \App\Util\BasicFunctions::asset('js/customCode.js') }}"></script>
@stack('js')
</body>
</html>
