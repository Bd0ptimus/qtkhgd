<body
    class="horizontal-layout horizontal-menu {{$configData['horizontalMenuType']}} {{ $configData['blankPageClass'] }} {{ $configData['bodyClass'] }}  {{($configData['theme'] === 'dark') ? 'dark-layout' : 'light' }} {{ $configData['footerType'] }}  footer-light"
    data-menu="horizontal-menu" data-col="2-columns" data-open="hover" data-layout="{{ $configData['theme'] }}">

    {{-- Include Sidebar --}}
    @include('panels.sidebar')

    <!-- BEGIN: Header-->
    {{-- Include Navbar --}}
    @include('panels.navbar')

    {{-- Include Sidebar --}}
    @include('panels.horizontalMenu')

    <!-- BEGIN: Content-->
    <div class="app-content content">
        <div class="content-overlay"></div>
        <div class="header-navbar-shadow"></div>
        @if(($configData['contentLayout']!=='default') && isset($configData['contentLayout']))
        <div class="content-area-wrapper">
            <div class="{{ $configData['sidebarPositionClass'] }}">
                <div class="sidebar">
                    {{-- Include Sidebar Content --}}
                    @yield('content-sidebar')
                </div>
            </div>
            <div class="{{ $configData['contentsidebarClass'] }}">
                <div class="content-wrapper">
                    <div class="content-body">
                        {{-- Include Page Content --}}
                        @yield('content')

                    </div>
                </div>
            </div>
        </div>
        @else
        <div class="content-wrapper">
            {{-- Include Breadcrumb --}}
            @if($configData['pageHeader'] == true)
                @include('panels.breadcrumb')
            @endif
            <section>
                <h1>
                    <i class="{{ $icon??'' }}" aria-hidden="true"></i> {!! $title??'' !!}
                    <small>{!!$sub_title??'' !!}</small>
                </h1>
                <div class="more_info">{!! $more_info??'' !!}</div>
                <!-- breadcrumb start -->
                <ol class="breadcrumb">
                    <li><a href="{{ route('admin.home') }}"><i class="fa fa-dashboard"></i> {{ trans('admin.home') }}</a></li>
                    <li>{!! $title??'' !!}</li>
                </ol>
                <!-- breadcrumb end -->
            </section>
            <div class="content-body">
                {{-- Include Page Content --}}
                @yield('main')
            </div>
        </div>
        @endif

    </div>
    <!-- End: Content-->

    @if($configData['blankPage'] == false && isset($configData['blankPage']))

    @endif

    <div class="sidenav-overlay"></div>
    <div class="drag-target"></div>

    {{-- include footer --}}
    @include('panels/footer')

    {{-- include default scripts --}}
    @include('panels/scripts')
    @include('admin/component/alerts')
    @stack('scripts')
</body>

</html>