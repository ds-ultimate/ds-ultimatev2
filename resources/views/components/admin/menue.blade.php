@props(['access', 'route', 'icon', 'name'])

@can($access)
    <li class="@hasSlot c-sidebar-nav-dropdown @else c-sidebar-nav-item @endif">
        <a href="{{ ($route!="")?route($route):'#' }}" class="c-sidebar-nav-{{ ($route!="")?'link':'dropdown-toggle' }}">
            <i class="c-sidebar-nav-icon fa-fw fas {{ $icon }}"></i> {{ $name }}
        </a>
        @hasSlot
            <ul class="c-sidebar-nav-dropdown-items">
                {{ $slot }}
            </ul>
        @endif
    </li>
@endcan