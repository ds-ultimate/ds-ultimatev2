<div class="admin-nav-wrapper">
    <a class="admin-nav-brand" href="{{ route('index') }}">
        DS-Ultimate
    </a>

    <ul id="admin-nav-main">
        <x-admin.menue access="dashboard_access" route="admin.home" icon="fa-tachometer-alt" :name="__('admin.dashboard.title')" />
        <x-admin.menue access="news_access" route="admin.news.index" icon="fa-newspaper" :name="__('admin.news.title')" />
        <x-admin.menue access="changelog_access" route="admin.changelogs.index" icon="fa-file-code" :name="__('admin.changelogs.title')" />

        <x-admin.menue access="user_management_access" route="" icon="fa-users" dropdownId="drop_user" :name="__('admin.userManagement.title')">
            <x-admin.menue access="role_access" route="admin.roles.index" icon="fa-briefcase" :name="__('admin.roles.title')" />
            <x-admin.menue access="user_access" route="admin.users.index" icon="fa-user" :name="__('admin.users.title')" />
        </x-admin.menue>

        <x-admin.menue access="server_management_access" route="" icon="fa-cloud" dropdownId="drop_server" :name="__('admin.serverManagement.title')">
            <x-admin.menue access="server_access" route="admin.server.index" icon="fa-unlock-alt" :name="__('admin.server.title')" />
            <x-admin.menue access="world_access" route="admin.worlds.index" icon="fa-globe" :name="__('admin.worlds.title')" />
        </x-admin.menue>

        <x-admin.menue access="bugreport_management_access" route="" icon="fa-bug" dropdownId="drop_bug" :name="__('admin.bugreport.title')">
            <x-admin.menue access="bugreport_access" route="admin.bugreports.index" icon="fa-list">
                <x-slot name="name">
                    {{ __('admin.bugreport.title') }}
                    <i class="badge badge-light pl-1" style="float: none">{{ \App\Bugreport::countNew() }}</i>
                </x-slot>
            </x-admin.menue>
        </x-admin.menue>

        {{-- <x-admin.menue access="translation_access" route="" icon="fa-language" :name="__('user.translations')" /> --}}
        @can('translation_access')
            <li class="admin-nav-item">
                <a href="{{ route("index") }}/translations" class="admin-nav-link">
                    <i class="admin-nav-icon fa-fw fas fa-language"></i> {{ __('user.translations') }}
                </a>
            </li>
        @endcan

        <x-admin.menue access="cacheStat_access" route="admin.cacheStats" icon="fa-info-circle" :name="__('admin.cacheStats.title')" />
        <x-admin.menue access="applog_access" route="admin.appLog" icon="fa-info-circle" :name="__('admin.applog')" />
    </ul>
    <a id="admin-nav-minimizer" class="admin-nav-link" aria-controls="admin-nav-main" aria-expanded="true">
        <i class="fas fa-chevron-left float-right"></i>
        <i class="fas fa-chevron-right float-right"></i>
    </a>
</div>

@push('js')
<script>
    $(function() {
        $('#admin-nav-minimizer').click(function(e) {
            $('.admin-nav-wrapper').toggleClass('admin-nav-minimized');
            if($('.admin-nav-wrapper').hasClass('admin-nav-minimized')) {
                $('.admin-nav-dropdown-items').collapse('hide');
            }
        });
        
        var curElm = $('a[href$="'+window.location.href+'"]', "#admin-nav-main");
        var outer = curElm.parentsUntil("#admin-nav-main").last().children('a');
        if(curElm[0] === outer[0]) {
            curElm.addClass("admin-nav-item-hovering");
        } else {
            outer.click();
        }
    });
</script>
@endpush
