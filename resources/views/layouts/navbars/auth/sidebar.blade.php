<style>
 #sidenav-collapse-main .nav > .nav-item > .nav-link {
    padding-left: 0; /* Jika perlu, hilangkan padding untuk menghapus spasi di sebelah kiri */
}
.sidebar-icon{
    width: 24px;
    height: 24px;
}
</style>
<aside
    class="sidenav navbar navbar-vertical navbar-expand-xs border-0 border-radius-xl my-3 {{ \Request::is('pages-rtl') ? 'fixed-end me-3 rotate-caret' : 'fixed-start ms-3' }}"
    id="sidenav-main">
    
    <div class="sidenav-header">
        <i class="fas fa-times p-3 cursor-pointer text-secondary opacity-5 position-absolute end-0 top-0 d-none d-xl-none"
            aria-hidden="true" id="iconSidenav"></i>
        <a class="align-items-center d-flex m-0 navbar-brand text-wrap" href="{{ url('dashboard-default') }}">
            <img src="{{ URL::asset('assets/img/logo-ct.png') }}" class="navbar-brand-img h-100" alt="main_logo">
            <span class="ms-3 font-weight-bold">Pool Dashboard </span>
        </a>
    </div>
    <hr class="horizontal dark mt-0">
    <div class="collapse navbar-collapse w-auto h-auto" id="sidenav-collapse-main"
         style="overflow-x: hidden"
    >
        <ul class="navbar-nav">
            <li class="nav-item">
                <a 
                    class="nav-link {{ $parentFolder == 'dashboards' ? ' active' : '' }}"
                    
                    {{-- href="{{ url('dashboard-smart-home') }}"> --}}
                    href="{{ url('main-dashboard') }}">
                    <span class="sidebar-icon mr-10">
                        @include('layouts.navbars.auth.icon.dashboard')
                    </span>
                    <span class="nav-link-text ms-1">Main Dashboard</span>
                </a>
                {{-- <div class="collapse {{ $parentFolder == 'dashboards' ? ' show' : '' }}" id="dashboardsExamples">
                    <ul class="nav ms-4 ps-3"> --}}

                        {{-- <li class="nav-item {{ Request::is('dashboard-smart-home') ? 'active' : '' }}">
                            <a class="nav-link {{ Request::is('dashboard-smart-home') ? 'active' : '' }}"
                                href="{{ url('dashboard-smart-home') }}">
                                <span class="sidenav-mini-icon"> S </span>
                                <span class="sidenav-normal">Summary Dashboard Level 0 </span>
                            </a>
                        </li> --}}
                        {{-- @foreach(\App\Models\AppSettings::$natwaveDevices as $device)
                            <li class="nav-item {{ Request::is('detail') && request()->query('device') == $device
? 'active' : '' }}">
                                <a class="nav-link {{ Request::is('detail') && request()->query('device') == $device
? 'active' : '' }}"
                                   href="{{ route('detail', [
                                'device' => $device
                                ]) }}">
                                <span class="sidenav-mini-icon"> S </span>
                                    <span
                                        class="sidenav-normal">Detailed Dashboard {{__('devices_name.' . $device)}}</span>
                            </a>
                        </li>
                        @endforeach --}}
                    {{-- </ul>
                </div> --}}
            </li>

            {{-- WATERPOOL --}}


            <li class="nav-item">
                <a data-bs-toggle="collapse" href="#waterpoolExamples"
                    class="nav-link {{ $parentFolder == 'waterpool' ? ' active' : '' }}"
                    aria-controls="applicationsExamples" role="button" aria-expanded="true">
                    <span class="sidebar-icon mr-10">
                        @include('layouts.navbars.auth.icon.Detail')
                    </span>
                <span class="nav-link-text ms-1">Detailed</span>
                </a>
                {{-- @foreach(\App\Models\AppSettings::$natwaveDevices as $device)
                            <li class="nav-item {{ Request::is('detail') && request()->query('device') == $device
? 'active' : '' }}">
                                <a class="nav-link {{ Request::is('detail') && request()->query('device') == $device
? 'active' : '' }}"
                                   href="{{ route('detail', [
                                'device' => $device
                                ]) }}">
                                <span class="sidenav-mini-icon"> S </span>
                                    <span
                                        class="sidenav-normal">D {{__('devices_name.' . $device)}}</span>
                            </a>
                        </li>
                        @endforeach --}}
                @foreach(\App\Models\AppSettings::$natwaveDevices as $device)
                <div class="collapse show" id="waterpoolExamples">
                    <ul class="nav ms-4 ps-3">
                        <li class="nav-item {{ Request::is('detail') && request()->query('device') == $device ? 'active' : '' }}">
                            <a class="nav-link {{ Request::is('detail') && request()->query('device') == $device ? 'active' : '' }}"
                                href="{{ route('detail', [
                             'device' => $device
                             ]) }}">
                             <span class="sidenav-mini-icon"></span>
                                 <span class="sidenav-normal">{{__('devices_name.' . $device)}}</span>
                            </a>                                               
                        </li>
                    </ul>
                </div>
                @endforeach
            </li>

            {{-- Setting --}}

            <li class="nav-item">
                <a data-bs-toggle="collapse" href="#appSettings"
                   class="nav-link {{ $parentFolder == 'appSettings' ? ' active' : '' }}"
                   aria-controls="applicationsExamples" role="button" aria-expanded="true">
                   
                   <span class="sidebar-icon mr-10">
                    @include('layouts.navbars.auth.icon.setting')
                    </span>
                    <span class="nav-link-text ms-1">Settings</span>
                </a>
                <div class="collapse show" id="appSettings">
                    <ul class="nav ms-4 ps-3">
                        <li class="nav-item {{ Request::is('app-settings') ? 'active' : '' }}">
                            <a class="nav-link {{ Request::is('app-settings') ? 'active' : '' }}"
                               href="{{ route('app-settings', ['general']) }}">
                                <span class="sidenav-normal"> Application Settings </span>
                            </a>
                        </li>
                    </ul>
                    <ul class="nav ms-4 ps-3">
                        <li class="nav-item {{ Request::is('settings-parameter') ? 'active' : '' }}">
                            <a class="nav-link {{ Request::is('settings-parameter') ? 'active' : '' }}"
                               href="{{ route('settings.parameter') }}">
                                <span class="sidenav-normal"> Parameter </span>
                            </a>
                        </li>
                    </ul>
                </div>
            </li>

        </ul>
    </div>
</aside>
