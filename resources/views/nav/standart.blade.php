@forceSet($server)
@forceSet($worldData)
<?php
function generateMenuEntry($entry, $level=0) {
    if($entry['subElements'] === null) {//normal entry
        ?>
        @if($level==0)
            <li class="nav-item">
                @if($entry['enabled'])
                    <a id="{{ $entry['id'] }}" class="nav-link" href="{{ $entry['link'] }}"@if($entry['noFollow']) rel="nofollow"@endif>{{ $entry['title']}}</a>
                @else
                    <span class="nav-link d-inline-block nav-tooltip" title="{{ $entry['tooltip'] }}">
                        <a id="{{ $entry['id'] }}" class="nav-link btn-link disabled nav-tooltip" href="#">{{ $entry['title']}}</a>
                    </span>
                @endif
            </li>
        @else
            <li id="{{ $entry['id'] }}-cont" class="dropdown-item">
                @if($entry['enabled'])
                    <a id="{{ $entry['id'] }}" href="{{ $entry['link'] }}"@if($entry['noFollow']) rel="nofollow"@endif>{{ $entry['title']}}</a>
                @else
                    <span class="d-inline-block nav-tooltip" title="{{ $entry['tooltip'] }}">
                        <a id="{{ $entry['id'] }}" class="btn-link disabled" href="#">{{ $entry['title']}}</a>
                    </span>
                @endif
            </li>
        @endif
        <?php
    } else {
        ?>
        @if($level==0)
            <li class="nav-item dropdown">
                <a id="{{ $entry['id'] }}" class="nav-link dropdown-toggle" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    {{ $entry['title']}}
                </a>
                <ul class="dropdown-menu multi-level" role="menu">
                    <?php foreach($entry['subElements'] as $item) {
                        generateMenuEntry($item, $level+1);
                    } ?>
                </ul>
            </li>
        @else
            <li class="dropdown-submenu">
                <a id="{{ $entry['id'] }}" class="dropdown-item dropdown-toggle" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    {{ $entry['title'] }}
                </a>
                <ul class="dropdown-menu multi-level" role="menu">
                    <?php foreach($entry['subElements'] as $item) {
                        generateMenuEntry($item, $level+1);
                    } ?>
                </ul>
            </li>
        @endif
        <?php
    }
}

$navNum = 1;
function generateMobileMenuEntry($entry, $level=0) {
    global $navNum;
    if($entry['subElements'] === null) {//normal entry
        ?>
        <li class="nav-item">
            @if($entry['enabled'])
                <a id="{{ $entry['id'] }}" class="nav-link" href="{{ $entry['link'] }}" style="padding-left: {{ $level * 10 }}px"@if($entry['noFollow']) rel="nofollow"@endif>
                    @isset($entry['icon'])<span class="{{ $entry['icon'] }} mr-1"></span>@endisset{{ $entry['title']}}
                </a>
            @else
                <span class="d-inline-block nav-tooltip" title="{{ $entry['tooltip'] }}">
                    <a id="{{ $entry['id'] }}" class="nav-link btn-link disabled" style="padding-left: {{ $level * 10 }}px"@if($entry['noFollow']) rel="nofollow"@endif>
                        @isset($entry['icon'])<span class="{{ $entry['icon'] }} mr-1"></span>@endisset{{ $entry['title']}}
                    </a>
                </span>
            @endif
        </li>
        <?php
    } else {
        ?>
        <li class="nav-item">
            <a id="{{ $entry['id'] }}" class="nav-link dropdown-toggle" role="button" style="padding-left: {{ $level * 10 }}px"
                data-toggle="collapse" data-target="#navbar-m{{ $navNum }}" aria-controls="navbar-m{{ $navNum }}" aria-expanded="false">
                @if(Auth::check() && $entry['id'] == str_replace(".", "", Auth::user()->name))
                    <img src="{{ Auth::user()->avatarPath() }}" class="rounded-circle" alt="" style="height: 20px; width: 20px">
                @endif
                {{ $entry['title']}}
            </a>
            <ul id="navbar-m{{ $navNum++ }}" class="navbar-nav collapse" role="menu">
                <?php foreach($entry['subElements'] as $item) {
                    generateMobileMenuEntry($item, $level+1);
                } ?>
            </ul>
        </li>
        <?php
    }
}
?>
<nav class="navbar fixed-top nav-bg">
    <a class="navbar-brand" href="{{ route('index') }}">
        DS-Ultimate
    </a>

    <!-- Desktop Menue -->
    <ul class="d-none d-xl-flex navbar-nav mr-auto">
        @foreach(\App\Util\Navigation::generateNavArray($server, $worldData) as $item)
            <?php generateMenuEntry($item) ?>
        @endforeach
    </ul>
    <ul class="d-none d-xl-flex navbar-nav">
        <li class="nav-item">
            <a class="btn @toDarkmode(btn-outline-dark) mr-sm-2" href="{{ route('darkmode', (session('darkmode', false))?("false"):("true")) }}">
                @darkmode
                    {{ __('ui.lightmode') }}
                @else
                    {{ __('ui.darkmode') }}
                @enddarkmode
            </a>
        </li>
        @if (isset($server))
            <form class="form-inline" action="{{ route('searchForm', [$server->code]) }}" method="POST" role="search">
                <li class="nav-item">
                    @csrf
                    <input class="form-control mr-sm-2" name="search" type="search" placeholder="{{ __('ui.titel.search') }}" aria-label="Search" @if (isset($search))
                        value="{{ $search }}"
                    @endif>
                </li>
                <li class="nav-item">
                    <div class="dropdown">
                        <button class="btn @toDarkmode(btn-outline-dark) dropdown-toggle mr-sm-2" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            {{ __('ui.titel.search') }}
                        </button>
                        <div class="dropdown-menu dropdown-menu-lg-right" aria-labelledby="dropdownMenuButton" style="width: 100px">
                            <button class="dropdown-item" name="submit" type="submit"  value="player"><i class="p-1 fas fa-user"></i>{{ ucfirst(__('ui.table.player')) }}</button>
                            <button class="dropdown-item" name="submit" type="submit" value="ally"><i class="p-1 fas fa-users"></i>{{ ucfirst(__('ui.table.ally')) }}</button>
                            <button class="dropdown-item" name="submit" type="submit" value="village"><i class="p-1 fab fa-fort-awesome"></i>{{ ucfirst(__('ui.table.village')) }}</button>
                        </div>
                    </div>
                </li>
            </form>
        @endif
            <li class="nav-item">
                <div class="dropdown">
                    <button class="btn @toDarkmode(btn-outline-dark) dropdown-toggle mr-sm-2" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        {{ __('ui.language') }}
                    </button>
                    <div class="dropdown-menu dropdown-menu-lg-right" aria-labelledby="dropdownMenuButton" style="width: 100px">
                        @foreach(\App\Util\Navigation::getAvailableTranslations() as $trans)
                        <a class="dropdown-item" href="{{ route('locale', $trans['s']) }}"><span class="flag-icon {{ $trans['f'] }}"></span> {{ $trans['n'] }}</a>
                        @endforeach
                    </div>
                </div>
            </li>
        @guest
            <li class="nav-item">
                <div class="dropdown">
                    <button class="btn @toDarkmode(btn-outline-dark) dropdown-toggle mr-sm-2" type="button" id="dropdownLoginButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        {{ __('user.login') }}
                    </button>
                    <div class="dropdown-menu dropdown-menu-lg-right" aria-labelledby="dropdownLoginButton" style="width: 100px">
                        <a class="dropdown-item" href="{{ route('login') }}">{{ __('user.login') }}</a>
                        @if (Route::has('register'))
                            <a class="dropdown-item" href="{{ route('register') }}">{{ __('user.register') }}</a>
                        @endif
                    </div>
                </div>
            </li>
        @else
            <li class="nav-item">
                <div class="dropdown">
                    <button class="btn @toDarkmode(btn-outline-dark) dropdown-toggle mr-sm-2" type="button" id="navbarDropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <img src="{{ Auth::user()->avatarPath() }}" class="rounded-circle" alt="" style="height: 20px; width: 20px">
                        {{ Auth::user()->name }} <span class="caret"></span>
                    </button>

                    <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
                        <a class="dropdown-item" href="{{ route('user.overview', ['myMap']) }}">
                            {{ __('ui.titel.overview') }}
                        </a>
                        @can('dashboard_access')
                            <a class="dropdown-item" href="{{ route('admin.home') }}">
                                {{ __('user.dashboard') }}
                            </a>
                        @endcan
                        @can('translation_access')
                            <a class="dropdown-item" href="{{ route('index') }}/translations">
                                {{ __('user.translations') }}
                            </a>
                        @endcan
                        <a class="dropdown-item" href="{{ route('user.settings', ['settings-profile']) }}">
                            {{ __('ui.personalSettings.title') }}
                        </a>
                        <a class="dropdown-item" href="{{ route('logout') }}"
                            onclick="event.preventDefault();
                            document.getElementById('logout-form').submit();">
                            {{ __('user.logout') }}
                        </a>
                        <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                            @csrf
                        </form>
                    </div>
                </div>
            </li>
        @endauth
    </ul>

    <!-- Mobile Menue -->
    <div class="d-xl-none">
        @if (isset($server))
            <button class="ml-auto navbar-toggler" type="button" data-toggle="collapse" data-target="#navbar-search-mobile" aria-controls="navbar-search-mobile" aria-expanded="false">
                <i class="p-1 fas fa-search"></i>
            </button>
        @endif
        <button class="ml-2 navbar-toggler" type="button" data-toggle="collapse" data-target="#navbar-mobile" aria-controls="navbar-mobile" aria-expanded="false" aria-label="{{ __('ui.nav.toggle') }}">
            <i class="p-1 fas fa-bars"></i>
        </button>
    </div>
    <ul id="navbar-mobile" class="navbar-nav d-lg-none navbar-collapse collapse">
        @foreach(\App\Util\Navigation::generateMobileNavArray($server, $worldData) as $item)
            <?php generateMobileMenuEntry($item) ?>
        @endforeach
    </ul>
    @if (isset($server))
        <nav id="navbar-search-mobile" class="d-lg-none navbar-collapse collapse mt-2 navbar-nav">
            <form class="form-inline w-100" action="{{ route('searchForm', [$server->code]) }}" method="POST" role="search">
                <li class="nav-item d-flex" style="width: calc(100% - 5rem)">
                    @csrf
                    <input class="form-control mr-sm-2 w-100" name="search" type="search" placeholder="{{ __('ui.titel.search') }}" aria-label="Search" @if (isset($search))
                        value="{{ $search }}"
                    @endif>
                </li>
                <li class="nav-item ml-2 w-auto">
                    <div class="dropdown">
                        <button class="dropdown-toggle navbar-toggler" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="p-1 fas fa-user"></i>
                        </button>
                        <div id="dropdown-search" class="dropdown-menu dropdown-menu-right position-absolute" style="min-width: 0 !important" aria-labelledby="dropdownMenuButton">
                            <button class="dropdown-item" name="submit" type="submit"  value="player"><i class="p-1 fas fa-user"></i></button>
                            <button class="dropdown-item" name="submit" type="submit" value="ally"><i class="p-1 fas fa-users"></i></button>
                            <button class="dropdown-item" name="submit" type="submit" value="village"><i class="p-1 fab fa-fort-awesome"></i></button>
                        </div>
                    </div>
                </li>
            </form>
        </nav>
    @endif
</nav>

@push('js')
<script>
    $('#userlogout').click(function(e) {
        e.preventDefault();
        $('#logout-form').submit();
    });
    $(function () {
        $('.nav-tooltip').tooltip({classes: {"ui-tooltip": "ui-corner-all"}});
        $(".navbar-nav .dropdown-item a").parent().click(function (e) {
            window.location.href = $(">a", this)[0].href;
        })
    })
</script>
@endpush
