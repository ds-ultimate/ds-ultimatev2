<header class="c-header c-header-light c-header-fixed">
    <button class="c-header-toggler c-class-toggler d-lg-none mr-auto" type="button" data-target="#sidebar" data-class="c-sidebar-show">
        <span class="c-header-toggler-icon"></span>
    </button>
    <button class="c-header-toggler c-class-toggler ml-3 d-md-down-none" type="button" data-target="#sidebar" data-class="c-sidebar-lg-show" responsive="true">
        <span class="c-header-toggler-icon"></span>
    </button>
    <ol class="c-header-nav m-0">
      <li class="breadcrumb-item"><a href="/">Home</a></li>
      <?php $segments = ''; ?>
      @for($i = 1; $i <= count(Request::segments()); $i++)
          <?php $segments .= '/'. Request::segment($i); ?>
          @if($i < count(Request::segments()))
              <li class="breadcrumb-item">{{ Request::segment($i) }}</li>
          @else
              <li class="breadcrumb-item active">{{ Request::segment($i) }}</li>
          @endif
      @endfor
    </ol>
    
    <ul class="c-header-nav ml-auto mr-4">
        <li class="c-header-nav-item dropdown">
            <button class="btn btn-outline-dark dropdown-toggle mr-sm-2" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                {{ __('ui.language') }}
            </button>
            <div class="dropdown-menu dropdown-menu-lg-right" aria-labelledby="dropdownMenuButton" style="width: 100px">
                <a class="dropdown-item" href="{{ route('locale', 'de') }}"><span class="flag-icon flag-icon-de"></span> Deutsch</a>
                <a class="dropdown-item" href="{{ route('locale', 'en') }}"><span class="flag-icon flag-icon-gb"></span> English</a>
            </div>
        </li>
        <li class="c-header-nav-item dropdown">
            <button class="btn btn-outline-dark dropdown-toggle mr-sm-2" type="button" id="navbarDropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <img src="{{ Auth::user()->avatarPath() }}" class="rounded-circle" alt="" style="height: 20px; width: 20px">
                {{ Auth::user()->name }} <span class="caret"></span>
            </button>

            <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
                <a class="dropdown-item" href="{{ route('user.overview', ['myMap']) }}">
                    {{ __('ui.titel.overview') }}
                </a>
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
        </li>
    </ul>
</header>