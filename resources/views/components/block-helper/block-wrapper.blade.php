<div
  class="w-full @unless($block->preview){{ $block->classes }} @else min-h-5 @endunless"
  @unless($block->preview) {{ $attributes }} @endunless
>
  {{ $slot }}
</div>
