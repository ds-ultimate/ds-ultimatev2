<div class="sidebar">
    <nav class="sidebar-nav ps ps--active-y">

        <ul class="nav">
            <li class="nav-item">
                <a href="{{ route("admin.home") }}" class="nav-link">
                    <i class="nav-icon fas fa-fw fa-tachometer-alt">

                    </i>
                    {{ trans('global.dashboard') }}
                </a>
            </li>
            @can('user_management_access')
                <li class="nav-item nav-dropdown">
                    <a class="nav-link  nav-dropdown-toggle">
                        <i class="fa-fw fas fa-users nav-icon">

                        </i>
                        {{ trans('cruds.userManagement.title') }}
                    </a>
                    <ul class="nav-dropdown-items">
                        @can('permission_access')
                            <li class="nav-item">
                                <a href="{{ route("admin.permissions.index") }}" class="nav-link {{ request()->is('admin/permissions') || request()->is('admin/permissions/*') ? 'active' : '' }}">
                                    <i class="fa-fw fas fa-unlock-alt nav-icon">

                                    </i>
                                    {{ trans('cruds.permission.title') }}
                                </a>
                            </li>
                        @endcan
                        @can('role_access')
                            <li class="nav-item">
                                <a href="{{ route("admin.roles.index") }}" class="nav-link {{ request()->is('admin/roles') || request()->is('admin/roles/*') ? 'active' : '' }}">
                                    <i class="fa-fw fas fa-briefcase nav-icon">

                                    </i>
                                    {{ trans('cruds.role.title') }}
                                </a>
                            </li>
                        @endcan
                        @can('user_access')
                            <li class="nav-item">
                                <a href="{{ route("admin.users.index") }}" class="nav-link {{ request()->is('admin/users') || request()->is('admin/users/*') ? 'active' : '' }}">
                                    <i class="fa-fw fas fa-user nav-icon">

                                    </i>
                                    {{ trans('cruds.user.title') }}
                                </a>
                            </li>
                        @endcan
                    </ul>
                </li>
            @endcan
            @can('server_management_access')
                <li class="nav-item nav-dropdown">
                    <a class="nav-link  nav-dropdown-toggle">
                        <i class="fa-cloud fas fa-briefcase nav-icon">

                        </i>
                        {{ trans('cruds.serverManagement.title') }}
                    </a>
                    <ul class="nav-dropdown-items">
                        @can('server_access')
                            <li class="nav-item">
                                <a href="{{ route("admin.server.index") }}" class="nav-link {{ request()->is('admin/server') || request()->is('admin/server/*') ? 'active' : '' }}">
                                    <i class="fa-server fas fa-unlock-alt nav-icon">

                                    </i>
                                    {{ trans('cruds.server.titel') }}
                                </a>
                            </li>
                        @endcan
                        @can('world_access')
                            <li class="nav-item">
                                <a href="{{ route("admin.worlds.index") }}" class="nav-link {{ request()->is('admin/worlds') || request()->is('admin/worlds/*') ? 'active' : '' }}">
                                    <i class="fa-globe fas nav-icon">

                                    </i>
                                    {{ trans('cruds.world.title') }}
                                </a>
                            </li>
                        @endcan
                    </ul>
                </li>
            @endcan
            @can('bugreport_management_access')
                <li class="nav-item nav-dropdown">
                    <a class="nav-link  nav-dropdown-toggle">
                        <i class="fa-bug fas nav-icon">

                        </i>
                        {{ trans('cruds.bugreportManagement.title') }}
                    </a>
                    <ul class="nav-dropdown-items">
                        @can('bugreport_access')
                            <li class="nav-item">
                                <a href="{{ route("admin.bugreports.index") }}" class="nav-link {{ request()->is('admin/server') || request()->is('admin/server/*') ? 'active' : '' }}">
                                    <i class="fa-list fas nav-icon">

                                    </i>
                                    {{ trans('cruds.bugreport.title') }}
                                    <i class="badge badge-light pl-1" style="float: none">{{ \App\Bugreport::countNew() }}</i>
                                </a>
                            </li>
                        @endcan
                    </ul>
                </li>
            @endcan
            @can('translation_access')
                <li class="nav-item">
                    <a href="{{ route("index") }}/translations" class="nav-link">
                        <i class="fa-language fas nav-icon">

                        </i>
                        {{ __('user.translations') }}
                    </a>
                </li>
            @endcan
            <li class="nav-item">
                <a href="#" class="nav-link" onclick="event.preventDefault(); document.getElementById('logoutform').submit();">
                    <i class="nav-icon fas fa-fw fa-sign-out-alt">

                    </i>
                    {{ trans('global.logout') }}
                </a>
            </li>
        </ul>

        <div class="ps__rail-x" style="left: 0px; bottom: 0px;">
            <div class="ps__thumb-x" tabindex="0" style="left: 0px; width: 0px;"></div>
        </div>
        <div class="ps__rail-y" style="top: 0px; height: 869px; right: 0px;">
            <div class="ps__thumb-y" tabindex="0" style="top: 0px; height: 415px;"></div>
        </div>
    </nav>
    <button class="sidebar-minimizer brand-minimizer" type="button"></button>
</div>
