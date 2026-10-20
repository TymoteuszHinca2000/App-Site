
@php
    $isEditor = is_admin();
    $items = $blogPosts ?? [];
    $useSlider = !$isEditor && count($items) > 3;

    $titleHtml = wp_kses($title ?? '', [
        'strong' => [],
        'b' => [],
        'em' => [],
        'i' => [],
        'br' => [],
        'p' => [],
    ]);

    $titleHtml = preg_replace('/<\/p>\s*<p[^>]*>/i', '<br>', $titleHtml);
    $titleHtml = preg_replace('/<\/?p\b[^>]*>/i', '', $titleHtml);
    $titleHtml = preg_replace('/<(strong|b)>/i', '<span class="text-brand">', $titleHtml);
    $titleHtml = preg_replace('/<\/(strong|b)>/i', '</span>', $titleHtml);
    $titleHtml = trim($titleHtml);

    $descriptionHtml = wp_kses_post($description ?? '');
    $readMoreText = $postButtonText ?? __('Czytaj wpis', 'appandsite');
@endphp

<x-block-helper.block-wrapper :block="$block" :attributes="$attributes">
    @if($isEditor || !empty($items))
        <section @if(!$isEditor) x-data="alpRelatedBlogs()" @endif class="relative overflow-hidden bg-white">
            <x-ui.container>
                @if(!empty($prefix) || $titleHtml !== '' || $descriptionHtml !== '' || !empty($button['url']))
                    <div class="flex flex-col gap-6 lg:flex-row lg:items-end lg:justify-between lg:gap-12">
                        <div class="w-full max-w-[820px]">
                            @if(!empty($prefix))
                                <div @if(!$isEditor) data-aos="fade-right" data-aos-duration="500" data-aos-once="true" data-aos-easing="ease-out-cubic" @endif class="mb-5 flex items-center gap-3 text-[11px] font-semibold uppercase tracking-[0.14em] text-muted sm:text-[12px]">
                                    <span class="flex h-[24px] w-[24px] shrink-0 items-center justify-center rounded-[6px] border border-brand/60 bg-brand/10 text-brand">
                                        <svg class="h-[13px] w-[13px]" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                            <path d="M12 3L13.6 8.4L19 10L13.6 11.6L12 17L10.4 11.6L5 10L10.4 8.4L12 3Z" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round" />
                                            <path d="M18.5 15.5L19.2 17.8L21.5 18.5L19.2 19.2L18.5 21.5L17.8 19.2L15.5 18.5L17.8 19.2L18.5 21.5Z" stroke="currentColor" stroke-width="1.5" stroke-linejoin="round" />
                                        </svg>
                                    </span>
                                    <span>{{ $prefix }}</span>
                                </div>
                            @endif

                            @if($titleHtml !== '')
                                <h2 @if(!$isEditor) data-aos="fade-up" data-aos-duration="650" data-aos-delay="80" data-aos-once="true" data-aos-easing="ease-out-cubic" @endif class="w-full">{!! $titleHtml !!}</h2>
                            @endif

                            @if($descriptionHtml !== '')
                                <div @if(!$isEditor) data-aos="fade-up" data-aos-duration="650" data-aos-delay="150" data-aos-once="true" data-aos-easing="ease-out-cubic" @endif class="wysiwyg mt-4 max-w-[720px]">
                                    {!! $descriptionHtml !!}
                                </div>
                            @endif
                        </div>

                        @if(!empty($button['url']) && !empty($button['title']))
                            <div @if(!$isEditor) data-aos="fade-left" data-aos-duration="650" data-aos-delay="160" data-aos-once="true" data-aos-easing="ease-out-cubic" @endif class="shrink-0">
                                <a href="{{ esc_url($button['url']) }}" target="{{ esc_attr($button['target'] ?? '_self') }}" @if(($button['target'] ?? '') === '_blank') rel="noopener noreferrer" @endif class="inline-flex min-h-[48px] items-center justify-center gap-3 rounded-[8px] border border-brand/70 bg-white px-6 text-[13px] font-bold text-primary transition-[background-color,border-color,color] duration-500 ease-out hover:border-brand hover:bg-brand hover:text-primary focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-brand">
                                    <span>{{ $button['title'] }}</span>
                                    <svg class="h-[16px] w-[16px] shrink-0" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                        <path d="M5 12H19M13 6L19 12L13 18" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                    </svg>
                                </a>
                            </div>
                        @endif
                    </div>
                @endif

                @if(!empty($items))
                    <div @if(!$isEditor) data-aos="fade-up" data-aos-duration="700" data-aos-delay="180" data-aos-once="true" data-aos-easing="ease-out-cubic" @endif class="mt-8 min-w-0">
                        <div @if($useSlider) x-ref="swiper" @endif class="{{ $useSlider ? 'swiper !overflow-visible' : '' }} w-full py-4">
                            <div x-ref="cards" class="{{ $useSlider ? 'swiper-wrapper items-stretch' : 'grid auto-rows-fr grid-cols-1 items-stretch gap-5 sm:grid-cols-2 xl:grid-cols-3 xl:gap-6' }}">
                                @foreach($items as $post)
                                    @php
                                        $postId = (int) ($post['id'] ?? 0);
                                        $postUrl = !empty($post['url']) ? $post['url'] : ($postId ? get_permalink($postId) : '');
                                        $postTitle = (string) ($post['title'] ?? '');
                                        $rawExcerpt = trim((string) ($post['excerpt'] ?? ''));

                                        if($rawExcerpt === '' && $postId) {
                                            $rawExcerpt = (string) get_the_excerpt($postId);
                                        }

                                        $excerptText = html_entity_decode(
                                            wp_trim_words(wp_strip_all_tags($rawExcerpt), 25, '…'),
                                            ENT_QUOTES | ENT_HTML5,
                                            'UTF-8'
                                        );
                                    @endphp

                                    <div data-related-blog-slide class="{{ $useSlider ? 'swiper-slide flex min-w-0 self-stretch [&.swiper-slide]:h-auto' : 'flex min-w-0' }}">
                                        <article data-related-blog-card class="group/card relative isolate flex w-full min-w-0 flex-1 flex-col overflow-hidden rounded-[12px] border border-border bg-white shadow-card-soft transform-gpu transition-[transform,box-shadow,border-color] duration-[900ms] ease-[cubic-bezier(0.22,1,0.36,1)] hover:[transform:translateY(-4px)] hover:border-brand/40 hover:shadow-card-hover-soft motion-reduce:transition-none">
                                            <a href="{{ esc_url($postUrl) }}" aria-label="{{ esc_attr($postTitle) }}" class="swiper-no-swiping relative block aspect-[16/9] w-full shrink-0 overflow-hidden bg-primary-dark focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-brand">
                                                @if(!empty($post['image_id']))
                                                    {!! wp_get_attachment_image($post['image_id'], 'large', false, [
                                                        'class' => 'absolute inset-0 h-full w-full object-cover transition-transform duration-[1100ms] ease-[cubic-bezier(0.22,1,0.36,1)] group-hover/card:scale-[1.055] motion-reduce:transition-none',
                                                        'alt' => $postTitle,
                                                        'loading' => 'lazy',
                                                        'decoding' => 'async',
                                                    ]) !!}
                                                @else
                                                    <div class="absolute inset-0 flex items-center justify-center bg-gradient-to-br from-primary-dark to-primary-light">
                                                        <svg class="h-[52px] w-[52px] text-brand/60 transition-transform duration-[1100ms] ease-[cubic-bezier(0.22,1,0.36,1)] group-hover/card:scale-110 group-hover/card:rotate-[8deg] motion-reduce:transition-none" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                                            <rect x="3" y="4" width="18" height="16" rx="2" stroke="currentColor" stroke-width="1.5" />
                                                            <path d="M7 9H17M7 13H17M7 17H13" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" />
                                                        </svg>
                                                    </div>
                                                @endif
                                                <span aria-hidden="true" class="pointer-events-none absolute inset-0 bg-gradient-to-t from-primary-dark/25 to-transparent"></span>
                                            </a>

                                            <div class="relative flex w-full min-w-0 flex-1 flex-col p-5 sm:p-6">
                                                <div class="mb-4 flex w-full flex-wrap items-center justify-between gap-2 text-[11px] text-muted sm:text-[12px]">
                                                    @if(!empty($post['category']))
                                                        @if(!empty($post['category_url']))
                                                            <a href="{{ esc_url($post['category_url']) }}" class="swiper-no-swiping relative z-20 inline-flex items-center rounded-[6px] border border-brand/30 bg-brand/10 px-2.5 py-1 font-semibold text-brand-active transition-[background-color,border-color] duration-300 ease-out hover:border-brand hover:bg-brand/15 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-brand">
                                                                {{ $post['category'] }}
                                                            </a>
                                                        @else
                                                            <span class="inline-flex items-center rounded-[6px] border border-brand/30 bg-brand/10 px-2.5 py-1 font-semibold text-brand-active">
                                                                {{ $post['category'] }}
                                                            </span>
                                                        @endif
                                                    @endif
                                                    <span class="shrink-0">{{ $post['date'] ?? '' }}</span>
                                                </div>

                                                <div class="flex w-full min-w-0 flex-1 flex-col">
                                                    <div class="relative z-20 mb-3 w-full min-w-0 pb-[5px]">
                                                        <a href="{{ esc_url($postUrl) }}" class="swiper-no-swiping relative z-20 block w-full max-w-none cursor-pointer text-primary no-underline pointer-events-auto hover:text-primary hover:no-underline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-brand">
                                                            <h5 class="m-0 w-full max-w-none text-wrap text-primary">
                                                                {{ $postTitle }}
                                                            </h5>
                                                        </a>
                                                        <span aria-hidden="true" class="pointer-events-none absolute bottom-0 left-0 h-[2px] w-full origin-left scale-x-0 rounded-full bg-brand transition-transform duration-500 ease-out group-hover/card:scale-x-100 motion-reduce:transition-none"></span>
                                                    </div>

                                                    @if($excerptText !== '')
                                                        <p class="m-0 line-clamp-3 text-[13px] leading-[1.65] text-muted sm:text-[14px]">
                                                            {{ $excerptText }}
                                                        </p>
                                                    @endif
                                                </div>

                                                <div class="mt-5 flex w-full flex-wrap items-center justify-between gap-x-3 gap-y-3 border-t border-border/70 pt-4">
                                                    <a href="{{ esc_url($postUrl) }}" class="swiper-no-swiping relative z-20 inline-flex w-fit cursor-pointer items-center gap-3 text-brand-active no-underline hover:text-brand-active hover:no-underline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-brand">
                                                        <span class="relative inline-block pb-[3px] text-[13px] font-semibold sm:text-[14px]">
                                                            {{ $readMoreText }}
                                                            <span aria-hidden="true" class="pointer-events-none absolute bottom-0 left-0 h-px w-full bg-current"></span>
                                                        </span>
                                                        <svg class="h-[17px] w-[17px] shrink-0 text-brand" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                                            <path d="M5 12H19M13 6L19 12L13 18" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                                        </svg>
                                                    </a>

                                                    <span class="inline-flex shrink-0 items-center gap-1.5 whitespace-nowrap text-[11px] text-muted sm:text-[12px]">
                                                        <svg class="h-[14px] w-[14px] shrink-0 text-brand-active" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                                            <circle cx="12" cy="12" r="9" stroke="currentColor" stroke-width="1.8" />
                                                            <path d="M12 7V12L15.5 14" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" />
                                                        </svg>
                                                        {{ $post['reading_time'] ?? 1 }} {{ __('min czytania', 'appandsite') }}
                                                    </span>
                                                </div>
                                            </div>
                                        </article>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        @if($useSlider)
                            <div class="mt-5 flex items-center justify-end gap-3">
                                <button type="button" x-ref="prev" aria-label="{{ esc_attr__('Poprzedni artykuł', 'appandsite') }}" class="flex h-[44px] w-[44px] items-center justify-center rounded-full border border-border bg-white text-primary transition-[border-color,background-color,opacity] duration-500 ease-out hover:border-brand hover:bg-brand focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-brand [&.swiper-button-disabled]:pointer-events-none [&.swiper-button-disabled]:opacity-30">
                                    <svg class="h-[17px] w-[17px]" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                        <path d="M19 12H5M11 6L5 12L11 18" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                    </svg>
                                </button>
                                <button type="button" x-ref="next" aria-label="{{ esc_attr__('Następny artykuł', 'appandsite') }}" class="flex h-[44px] w-[44px] items-center justify-center rounded-full border border-border bg-white text-primary transition-[border-color,background-color,opacity] duration-500 ease-out hover:border-brand hover:bg-brand focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-brand [&.swiper-button-disabled]:pointer-events-none [&.swiper-button-disabled]:opacity-30">
                                    <svg class="h-[17px] w-[17px]" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                        <path d="M5 12H19M13 6L19 12L13 18" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                    </svg>
                                </button>
                            </div>
                        @endif
                    </div>
                @elseif($isEditor)
                    <div class="mt-8 rounded-[12px] border border-border bg-surface p-6 text-center text-muted">
                        {{ __('Brak artykułów do wyświetlenia.', 'appandsite') }}
                    </div>
                @endif
            </x-ui.container>
        </section>
    @endif
</x-block-helper.block-wrapper>
