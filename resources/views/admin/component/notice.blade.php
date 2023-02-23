@php
  $newOrders = [];
  $totalNotifications = App\Models\Notification::where('user_id', Admin::user()->id)->where('read', false)->get();
  $orders = [];
@endphp
          <!-- Notifications: style can be found in dropdown.less -->
          <li class="dropdown notifications-menu">
            <a href="#" class="dropdown-toggle" data-toggle="dropdown">
              <i class="fa fa-bell-o"></i>
              <span class="label label-warning">{{ count($totalNotifications)}}</span>
            </a>
            <ul class="dropdown-menu">
              <li class="header"> Bạn có {{ count($totalNotifications) }} thông báo</li>
              <li>
                <!-- inner menu: contains the actual data -->
                <ul class="menu">
                  @foreach ($totalNotifications as $key => $notification)
                    <li>
                      <a href="#">
                        #{{ $key + 1}} - {{ $notification->title }}
                      </a>
                    </li>
                  @endforeach
                </ul>
              </li>
              <li class="footer"><a href="{{ route('notification.index')}}">{{ trans('admin.menu_notice.view_all') }}</a></li>
            </ul>
          </li>
