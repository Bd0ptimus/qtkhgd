<div>
    <li class="dropdown dropdown-notification nav-item">
        <a class="nav-link nav-link-label" href="#" data-toggle="dropdown">
            <i class="ficon feather icon-bell"></i>
            <span class="badge badge-pill badge-primary badge-up">{{ count($adminNotifications) }}</span>
        </a>
        <ul class="dropdown-menu dropdown-menu-media dropdown-menu-right">
            <li class="dropdown-menu-header">
                <div class="dropdown-header m-0 p-2">
                    <h3 class="white">Bạn đang có {{ count($adminNotifications) }}</h3><span
                            class="grey darken-2">Thông báo</span>
                </div>
            </li>
            <li class="scrollable-container media-list">
                @foreach ($adminNotifications as $notification)
                    <a class="d-flex justify-content-between" wire:click.prevent="handleReadNotification({{ $notification }})">
                        <div class="media d-flex align-items-start pl-0">
                            <div class="media-left">
                                @if($notification->isTypeNotification())
                                    <i class="feather icon-plus-square font-medium-5 primary"></i>
                                @elseif($notification->isTypeDanger())
                                    <i class="feather icon-alert-triangle font-medium-5 danger"></i>
                                @endif
                            </div>
                            <div class="media-body">
                                <h6 class="{{ $notification->isTypeNotification() ? "primary" : ($notification->isTypeDanger() ? "danger" : "")  }} media-heading">{{ $notification->title }}</h6>
                                <small class="notification-text">{!! $notification->content !!}</small>
                            </div>
                            <small>
                                <time class="media-meta">{{ $notification->created_at->diffForHumans() }}</time>
                            </small>
                        </div>
                    </a>
                @endforeach
            </li>
            <li class="dropdown-menu-footer">
                <a class="dropdown-item p-1 text-center" href="{{ route('notification.index')}}">
                    {{ trans('admin.menu_notice.view_all') }}
                </a>
            </li>
        </ul>
    </li>
</div>
