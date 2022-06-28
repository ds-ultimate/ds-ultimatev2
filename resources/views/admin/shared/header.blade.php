<nav class="navbar navbar-light nav-bg">
    <ul class="ml-auto navbar-nav flex-row">
        <li class="nav-item">
            <div class="dropdown">
                <button class="btn btn-outline-dark dropdown-toggle mr-sm-2" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
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
                        <img src="{{ asset('images/default/user.png') }}" class="rounded-circle" alt="" style="height: 20px; width: 20px">
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
</nav>

@push('js')
<script>
    $('#userlogout').click(function(e) {
        e.preventDefault();
        $('#logout-form').submit();
    });
</script>
@endpush
