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

    $contentHtml = wp_kses_post($content ?? '');

    $contentOrder = $reverseLayout
        ? 'lg:order-2'
        : 'lg:order-1';

    $imageOrder = $reverseLayout
        ? 'lg:order-1'
        : 'lg:order-2';

    $contentAos = $reverseLayout
        ? 'fade-left'
        : 'fade-right';

    $imageAos = $reverseLayout
        ? 'fade-right'
        : 'fade-left';
@endphp

<x-block-helper.block-wrapper :block="$block" :attributes="$attributes">
    @if($isEditor || is_singular('offer'))
        <section class="relative bg-white">
            <x-ui.container>
                <div class="grid grid-cols-1 items-start gap-y-8 lg:grid-cols-[45%_45%] lg:justify-between">
                    <div
                        @if(!$isEditor)
                            data-aos="{{ $contentAos }}"
                            data-aos-duration="700"
                            data-aos-once="true"
                            data-aos-easing="ease-out-cubic"
                        @endif
                        class="min-w-0 self-start {{ $contentOrder }}"
                    >
                        @if(!empty($prefix))
                            <div class="mb-5 flex items-center gap-3 text-[11px] font-semibold uppercase tracking-[0.14em] text-muted sm:text-[12px]">
                                <span class="flex h-[24px] w-[24px] shrink-0 items-center justify-center rounded-[6px] border border-brand/60 bg-brand/10 text-brand">
                                    <svg class="h-[13px] w-[13px]" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                        <path d="M12 3L13.6 8.4L19 10L13.6 11.6L12 17L10.4 11.6L5 10L10.4 8.4L12 3Z" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round" />
                                        <path d="M18.5 15.5L19.2 17.8L21.5 18.5L19.2 19.2L18.5 21.5L17.8 19.2L15.5 18.5L17.8 17.8L18.5 15.5Z" stroke="currentColor" stroke-width="1.5" stroke-linejoin="round" />
                                    </svg>
                                </span>

                                <span>{{ $prefix }}</span>
                            </div>
                        @endif

                        @if(!empty($titleHtml))
                            <h2 class="!m-0 w-full">
                                {!! $titleHtml !!}
                            </h2>
                        @endif

                        @if(!empty($contentHtml))
                            <div class="wysiwyg mt-5 w-full [&_p]:!m-0 [&_p+p]:!mt-4 [&_ul+*]:mt-6 [&_ol+*]:mt-6">
                                {!! $contentHtml !!}
                            </div>
                        @endif
                    </div>

                    @if(!empty($image['ID']))
                        <div
                            @if(!$isEditor)
                                data-aos="{{ $imageAos }}"
                                data-aos-duration="750"
                                data-aos-delay="80"
                                data-aos-once="true"
                                data-aos-easing="ease-out-cubic"
                            @endif
                            class="group/image w-full min-w-0 self-start {{ $imageOrder }}"
                        >
                            <div class="overflow-hidden rounded-[16px] border border-border bg-white/95 shadow-card-soft transition-[box-shadow,border-color,background-color] duration-700 ease-[cubic-bezier(0.22,1,0.36,1)] group-hover/image:border-brand/45 group-hover/image:bg-white group-hover/image:shadow-card-hover">
                                {!! wp_get_attachment_image(
                                    $image['ID'],
                                    'large',
                                    false,
                                    [
                                        'class' => 'block aspect-[3/2] w-full object-cover transition-transform duration-[1100ms] ease-[cubic-bezier(0.22,1,0.36,1)] group-hover/image:scale-[1.035] motion-reduce:transform-none motion-reduce:transition-none',
                                        'loading' => $isEditor ? 'eager' : 'lazy',
                                        'decoding' => 'async',
                                    ]
                                ) !!}
                            </div>
                        </div>
                    @endif
                </div>
            </x-ui.container>
        </section>
    @endif
</x-block-helper.block-wrapper>
