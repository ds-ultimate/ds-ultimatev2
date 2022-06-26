<nav class="navbar footer-bg">
    <div class="col-12 px-0 text-center">
        <ul class="list-inline my-0">
            <li class="list-inline-item mr-0">
                <small><a href="{{ route('legalPage') }}">Impressum</a></small>
            </li>
            <li class="list-inline-item mr-0">
                <small>-</small>
            </li>
            <li class="list-inline-item mr-0">
                <small><a href="{{ route('changelog') }}">Changelog</a>
                    @if ($newCangelog)
                        <span class="badge badge-pill badge-info blink_me">!</span>
                    @endif
                </small>
            </li>
            <li class="list-inline-item mr-0">
                <small>-</small>
            </li>
            <li class="list-inline-item mr-0">
                <small><a href="{{ route('team') }}">Team</a></small>
            </li>
            <li class="list-inline-item mr-0">
                <small>-</small>
            </li>
            <li class="list-inline-item mr-0">
                <small><a href="{{ route('form.bugreport') }}">{{ __('user.bugreport.title') }}</a></small>
            </li>
            <li class="list-inline-item">
                <div class="dropup">
                    <a href="https://discord.gg/g3AqvaWhkg"><small>Discord</small></a>
                </div>
            </li>
        </ul>
    </div>
</nav>
