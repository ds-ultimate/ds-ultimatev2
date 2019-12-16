<nav class="navbar navbar-expand-lg navbar-light bg-nav">
    <a class="navbar-brand" href="{{ route('index') }}">
        DS-Ultimate
    </a>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav mr-auto">
            @if (isset($server))
            <li class="nav-item">
                <a class="nav-link" href="{{ route('server', [$server]) }}">{{ucfirst(__('ui.titel.worldOverview'))}} <span class="sr-only">(current)</span></a>
            </li>
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" id="dropdownMenu1" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    {{ ucfirst(__('ui.server.worlds')) }}
                </a>
                <ul class="dropdown-menu multi-level" role="menu" aria-labelledby="dropdownMenu">
                    @foreach(\App\World::worldsCollection($server) as $worlds)
                        <li class="dropdown-submenu">
                            <a  class="dropdown-item" tabindex="-1" href="#">{!! (($worlds->get(0)->sortType() == "world")? ucfirst(__('ui.tabletitel.normalWorlds')): ucfirst(__('ui.tabletitel.specialWorlds'))) !!}</a>
                            <ul class="dropdown-menu">
                                @foreach($worlds as $world)
                                    <li class="dropdown-item">
                                        {!! \App\Util\BasicFunctions::linkWorld($world, $world->displayName()) !!}
                                    </li>
                                @endforeach
                            </ul>
                        </li>
                    @endforeach
                </ul>
            </li>
            @endif
            @if (isset($worldData))
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" id="dropdownMenu1" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        {{__('ui.server.ranking')}}
                    </a>
                    <ul class="dropdown-menu multi-level" role="menu" aria-labelledby="dropdownMenu">
                        <li class="dropdown-item"><a href="{{ route('world', [$worldData->server->code, $worldData->name]) }}">{{ ucfirst(__('ui.tabletitel.top10')) }}</a></li>
                        <li class="dropdown-item"><a href="{{ route('worldPlayer', [$worldData->server->code, $worldData->name]) }}">{{ ucfirst(__('ui.table.player')) }} ({{ __('ui.nav.current') }})</a></li>
                        <li class="dropdown-item"><a href="{{ route('rankPlayer', [$worldData->server->code, $worldData->name]) }}">{{ ucfirst(__('ui.table.player')) }} ({{ __('ui.nav.history') }})</a></li>
                        <li class="dropdown-item"><a href="{{ route('worldAlly', [$worldData->server->code, $worldData->name]) }}">{{ ucfirst(__('ui.table.ally')) }} ({{ __('ui.nav.current') }})</a></li>
                        <li class="dropdown-item"><a href="{{ route('rankAlly', [$worldData->server->code, $worldData->name]) }}">{{ ucfirst(__('ui.table.ally')) }} ({{ __('ui.nav.history') }})</a></li>
                    </ul>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" id="dropdownMenu1" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        {{__('ui.server.tools')}}
                    </a>
                    <ul class="dropdown-menu multi-level" role="menu" aria-labelledby="dropdownMenu">
                        <li class="dropdown-item"><a rel="nofollow"  href="{{ route('tools.distanceCalc', [$worldData->server->code, $worldData->name]) }}">{{ ucfirst(__('tool.distCalc.title')) }}</a></li>
                        @if($worldData->config != null && $worldData->units != null)
                            <li class="dropdown-item"><a rel="nofollow" href="{{ route('tools.attackPlannerNew', [$worldData->server->code, $worldData->name]) }}">{{ ucfirst(__('tool.attackPlanner.title')) }}</a></li>
                        @endif
                        @if($worldData->units != null)
                            <li class="dropdown-item"><a rel="nofollow"  href="{{ route('tools.mapNew', [$worldData->server->code, $worldData->name]) }}">{{ ucfirst(__('tool.map.title')) }}</a></li>
                        @endif
                        @if($worldData->config != null && $worldData->buildings != null)
                            <li class="dropdown-item"><a rel="nofollow"  href="{{ route('tools.pointCalc', [$worldData->server->code, $worldData->name]) }}">{{ ucfirst(__('tool.pointCalc.title')) }}</a></li>
                        @endif
                    </ul>
                </li>
            @endif
        </ul>
        <ul class="navbar-nav">
        @if (isset($server))
            <form class="form-inline" action="{{ route('searchForm', [$server]) }}" method="POST" role="search">
                <li class="nav-item">
                        @csrf
                        <input class="form-control mr-sm-2" name="search" type="search" placeholder="{{ __('ui.titel.search') }}" aria-label="Search" @if (isset($search))
                            value="{{ $search }}"
                        @endif>
                </li>
                <li class="nav-item">
                    <div class="dropdown">
                        <button class="btn btn-outline-dark dropdown-toggle mr-sm-2" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            {{ __('ui.titel.search') }}
                        </button>
                        <div class="dropdown-menu dropdown-menu-lg-right" aria-labelledby="dropdownMenuButton" style="width: 100px">
                            <button class="dropdown-item" name="submit" type="submit"  value="player">{{ ucfirst(__('ui.table.player')) }}</button>
                            <button class="dropdown-item" name="submit" type="submit" value="ally">{{ ucfirst(__('ui.table.ally')) }}</button>
                            <button class="dropdown-item" name="submit" type="submit" value="village">{{ ucfirst(__('ui.table.village')) }}</button>
                        </div>
                    </div>
                </li>
            </form>
        @endif
            <li class="nav-item">
                <div class="dropdown">
                    <button class="btn btn-outline-dark dropdown-toggle mr-sm-2" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        {{ __('ui.language') }}
                    </button>
                    <div class="dropdown-menu dropdown-menu-lg-right" aria-labelledby="dropdownMenuButton" style="width: 100px">
                        <a class="dropdown-item" href="{{ route('locale', 'de') }}"><span class="flag-icon flag-icon-de"></span> Deutsch</a>
                        <a class="dropdown-item" href="{{ route('locale', 'en') }}"><span class="flag-icon flag-icon-gb"></span> English</a>
                    </div>
                </div>
            </li>
        @guest
            <li class="nav-item">
                <div class="dropdown">
                    <button class="btn btn-outline-dark dropdown-toggle mr-sm-2" type="button" id="dropdownLoginButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
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
                    <button class="btn btn-outline-dark dropdown-toggle mr-sm-2" type="button" id="navbarDropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
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
    </div>
</nav>
