<div class="c-sidebar-brand"><a href="{{ route('index') }}" style="color:#FFF">
    <img class="c-sidebar-brand-full" width="118" height="46" alt="DS-Ultimate">
    <img class="c-sidebar-brand-minimized" width="118" height="46" alt="DS-Ultimate">
</a></div>

<ul class="c-sidebar-nav">
    <x-admin.menue access="dashboard_access" route="admin.home" icon="fa-tachometer-alt" :name="__('admin.dashboard.title')" />
    <x-admin.menue access="news_access" route="admin.news.index" icon="fa-newspaper" :name="__('admin.news.title')" />
    <x-admin.menue access="changelog_access" route="admin.changelogs.index" icon="fa-file-code" :name="__('admin.changelogs.title')" />
                   
    <x-admin.menue access="user_management_access" route="" icon="fa-users" :name="__('admin.userManagement.title')">
        <x-admin.menue access="role_access" route="admin.roles.index" icon="fa-briefcase" :name="__('admin.roles.title')" />
        <x-admin.menue access="user_access" route="admin.users.index" icon="fa-user" :name="__('admin.users.title')" />
    </x-admin.menue>
    
    <x-admin.menue access="server_management_access" route="" icon="fa-cloud" :name="__('admin.serverManagement.title')">
        <x-admin.menue access="server_access" route="admin.server.index" icon="fa-unlock-alt" :name="__('admin.server.title')" />
        <x-admin.menue access="world_access" route="admin.worlds.index" icon="fa-globe" :name="__('admin.worlds.title')" />
    </x-admin.menue>
        
    <x-admin.menue access="bugreport_management_access" route="" icon="fa-bug" :name="__('admin.bugreport.title')">
        <x-admin.menue access="bugreport_access" route="admin.bugreports.index" icon="fa-list">
            <x-slot name="name">
                {{ __('admin.bugreport.title') }}
                <i class="badge badge-light pl-1" style="float: none">{{ \App\Bugreport::countNew() }}</i>
            </x-slot>
        </x-admin.menue>
    </x-admin.menue>
    
    {{--<x-admin.menue access="translation_access" route="" icon="fa-language" :name="__('user.translations')" /> --}}
    @can('translation_access')
        <li class="c-sidebar-nav-item">
            <a href="{{ route("index") }}/translations" class="c-sidebar-nav-link">
                <i class="c-sidebar-nav-icon fa-fw fas fa-language"></i> {{ __('user.translations') }}
            </a>
        </li>
    @endcan
    
    <x-admin.menue access="applog_access" route="admin.appLog" icon="fa-info-circle" :name="__('admin.applog')" />
</ul>
<button class="c-sidebar-minimizer c-class-toggler" type="button" data-target="_parent" data-class="c-sidebar-minimized"></button>
