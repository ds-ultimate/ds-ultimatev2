@extends('layouts.app')

@push('style')
    <style>
        .card-wrapper .avatar {
            display: block;
            margin-top: -60px;
            overflow: hidden;
            width: 120px;
        }
        .card-rotating {
            height: 450px;
            transform-style: preserve-3d;
            transition: .5s;
        }
        .card-team {
            font-weight: 400;
            border: 0;
            box-shadow: 0 2px 5px 0 rgba(0,0,0,.16), 0 2px 10px 0 rgba(0,0,0,.12);
        }
        .front{
            z-index: -1;
            border-top-left-radius: calc(5rem - 1px);
        }
        .back{
            z-index: -1;
        }
        .fb-ic {
            color: #3b5998!important;
        }
        .tw-ic {
            color: #55acee!important;
        }
        .twitch-ic {
            color: #6441A4!important;
        }
        .card-img-top {
            width: 100%;
            border-top-left-radius: calc(5rem - 1px);
        }
    </style>
@endpush

@section('content')
    <div class="row justify-content-center">
        <div class="col-12">
            <div class="col-md-5 p-lg-3 mx-auto my-1 text-center">
                <h1 class="font-weight-normal">{{ config('app.name') }}</h1>
            </div>
        </div>
        <!-- Normale Welten -->
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <!-- Section: Team v.4 -->
                    <section class="my-5">
                        <!-- Section heading -->
                        <h2 class="h1-responsive font-weight-bold text-center my-5">Das DS-Ultimate Team</h2>
                        <!-- Section description -->
                        <p class="grey-text text-center w-responsive mx-auto mb-5">
                            DS-Ultimate ist ein Projekt das von uns in der Freizeit weiter entwickelt und verbessert wird.
                        </p>
                        <!-- Grid row -->
                        <div class="row mb-4">
                            <!-- Grid column -->
                            <div class="col-lg-4 col-md-12 mb-lg-0 mb-4">
                                <!-- Rotating card -->
                                <div id="card" class="card-wrapper">
                                    <div id="card-1" class="card-rotating text-center">
                                        <!-- Front Side -->
                                        <div class="face front bg-white card-team">
                                            <!-- Image -->
                                            <div class="card-up">
                                                <img class="card-img-top" height="200px" src="{{ asset('images/team/background/keyboard.jpg') }}" alt="Team member card image">
                                            </div>
                                            <!-- Avatar -->
                                            <div class="avatar mx-auto white">
                                                <img src="{{ asset('images/team/avatar/dominik.jpg') }}" class="rounded-circle img-fluid" alt="First sample avatar image">
                                            </div>
                                            <!-- Content -->
                                            <div class="card-body">
                                                <h4 class="font-weight-bold mt-1 mb-3">Dominik 'Valerius2101'</h4>
                                                <p class="font-weight-bold dark-grey-text h5 mb-4">
                                                    Initiator
                                                </p>
                                                <span class="flag-icon flag-icon-de rounded" style="width: 2.666666em; line-height: 2em;"></span>
                                                <!-- Triggering button -->
                                                {{--<i class="fixed-bottom">Click to rotate</i>--}}
                                            </div>
                                        </div>
                                        <!-- Front Side -->
                                        <!-- Back Side -->
                                        <div class="face back bg-white card-team">
                                            <!-- Content -->
                                            {{--<div class="card-body">--}}
                                                {{--<!-- Content -->--}}
                                                {{--<h4 class="font-weight-bold mt-4 mb-2">--}}
                                                    {{--<strong> </strong>--}}
                                                {{--</h4>--}}
                                                {{--<hr>--}}
                                                {{--<p>--}}
                                                    {{--<br>--}}
                                                    {{--<br>--}}
                                                    {{--<br>--}}
                                                    {{--<br>--}}
                                                {{--</p>--}}
                                                {{--<hr>--}}
                                                {{--<!-- Social Icons -->--}}
                                                {{--<ul class="list-inline list-unstyled">--}}
                                                    {{--<li class="list-inline-item">--}}
                                                        {{--<a class="p-2 fa-lg fb-ic" href="https://www.facebook.com/valerius2101.de">--}}
                                                            {{--<i class="fab fa-facebook-f"></i>--}}
                                                        {{--</a>--}}
                                                    {{--</li>--}}
                                                    {{--<li class="list-inline-item">--}}
                                                        {{--<a class="p-2 fa-lg twitch-ic" href="https://www.twitch.tv/valerius2101">--}}
                                                            {{--<i class="fab fa-twitch"> </i>--}}
                                                        {{--</a>--}}
                                                    {{--</li>--}}
                                                    {{--<li class="list-inline-item">--}}
                                                        {{--<a class="p-2 fa-lg tw-ic" href="https://twitter.com/xXValeriusXx">--}}
                                                            {{--<i class="fab fa-twitter"> </i>--}}
                                                        {{--</a>--}}
                                                    {{--</li>--}}
                                                {{--</ul>--}}
                                                {{--<!-- Triggering button -->--}}
                                                {{--<i class="fixed-bottom">Click to rotate</i>--}}
                                            {{--</div>--}}
                                        </div>
                                        <!-- Back Side -->
                                    </div>
                                </div>
                                <!-- Rotating card -->
                            </div>
                            <!-- Grid column -->
                            <!-- Grid column -->
                            <div class="col-lg-4 col-md-12 mb-lg-0 mb-4">
                                <!-- Rotating card -->
                                <div id="card" class="card-wrapper">
                                    <div id="card-1" class="card-rotating text-center">
                                        <!-- Front Side -->
                                        <div class="face front bg-white card-team">
                                            <!-- Image -->
                                            <div class="card-up">
                                                <img class="card-img-top" height="200px" src="{{ asset('images/team/background/system.jpg') }}" alt="Team member card image">
                                            </div>
                                            <!-- Avatar -->
                                            <div class="avatar mx-auto white">
                                                <img src="{{ asset('images/team/avatar/sebastian.jpg') }}" class="rounded-circle img-fluid" alt="First sample avatar image">
                                            </div>
                                            <!-- Content -->
                                            <div class="card-body">
                                                <h4 class="font-weight-bold mt-1 mb-3">Sebastian 'Nehoz'</h4>
                                                <p class="font-weight-bold dark-grey-text h5 mb-4">
                                                    Systemadministrator (Funder)
                                                </p>
                                                <span class="flag-icon flag-icon-de rounded" style="width: 2.666666em; line-height: 2em;"></span>
                                                <!-- Triggering button -->
                                                {{--<i class="fixed-bottom">Click to rotate</i>--}}
                                            </div>
                                        </div>
                                        <!-- Front Side -->
                                        <!-- Back Side -->
                                        <div class="face back bg-white card-team">
                                            <!-- Content -->
                                            {{--<div class="card-body">--}}
                                                {{--<!-- Content -->--}}
                                                {{--<h4 class="font-weight-bold mt-4 mb-2">--}}
                                                    {{--<strong>About me</strong>--}}
                                                {{--</h4>--}}
                                                {{--<hr>--}}
                                                {{--<p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Maxime quae, dolores dicta.--}}
                                                    {{--Blanditiis rem amet repellat, dolores nihil quae in mollitia asperiores ut rerum repellendus,--}}
                                                    {{--voluptatum eum, officia laudantium quaerat?--}}
                                                {{--</p>--}}
                                                {{--<hr>--}}
                                                {{--<!-- Social Icons -->--}}
                                                {{--<ul class="list-inline list-unstyled">--}}
                                                    {{--<li class="list-inline-item">--}}
                                                        {{--<a class="p-2 fa-lg fb-ic">--}}
                                                            {{--<i class="fab fa-facebook-f"></i>--}}
                                                        {{--</a>--}}
                                                    {{--</li>--}}
                                                    {{--<li class="list-inline-item">--}}
                                                        {{--<a class="p-2 fa-lg pin-ic">--}}
                                                            {{--<i class="fab fa-pinterest"> </i>--}}
                                                        {{--</a>--}}
                                                    {{--</li>--}}
                                                    {{--<li class="list-inline-item">--}}
                                                        {{--<a class="p-2 fa-lg ins-ic">--}}
                                                            {{--<i class="fab fa-instagram"> </i>--}}
                                                        {{--</a>--}}
                                                    {{--</li>--}}
                                                    {{--<li class="list-inline-item">--}}
                                                        {{--<a class="p-2 fa-lg tw-ic">--}}
                                                            {{--<i class="fab fa-twitter"> </i>--}}
                                                        {{--</a>--}}
                                                    {{--</li>--}}
                                                {{--</ul>--}}
                                                {{--<!-- Triggering button -->--}}
                                                {{--<i class="fixed-bottom">Click to rotate</i>--}}
                                            {{--</div>--}}
                                        </div>
                                        <!-- Back Side -->
                                    </div>
                                </div>
                                <!-- Rotating card -->
                            </div>
                            <!-- Grid column -->
                            <!-- Grid column -->
                            <div class="col-lg-4 col-md-12 mb-lg-0 mb-4">
                                <!-- Rotating card -->
                                <div id="card" class="card-wrapper">
                                    <div id="card-1" class="card-rotating text-center">
                                        <!-- Front Side -->
                                        <div class="face front bg-white card-team">
                                            <!-- Image -->
                                            <div class="card-up">
                                                <img class="card-img-top" height="200px" src="{{ asset('images/team/background/dev1.jpg') }}" alt="Team member card image">
                                            </div>
                                            <!-- Avatar -->
                                            <div class="avatar mx-auto white">
                                                <img src="{{ asset('images/team/avatar/michael.png') }}" class="rounded-circle img-fluid" alt="First sample avatar image">
                                            </div>
                                            <!-- Content -->
                                            <div class="card-body">
                                                <h4 class="font-weight-bold mt-1 mb-3">Michael 'MKich'</h4>
                                                <p class="font-weight-bold dark-grey-text h5 mb-4">Front-/Back-end Developer</p>
                                                <span class="flag-icon flag-icon-at rounded" style="width: 2.666666em; line-height: 2em;"></span>
                                                <!-- Triggering button -->
                                                {{--<i class="fixed-bottom">Click to rotate</i>--}}
                                            </div>
                                        </div>
                                        <!-- Front Side -->
                                        <!-- Back Side -->
                                        <div class="face back bg-white card-team">
                                            <!-- Content -->
                                            {{--<div class="card-body">--}}
                                                {{--<!-- Triggering button -->--}}
                                                {{--<i class="fixed-bottom">Click to rotate</i>--}}
                                            {{--</div>--}}
                                        </div>
                                        <!-- Back Side -->
                                    </div>
                                </div>
                                <!-- Rotating card -->
                            </div>
                            <!-- Grid column -->
                        </div>
                        <!-- Grid row -->
                        <!-- Grid row -->
                        <div class="row mb-4">
                            <!-- Grid column -->
                            <div class="col-lg-4 col-md-12 mb-lg-0 mb-4">
                                <!-- Rotating card -->
                                <div id="card" class="card-wrapper">
                                    <div id="card-1" class="card-rotating text-center">
                                        <!-- Front Side -->
                                        <div class="face front bg-white card-team">
                                            <!-- Image -->
                                            <div class="card-up">
                                                <img class="card-img-top" height="200px" src="{{ asset('images/team/background/dev.jpg') }}" alt="Team member card image">
                                            </div>
                                            <!-- Avatar -->
                                            <div class="avatar mx-auto white">
                                                <img src="{{ asset('images/team/avatar/marc.png') }}" class="rounded-circle img-fluid" alt="First sample avatar image">
                                            </div>
                                            <!-- Content -->
                                            <div class="card-body">
                                                <h4 class="font-weight-bold mt-1 mb-3">Marc 'skatecram'</h4>
                                                <p class="font-weight-bold dark-grey-text h5 mb-4">Front-/Back-end Developer</p>
                                                <span class="flag-icon flag-icon-ch rounded" style="width: 2.666666em; line-height: 2em;"></span>
                                                <!-- Triggering button -->
                                                {{--<i class="fixed-bottom">Click to rotate</i>--}}
                                            </div>
                                        </div>
                                        <!-- Front Side -->
                                        <!-- Back Side -->
                                        <div class="face back bg-white card-team">
                                            {{--<!-- Content -->--}}
                                            {{--<div class="card-body">--}}
                                                {{--<!-- Triggering button -->--}}
                                                {{--<i class="fixed-bottom">Click to rotate</i>--}}
                                            {{--</div>--}}
                                        </div>
                                        <!-- Back Side -->
                                    </div>
                                </div>
                                <!-- Rotating card -->
                            </div>
                            <!-- Grid column -->
                            <!-- Grid column -->
                            <div class="col-lg-4 col-md-12 mb-lg-0 mb-4">
                                <!-- Rotating card -->
                                <div id="card" class="card-wrapper">
                                    <div id="card-1" class="card-rotating text-center">
                                        <!-- Front Side -->
                                        <div class="face front bg-white card-team">
                                            <!-- Image -->
                                            <div class="card-up">
                                                <img class="card-img-top" height="200px" src="{{ asset('images/team/background/system.jpg') }}" alt="Team member card image">
                                            </div>
                                            <!-- Avatar -->
                                            <div class="avatar mx-auto white">
                                                <img src="{{ asset('images/team/avatar/jonas.jpg') }}" class="rounded-circle img-fluid" alt="First sample avatar image">
                                            </div>
                                            <!-- Content -->
                                            <div class="card-body">
                                                <h4 class="font-weight-bold mt-1 mb-3">Jonas 'EchtkPvL'</h4>
                                                <p class="font-weight-bold dark-grey-text h5 mb-4">Systemadministrator</p>
                                                <span class="flag-icon flag-icon-de rounded" style="width: 2.666666em; line-height: 2em;"></span>
                                                <!-- Triggering button -->
                                                {{--<i class="fixed-bottom">Click to rotate</i>--}}
                                            </div>
                                        </div>
                                        <!-- Front Side -->
                                        <!-- Back Side -->
                                        <div class="face back bg-white card-team">
                                            <!-- Content -->
                                            {{--<div class="card-body">--}}
                                                {{--<!-- Content -->--}}
                                                {{--<h4 class="font-weight-bold mt-4 mb-2">--}}
                                                    {{--<strong>About me</strong>--}}
                                                {{--</h4>--}}
                                                {{--<hr>--}}
                                                {{--<p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Maxime quae, dolores dicta.--}}
                                                    {{--Blanditiis rem amet repellat, dolores nihil quae in mollitia asperiores ut rerum repellendus,--}}
                                                    {{--voluptatum eum, officia laudantium quaerat?--}}
                                                {{--</p>--}}
                                                {{--<hr>--}}
                                                {{--<!-- Social Icons -->--}}
                                                {{--<ul class="list-inline list-unstyled">--}}
                                                    {{--<li class="list-inline-item">--}}
                                                        {{--<a class="p-2 fa-lg fb-ic">--}}
                                                            {{--<i class="fab fa-facebook-f"></i>--}}
                                                        {{--</a>--}}
                                                    {{--</li>--}}
                                                    {{--<li class="list-inline-item">--}}
                                                        {{--<a class="p-2 fa-lg pin-ic">--}}
                                                            {{--<i class="fab fa-pinterest"> </i>--}}
                                                        {{--</a>--}}
                                                    {{--</li>--}}
                                                    {{--<li class="list-inline-item">--}}
                                                        {{--<a class="p-2 fa-lg ins-ic">--}}
                                                            {{--<i class="fab fa-instagram"> </i>--}}
                                                        {{--</a>--}}
                                                    {{--</li>--}}
                                                    {{--<li class="list-inline-item">--}}
                                                        {{--<a class="p-2 fa-lg tw-ic">--}}
                                                            {{--<i class="fab fa-twitter"> </i>--}}
                                                        {{--</a>--}}
                                                    {{--</li>--}}
                                                {{--</ul>--}}
                                                {{--<!-- Triggering button -->--}}
                                                {{--<i class="fixed-bottom">Click to rotate</i>--}}
                                            {{--</div>--}}
                                        </div>
                                        <!-- Back Side -->
                                    </div>
                                </div>
                                <!-- Rotating card -->
                            </div>
                            <!-- Grid column -->
                        </div>
                        <!-- Grid row -->
                    </section>
                    <!-- Section: Team v.4 -->
                </div>
            </div>
        </div>
        <!-- ENDE Normale Welten -->
    </div>
@endsection

@push('js')
    <script src="https://cdn.rawgit.com/nnattawat/flip/master/dist/jquery.flip.min.js"></script>
    <script>
        $(function($) {
            $(".card-wrapper").flip();
        });
    </script>
@endpush
