@props(['access', 'route', 'icon', 'name', 'dropdownId'])

@can($access)
    <li class="admin-nav-item">
        @hasSlot
            <a href="#" class="admin-nav-link" data-toggle="collapse" data-target="#{{ $dropdownId }}">
                <i class="admin-nav-icon fa-fw fas {{ $icon }}"></i> {{ $name }}
                <i class="admin-nav-chevron fas fa-chevron-left"></i>
                <i class="admin-nav-chevron fas fa-chevron-down"></i>
            </a>
            <ul id="{{ $dropdownId }}" class="collapse admin-nav-dropdown-items" data-parent="#admin-nav-main">
                {{ $slot }}
            </ul>
        @else
            <a href="{{ route($route) }}" class="admin-nav-link">
                <i class="admin-nav-icon fa-fw fas {{ $icon }}"></i> {{ $name }}
            </a>
        @endif
    </li>
@endcan