{{-- For Horizontal submenu --}}
<ul class="dropdown-menu">
  @if(isset($menu))
  @foreach($menu as $submenu)
  <li  class="">
    <a href="{{ sc_url_render($submenu->uri) }}" class="dropdown-item">
      <i class="fa {{ isset($submenu->icon) ? $submenu->icon : '' }}"></i>
      <span>{{ sc_language_render($submenu->title) }}</span>
    </a>
  </li>
  @endforeach
  @endif
</ul>