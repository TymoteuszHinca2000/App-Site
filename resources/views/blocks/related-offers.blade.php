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

    $descriptionHtml = wp_kses_post(
        $description ?? ''
    );

    $currentPostId = (int) get_the_ID();

    $currentLanguage =
        function_exists('pll_current_language')
            ? pll_current_language('slug')
            : null;

    $offersLimit = isset($limit)
        ? (int) $limit
        : 0;

    $offersLimit = max(
        0,
        $offersLimit
    );

    $offerIds = [];

    foreach ($offers ?? [] as $offer) {
        $offerId = is_object($offer)
            ? (int) $offer->ID
            : (int) $offer;

        if (!$offerId) {
            continue;
        }

        
        if (
            $currentLanguage
            && function_exists('pll_get_post')
        ) {
            $translatedOfferId = pll_get_post(
                $offerId,
                $currentLanguage
            );

            if ($translatedOfferId) {
                $offerId = (int) $translatedOfferId;
            }
        }

        if (
            !$offerId
            || get_post_type($offerId) !== 'offer'
            || get_post_status($offerId) !== 'publish'
            || in_array($offerId, $offerIds, true)
        ) {
            continue;
        }

        
        if (
            is_singular('offer')
            && $offerId === $currentPostId
        ) {
            continue;
        }

        $offerIds[] = $offerId;
    }

    $excludedIds = $offerIds;

    if (
        is_singular('offer')
        && $currentPostId
    ) {
        $excludedIds[] = $currentPostId;
    }

    $excludedIds = array_values(
        array_unique(
            array_map(
                'intval',
                $excludedIds
            )
        )
    );

    $remainingQueryArgs = [
        'post_type' => 'offer',
        'post_status' => 'publish',
        'posts_per_page' => -1,
        'post__not_in' => $excludedIds,
        'fields' => 'ids',

        
        'orderby' => [
            'menu_order' => 'ASC',
            'date' => 'DESC',
        ],

        'suppress_filters' => false,
    ];

    if ($currentLanguage) {
        $remainingQueryArgs['lang'] =
            $currentLanguage;
    }

    $remainingOfferIds = get_posts(
        $remainingQueryArgs
    );

    foreach ($remainingOfferIds as $remainingOfferId) {
        $remainingOfferId =
            (int) $remainingOfferId;

        if (
            !$remainingOfferId
            || in_array(
                $remainingOfferId,
                $offerIds,
                true
            )
        ) {
            continue;
        }

        if (
            is_singular('offer')
            && $remainingOfferId === $currentPostId
        ) {
            continue;
        }

        $offerIds[] =
            $remainingOfferId;
    }

    if ($offersLimit > 0) {
        $offerIds = array_slice(
            $offerIds,
            0,
            $offersLimit
        );
    }

    $offerItems = [];

    foreach ($offerIds as $offerId) {
        $offerItems[] = [
            'id' => $offerId,

            'title' => get_the_title(
                $offerId
            ),

            'excerpt' => get_the_excerpt(
                $offerId
            ),

            'url' => get_permalink(
                $offerId
            ),

            'image_id' => get_post_thumbnail_id(
                $offerId
            ),
        ];
    }
@endphp

<x-block-helper.block-wrapper
    :block="$block"
    :attributes="$attributes"
>
    <section
        @if(!$isEditor && !empty($offerItems))
            x-data="alpRelatedOffers()"
        @endif
        class="relative overflow-hidden bg-white"
    >

        <x-ui.container>

            
            <div
                class="flex flex-col gap-6
                    lg:flex-row
                    lg:items-end
                    lg:justify-between
                    lg:gap-12"
            >

                
                <div class="w-full max-w-[820px]">

                    
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
                                text-muted
                                sm:text-[12px]"
                        >

                            <span
                                class="flex h-[24px] w-[24px] shrink-0 items-center justify-center
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
                            class="!m-0 w-full"
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
                            class="wysiwyg mt-4 max-w-[720px]
                                [&_p]:!m-0
                                [&_p+p]:!mt-4"
                        >
                            {!! $descriptionHtml !!}
                        </div>

                    @endif

                </div>

                
                @if(
                    !empty($button['url'])
                    && !empty($button['title'])
                )

                    <div
                        @if(!$isEditor)
                            data-aos="fade-left"
                            data-aos-duration="650"
                            data-aos-delay="160"
                            data-aos-once="true"
                            data-aos-easing="ease-out-cubic"
                        @endif
                        class="shrink-0"
                    >

                        <a
                            href="{{ esc_url($button['url']) }}"
                            target="{{ esc_attr($button['target'] ?: '_self') }}"
                            @if(($button['target'] ?? '') === '_blank')
                                rel="noopener noreferrer"
                            @endif
                            class="group inline-flex min-h-[48px] items-center justify-center gap-3
                                rounded-[8px]
                                border
                                border-brand/70
                                bg-white
                                px-6
                                text-[13px]
                                font-bold
                                text-primary

                                transition-[background-color,border-color,color]
                                duration-300

                                hover:border-brand
                                hover:bg-brand
                                hover:text-primary

                                focus-visible:outline-2
                                focus-visible:outline-offset-3
                                focus-visible:outline-brand"
                        >

                            <span>
                                {{ $button['title'] }}
                            </span>

                            <svg
                                class="h-[16px] w-[16px] shrink-0"
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

                @endif

            </div>

            
            @if(!empty($offerItems))

                
                @if($isEditor)

                    <div
                        class="mt-8 grid grid-cols-1 gap-5
                            md:grid-cols-2
                            xl:grid-cols-3
                            xl:gap-6"
                    >

                        @foreach($offerItems as $offerItem)

                            <article
                                class="group relative flex min-h-[300px] flex-col
                                    rounded-[14px]
                                    border
                                    border-border
                                    bg-white
                                    p-5

                                    shadow-card-subtle

                                    transition-[border-color,box-shadow]
                                    duration-700
                                    ease-[cubic-bezier(0.22,1,0.36,1)]

                                    hover:border-brand/40
                                    hover:shadow-card-hover-soft

                                    sm:p-6"
                            >

                                
                                <div class="flex items-start gap-4">

                                    @if(!empty($offerItem['image_id']))

                                        <a
                                            href="{{ esc_url($offerItem['url']) }}"
                                            class="relative z-10 flex h-[58px] w-[58px] shrink-0
                                                items-center
                                                justify-center
                                                rounded-full
                                                bg-brand/10

                                                transition-transform
                                                duration-700
                                                ease-[cubic-bezier(0.22,1,0.36,1)]

                                                group-hover:scale-110

                                                lg:h-[62px]
                                                lg:w-[62px]"
                                            aria-label="{{ esc_attr($offerItem['title']) }}"
                                        >

                                            {!! wp_get_attachment_image(
                                                $offerItem['image_id'],
                                                'thumbnail',
                                                false,
                                                [
                                                    'class' => 'h-[30px] w-[30px] object-contain transition-transform duration-[1200ms] ease-[cubic-bezier(0.22,1,0.36,1)] group-hover:rotate-[8deg] lg:h-[32px] lg:w-[32px]',
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
                                                class="!m-0 !text-[18px] !font-bold !leading-[1.3]
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

                                                    group-hover:scale-x-100"
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

                        @endforeach

                    </div>

                
                @else

                    <div
                        data-aos="fade-up"
                        data-aos-duration="700"
                        data-aos-delay="220"
                        data-aos-once="true"
                        data-aos-easing="ease-out-cubic"
                        class="mt-8"
                    >

                        <div
                            x-ref="swiper"
                            class="swiper !overflow-visible"
                        >

                            <div class="swiper-wrapper">

                                @foreach($offerItems as $offerItem)

                                    <div class="swiper-slide !h-auto">

                                        <article
                                            class="group relative flex h-full min-h-[300px] flex-col
                                                rounded-[14px]
                                                border
                                                border-border
                                                bg-white
                                                p-5

                                                shadow-card-subtle

                                                transition-[border-color,box-shadow]
                                                duration-700
                                                ease-[cubic-bezier(0.22,1,0.36,1)]

                                                hover:border-brand/40
                                                hover:shadow-card-hover-soft

                                                sm:p-6"
                                        >

                                            
                                            <div class="flex items-start gap-4">

                                                @if(!empty($offerItem['image_id']))

                                                    <a
                                                        href="{{ esc_url($offerItem['url']) }}"
                                                        class="relative z-10 flex h-[58px] w-[58px] shrink-0
                                                            items-center
                                                            justify-center
                                                            rounded-full
                                                            bg-brand/10

                                                            transition-transform
                                                            duration-700
                                                            ease-[cubic-bezier(0.22,1,0.36,1)]

                                                            group-hover:scale-110

                                                            lg:h-[62px]
                                                            lg:w-[62px]"
                                                        aria-label="{{ esc_attr($offerItem['title']) }}"
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
                                                            class="!m-0 !text-[18px] !font-bold !leading-[1.3]
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

                        </div>

                        
                        @if(count($offerItems) > 1)

                            <div
                                class="mt-5 flex items-center justify-end gap-3"
                            >

                                <button
                                    type="button"
                                    x-ref="prev"
                                    aria-label="{{ __('Poprzednia oferta', 'appandsite') }}"
                                    class="flex h-[44px] w-[44px] items-center justify-center
                                        rounded-full
                                        border
                                        border-border
                                        bg-white
                                        text-primary

                                        transition-[border-color,background-color,color,opacity]
                                        duration-300

                                        hover:border-brand
                                        hover:bg-brand

                                        disabled:pointer-events-none
                                        disabled:opacity-30"
                                >

                                    <svg
                                        class="h-[17px] w-[17px]"
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

                                </button>

                                <button
                                    type="button"
                                    x-ref="next"
                                    aria-label="{{ __('Następna oferta', 'appandsite') }}"
                                    class="flex h-[44px] w-[44px] items-center justify-center
                                        rounded-full
                                        border
                                        border-border
                                        bg-white
                                        text-primary

                                        transition-[border-color,background-color,color,opacity]
                                        duration-300

                                        hover:border-brand
                                        hover:bg-brand

                                        disabled:pointer-events-none
                                        disabled:opacity-30"
                                >

                                    <svg
                                        class="h-[17px] w-[17px]"
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

                                </button>

                            </div>

                        @endif

                    </div>

                @endif

            @endif

        </x-ui.container>

    </section>
</x-block-helper.block-wrapper>