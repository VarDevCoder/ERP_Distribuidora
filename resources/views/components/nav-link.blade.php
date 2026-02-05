@props(['href', 'active' => false])

<a href="{{ $href }}"
   class="nav-link {{ $active ? 'nav-link-active' : '' }}">
    {{ $slot }}
</a>
