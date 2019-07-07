<nav class="navbar fixed-bottom navbar-light bg-primary">
    <div class="col-12 mx-auto text-center">
        <ul class="list-inline my-0">
            <li class="list-inline-item">
                <a href="#">Impressum</a>
            </li>
            <li class="list-inline-item">
                -
            </li>
            <li class="list-inline-item">
                <a href="#">Changelog</a>
            </li>
            <li class="list-inline-item">
                <div class="dropup">
                    <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        Discord
                    </a>
                    <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown" style="background-color: transparent; border: transparent; border: none;">
                        <iframe class="discord" src="https://discordapp.com/widget?id=573152064901742616&theme=dark" width="350" height="500" allowtransparency="true" frameborder="0"></iframe>
                    </div>
                </div>
            </li>
        </ul>
    </div>
    <div class="col-12 mx-auto text-center">
        <ul class="list-inline my-0">
            @if (isset($worldData))
                <li class="list-inline-item">
                    <small class="text-muted">{{ __('Letztes Update') }}: {{ $worldData->updated_at->diffForHumans() }}</small>
                </li>
                <li class="list-inline-item">
                    <small class="text-muted">||</small>
                </li>
            @endif
            <li class="list-inline-item">
                <small class="text-muted">{{ __('Ladezeit') }} {{ substr((microtime(true) - LARAVEL_START), 0, 7) }} {{ __('Sek.') }}</small>
            </li>
        </ul>
    </div>
</nav>
