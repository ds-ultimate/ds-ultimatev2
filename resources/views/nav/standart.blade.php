<nav class="navbar navbar-expand-lg navbar-light bg-primary">
    <a class="navbar-brand" href="{{ route('index') }}">
        <img src="{{ asset('images/logo.png') }}" height="30" class="d-inline-block align-top" alt="">
        DS-Ultimate
    </a>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav mr-auto">
            <li class="nav-item active">
                <a class="nav-link" href="#">{{__('Startseite')}} <span class="sr-only">(current)</span></a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="#">Features</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="#">Pricing</a>
            </li>
            <li class="nav-item">
                <a class="nav-link disabled" href="#" tabindex="-1" aria-disabled="true">Disabled</a>
            </li>
        </ul>
        <form class="form-inline my-2 my-lg-0">
            <input class="form-control mr-sm-2" type="search" placeholder="{{ __('Suche') }}" aria-label="Search">
            <button class="btn btn-outline-dark my-2 my-sm-0 mr-sm-2" type="submit">{{ __('Suche') }}</button>
        </form>
        <div class="dropdown">
            <button class="btn btn-outline-dark dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                {{ __('Sprache') }}
            </button>
            <div class="dropdown-menu dropdown-menu-lg-right" aria-labelledby="dropdownMenuButton" style="width: 100px">
                <a class="dropdown-item" href="{{ route('locale', 'de') }}"><span class="flag-icon flag-icon-de"></span> Deutsch</a>
                <a class="dropdown-item" href="{{ route('locale', 'en') }}"><span class="flag-icon flag-icon-gb"></span> English</a>
            </div>
        </div>
    </div>
</nav>
