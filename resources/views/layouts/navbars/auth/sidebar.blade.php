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
                <a data-bs-toggle="collapse" href="#dashboardsExamples"
                    class="nav-link {{ $parentFolder == 'dashboards' ? ' active' : '' }}"
                    aria-controls="dashboardsExamples" role="button" aria-expanded="false">

                    <span class="nav-link-text ms-1">Executive Dashboard</span>
                </a>
                <div class="collapse {{ $parentFolder == 'dashboards' ? ' show' : '' }}" id="dashboardsExamples">
                    <ul class="nav ms-4 ps-3">

                        <li class="nav-item {{ Request::is('dashboard-smart-home') ? 'active' : '' }}">
                            <a class="nav-link {{ Request::is('dashboard-smart-home') ? 'active' : '' }}"
                                href="{{ url('dashboard-smart-home') }}">
                                <span class="sidenav-mini-icon"> S </span>
                                <span class="sidenav-normal">Summary Dashboard Level 0 </span>
                            </a>
                        </li>

                        <li class="nav-item {{ Request::is('dashboard-detailed-dashboard') ? 'active' : '' }}">
                            <a class="nav-link {{ Request::is('dashboard-detailed-dashboard') ? 'active' : '' }}"
                                href="{{ url('dashboard-detailed-dashboard') }}">
                                <span class="sidenav-mini-icon"> S </span>
                                <span class="sidenav-normal">Detailed Dashboard</span>
                            </a>
                        </li>
                    </ul>
                </div>
            </li>

            {{-- WATERPOOL --}}


            <li class="nav-item">
                <a data-bs-toggle="collapse" href="#waterpoolExamples"
                    class="nav-link {{ $parentFolder == 'waterpool' ? ' active' : '' }}"
                    aria-controls="applicationsExamples" role="button" aria-expanded="true">

                <span class="nav-link-text ms-1">Kolam</span>
                </a>
                <div class="collapse show" id="waterpoolExamples">
                    <ul class="nav ms-4 ps-3">
                        <li class="nav-item {{ $childFolder == 'items' ? 'active' : '' }}">
                            <a class="nav-link {{ $childFolder == 'items' ? 'active' : '' }}"
                                href="{{ Route('waterpool-index') }}">
                                <span class="sidenav-mini-icon"> I </span>
                                <span class="sidenav-normal"> Data Kolam sensor </span>
                            </a>
                        </li>
                    </ul>
                </div>
            </li>

            {{-- Setting --}}

            <li class="nav-item">
                <a data-bs-toggle="collapse" href="#appSettings"
                   class="nav-link {{ $parentFolder == 'appSettings' ? ' active' : '' }}"
                   aria-controls="applicationsExamples" role="button" aria-expanded="true">

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
