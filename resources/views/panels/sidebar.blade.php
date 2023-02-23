@php
$configData = \App\Admin\Helpers\Layout::applClasses();
@endphp
<div
  class="main-menu menu-fixed {{($configData['theme'] === 'light') ? "menu-light" : "menu-dark"}} menu-accordion menu-shadow"
  data-scroll-to-active="true">
  <div class="navbar-header">
    <ul class="nav navbar-nav flex-row">
      <li class="nav-item mr-auto">
        <a class="navbar-brand" href="/">
          <div class="brand-logo"></div>
          <h2 class="brand-text mb-0">SMART - PLAN</h2>
        </a>
      </li>
      <!-- <li class="nav-item nav-toggle">
        <a class="nav-link modern-nav-toggle pr-0" data-toggle="collapse">
          <i class="feather icon-x d-block d-xl-none font-medium-4 primary toggle-icon"></i>
          <i class="toggle-icon feather icon-disc font-medium-4 d-none d-xl-block primary collapse-toggle-icon"
            data-ticon="icon-disc">
          </i>
        </a>
      </li> -->
    </ul>
  </div>
  <div class="shadow-bottom"></div>
  <div class="main-menu-content">

      @php
        $menus = Admin::getMenuVisible();
      @endphp
    <ul class="navigation navigation-main" id="main-menu-navigation" data-menu="menu-navigation">
    {{-- Level 0 --}}
        @foreach ($menus[0] as $level0)
        @if($level0->uri == 'admin::user' && Admin::user()->isAgencyTier2()) @php  continue; @endphp @endif
          @if ($level0->type ==1)
            <li class="navigation-header">{{ sc_language_render($level0->title) }}</li>
          @elseif($level0->uri)
              <li class=""><a href="{{ $level0->uri?sc_url_render($level0->uri):'#' }}"><i class="fa {{ $level0->icon }}"></i>{{ sc_language_render($level0->title) }}</a></li>
          @else
            <li class="treeview">
              <a href="#">
                <i class="fa {{ $level0->icon }}"></i> <span>{{ sc_language_render($level0->title) }}</span>
              </a>
            {{-- Level 1 --}}
            @if (isset($menus[$level0->id]) && count($menus[$level0->id]))
              <ul class="treeview-menu">
                @foreach ($menus[$level0->id] as $level1)
                  @if($level1->uri)
                    <li class=""><a href="{{ $level1->uri?sc_url_render($level1->uri):'#' }}"><i class="fa {{ $level1->icon }}"></i> {{ sc_language_render($level1->title) }}</a></li>
                  @else
                  <li class="treeview">
                    <a href="#">
                      <i class="fa {{ $level1->icon }}"></i> <span>{{ sc_language_render($level1->title) }}</span>
                    </a>
            {{-- LEvel 2  --}}
                        @if (isset($menus[$level1->id]) && count($menus[$level1->id]))
                          <ul class="treeview-menu" style="margin-left:15px">
                            @foreach ($menus[$level1->id] as $level2)
                              @if($level2->uri)
                                <li class=""><a href="{{ $level2->uri?sc_url_render($level2->uri):'#' }}"><i class="fa {{ $level2->icon }}"></i> {{ sc_language_render($level2->title) }}</a></li>
                              @else
                              <li class="treeview">
                                <a href="#">
                                  <i class="fa {{ $level2->icon }}"></i> <span>{{ sc_language_render($level2->title) }}</span>
                                  <span class="pull-right-container">
                                    <i class="fa fa-angle-left pull-right"></i>
                                  </span>
                                </a>

                                </li>
                              @endif
                            @endforeach
                          </ul>
                        @endif
                        {{--  end level 2 --}}
                    </li>
                  @endif
                 @endforeach
              </ul>
            @endif
              {{-- end level 1 --}}
            </li>
          @endif

        @endforeach
      {{-- end level 0 --}}
    </ul>
  </div>
</div>
<!-- END: Main Menu-->