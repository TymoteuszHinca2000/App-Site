@php
    $isEditor = is_admin();

    $titleHtml = wp_kses($title ?? '', [
        'strong' => [],
        'b' => [],
        'em' => [],
        'i' => [],
        'br' => [],
        'p' => [],
    ]);

    $titleHtml = preg_replace(
        '/<\/p>\s*<p[^>]*>/i',
        '<br>',
        $titleHtml
    );

    $titleHtml = preg_replace(
        '/<\/?p\b[^>]*>/i',
        '',
        $titleHtml
    );

    $titleHtml = preg_replace(
        '/<(strong|b)>/i',
        '<span class="text-brand">',
        $titleHtml
    );

    $titleHtml = preg_replace(
        '/<\/(strong|b)>/i',
        '</span>',
        $titleHtml
    );

    $titleHtml = trim($titleHtml);

    $descriptionHtml = wp_kses_post(
        $description ?? ''
    );

    $backgroundId = is_array($background ?? null)
        ? ($background['ID'] ?? null)
        : $background;
@endphp

<x-block-helper.block-wrapper :block="$block" :attributes="$attributes">
    <section class="relative left-1/2 w-screen -translate-x-1/2 overflow-hidden bg-primary-dark">
        @if(!empty($backgroundId))
            <div class="absolute inset-0">
                {!! wp_get_attachment_image(
                    $backgroundId,
                    'full',
                    false,
                    [
                        'class' => 'h-full w-full object-cover',
                        'loading' => 'lazy',
                        'decoding' => 'async',
                    ]
                ) !!}
            </div>
        @endif

        <div class="absolute inset-0 full-cta-shade-horizontal"></div>

        <div class="absolute inset-0 full-cta-shade-vertical"></div>

        <x-ui.container>
            <div class="relative z-10 flex min-h-[480px] items-center py-14 sm:min-h-[500px] sm:py-16 lg:min-h-[560px] lg:py-20">
                <div class="w-full max-w-[680px]">
                    @if(!empty($prefix))
                        <div
                            @if(!$isEditor)
                                data-aos="fade-right"
                                data-aos-duration="500"
                                data-aos-once="true"
                                data-aos-easing="ease-out-cubic"
                            @endif
                            class="mb-5 flex items-center gap-3 text-[11px] font-semibold uppercase tracking-[0.14em] text-white/70 sm:text-[12px]"
                        >
                            <span class="flex h-[24px] w-[24px] shrink-0 items-center justify-center rounded-[6px] border border-brand/60 bg-brand/10 text-brand">
                                <svg class="h-[13px] w-[13px]" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                    <path d="M12 3L13.6 8.4L19 10L13.6 11.6L12 17L10.4 11.6L5 10L10.4 8.4L12 3Z" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round" />
                                    <path d="M18.5 15.5L19.2 17.8L21.5 18.5L19.2 19.2L18.5 21.5L17.8 19.2L15.5 18.5L17.8 17.8L18.5 15.5Z" stroke="currentColor" stroke-width="1.5" stroke-linejoin="round" />
                                </svg>
                            </span>

                            <span>
                                {{ $prefix }}
                            </span>
                        </div>
                    @endif

                    @if(!empty($titleHtml))
                        <h2
                            @if(!$isEditor)
                                data-aos="fade-up"
                                data-aos-duration="650"
                                data-aos-delay="80"
                                data-aos-once="true"
                                data-aos-easing="ease-out-cubic"
                            @endif
                            class="!m-0 max-w-[650px] !text-white"
                        >
                            {!! $titleHtml !!}
                        </h2>
                    @endif

                    @if(!empty($descriptionHtml))
                        <div
                            @if(!$isEditor)
                                data-aos="fade-up"
                                data-aos-duration="650"
                                data-aos-delay="150"
                                data-aos-once="true"
                                data-aos-easing="ease-out-cubic"
                            @endif
                            class="wysiwyg mt-5 max-w-[610px] text-white/75 [&_a]:!text-brand [&_li]:!text-white/75 [&_p]:!m-0 [&_p]:!text-white/75 [&_p+p]:!mt-4 [&_strong]:!text-white"
                        >
                            {!! $descriptionHtml !!}
                        </div>
                    @endif

                    @if(!empty($button['url']) && !empty($button['title']))
                        <div
                            @if(!$isEditor)
                                data-aos="fade-up"
                                data-aos-duration="650"
                                data-aos-delay="220"
                                data-aos-once="true"
                                data-aos-easing="ease-out-cubic"
                            @endif
                            class="mt-8"
                        >
                            <a
                                href="{{ esc_url($button['url']) }}"
                                target="{{ esc_attr($button['target'] ?: '_self') }}"
                                @if(($button['target'] ?? '') === '_blank')
                                    rel="noopener noreferrer"
                                @endif
                                class="group relative isolate inline-flex min-h-[52px] items-center justify-center gap-4 overflow-hidden rounded-[8px] bg-brand px-7 text-[14px] font-bold text-primary transition-colors duration-300 before:absolute before:inset-0 before:z-0 before:origin-left before:scale-x-0 before:bg-brand-active before:content-[''] before:transition-transform before:duration-300 before:ease-out hover:text-white hover:before:scale-x-100 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-brand motion-reduce:before:transition-none"
                            >
                                <span class="relative z-10">
                                    {{ $button['title'] }}
                                </span>

                                <svg class="relative z-10 h-4 w-4 transition-transform duration-300 group-hover:translate-x-1 motion-reduce:transition-none" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                    <path d="M5 12H19M13 6L19 12L13 18" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </x-ui.container>
    </section>
</x-block-helper.block-wrapper>