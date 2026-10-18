
@php
    $isEditor = is_admin();
    $postId = (int) (get_the_ID() ?: get_queried_object_id());
    $postTitle = html_entity_decode((string) get_the_title($postId), ENT_QUOTES | ENT_HTML5, 'UTF-8');
    $rawTitle = html_entity_decode((string) ($title ?? ''), ENT_QUOTES | ENT_HTML5, 'UTF-8');

    $titleHtml = wp_kses($rawTitle, [
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

    if ($titleHtml === '') {
        $titleHtml = esc_html($postTitle);
    }

    $galleryItems = [];

    foreach ($backgroundGallery ?? [] as $galleryImage) {
        $imageId = is_array($galleryImage) ? ($galleryImage['ID'] ?? null) : $galleryImage;

        if (!$imageId) {
            continue;
        }

        $imageUrl = wp_get_attachment_image_url($imageId, 'full');

        if (!$imageUrl) {
            continue;
        }

        $galleryItems[] = [
            'id' => (int) $imageId,
            'url' => $imageUrl,
        ];
    }

    if (empty($galleryItems) && has_post_thumbnail($postId)) {
        $imageId = get_post_thumbnail_id($postId);

        $galleryItems[] = [
            'id' => (int) $imageId,
            'url' => wp_get_attachment_image_url($imageId, 'full'),
        ];
    }

    $videoUrl = is_array($backgroundVideo ?? null) ? ($backgroundVideo['url'] ?? '') : '';
    $videoMime = is_array($backgroundVideo ?? null) ? ($backgroundVideo['mime_type'] ?? '') : '';
    $posterUrl = is_array($backgroundVideoPoster ?? null) ? ($backgroundVideoPoster['url'] ?? '') : '';

    if (!$posterUrl && has_post_thumbnail($postId)) {
        $posterUrl = get_the_post_thumbnail_url($postId, 'full');
    }

    $mediaType = ($backgroundType ?? 'gallery') === 'video' && $videoUrl ? 'video' : 'gallery';

    $authorId = (int) get_post_field('post_author', $postId);
    $firstName = trim((string) get_user_meta($authorId, 'first_name', true));
    $lastName = trim((string) get_user_meta($authorId, 'last_name', true));
    $authorName = trim($firstName . ' ' . $lastName);

    if ($authorName === '') {
        $authorName = get_the_author_meta('display_name', $authorId);
    }

    $readingTime = (int) get_field('blog_reading_time', $postId);

    if ($readingTime <= 0) {
        $postContent = wp_strip_all_tags(strip_shortcodes((string) get_post_field('post_content', $postId)));
        preg_match_all('/[\p{L}\p{N}]+/u', $postContent, $matches);
        $readingTime = max(1, (int) ceil(count($matches[0] ?? []) / 200));
    }

    $publishedDate = get_the_date('j F Y', $postId);
    $publishedDateIso = get_the_date('c', $postId);

    $language = function_exists('pll_get_post_language') ? pll_get_post_language($postId, 'slug') : null;
    $homeUrl = function_exists('pll_home_url') ? pll_home_url($language ?: null) : home_url('/');

    $blogPage = get_page_by_path('blog', OBJECT, 'page');
    $blogPageId = $blogPage ? (int) $blogPage->ID : 0;

    if ($blogPageId && $language && function_exists('pll_get_post')) {
        $translatedId = pll_get_post($blogPageId, $language);

        if ($translatedId) {
            $blogPageId = (int) $translatedId;
        }
    }

    $blogUrl = $blogPageId ? get_permalink($blogPageId) : '';
    $blogTitle = $blogPageId
        ? html_entity_decode((string) get_the_title($blogPageId), ENT_QUOTES | ENT_HTML5, 'UTF-8')
        : __('Blog', 'appandsite');

    $blogCategory = null;
    $blogCategoryUrl = '';
    $blogTerms = get_the_terms($postId, 'blog_category');

    if (!is_wp_error($blogTerms) && !empty($blogTerms)) {
        $blogCategory = reset($blogTerms);

        if ($blogPageId && function_exists('App\\appandsite_blog_page_url')) {
            $blogCategoryUrl = \App\appandsite_blog_page_url($blogPageId, 1, $blogCategory->slug);
        }
    }

    $blogCategoryName = $blogCategory
        ? html_entity_decode((string) $blogCategory->name, ENT_QUOTES | ENT_HTML5, 'UTF-8')
        : '';
@endphp

<x-block-helper.block-wrapper :block="$block" :attributes="$attributes">
    <section @if(!$isEditor && $mediaType === 'gallery' && !empty($galleryItems)) x-data="alpBanner(@js($galleryItems), 6500)" @endif class="relative isolate overflow-hidden bg-primary-dark">
        <div class="absolute inset-0 z-0 overflow-hidden">
            @if($mediaType === 'video')
                <video class="h-full w-full object-cover" autoplay muted loop playsinline preload="metadata" @if($posterUrl) poster="{{ esc_url($posterUrl) }}" @endif aria-hidden="true">
                    <source src="{{ esc_url($videoUrl) }}" @if($videoMime) type="{{ esc_attr($videoMime) }}" @endif>
                </video>
            @elseif(!empty($galleryItems))
                @foreach($galleryItems as $index => $galleryItem)
                    @if($isEditor && $index > 0)
                        @continue
                    @endif

                    @if(!$isEditor)
                        <div @if($index > 0) x-cloak @endif x-show="activeIndex === {{ $index }}" x-transition:enter="transition-opacity duration-1000 ease-out" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="transition-opacity duration-1000 ease-out" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="absolute inset-0 overflow-hidden">
                            <div x-bind:class="activeIndex === {{ $index }} && motionActive ? (motionDirection ? 'scale-[1.085] translate-x-[1.4%] -translate-y-[0.7%]' : 'scale-[1.085] -translate-x-[1.4%] translate-y-[0.7%]') : 'scale-[1.02] translate-x-0 translate-y-0'" class="absolute -inset-[4%] transform-gpu transition-transform duration-[7600ms] ease-in-out will-change-transform motion-reduce:transform-none motion-reduce:transition-none">
                                {!! wp_get_attachment_image($galleryItem['id'], 'full', false, [
                                    'class' => 'h-full w-full object-cover',
                                    'alt' => '',
                                    'aria-hidden' => 'true',
                                    'loading' => $index === 0 ? 'eager' : 'lazy',
                                    'fetchpriority' => $index === 0 ? 'high' : 'auto',
                                    'decoding' => 'async',
                                ]) !!}
                            </div>
                        </div>
                    @else
                        <div class="absolute inset-0">
                            {!! wp_get_attachment_image($galleryItem['id'], 'full', false, [
                                'class' => 'h-full w-full object-cover',
                                'alt' => '',
                                'aria-hidden' => 'true',
                                'loading' => 'eager',
                                'decoding' => 'async',
                            ]) !!}
                        </div>
                    @endif
                @endforeach
            @endif
        </div>

        <div class="pointer-events-none absolute inset-0 z-[1] banner-shade-horizontal"></div>
        <div class="pointer-events-none absolute inset-0 z-[1] banner-shade-vertical"></div>

        <x-ui.container>
            <div class="relative z-10 flex flex-col justify-center pb-12 pt-[120px] sm:pb-14 sm:pt-[130px] lg:pb-16 lg:pt-[145px] xl:pb-[72px] xl:pt-[155px]">
                <nav @if(!$isEditor) data-aos="fade-right" data-aos-duration="500" data-aos-once="true" data-aos-easing="ease-out-cubic" @endif aria-label="{{ esc_attr__('Ścieżka nawigacji', 'appandsite') }}" class="mb-5 sm:mb-6">
                    <ol class="flex flex-wrap items-center gap-x-2 gap-y-1 text-[12px] font-medium text-white/65 sm:text-[13px]">
                        <li class="flex items-center">
                            <a href="{{ esc_url($homeUrl) }}" class="flex items-center gap-2 transition-colors duration-300 hover:text-brand">
                                <svg class="h-[15px] w-[15px] shrink-0 text-brand" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                    <path d="M3 10.5L12 3L21 10.5V20C21 20.5523 20.5523 21 20 21H15V14H9V21H4C3.44772 21 3 20.5523 3 20V10.5Z" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round" />
                                </svg>
                                <span>{{ __('Strona główna', 'appandsite') }}</span>
                            </a>
                        </li>

                        @if($blogUrl)
                            <li aria-hidden="true" class="text-white/35">/</li>
                            <li>
                                <a href="{{ esc_url($blogUrl) }}" class="transition-colors duration-300 hover:text-brand">
                                    {{ $blogTitle }}
                                </a>
                            </li>
                        @endif

                        @if($blogCategoryName !== '')
                            <li aria-hidden="true" class="text-white/35">/</li>
                            <li>
                                @if($blogCategoryUrl !== '')
                                    <a href="{{ esc_url($blogCategoryUrl) }}" class="transition-colors duration-300 hover:text-brand">
                                        {{ $blogCategoryName }}
                                    </a>
                                @else
                                    <span>{{ $blogCategoryName }}</span>
                                @endif
                            </li>
                        @endif

                        <li aria-hidden="true" class="text-white/35">/</li>
                        <li>
                            <span aria-current="page" class="text-white/90">
                                {{ $postTitle }}
                            </span>
                        </li>
                    </ol>
                </nav>

                <div class="w-full max-w-[1100px]">
                    @if($titleHtml !== '')
                        <h1 @if(!$isEditor) data-aos="fade-up" data-aos-duration="700" data-aos-delay="80" data-aos-once="true" data-aos-easing="ease-out-cubic" @endif class="max-w-[1050px] text-[40px] font-extrabold leading-[1.02] tracking-[-0.04em] text-white sm:text-[46px] md:text-[52px] lg:text-[58px] xl:text-[64px]">
                            {!! $titleHtml !!}
                        </h1>
                    @endif

                    <div @if(!$isEditor) data-aos="fade-up" data-aos-duration="700" data-aos-delay="160" data-aos-once="true" data-aos-easing="ease-out-cubic" @endif class="mt-7 flex flex-wrap items-center gap-x-5 gap-y-4 sm:mt-8 lg:mt-9">
                        @if($authorId)
                            <div class="flex min-w-0 items-center gap-3">
                                <div class="h-[48px] w-[48px] shrink-0 overflow-hidden rounded-full border-2 border-white/30 bg-primary-light sm:h-[54px] sm:w-[54px]">
                                    {!! get_avatar($authorId, 108, '', $authorName, [
                                        'class' => 'h-full w-full object-cover',
                                        'loading' => 'lazy',
                                        'extra_attr' => 'decoding="async"',
                                    ]) !!}
                                </div>
                                <div class="flex min-w-0 flex-col gap-0.5">
                                    <span class="text-[11px] font-medium text-white/60 sm:text-[12px]">
                                        {{ __('Autor artykułu', 'appandsite') }}
                                    </span>
                                    <span class="text-[13px] font-bold leading-[1.4] text-white sm:text-[14px]">
                                        {{ $authorName }}
                                    </span>
                                </div>
                            </div>
                        @endif

                        <span class="hidden h-9 w-px bg-white/20 sm:block"></span>

                        <div class="flex flex-wrap items-center gap-x-5 gap-y-3 text-[12px] font-medium text-white/80 sm:text-[13px]">
                            <div class="flex items-center gap-2">
                                <svg class="h-[17px] w-[17px] shrink-0 text-brand" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                    <rect x="3" y="5" width="18" height="16" rx="2" stroke="currentColor" stroke-width="1.8" />
                                    <path d="M7 3V7M17 3V7M3 10H21" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" />
                                </svg>
                                <time datetime="{{ esc_attr($publishedDateIso) }}">
                                    {{ $publishedDate }}
                                </time>
                            </div>

                            <div class="flex items-center gap-2">
                                <svg class="h-[17px] w-[17px] shrink-0 text-brand" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                    <circle cx="12" cy="12" r="9" stroke="currentColor" stroke-width="1.8" />
                                    <path d="M12 7V12L15.5 14" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" />
                                </svg>
                                <span>
                                    {{ $readingTime }} {{ __('min czytania', 'appandsite') }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </x-ui.container>
    </section>
</x-block-helper.block-wrapper>
