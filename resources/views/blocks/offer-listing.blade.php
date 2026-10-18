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

    $descriptionHtml = wp_kses(
        $description ?? '',
        [
            'p' => [],
            'strong' => [],
            'b' => [],
            'em' => [],
            'i' => [],
            'br' => [],
            'ul' => [],
            'ol' => [],
            'li' => [],
            'a' => [
                'href' => [],
                'target' => [],
                'rel' => [],
            ],
        ]
    );

    $paginationBase = str_replace(
        999999999,
        '%#%',
        esc_url(
            get_pagenum_link(
                999999999
            )
        )
    );
@endphp

<x-block-helper.block-wrapper
    :block="$block"
    :attributes="$attributes"
>
    <section class="relative bg-white">

        <x-ui.container>

            <div
                class="grid gap-6
                    lg:grid-cols-[minmax(0,1.3fr)_minmax(320px,0.7fr)]
                    lg:items-start
                    lg:gap-12"
            >

                <div>

                    @if(!empty($prefix))

                        <div
                            @if(!$isEditor)
                                data-aos="fade-right"
                                data-aos-duration="500"
                                data-aos-once="true"
                                data-aos-easing="ease-out-cubic"
                            @endif
                            class="mb-4 flex items-center gap-3
                                text-[11px]
                                font-semibold
                                uppercase
                                tracking-[0.14em]
                                text-brand
                                sm:text-[12px]"
                        >

                            <span
                                class="flex h-[24px] w-[24px] shrink-0
                                    items-center
                                    justify-center
                                    rounded-[6px]
                                    border
                                    border-brand/60
                                    bg-brand/10
                                    text-brand"
                            >

                                <svg
                                    class="h-[13px] w-[13px]"
                                    viewBox="0 0 24 24"
                                    fill="none"
                                    aria-hidden="true"
                                >
                                    <path
                                        d="M12 3L13.6 8.4L19 10L13.6 11.6L12 17L10.4 11.6L5 10L10.4 8.4L12 3Z"
                                        stroke="currentColor"
                                        stroke-width="1.8"
                                        stroke-linejoin="round"
                                    />

                                    <path
                                        d="M18.5 15.5L19.2 17.8L21.5 18.5L19.2 19.2L18.5 21.5L17.8 19.2L15.5 18.5L17.8 17.8L18.5 15.5Z"
                                        stroke="currentColor"
                                        stroke-width="1.5"
                                        stroke-linejoin="round"
                                    />
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
                            class="!m-0 max-w-[850px]"
                        >
                            {!! $titleHtml !!}
                        </h2>

                    @endif

                </div>

                @if(!empty($descriptionHtml))

                    <div
                        @if(!$isEditor)
                            data-aos="fade-up"
                            data-aos-duration="650"
                            data-aos-delay="140"
                            data-aos-once="true"
                            data-aos-easing="ease-out-cubic"
                        @endif
                        class="lg:pt-8
                            [&_a]:font-semibold
                            [&_a]:text-brand
                            [&_a]:underline
                            [&_a]:underline-offset-4
                            [&_p]:!m-0
                            [&_p+p]:!mt-4"
                    >
                        {!! $descriptionHtml !!}
                    </div>

                @endif

            </div>

            @if(!empty($offerItems))

                <div
                    class="mt-9 grid grid-cols-1 gap-5
                        md:grid-cols-2
                        xl:grid-cols-3
                        xl:gap-6"
                >

                    @foreach($offerItems as $offerItem)

                        <div
                            @if(!$isEditor)
                                data-aos="fade-up"
                                data-aos-duration="650"
                                data-aos-delay="{{ ($loop->index % 3) * 80 }}"
                                data-aos-once="true"
                                data-aos-easing="ease-out-cubic"
                            @endif
                            class="h-full"
                        >

                            <article
                                class="group relative flex h-full min-h-[420px] flex-col
                                    rounded-[14px]
                                    border
                                    border-border
                                    bg-white
                                    p-5
                                    shadow-card-subtle
                                    transition-[border-color,box-shadow]
                                    duration-[900ms]
                                    ease-[cubic-bezier(0.22,1,0.36,1)]
                                    hover:border-brand/40
                                    hover:shadow-card-hover-soft
                                    sm:p-6"
                            >

                                <div class="flex items-start gap-4">

                                    @if(!empty($offerItem['image_id']))

                                        <a
                                            href="{{ esc_url($offerItem['url']) }}"
                                            aria-label="{{ esc_attr($offerItem['title']) }}"
                                            class="relative z-10 flex
                                                h-[58px]
                                                w-[58px]
                                                shrink-0
                                                items-center
                                                justify-center
                                                rounded-full
                                                bg-brand/10
                                                transition-transform
                                                duration-700
                                                ease-[cubic-bezier(0.22,1,0.36,1)]
                                                group-hover:scale-110
                                                motion-reduce:transition-none
                                                lg:h-[62px]
                                                lg:w-[62px]"
                                        >

                                            {!! wp_get_attachment_image(
                                                $offerItem['image_id'],
                                                'thumbnail',
                                                false,
                                                [
                                                    'class' => 'h-[30px] w-[30px] object-contain transition-transform duration-[1200ms] ease-[cubic-bezier(0.22,1,0.36,1)] group-hover:rotate-[8deg] motion-reduce:transition-none lg:h-[32px] lg:w-[32px]',
                                                    'loading' => 'lazy',
                                                    'decoding' => 'async',
                                                ]
                                            ) !!}

                                        </a>

                                    @endif

                                    <div class="min-w-0 flex-1">

                                        <div
                                            class="relative inline-block max-w-full pb-[5px]"
                                        >

                                            <h3
                                                class="!m-0
                                                    !text-[18px]
                                                    !font-bold
                                                    !leading-[1.3]
                                                    sm:!text-[19px]"
                                            >

                                                <a
                                                    href="{{ esc_url($offerItem['url']) }}"
                                                    class="relative z-10"
                                                >
                                                    {{ $offerItem['title'] }}
                                                </a>

                                            </h3>

                                            <span
                                                class="absolute bottom-0 left-0
                                                    h-[2px]
                                                    w-full
                                                    origin-left
                                                    scale-x-0
                                                    rounded-full
                                                    bg-brand
                                                    transition-transform
                                                    duration-500
                                                    ease-out
                                                    group-hover:scale-x-100
                                                    motion-reduce:transition-none"
                                            ></span>

                                        </div>

                                    </div>

                                </div>

                                @if(!empty($offerItem['excerpt']))

                                    <p
                                        class="mt-5 !mb-0
                                            !text-[14px]
                                            !leading-[1.65]
                                            text-muted
                                            sm:!text-[15px]"
                                    >
                                        {{ $offerItem['excerpt'] }}
                                    </p>

                                @endif

                                @if(!empty($offerItem['features']))

                                    <ul class="mt-6 flex flex-col gap-2.5">

                                        @foreach($offerItem['features'] as $feature)

                                            @if(!empty($feature['text']))

                                                <li class="flex items-start gap-3">

                                                    <span
                                                        class="mt-[3px] flex
                                                            h-[17px]
                                                            w-[17px]
                                                            shrink-0
                                                            items-center
                                                            justify-center
                                                            text-brand"
                                                    >

                                                        <svg
                                                            class="h-[15px] w-[15px]"
                                                            viewBox="0 0 24 24"
                                                            fill="none"
                                                            aria-hidden="true"
                                                        >
                                                            <path
                                                                d="M5 12.5L9.5 17L19 7.5"
                                                                stroke="currentColor"
                                                                stroke-width="2.4"
                                                                stroke-linecap="round"
                                                                stroke-linejoin="round"
                                                            />
                                                        </svg>

                                                    </span>

                                                    <span
                                                        class="text-[13px]
                                                            leading-[1.5]
                                                            text-muted
                                                            sm:text-[14px]"
                                                    >
                                                        {{ $feature['text'] }}
                                                    </span>

                                                </li>

                                            @endif

                                        @endforeach

                                    </ul>

                                @endif

                                <div class="relative z-10 mt-auto pt-7">

                                    <a
                                        href="{{ esc_url($offerItem['url']) }}"
                                        class="flex items-center justify-between gap-4"
                                    >

                                        <span
                                            class="relative inline-block pb-[3px]
                                                text-[14px]
                                                font-semibold
                                                text-brand-active"
                                        >

                                            {{ __('Dowiedz się więcej', 'appandsite') }}

                                            <span
                                                class="absolute bottom-0 left-0 h-px w-full bg-current"
                                            ></span>

                                        </span>

                                        <svg
                                            class="h-[18px] w-[18px] shrink-0 text-brand"
                                            viewBox="0 0 24 24"
                                            fill="none"
                                            aria-hidden="true"
                                        >
                                            <path
                                                d="M5 12H19M13 6L19 12L13 18"
                                                stroke="currentColor"
                                                stroke-width="2"
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                            />
                                        </svg>

                                    </a>

                                </div>

                            </article>

                        </div>

                    @endforeach

                </div>

                @if($maxPages > 1)

                    <nav
                        aria-label="{{ esc_attr__('Paginacja ofert', 'appandsite') }}"
                        class="mt-10 flex justify-center lg:mt-12"
                    >

                        @php
                            $prevIcon = '
                                <svg
                                    class="h-[18px] w-[18px]"
                                    viewBox="0 0 24 24"
                                    fill="none"
                                    aria-hidden="true"
                                >
                                    <path
                                        d="M19 12H5M11 6L5 12L11 18"
                                        stroke="currentColor"
                                        stroke-width="2"
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                    />
                                </svg>
                            ';

                            $nextIcon = '
                                <svg
                                    class="h-[18px] w-[18px]"
                                    viewBox="0 0 24 24"
                                    fill="none"
                                    aria-hidden="true"
                                >
                                    <path
                                        d="M5 12H19M13 6L19 12L13 18"
                                        stroke="currentColor"
                                        stroke-width="2"
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                    />
                                </svg>
                            ';

                            $pagination = paginate_links([
                                'base' => $paginationBase,
                                'format' => '?paged=%#%',
                                'current' => $currentPage,
                                'total' => $maxPages,
                                'type' => 'array',
                                'prev_text' => $prevIcon,
                                'next_text' => $nextIcon,
                                'mid_size' => 1,
                                'end_size' => 1,
                            ]);
                        @endphp

                        @if(!empty($pagination))

                            <div class="flex flex-wrap items-center justify-center gap-2">

                                @foreach($pagination as $pageLink)

                                    @php
                                        $isCurrent = str_contains(
                                            $pageLink,
                                            'current'
                                        );

                                        $pageLink = str_replace(
                                            'page-numbers',
                                            'page-numbers inline-flex h-[42px] min-w-[42px] items-center justify-center rounded-[8px] border px-3 text-[14px] font-semibold transition-[border-color,background-color,color] duration-300 ' .
                                            (
                                                $isCurrent
                                                    ? 'border-brand bg-brand text-primary'
                                                    : 'border-border bg-white text-primary hover:border-brand hover:text-brand'
                                            ),
                                            $pageLink
                                        );
                                    @endphp

                                    {!! $pageLink !!}

                                @endforeach

                            </div>

                        @endif

                    </nav>

                @endif

            @else

                <div
                    class="mt-8 rounded-[12px]
                        border
                        border-border
                        bg-surface
                        p-6"
                >

                    <p class="!m-0 text-muted">
                        {{ __('Brak ofert do wyświetlenia.', 'appandsite') }}
                    </p>

                </div>

            @endif

        </x-ui.container>

    </section>
</x-block-helper.block-wrapper>