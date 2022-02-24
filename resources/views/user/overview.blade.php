@extends('layouts.app')

@section('titel', ucfirst(__('user.title.overview')).' '.Auth::user()->name)

@section('content')
    <div class="row justify-content-center">
        <!-- Titel für Tablet | PC -->
        <div class="p-lg-5 mx-auto my-1 text-center d-none d-lg-block">
            <h1 class="font-weight-normal">{{ ucfirst(__('user.title.overview')).' '.Auth::user()->name }}</h1>
        </div>
        <!-- ENDE Titel für Tablet | PC -->
        <!-- Titel für Mobile Geräte -->
        <div class="p-lg-5 mx-auto my-1 text-center d-lg-none truncate">
            <h1 class="font-weight-normal">
                {{ ucfirst(__('user.title.overview')) }}
            </h1>
            <h4>
                {{ Auth::user()->name }}
            </h4>
        </div>
        <!-- ENDE Titel für Geräte | PC -->
        <div class="col-12">
            <div class="card">
                <div id="user-overview" class="card-body">
                    <ul class="nav nav-tabs" id="myTab" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link {{ ($page == 'myMap')? 'active' : '' }}" id="myMap-tab" data-toggle="tab" href="#myMap" role="tab" aria-controls="home" aria-selected="true">{{ __('ui.own.maps') }}</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ ($page == 'myAttackplanner')? 'active' : '' }}" id="myAttackplanner-tab" data-toggle="tab" href="#myAttackplanner" role="tab" aria-controls="myAttackplanner" aria-selected="false">{{ __('ui.own.attackplanner') }}</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ ($page == 'myAnimatedMap')? 'active' : '' }}" id="myAnimatedMap-tab" data-toggle="tab" href="#myAnimatedMap" role="tab" aria-controls="myAnimatedMap" aria-selected="false">{{ __('ui.own.animatedMap') }}</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ ($page == 'myRenderedAnimatedMap')? 'active' : '' }}" id="myRenderedAnimatedMap-tab" data-toggle="tab" href="#myRenderedAnimatedMap" role="tab" aria-controls="myRenderedAnimatedMap" aria-selected="false">{{ __('ui.own.renderedAnimatedMap') }}</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ ($page == 'followMap')? 'active' : '' }}" id="followMap-tab" data-toggle="tab" href="#followMap" role="tab" aria-controls="followMap" aria-selected="false">{{ __('ui.follow.map') }}</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ ($page == 'followAttackplanner')? 'active' : '' }}" id="followAttackplanner-tab" data-toggle="tab" href="#followAttackplanner" role="tab" aria-controls="followAttackplanner" aria-selected="false">{{ __('ui.follow.attackplanner') }}</a>
                        </li>
                    </ul>
                    <div class="tab-content" id="myTabContent">
                        @include('user.overviewParts.ownMaps')
                        @include('user.overviewParts.ownAttackLists')
                        @include('user.overviewParts.ownAnimatedMaps')
                        @include('user.overviewParts.ownRenderedAnimatedMaps')
                        @include('user.overviewParts.followMaps')
                        @include('user.overviewParts.followAttackLists')
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('js')
    <script src="{{ asset('plugin/bootstrap-confirmation/bootstrap-confirmation.min.js') }}"></script>
    <script>
        $(document).ready(function () {
            $('.nav-link', $('#user-overview')).on("click", function (e) {
                var href = $(this).attr("href");
                history.pushState(null, null, href.replace('#', '/user/overview/'));
                e.preventDefault();
            });

            $('[data-toggle=confirmation]').confirmation({
                rootSelector: '[data-toggle=confirmation]',
                popout: true,
                title: "{{ __('user.confirm.destroy.title') }}",
                btnOkLabel: "{{ __('user.confirm.destroy.ok') }}",
                btnOkClass: 'btn btn-danger',
                btnCancelLabel: "{{ __('user.confirm.destroy.cancel') }}",
                btnCancelClass: 'btn btn-info',
            });
        })

        function copy(type) {
            /* Get the text field */
            var copyText = $("#" + type);
            /* Select the text field */
            copyText.select();
            /* Copy the text inside the text field */
            document.execCommand("copy");
        }
    </script>
@endpush
