<header class="main-header">
    <!-- Logo -->
    <a href="{{ route('admin.home') }}" class="logo">
        <!-- mini logo for sidebar mini 50x50 pixels -->
        <span class="logo-mini">{!! sc_config('ADMIN_LOGO_MINI', sc_config('ADMIN_NAME')) !!}</span>
        <!-- logo for regular state and mobile devices -->
        <span class="logo-lg">{!! sc_config('ADMIN_LOGO', sc_config('ADMIN_NAME')) !!}</span>
    </a>
    <!-- Header Navbar: style can be found in header.less -->
    <nav class="navbar navbar-static-top">

      <div>
            <div style="margin: 0px; padding: 0px" class="col-md-1">
              <!-- Sidebar toggle button-->
              <a href="#" class="sidebar-toggle" data-toggle="push-menu" role="button">
                <span class="sr-only">Toggle navigation</span>
              </a>
            </div>

            <div style="margin: 0px; padding: 13px 0px" class="col-md-7">
              <marquee style="font-size:18px; color:##E46B25;">{!!  sc_config('SYSTEM_NOTIFICATION') !!}</marquee>
            </div>

            <div style="float:right" class="col-md-4">
                <div class="navbar-custom-menu">
                  <ul class="nav navbar-nav">
                    <li><a target="_new" title="Home" href="{{ route('home') }}"><i class="fa fa-home fa-1x" aria-hidden="true"></i></a></li>
                    @include('admin.component.notice')
                    <!-- User Account: style can be found in dropdown.less -->

                    <li class="dropdown user user-menu">
                      <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                        <img src=" {{ asset(Admin::user()->avatar) }}" class="user-image" alt="User Image">
                        <span class="hidden-xs">{{ Admin::user()->name }}</span>
                      </a>
                      <ul class="dropdown-menu">
                        <!-- User image -->
                        <li class="user-header">
                          <img src="{{ asset(Admin::user()->avatar) }}" class="img-circle" alt="User Image">

                          <p>
                            {{ Admin::user()->name }}
                            <small>{{ trans('user.member_since') }}  {{ Admin::user()->created_at }}</small>
                          </p>
                        </li>
                        <!-- Menu Footer-->
                          <li class="user-footer">
                              <div class="pull-left">
                                  <a href="{{ route('admin.setting') }}" class="btn btn-default btn-flat">T??i kho???n c???a t??i</a>
                              </div>
                              <div class="pull-right">
                                  <a href="{{ route('admin.logout') }}" class="btn btn-default btn-flat">{{ trans('admin.logout') }}</a>
                              </div>
                          </li>
                      </ul>
                    </li>
                  </ul>
                </div>
            </div>
      </div>
    </nav>
  </header>
