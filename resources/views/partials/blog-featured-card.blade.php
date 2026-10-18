
@php
    $featuredTitle = html_entity_decode((string) ($featuredPost['title'] ?? ''), ENT_QUOTES | ENT_HTML5, 'UTF-8');
    $categoryName = html_entity_decode((string) ($featuredPost['category'] ?? ''), ENT_QUOTES | ENT_HTML5, 'UTF-8');
    $categorySlug = (string) ($featuredPost['category_slug'] ?? '');
    $categoryUrl = !empty($pageId) && $categorySlug !== '' ? \App\appandsite_blog_page_url((int) $pageId, 1, $categorySlug) : '';
@endphp

<article class="group/featured relative isolate flex min-h-[350px] overflow-hidden rounded-[14px] bg-primary-dark sm:min-h-[400px] lg:min-h-[420px]">
    @if(!empty($featuredPost['image_id']))
        {!! wp_get_attachment_image($featuredPost['image_id'], 'full', false, [
            'class' => 'absolute inset-0 h-full w-full object-cover transition-transform duration-[1200ms] ease-[cubic-bezier(0.22,1,0.36,1)] group-hover/featured:scale-[1.04] motion-reduce:transition-none',
            'alt' => $featuredTitle,
            'loading' => 'lazy',
            'decoding' => 'async',
        ]) !!}
    @endif

    <div class="pointer-events-none absolute inset-0 full-cta-shade-horizontal"></div>
    <div class="pointer-events-none absolute inset-0 full-cta-shade-vertical"></div>

    <div class="relative z-10 flex w-full flex-col justify-center px-5 py-10 text-white sm:px-8 sm:py-12 lg:px-12 lg:py-14">
        @if(!empty($featuredPrefix) || $categoryName !== '')
            <div class="mb-5 flex flex-wrap items-center gap-x-4 gap-y-3">
                @if(!empty($featuredPrefix))
                    <div class="flex items-center gap-3 text-[11px] font-semibold uppercase tracking-[0.14em] text-white/80 sm:text-[12px]">
                        <span class="flex h-[24px] w-[24px] shrink-0 items-center justify-center rounded-[6px] border border-brand/60 bg-brand/10 text-brand">
                            <svg class="h-[13px] w-[13px]" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                <path d="M12 3L13.6 8.4L19 10L13.6 11.6L12 17L10.4 11.6L5 10L10.4 8.4L12 3Z" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round" />
                                <path d="M18.5 15.5L19.2 17.8L21.5 18.5L19.2 19.2L18.5 21.5L17.8 19.2L15.5 18.5L17.8 17.8L18.5 21.5Z" stroke="currentColor" stroke-width="1.5" stroke-linejoin="round" />
                            </svg>
                        </span>
                        <span>{{ $featuredPrefix }}</span>
                    </div>
                @endif

                @if($categoryName !== '')
                    @if($categoryUrl !== '')
                        <a href="{{ esc_url($categoryUrl) }}" class="inline-flex min-h-[28px] items-center justify-center rounded-[6px] border border-brand/45 bg-brand/10 px-3 text-[11px] font-semibold text-brand transition-[background-color,border-color,color] duration-500 ease-out hover:border-brand hover:bg-brand hover:text-primary focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-brand sm:text-[12px]">
                            {{ $categoryName }}
                        </a>
                    @else
                        <span class="inline-flex min-h-[28px] items-center rounded-[6px] border border-brand/45 bg-brand/10 px-3 text-[11px] font-semibold text-brand sm:text-[12px]">
                            {{ $categoryName }}
                        </span>
                    @endif
                @endif
            </div>
        @endif

        <div class="max-w-[700px]">
            <h3 class="text-white">
                <a href="{{ esc_url($featuredPost['url']) }}" class="transition-colors duration-300 hover:text-brand">
                    {{ $featuredTitle }}
                </a>
            </h3>
        </div>

        @if(!empty($featuredPost['excerpt']))
            <p class="mt-4 max-w-[640px] text-[14px] leading-[1.7] text-white/80 sm:text-[16px]">
                {{ $featuredPost['excerpt'] }}
            </p>
        @endif

        <div class="mt-7 flex flex-wrap items-center gap-x-5 gap-y-4">
            <a href="{{ esc_url($featuredPost['url']) }}" class="group/btn relative isolate inline-flex min-h-[48px] items-center justify-center gap-3 overflow-hidden rounded-[8px] bg-brand px-6 text-[13px] font-bold text-primary transition-colors duration-300 before:absolute before:inset-0 before:z-0 before:origin-left before:scale-x-0 before:bg-brand-active before:content-[''] before:transition-transform before:duration-500 before:ease-out hover:text-white hover:before:scale-x-100 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-brand motion-reduce:before:transition-none sm:min-h-[52px] sm:px-7 sm:text-[14px]">
                <span class="relative z-10">{{ $featuredButtonText }}</span>
                <svg class="relative z-10 h-4 w-4 transition-transform duration-300 group-hover/btn:translate-x-1" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                    <path d="M5 12H19M13 6L19 12L13 18" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                </svg>
            </a>

            <span class="inline-flex items-center gap-2 text-[12px] text-white/80 sm:text-[13px]">
                <svg class="h-[16px] w-[16px] text-brand" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                    <rect x="3" y="5" width="18" height="16" rx="2" stroke="currentColor" stroke-width="1.7" />
                    <path d="M7 3V7M17 3V7M3 10H21" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" />
                </svg>
                {{ $featuredPost['date'] }}
            </span>

            <span class="inline-flex items-center gap-2 text-[12px] text-white/80 sm:text-[13px]">
                <svg class="h-[16px] w-[16px] text-brand" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                    <circle cx="12" cy="12" r="9" stroke="currentColor" stroke-width="1.7" />
                    <path d="M12 7V12L15.5 14" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" />
                </svg>
                {{ $featuredPost['reading_time'] }} {{ __('min czytania', 'appandsite') }}
            </span>
        </div>
    </div>
</article>
