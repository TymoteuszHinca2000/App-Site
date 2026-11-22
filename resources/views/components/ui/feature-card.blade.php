@props(['iconId' => 0, 'titleHtml' => '', 'descriptionHtml' => '', 'compact' => false])

<article {{ $attributes->class(['content-card group flex h-full min-w-0 items-start rounded-[14px] border border-border bg-white shadow-card-subtle', 'gap-2 p-3' => $compact, 'gap-3 p-4 sm:p-5' => !$compact]) }}>
    @if($iconId)
        {!! wp_get_attachment_image($iconId, 'thumbnail', false, ['class' => 'icon-tilt shrink-0 object-contain group-hover:scale-105 motion-reduce:group-hover:scale-100 ' . ($compact ? 'h-8 w-8' : 'h-9 w-9 sm:h-10 sm:w-10'), 'alt' => '', 'aria-hidden' => 'true', 'loading' => 'lazy', 'decoding' => 'async']) !!}
    @endif
    <div class="min-w-0 flex-1">
        @if($titleHtml !== '')
            <h3 class="heading-6 {{ $compact ? 'text-[14px]' : '' }}">{!! $titleHtml !!}</h3>
        @endif
        @if($descriptionHtml !== '')
            <div class="wysiwyg {{ $titleHtml !== '' ? ($compact ? 'mt-1' : 'mt-2') : '' }} {{ $compact ? '[&_p]:text-[13px] [&_li]:text-[13px]' : '[&_p]:text-[14px] [&_li]:text-[14px]' }}">{!! $descriptionHtml !!}</div>
        @endif
    </div>
</article>
