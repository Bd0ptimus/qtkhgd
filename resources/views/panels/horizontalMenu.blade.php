@php
$configData = \App\Admin\Helpers\Layout::applClasses();
@endphp
{{-- Horizontal Menu --}}
<div class="horizontal-menu-wrapper">
  <div
    class="header-navbar navbar-expand-sm navbar navbar-horizontal {{$configData['horizontalMenuClass']}} {{($configData['theme'] === 'light') ? "navbar-light" : "navbar-dark" }} navbar-without-dd-arrow navbar-shadow navbar-brand-center"
    role="navigation" data-menu="menu-wrapper" data-nav="brand-center">
    <div class="navbar-header">
      <ul class="nav navbar-nav flex-row">
        <li class="nav-item mr-auto"><a class="navbar-brand" href="/">
            <div class="brand-logo"></div>
            <h2 class="brand-text mb-0">Đấu Giá Cước</h2>
          </a></li>
        <li class="nav-item nav-toggle">
          <a class="nav-link modern-nav-toggle pr-0" data-toggle="collapse">
            <i class="feather icon-x d-block d-xl-none font-medium-4 primary toggle-icon"></i>
            <i class="toggle-icon feather icon-disc font-medium-4 d-none d-xl-block collapse-toggle-icon primary"
              data-ticon="icon-disc"></i>
          </a>
        </li>
      </ul>
    </div>
    <!-- Horizontal menu content-->
    @php
        $menus = Admin::getMenuVisible();
    @endphp
    <div class="navbar-container main-menu-content" data-menu="menu-container">
      <ul class="nav navbar-nav" id="main-menu-navigation" data-menu="menu-navigation">
          @foreach ($menus[0] as $level0)
            @if($level0->uri == 'admin::user' && Admin::user()->isAgencyTier2()) @php  continue; @endphp @endif
            <li class="@if(isset($menus[$level0->id]) && count($menus[$level0->id])){{'dropdown'}}@endif nav-item }}" @if(isset($menus[$level0->id]) && count($menus[$level0->id])){{'data-menu=dropdown'}}@endif>
              <a href="{{ $level0->uri?sc_url_render($level0->uri):'#' }}" class="@if(isset($menus[$level0->id]) && count($menus[$level0->id])){{'dropdown-toggle'}}@endif nav-link"  @if(isset($menus[$level0->id]) && count($menus[$level0->id])){{'data-toggle=dropdown'}}@endif>
                <i class="fa {{ $level0->icon }}"></i>
                <span>{{ sc_language_render($level0->title) }}</span>
              </a>
              @if(isset($menus[$level0->id]) && count($menus[$level0->id]))
                @include('panels/horizontalSubmenu', ['menu' => $menus[$level0->id]])
              @endif
          </li>
          @endforeach
        {{-- Foreach menu item ends --}}
      </ul>
    </div>
  </div>
</div>