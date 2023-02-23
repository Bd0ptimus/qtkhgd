  <!-- Left side column. contains the logo and sidebar -->
  <aside class="main-sidebar">
    <!-- sidebar: style can be found in sidebar.less -->
    <section class="sidebar">
      <!-- Sidebar user panel -->
      <div class="user-panel">
        <div class="pull-left image">
          <img src="{{ asset(Admin::user()->avatar) }}" class="img-circle" alt="User Image">
        </div>
        <div class="pull-left info">
          <p>{{ Admin::user()->name }}</p>
          <a href="#"><i class="fa fa-circle text-success"></i> Online</a>
        </div>
      </div>

      
      @php
        $menus = Admin::getMenuVisible();
      @endphp
      <ul class="sidebar-menu" data-widget="tree">


        {{-- Level 0 --}}
        @foreach ($menus[0] as $level0)
        @if($level0->uri == 'admin::user' && Admin::user()->isAgencyTier2()) @php  continue; @endphp @endif
          @if ($level0->type ==1)
            <li class="header">{{ sc_language_render($level0->title) }}</li>
          @elseif($level0->uri)
              <li class=""><a href="{{ $level0->uri?sc_url_render($level0->uri):'#' }}"><i class="fa {{ $level0->icon }}"></i>{{ sc_language_render($level0->title) }}</a></li>
          @else
            <li class="treeview">
              <a href="#">
                <i class="fa {{ $level0->icon }}"></i> <span>{{ sc_language_render($level0->title) }}</span>
                <span class="pull-right-container">
                  <i class="fa fa-angle-left pull-right"></i>
                </span>
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
                      <span class="pull-right-container">
                        <i class="fa fa-angle-left pull-right"></i>
                      </span>
                    </a>
            {{-- LEvel 2  --}}
                        @if (isset($menus[$level1->id]) && count($menus[$level1->id]))
                          <ul class="treeview-menu">
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

@include('admin.component.sidebar_bottom')

    </section>
    <!-- /.sidebar -->
  </aside>
