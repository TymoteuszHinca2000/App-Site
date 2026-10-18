@php
    $isEditor = is_admin();
    $scrollTargetId = wp_unique_id('home-hero-end-');

    $titleHtml = wp_kses($title ?? '', [
        'strong' => [],
        'b' => [],
        'em' => [],
        'i' => [],
        'br' => [],
        'p' => [],
    ]);

    $titleHtml = preg_replace('/<\/p>\s*<p[^>]*>/', '<br>', $titleHtml);
    $titleHtml = preg_replace('/<\/?p[^>]*>/', '', $titleHtml);

    $titleHtml = str_replace(
        ['<strong>', '</strong>', '<b>', '</b>'],
        ['<span class="text-brand">', '</span>', '<span class="text-brand">', '</span>'],
        $titleHtml
    );

    $descriptionHtml = wp_kses($description ?? '', [
        'p' => [],
        'strong' => [],
        'b' => [],
        'em' => [],
        'i' => [],
        'br' => [],
        'a' => [
            'href' => [],
            'target' => [],
            'rel' => [],
        ],
    ]);

    $galleryItems = [];

    foreach ($backgroundGallery ?? [] as $galleryImage) {
        $imageId = is_array($galleryImage) ? ($galleryImage['ID'] ?? null) : $galleryImage;

        if (!$imageId) {
            continue;
        }

        $galleryItems[] = [
            'id' => $imageId,
            'url' => wp_get_attachment_image_url($imageId, 'full'),
            'alt' => get_post_meta($imageId, '_wp_attachment_image_alt', true),
        ];
    }

    $videoUrl = is_array($backgroundVideo ?? null) ? ($backgroundVideo['url'] ?? '') : '';
    $videoMime = is_array($backgroundVideo ?? null) ? ($backgroundVideo['mime_type'] ?? '') : '';
    $posterUrl = is_array($backgroundVideoPoster ?? null) ? ($backgroundVideoPoster['url'] ?? '') : '';
    $mediaType = ($backgroundType ?? 'gallery') === 'video' && $videoUrl ? 'video' : 'gallery';
@endphp

<x-block-helper.block-wrapper :block="$block" :attributes="$attributes">
    <section @if(!$isEditor) x-data="alpHomeHero(@js($galleryItems), 6500)" @endif class="relative min-h-[100svh] overflow-hidden bg-primary-dark lg:min-h-screen">
        <div class="absolute inset-0">
            @if($mediaType === 'video')
                <video class="h-full w-full object-cover" autoplay muted loop playsinline preload="metadata" @if($posterUrl) poster="{{ $posterUrl }}" @endif aria-hidden="true"><source src="{{ $videoUrl }}" @if($videoMime) type="{{ $videoMime }}" @endif></video>
            @elseif(!empty($galleryItems))
                @foreach($galleryItems as $index => $galleryItem)
                    @if($isEditor && $index > 0)
                        @continue
                    @endif
                    <div
                        @if(!$isEditor)
                            @if($index > 0) x-cloak @endif
                            x-show="activeIndex === {{ $index }}"
                            x-transition:enter="transition-opacity duration-1000 ease-out"
                            x-transition:enter-start="opacity-0"
                            x-transition:enter-end="opacity-100"
                            x-transition:leave="transition-opacity duration-1000 ease-out"
                            x-transition:leave-start="opacity-100"
                            x-transition:leave-end="opacity-0"
                        @endif
                        class="absolute inset-0 overflow-hidden"
                    >
                        <div
                            @if(!$isEditor)
                                :class="motionActive && activeIndex === {{ $index }} ? (motionDirection ? 'home-hero-image--forward' : 'home-hero-image--reverse') : 'home-hero-image--rest'"
                            @endif
                            class="home-hero-image h-full w-full"
                        >
                            {!! wp_get_attachment_image(
                                $galleryItem['id'],
                                'full',
                                false,
                                [
                                    'class' => 'h-full w-full object-cover',
                                    'alt' => '',
                                    'aria-hidden' => 'true',
                                    'loading' => $index === 0 ? 'eager' : 'lazy',
                                    'fetchpriority' => $index === 0 ? 'high' : 'auto',
                                    'decoding' => 'async',
                                ]
                            ) !!}
                        </div>
                    </div>
                @endforeach
            @endif
        </div>

        <div class="absolute inset-0 z-10 home-hero-shade-horizontal"></div>
        <div class="absolute inset-0 z-10 home-hero-shade-vertical"></div>

        @if(!$isEditor)
            <canvas
                x-ref="waves"
                class="pointer-events-none absolute inset-0 z-[15] h-full w-full"
                aria-hidden="true"
            ></canvas>
        @endif

        <x-ui.container>
            <div class="relative z-20 flex min-h-[100svh] items-center pb-36 pt-24 sm:pb-36 sm:pt-28 lg:min-h-screen lg:pb-36 lg:pt-32">
                <div class="w-full max-w-[980px]">
                    @if($prefix)
                        <div @if(!$isEditor) data-aos="fade-right" data-aos-duration="500" data-aos-once="true" data-aos-easing="ease-out-cubic" @endif class="mb-5 flex items-center gap-3 text-[11px] font-semibold uppercase tracking-[0.14em] text-white/70 sm:text-[12px] lg:mb-6 lg:text-[13px]"><span class="flex h-[24px] w-[24px] shrink-0 items-center justify-center rounded-[6px] border border-brand/60 bg-brand/10 text-brand"><svg class="h-[13px] w-[13px]" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M12 3L13.6 8.4L19 10L13.6 11.6L12 17L10.4 11.6L5 10L10.4 8.4L12 3Z" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round" /><path d="M18.5 15.5L19.2 17.8L21.5 18.5L19.2 19.2L18.5 21.5L17.8 19.2L15.5 18.5L17.8 17.8L18.5 15.5Z" stroke="currentColor" stroke-width="1.5" stroke-linejoin="round" /></svg></span><span>{{ $prefix }}</span></div>
                    @endif

                    @if($titleHtml)
                        <h1 @if(!$isEditor) data-aos="fade-up" data-aos-duration="700" data-aos-delay="80" data-aos-once="true" data-aos-easing="ease-out-cubic" @endif class="m-0 max-w-[980px] text-[42px] font-extrabold leading-[0.98] tracking-[-0.045em] text-white sm:text-[50px] md:text-[58px] lg:text-[66px] xl:text-[72px] 2xl:text-[78px]">{!! $titleHtml !!}</h1>
                    @endif

                    @if($descriptionHtml)
                        <div @if(!$isEditor) data-aos="fade-up" data-aos-duration="700" data-aos-delay="160" data-aos-once="true" data-aos-easing="ease-out-cubic" @endif class="mt-6 max-w-[720px] text-[16px] leading-[1.65] text-white/85 sm:text-[17px] md:text-[18px] lg:mt-7 lg:text-[19px] xl:text-[20px] [&_a]:font-semibold [&_a]:text-brand [&_a]:underline [&_a]:underline-offset-4 [&_p]:m-0 [&_p]:text-inherit">{!! $descriptionHtml !!}</div>
                    @endif

                    @if($buttonPrimary || $buttonSecondary)
                        <div @if(!$isEditor) data-aos="fade-up" data-aos-duration="700" data-aos-delay="240" data-aos-once="true" data-aos-easing="ease-out-cubic" @endif class="mt-8 flex flex-col gap-3 sm:flex-row sm:flex-wrap sm:gap-4 lg:mt-9">
                            @if($buttonPrimary)
                                <a href="{{ $buttonPrimary['url'] }}" target="{{ $buttonPrimary['target'] ?: '_self' }}" @if(($buttonPrimary['target'] ?? '') === '_blank') rel="noopener noreferrer" @endif class="group relative isolate inline-flex min-h-[54px] w-full items-center justify-center gap-4 overflow-hidden rounded-[8px] bg-brand px-7 text-[14px] font-bold text-primary-dark transition-colors duration-300 before:absolute before:inset-0 before:z-0 before:origin-left before:scale-x-0 before:bg-brand-active before:content-[''] before:transition-transform before:duration-300 before:ease-out hover:text-white hover:before:scale-x-100 sm:w-auto sm:min-w-[220px] lg:min-h-[56px] lg:px-8 lg:text-[15px]"><span class="relative z-10">{{ $buttonPrimary['title'] }}</span><svg class="relative z-10 h-4 w-4 transition-transform duration-300 group-hover:translate-x-1" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M5 12H19M13 6L19 12L13 18" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" /></svg></a>
                            @endif
                            @if($buttonSecondary)
                                <a href="{{ $buttonSecondary['url'] }}" target="{{ $buttonSecondary['target'] ?: '_self' }}" @if(($buttonSecondary['target'] ?? '') === '_blank') rel="noopener noreferrer" @endif class="group relative isolate inline-flex min-h-[54px] w-full items-center justify-center gap-4 overflow-hidden rounded-[8px] border border-white/40 bg-white/[0.03] px-7 text-[14px] font-bold text-white backdrop-blur-sm transition-colors duration-300 before:absolute before:inset-0 before:z-0 before:origin-left before:scale-x-0 before:bg-white/10 before:content-[''] before:transition-transform before:duration-300 before:ease-out hover:border-brand hover:before:scale-x-100 sm:w-auto sm:min-w-[240px] lg:min-h-[56px] lg:px-8 lg:text-[15px]"><span class="relative z-10">{{ $buttonSecondary['title'] }}</span><svg class="relative z-10 h-4 w-4 transition-transform duration-300 group-hover:translate-x-1" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M5 12H19M13 6L19 12L13 18" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" /></svg></a>
                            @endif
                        </div>
                    @endif

                    @if($bottomDescription)
                        <p @if(!$isEditor) data-aos="fade-up" data-aos-duration="700" data-aos-delay="320" data-aos-once="true" data-aos-easing="ease-out-cubic" @endif class="mt-5 max-w-[720px] text-[12px] font-medium leading-[1.6] text-white/65 sm:text-[13px] lg:mt-6 lg:text-[14px]">{!! nl2br(esc_html($bottomDescription)) !!}</p>
                    @endif
                </div>
            </div>
        </x-ui.container>

        <span id="{{ $scrollTargetId }}" class="pointer-events-none absolute bottom-0 left-0 h-px w-px" aria-hidden="true"></span>

        @if(!$isEditor)
            <a
                href="#{{ $scrollTargetId }}"
                @click.prevent="scrollBelowHero()"
                class="home-hero-scroll group absolute bottom-5 left-1/2 z-30 flex -translate-x-1/2 flex-col items-center gap-2 text-white sm:bottom-8"
                aria-label="{{ esc_attr__('Przewiń do następnej sekcji', 'appandsite') }}"
            >
                <span class="text-[10px] font-bold uppercase tracking-[0.24em] text-white/90 transition-colors duration-300 group-hover:text-brand sm:text-[11px]">
                    SCROLL
                </span>

                <span class="home-hero-scroll-ring flex h-[44px] w-[44px] items-center justify-center rounded-full border border-brand/75 bg-primary-dark/55 text-brand backdrop-blur-sm transition-colors duration-300 group-hover:bg-brand group-hover:text-primary-dark">
                    <svg class="home-hero-scroll-arrow h-[21px] w-[21px]" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                        <path d="M12 4V20M5 13L12 20L19 13" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                </span>
            </a>
        @endif

    </section>
</x-block-helper.block-wrapper>