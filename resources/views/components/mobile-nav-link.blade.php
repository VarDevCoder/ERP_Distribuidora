@props(['href', 'active' => false])

<a href="{{ $href }}"
   class="block px-3 py-2 rounded-lg text-sm font-medium transition-colors
          {{ $active ? 'bg-blue-700 text-white' : 'text-blue-100 hover:bg-blue-800 hover:text-white' }}">
    {{ $slot }}
</a>
