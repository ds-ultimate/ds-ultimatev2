@extends('layouts.app')

@section('content')
    <div class="row justify-content-center">
        <div class="col-12 m-5">
            <h2 class="h1-responsive font-weight-bold text-center mb-5">Das DS-Ultimate Team</h2>
            <p class="grey-text text-center w-responsive mx-auto mb-5">
                DS-Ultimate ist ein Projekt das von uns in der Freizeit weiter entwickelt und verbessert wird.
            </p>
            
            <div class="col-12 row">
                <div class="col-xl-4 col-lg-6 col-md-12 mb-4">
                    <div class="card-team">
                        <img class="card-img-top" height="200px" src="{{ asset('images/team/background/system.jpg') }}">
                        <div class="avatar mx-auto white">
                            <img src="{{ asset('images/team/avatar/sebastian.jpg') }}" class="rounded-circle img-fluid">
                        </div>
                        <!-- Content -->
                        <div class="card-body">
                            <h4 class="font-weight-bold mt-1 mb-3">Sebastian 'Nehoz'</h4>
                            <p class="font-weight-bold dark-grey-text h5 mb-4">Systemadministrator (Funder)</p>
                            <span class="flag-icon flag-icon-de rounded" style="width: 2.666666em; line-height: 2em;"></span>
                        </div>
                    </div>
                </div>
                <div class="col-xl-4 col-lg-6 col-md-12 mb-4">
                    <div class="card-team">
                        <img class="card-img-top" height="200px" src="{{ asset('images/team/background/dev1.jpg') }}">
                        <div class="avatar mx-auto white">
                            <img src="{{ asset('images/team/avatar/michael.png') }}" class="rounded-circle img-fluid">
                        </div>
                        <!-- Content -->
                        <div class="card-body">
                            <h4 class="font-weight-bold mt-1 mb-3">Michael 'MKich'</h4>
                            <p class="font-weight-bold dark-grey-text h5 mb-4">Front-/Back-end Developer</p>
                            <span class="flag-icon flag-icon-at rounded" style="width: 2.666666em; line-height: 2em;"></span>
                        </div>
                    </div>
                </div>
                <div class="col-xl-4 col-lg-6 col-md-12 mb-4">
                    <div class="card-team">
                        <img class="card-img-top" height="200px" src="{{ asset('images/team/background/dev.jpg') }}">
                        <div class="avatar mx-auto white">
                            <img src="{{ asset('images/team/avatar/marc.png') }}" class="rounded-circle img-fluid">
                        </div>
                        <!-- Content -->
                        <div class="card-body">
                            <h4 class="font-weight-bold mt-1 mb-3">Marc 'skatecram'</h4>
                            <p class="font-weight-bold dark-grey-text h5 mb-4">Front-/Back-end Developer</p>
                            <span class="flag-icon flag-icon-ch rounded" style="width: 2.666666em; line-height: 2em;"></span>
                        </div>
                    </div>
                </div>
                <div class="col-xl-4 col-lg-6 col-md-12 mb-4">
                    <div class="card-team">
                        <!-- Content -->
                        <div class="card-body">
                            <h4 class="font-weight-bold mt-4 mb-2"><strong>Besonderer Dank</strong></h4>
                            <br>
                            <p><b>Jonas 'EchtkPvL'</b> (Systemadministrator) <span class="flag-icon flag-icon-de rounded" style="width: 1.333333em;"></span></p>
                            <br>
                            <p><b>Dominik 'Valerius2101'</b><span class="flag-icon flag-icon-de rounded" style="width: 1.333333em;"></span></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
