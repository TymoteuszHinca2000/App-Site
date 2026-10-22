@php
    $isEditor = is_admin();
    $projectId = (int) (get_the_ID() ?: get_queried_object_id());
    $projectTitle = $projectId ? html_entity_decode((string) get_the_title($projectId), ENT_QUOTES | ENT_HTML5, 'UTF-8') : '';
    $projectExcerpt = $projectId ? trim((string) get_the_excerpt($projectId)) : '';

    $titleValue = trim((string) ($title ?? ''));
    $titleHtml = $titleValue !== '' ? $titleValue : esc_html($projectTitle);
    $titleHtml = wp_kses(html_entity_decode($titleHtml, ENT_QUOTES | ENT_HTML5, 'UTF-8'), [
        'strong' => [], 'b' => [], 'em' => [], 'i' => [], 'br' => [], 'p' => [],
    ]);
    $titleHtml = preg_replace('/<\/p>\s*<p[^>]*>/i', '<br>', $titleHtml);
    $titleHtml = preg_replace('/<\/?p\b[^>]*>/i', '', $titleHtml);
    $titleHtml = preg_replace('/<(strong|b)>/i', '<span class="text-brand">', $titleHtml);
    $titleHtml = preg_replace('/<\/(strong|b)>/i', '</span>', $titleHtml);
    $titleHtml = trim($titleHtml);

    $descriptionValue = trim((string) ($description ?? ''));
    $descriptionHtml = $descriptionValue !== '' ? wp_kses_post($descriptionValue) : ($projectExcerpt !== '' ? wpautop(esc_html($projectExcerpt)) : '');

    $currentLanguage = function_exists('pll_current_language') ? pll_current_language('slug') : null;
    $homeUrl = function_exists('pll_home_url') ? pll_home_url() : home_url('/');
    $projectsPage = get_page_by_path('projekty', OBJECT, 'page');

    if(!$projectsPage) {
        $projectsPage = get_page_by_path('projects', OBJECT, 'page');
    }

    if($projectsPage && $currentLanguage && function_exists('pll_get_post')) {
        $translatedPageId = pll_get_post((int) $projectsPage->ID, $currentLanguage);
        if($translatedPageId) $projectsPage = get_post((int) $translatedPageId);
    }

    $projectsPageId = $projectsPage ? (int) $projectsPage->ID : 0;
    $projectsUrl = $projectsPageId ? get_permalink($projectsPageId) : '';
    $projectsLabel = $projectsPageId ? get_the_title($projectsPageId) : __('Projekty', 'appandsite');

    $projectCategories = $projectId ? get_the_terms($projectId, 'project_category') : [];
    $projectCategories = !is_wp_error($projectCategories) && !empty($projectCategories) ? $projectCategories : [];
    $mainCategory = !empty($projectCategories) ? reset($projectCategories) : null;
    $categoryUrl = '';

    if($mainCategory) {
        if($projectsPageId && function_exists('App\\appandsite_project_page_url')) {
            $categoryUrl = \App\appandsite_project_page_url($projectsPageId, 1, $mainCategory->slug);
        } else {
            $termUrl = get_term_link($mainCategory);
            $categoryUrl = !is_wp_error($termUrl) ? $termUrl : '';
        }
    }

    $primaryButton = $buttonPrimary ?? null;
    if((!is_array($primaryButton) || empty($primaryButton['url'])) && $projectId) {
        $websiteUrl = (string) get_field('project_url', $projectId);
        if($websiteUrl !== '') {
            $primaryButton = [
                'url' => $websiteUrl,
                'title' => get_field('project_url_label', $projectId) ?: __('Odwiedź witrynę', 'appandsite'),
                'target' => '_blank',
            ];
        }
    }

    $siteHost = strtolower((string) wp_parse_url(home_url('/'), PHP_URL_HOST));
    $siteHost = preg_replace('/^www\./', '', $siteHost);
    $buttons = [];

    foreach(['primary' => $primaryButton, 'secondary' => $buttonSecondary ?? null] as $variant => $buttonData) {
        if(!is_array($buttonData) || empty($buttonData['url']) || empty($buttonData['title'])) continue;
        $buttonHost = strtolower((string) wp_parse_url($buttonData['url'], PHP_URL_HOST));
        $buttonHost = preg_replace('/^www\./', '', $buttonHost);
        $isExternal = $buttonHost !== '' && $buttonHost !== $siteHost;
        $buttons[] = [
            'variant' => $variant,
            'url' => $buttonData['url'],
            'title' => $buttonData['title'],
            'target' => $isExternal ? '_blank' : ($buttonData['target'] ?? '_self'),
            'external' => $isExternal,
        ];
    }

    $galleryItems = [];
    if(($backgroundType ?? 'featured') === 'gallery') {
        foreach($backgroundGallery ?? [] as $galleryImage) {
            $imageId = is_array($galleryImage) ? (int) ($galleryImage['ID'] ?? 0) : (int) $galleryImage;
            if(!$imageId || !wp_attachment_is_image($imageId)) continue;
            $galleryItems[] = ['id' => $imageId];
        }
    }

    $videoUrl = is_array($backgroundVideo ?? null) ? ($backgroundVideo['url'] ?? '') : '';
    $videoMime = is_array($backgroundVideo ?? null) ? ($backgroundVideo['mime_type'] ?? '') : '';
    $featuredImageId = $projectId ? (int) get_post_thumbnail_id($projectId) : 0;

    if(empty($galleryItems) && $featuredImageId) {
        $galleryItems[] = ['id' => $featuredImageId];
    }

    $posterUrl = is_array($backgroundVideoPoster ?? null) ? ($backgroundVideoPoster['url'] ?? '') : '';
    if($posterUrl === '' && $featuredImageId) $posterUrl = (string) wp_get_attachment_image_url($featuredImageId, 'full');
    $mediaType = ($backgroundType ?? 'featured') === 'video' && $videoUrl ? 'video' : 'gallery';
    $animateGallery = !$isEditor && $mediaType === 'gallery' && count($galleryItems) > 1;
@endphp

<x-block-helper.block-wrapper :block="$block" :attributes="$attributes">
    @if($isEditor || is_singular('project'))
        <section @if($animateGallery) x-data="alpBanner(@js($galleryItems), 6500)" @endif class="relative isolate overflow-hidden bg-primary-dark">
            <div class="absolute inset-0 -z-30 overflow-hidden">
                @if($mediaType === 'video')
                    <video class="h-full w-full object-cover" autoplay muted loop playsinline preload="metadata" @if($posterUrl) poster="{{ esc_url($posterUrl) }}" @endif aria-hidden="true">
                        <source src="{{ esc_url($videoUrl) }}" @if($videoMime) type="{{ esc_attr($videoMime) }}" @endif>
                    </video>
                @elseif(!empty($galleryItems))
                    @foreach($galleryItems as $index => $image)
                        @if(!$animateGallery && $index > 0)
                            @continue
                        @endif
                        @if($animateGallery)
                            <div @if($index > 0) x-cloak @endif x-show="activeIndex === {{ $index }}" x-transition:enter="transition-opacity duration-1000 ease-out" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="transition-opacity duration-1000 ease-out" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="absolute inset-0 overflow-hidden [&[x-cloak]]:hidden">
                                <div x-bind:class="activeIndex === {{ $index }} && motionActive ? (motionDirection ? 'scale-[1.085] translate-x-[1.4%] -translate-y-[0.7%]' : 'scale-[1.085] -translate-x-[1.4%] translate-y-[0.7%]') : 'scale-[1.02] translate-x-0 translate-y-0'" class="absolute -inset-[4%] transform-gpu transition-transform duration-[7600ms] ease-in-out will-change-transform motion-reduce:transform-none motion-reduce:transition-none">
                                    {!! wp_get_attachment_image($image['id'], 'full', false, ['class' => 'h-full w-full object-cover', 'alt' => '', 'aria-hidden' => 'true', 'loading' => $index === 0 ? 'eager' : 'lazy', 'decoding' => 'async']) !!}
                                </div>
                            </div>
                        @else
                            <div class="absolute inset-0">
                                {!! wp_get_attachment_image($image['id'], 'full', false, ['class' => 'h-full w-full object-cover', 'alt' => '', 'aria-hidden' => 'true', 'loading' => 'eager', 'decoding' => 'async']) !!}
                            </div>
                        @endif
                    @endforeach
                @endif
            </div>
            <div class="pointer-events-none absolute inset-0 -z-20 bg-[linear-gradient(90deg,rgba(2,23,36,0.99)_0%,rgba(2,28,43,0.96)_34%,rgba(2,28,43,0.76)_58%,rgba(2,28,43,0.42)_100%)]"></div>
            <div class="pointer-events-none absolute inset-0 -z-20 bg-[linear-gradient(0deg,rgba(2,23,36,0.78)_0%,rgba(2,23,36,0.12)_55%,rgba(2,23,36,0.22)_100%)]"></div>
            <x-ui.container>
                <div class="relative z-10 flex flex-col justify-center pb-12 pt-[120px] sm:pb-14 sm:pt-[130px] lg:pb-16 lg:pt-[145px] xl:pb-[72px] xl:pt-[155px]">
                    <nav @if(!$isEditor) data-aos="fade-right" data-aos-duration="500" data-aos-once="true" data-aos-easing="ease-out-cubic" @endif aria-label="{{ esc_attr__('Ścieżka nawigacji', 'appandsite') }}" class="mb-5 sm:mb-6">
                        <ol class="flex flex-wrap items-center gap-x-2 gap-y-1 text-[12px] font-medium text-white/65 sm:text-[13px]">
                            <li>
                                <a href="{{ esc_url($homeUrl) }}" class="inline-flex items-center gap-2 text-white/65 transition-colors duration-300 hover:text-brand">
                                    <svg class="h-[15px] w-[15px] shrink-0 text-brand" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M3 10.5L12 3L21 10.5V20C21 20.5523 20.5523 21 20 21H15V14H9V21H4C3.44772 21 3 20.5523 3 20V10.5Z" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round" /></svg>
                                    <span>{{ __('Strona główna', 'appandsite') }}</span>
                                </a>
                            </li>
                            <li aria-hidden="true" class="text-white/35">/</li>
                            <li>
                                @if($projectsUrl)
                                    <a href="{{ esc_url($projectsUrl) }}" class="text-white/65 transition-colors duration-300 hover:text-brand">{{ $projectsLabel }}</a>
                                @else
                                    <span>{{ $projectsLabel }}</span>
                                @endif
                            </li>
                            @if($mainCategory && $categoryUrl)
                                <li aria-hidden="true" class="text-white/35">/</li>
                                <li><a href="{{ esc_url($categoryUrl) }}" class="text-white/65 transition-colors duration-300 hover:text-brand">{{ $mainCategory->name }}</a></li>
                            @endif
                            <li aria-hidden="true" class="text-white/35">/</li>
                            <li aria-current="page" class="text-white/90">{{ $projectTitle }}</li>
                        </ol>
                    </nav>
                    <div class="w-full max-w-[920px]">
                        @if($titleHtml !== '')
                            <h1 @if(!$isEditor) data-aos="fade-up" data-aos-duration="700" data-aos-delay="80" data-aos-once="true" data-aos-easing="ease-out-cubic" @endif class="m-0 max-w-[920px] text-[40px] font-extrabold leading-[1.02] tracking-[-0.04em] text-white sm:text-[46px] md:text-[52px] lg:text-[58px] xl:text-[64px]">{!! $titleHtml !!}</h1>
                        @endif
                        @if($descriptionHtml !== '')
                            <div @if(!$isEditor) data-aos="fade-up" data-aos-duration="700" data-aos-delay="160" data-aos-once="true" data-aos-easing="ease-out-cubic" @endif class="mt-5 max-w-[780px] text-[15px] leading-[1.65] text-white/85 sm:text-[16px] md:text-[17px] lg:mt-6 lg:text-[18px] [&_a]:font-semibold [&_a]:text-brand [&_a]:underline [&_a]:underline-offset-4 [&_p]:m-0 [&_p]:text-inherit [&_p+p]:mt-4 [&_ul]:mt-4 [&_ol]:mt-4">
                                {!! $descriptionHtml !!}
                            </div>
                        @endif
                        @if(!empty($buttons))
                            <div @if(!$isEditor) data-aos="fade-up" data-aos-duration="650" data-aos-delay="220" data-aos-once="true" data-aos-easing="ease-out-cubic" @endif class="mt-7 flex flex-col flex-wrap items-stretch gap-3 sm:flex-row sm:items-center sm:gap-4 lg:mt-8">
                                @foreach($buttons as $button)
                                    <a href="{{ esc_url($button['url']) }}" target="{{ esc_attr($button['target']) }}" @if($button['target'] === '_blank') rel="noopener noreferrer" @endif class="group relative isolate inline-flex min-h-[50px] w-full items-center justify-center gap-4 overflow-hidden rounded-[8px] px-6 text-[14px] font-bold transition-[border-color,color] duration-500 ease-out before:absolute before:inset-0 before:z-0 before:origin-left before:scale-x-0 before:content-[''] before:transition-transform before:duration-500 before:ease-out hover:before:scale-x-100 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-brand motion-reduce:before:transition-none sm:w-auto {{ $button['variant'] === 'primary' ? 'bg-brand text-primary before:bg-brand-active hover:text-primary' : 'border border-white/30 bg-white/[0.02] text-white before:bg-white/10 hover:border-brand hover:text-white' }}">
                                        <span class="relative z-10">{{ $button['title'] }}</span>
                                        @if($button['external'])
                                            <svg class="relative z-10 h-[16px] w-[16px] shrink-0" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M13 5H19V11M19 5L10 14" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" /><path d="M19 14V18C19 18.5523 18.5523 19 18 19H6C5.44772 19 5 18.5523 5 18V6C5 5.44772 5.44772 5 6 5H10" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" /></svg>
                                        @else
                                            <svg class="relative z-10 h-[16px] w-[16px] shrink-0" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M5 12H19M13 6L19 12L13 18" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" /></svg>
                                        @endif
                                    </a>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>
            </x-ui.container>
        </section>
    @endif
</x-block-helper.block-wrapper>
