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

    $titleHtml = trim(
        $titleHtml
    );

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

    $galleryItems = [];

    foreach ($backgroundGallery ?? [] as $galleryImage) {
        $imageId = is_array($galleryImage)
            ? ($galleryImage['ID'] ?? null)
            : $galleryImage;

        if (!$imageId) {
            continue;
        }

        $galleryItems[] = [
            'id' => $imageId,
            'url' => wp_get_attachment_image_url(
                $imageId,
                'full'
            ),
            'alt' => get_post_meta(
                $imageId,
                '_wp_attachment_image_alt',
                true
            ),
        ];
    }

    $videoUrl = is_array($backgroundVideo ?? null)
        ? ($backgroundVideo['url'] ?? '')
        : '';

    $videoMime = is_array($backgroundVideo ?? null)
        ? ($backgroundVideo['mime_type'] ?? '')
        : '';

    $posterUrl = is_array($backgroundVideoPoster ?? null)
        ? ($backgroundVideoPoster['url'] ?? '')
        : '';

    $mediaType =
        ($backgroundType ?? 'gallery') === 'video'
        && $videoUrl
            ? 'video'
            : 'gallery';

    $homeUrl = function_exists('pll_home_url')
        ? pll_home_url()
        : home_url('/');

    $breadcrumbs = [];

    $currentPostId = (int) get_queried_object_id();

    if ($currentPostId) {
        $currentPostType = get_post_type(
            $currentPostId
        );

        if (
            $currentPostType === 'offer'
            && is_singular('offer')
        ) {
            $offerPage = get_page_by_path(
                'oferta',
                OBJECT,
                'page'
            );

            if ($offerPage) {
                $offerPageId = (int) $offerPage->ID;

                
                if (
                    function_exists('pll_current_language')
                    && function_exists('pll_get_post')
                ) {
                    $currentLanguage = pll_current_language(
                        'slug'
                    );

                    if ($currentLanguage) {
                        $translatedOfferPageId = pll_get_post(
                            $offerPageId,
                            $currentLanguage
                        );

                        if ($translatedOfferPageId) {
                            $offerPageId = (int) $translatedOfferPageId;
                        }
                    }
                }

                $breadcrumbs[] = [
                    'title' => get_the_title(
                        $offerPageId
                    ),
                    'url' => get_permalink(
                        $offerPageId
                    ),
                ];
            }

            
            $breadcrumbs[] = [
                'title' => get_the_title(
                    $currentPostId
                ),
                'url' => '',
            ];
        }

        else {
            $ancestors = array_reverse(
                get_post_ancestors(
                    $currentPostId
                )
            );

            foreach ($ancestors as $ancestorId) {
                $breadcrumbs[] = [
                    'title' => get_the_title(
                        $ancestorId
                    ),
                    'url' => get_permalink(
                        $ancestorId
                    ),
                ];
            }

            $breadcrumbs[] = [
                'title' => get_the_title(
                    $currentPostId
                ),
                'url' => '',
            ];
        }
    }
@endphp

<x-block-helper.block-wrapper
    :block="$block"
    :attributes="$attributes"
>
    <section
        @if(
            !$isEditor
            && $mediaType === 'gallery'
            && !empty($galleryItems)
        )
            x-data="alpBanner(@js($galleryItems), 6500)"
        @endif
        class="relative isolate overflow-hidden bg-primary-dark"
    >

        
        <div class="absolute inset-0 -z-30 overflow-hidden">

            @if($mediaType === 'video')

                <video
                    class="h-full w-full object-cover"
                    autoplay
                    muted
                    loop
                    playsinline
                    preload="metadata"
                    @if($posterUrl)
                        poster="{{ esc_url($posterUrl) }}"
                    @endif
                    aria-hidden="true"
                >
                    <source
                        src="{{ esc_url($videoUrl) }}"
                        @if($videoMime)
                            type="{{ esc_attr($videoMime) }}"
                        @endif
                    >
                </video>

            @elseif(!empty($galleryItems))

                @foreach($galleryItems as $index => $galleryItem)

                    @if(
                        $isEditor
                        && $index > 0
                    )
                        @continue
                    @endif

                    @if(!$isEditor)

                        <div
                            @if($index > 0)
                                x-cloak
                            @endif
                            x-show="activeIndex === {{ $index }}"
                            x-transition:enter="transition-opacity duration-1000 ease-out"
                            x-transition:enter-start="opacity-0"
                            x-transition:enter-end="opacity-100"
                            x-transition:leave="transition-opacity duration-1000 ease-out"
                            x-transition:leave-start="opacity-100"
                            x-transition:leave-end="opacity-0"
                            class="absolute inset-0 overflow-hidden [&[x-cloak]]:hidden"
                        >
                            <div
                                x-bind:class="
                                    activeIndex === {{ $index }} && motionActive
                                        ? (
                                            motionDirection
                                                ? 'scale-[1.085] translate-x-[1.4%] -translate-y-[0.7%]'
                                                : 'scale-[1.085] -translate-x-[1.4%] translate-y-[0.7%]'
                                        )
                                        : 'scale-[1.02] translate-x-0 translate-y-0'
                                "
                                class="absolute -inset-[4%] transform-gpu transition-transform duration-[7600ms] ease-in-out will-change-transform motion-reduce:transform-none motion-reduce:transition-none"
                            >
                                {!! wp_get_attachment_image(
                                    $galleryItem['id'],
                                    'full',
                                    false,
                                    [
                                        'class' => 'h-full w-full object-cover',
                                        'alt' => '',
                                        'aria-hidden' => 'true',
                                        'loading' => $index === 0
                                            ? 'eager'
                                            : 'lazy',
                                        'fetchpriority' => $index === 0
                                            ? 'high'
                                            : 'auto',
                                        'decoding' => 'async',
                                    ]
                                ) !!}
                            </div>
                        </div>

                    @else

                        <div class="absolute inset-0">

                            {!! wp_get_attachment_image(
                                $galleryItem['id'],
                                'full',
                                false,
                                [
                                    'class' => 'h-full w-full object-cover',
                                    'alt' => '',
                                    'aria-hidden' => 'true',
                                    'loading' => 'eager',
                                    'fetchpriority' => 'high',
                                    'decoding' => 'async',
                                ]
                            ) !!}

                        </div>

                    @endif

                @endforeach

            @endif

        </div>

        
        <div
            class="absolute inset-0 -z-20 banner-shade-horizontal"
        ></div>

        <div
            class="absolute inset-0 -z-20 banner-shade-vertical"
        ></div>

        <x-ui.container>

            <div
                class="relative z-10 flex flex-col justify-center pb-12 pt-[120px] sm:pb-14 sm:pt-[130px] lg:pb-16 lg:pt-[145px] xl:pb-[72px] xl:pt-[155px]"
            >

                
                @if(!empty($breadcrumbs))

                    <nav
                        @if(!$isEditor)
                            data-aos="fade-right"
                            data-aos-duration="500"
                            data-aos-once="true"
                            data-aos-easing="ease-out-cubic"
                        @endif
                        aria-label="Breadcrumb"
                        class="mb-5 sm:mb-6"
                    >

                        <ol
                            class="flex flex-wrap items-center gap-x-2 gap-y-1 text-[12px] font-medium text-white/65 sm:text-[13px]"
                        >

                            
                            <li class="flex items-center">

                                <a
                                    href="{{ esc_url($homeUrl) }}"
                                    class="flex items-center gap-2 transition-colors duration-300 hover:text-brand"
                                >
                                    <svg
                                        class="h-[15px] w-[15px] shrink-0 text-brand"
                                        viewBox="0 0 24 24"
                                        fill="none"
                                        aria-hidden="true"
                                    >
                                        <path
                                            d="M3 10.5L12 3L21 10.5V20C21 20.5523 20.5523 21 20 21H15V14H9V21H4C3.44772 21 3 20.5523 3 20V10.5Z"
                                            stroke="currentColor"
                                            stroke-width="1.8"
                                            stroke-linejoin="round"
                                        />
                                    </svg>

                                    <span>
                                        {{ __('Strona główna', 'appandsite') }}
                                    </span>
                                </a>

                            </li>

                            
                            @foreach($breadcrumbs as $breadcrumb)

                                <li
                                    aria-hidden="true"
                                    class="text-white/35"
                                >
                                    /
                                </li>

                                <li>

                                    @if(!empty($breadcrumb['url']))

                                        <a
                                            href="{{ esc_url($breadcrumb['url']) }}"
                                            class="transition-colors duration-300 hover:text-brand"
                                        >
                                            {{ $breadcrumb['title'] }}
                                        </a>

                                    @else

                                        <span
                                            aria-current="page"
                                            class="text-white/90"
                                        >
                                            {{ $breadcrumb['title'] }}
                                        </span>

                                    @endif

                                </li>

                            @endforeach

                        </ol>

                    </nav>

                @endif

                
                <div class="w-full max-w-[920px]">

                    @if($titleHtml)

                        <h1
                            @if(!$isEditor)
                                data-aos="fade-up"
                                data-aos-duration="700"
                                data-aos-delay="80"
                                data-aos-once="true"
                                data-aos-easing="ease-out-cubic"
                            @endif
                            class="!m-0 max-w-[920px] !text-[40px] !font-extrabold !leading-[1.02] !tracking-[-0.04em] !text-white sm:!text-[46px] md:!text-[52px] lg:!text-[58px] xl:!text-[64px]"
                        >
                            {!! $titleHtml !!}
                        </h1>

                    @endif

                    @if($descriptionHtml)

                        <div
                            @if(!$isEditor)
                                data-aos="fade-up"
                                data-aos-duration="700"
                                data-aos-delay="160"
                                data-aos-once="true"
                                data-aos-easing="ease-out-cubic"
                            @endif
                            class="mt-5 max-w-[780px]
                                text-[15px]
                                leading-[1.65]
                                text-white/85
                                sm:text-[16px]
                                md:text-[17px]
                                lg:mt-6
                                lg:text-[18px]
                                [&_a]:font-semibold
                                [&_a]:text-brand
                                [&_a]:underline
                                [&_a]:underline-offset-4
                                [&_p]:!m-0
                                [&_p]:!text-inherit
                                [&_p+p]:!mt-4
                                [&_ul]:mt-4
                                [&_ol]:mt-4"
                        >
                            {!! $descriptionHtml !!}
                        </div>

                    @endif

                </div>

                
                @if(!empty($items))

                    <div
                        class="mt-8 grid w-full grid-cols-1 gap-x-8 gap-y-6
                            sm:grid-cols-2
                            lg:mt-10
                            lg:max-w-[1320px]
                            lg:grid-cols-3
                            lg:gap-x-10
                            xl:max-w-[1440px]
                            xl:gap-x-14
                            2xl:max-w-[1520px]
                            2xl:gap-x-16"
                    >

                        @foreach($items as $item)

                            <div
                                @if(!$isEditor)
                                    data-aos="fade-up"
                                    data-aos-duration="650"
                                    data-aos-delay="{{ 220 + ($loop->index * 100) }}"
                                    data-aos-once="true"
                                    data-aos-easing="ease-out-cubic"
                                @endif
                                class="group flex min-w-0 items-center gap-4 lg:gap-5 xl:gap-6"
                            >

                                @if(!empty($item['image']['url']))

                                    <div
                                        class="flex h-[58px] w-[58px] shrink-0 items-center justify-center rounded-full
                                            bg-brand/10
                                            ring-1
                                            ring-brand/40
                                            transition-transform
                                            duration-500
                                            ease-out
                                            group-hover:scale-110
                                            lg:h-[62px]
                                            lg:w-[62px]"
                                    >
                                        <img
                                            src="{{ esc_url($item['image']['url']) }}"
                                            alt=""
                                            aria-hidden="true"
                                            loading="lazy"
                                            decoding="async"
                                            class="h-[30px] w-[30px] object-contain
                                                transition-transform
                                                duration-700
                                                ease-out
                                                group-hover:rotate-[8deg]
                                                lg:h-[32px]
                                                lg:w-[32px]"
                                        >
                                    </div>

                                @endif

                                <div class="flex min-w-0 flex-1 flex-col items-start">

                                    @if(!empty($item['title']))

                                        <div
                                            class="relative inline-block max-w-full pb-[5px]"
                                        >

                                            <h5
                                                class="!m-0 !text-white lg:whitespace-nowrap"
                                            >
                                                {{ $item['title'] }}
                                            </h5>

                                            <span
                                                class="absolute bottom-0 left-0 h-[2px] w-full origin-left scale-x-0 rounded-full bg-brand
                                                    transition-transform
                                                    duration-500
                                                    ease-out
                                                    group-hover:scale-x-100"
                                            ></span>

                                        </div>

                                    @endif

                                    @if(!empty($item['description']))

                                        <p
                                            class="mt-1 !mb-0 !text-[13px] !leading-[1.5] !text-white/65 sm:!text-[14px]"
                                        >
                                            {{ $item['description'] }}
                                        </p>

                                    @endif

                                </div>

                            </div>

                        @endforeach

                    </div>

                @endif

            </div>

        </x-ui.container>

    </section>
</x-block-helper.block-wrapper>