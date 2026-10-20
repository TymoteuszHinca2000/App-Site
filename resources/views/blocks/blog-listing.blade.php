
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

    $titleHtml = preg_replace('/<\/p>\s*<p[^>]*>/i', '<br>', $titleHtml);
    $titleHtml = preg_replace('/<\/?p\b[^>]*>/i', '', $titleHtml);
    $titleHtml = preg_replace('/<(strong|b)>/i', '<span class="text-brand">', $titleHtml);
    $titleHtml = preg_replace('/<\/(strong|b)>/i', '</span>', $titleHtml);
    $titleHtml = trim($titleHtml);

    $descriptionHtml = wp_kses_post($description ?? '');
    $activeCategory = $selectedCategory ?: '';

    $pageNumbers = array_values(array_unique(array_filter([
        1,
        $currentPage - 2,
        $currentPage - 1,
        $currentPage,
        $currentPage + 1,
        $currentPage + 2,
        $maxPages,
    ], fn ($number) => $number >= 1 && $number <= $maxPages)));

    sort($pageNumbers);
@endphp

<x-block-helper.block-wrapper :block="$block" :attributes="$attributes">
    <section class="relative bg-white" @if(!$isEditor) x-data="alpBlogListing()" @endif>
        <x-ui.container>
            <div class="grid gap-5 lg:grid-cols-[minmax(0,1.2fr)_minmax(300px,0.8fr)] lg:items-start lg:gap-12">
                <div>
                    @if(!empty($prefix))
                        <div @if(!$isEditor) data-aos="fade-right" data-aos-duration="500" data-aos-once="true" data-aos-easing="ease-out-cubic" @endif class="mb-5 flex items-center gap-3 text-[11px] font-semibold uppercase tracking-[0.14em] text-muted sm:text-[12px]">
                            <span class="flex h-[24px] w-[24px] shrink-0 items-center justify-center rounded-[6px] border border-brand/60 bg-brand/10 text-brand">
                                <svg class="h-[13px] w-[13px]" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                    <path d="M12 3L13.6 8.4L19 10L13.6 11.6L12 17L10.4 11.6L5 10L10.4 8.4L12 3Z" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round" />
                                    <path d="M18.5 15.5L19.2 17.8L21.5 18.5L19.2 19.2L18.5 21.5L17.8 19.2L15.5 18.5Z" stroke="currentColor" stroke-width="1.5" stroke-linejoin="round" />
                                </svg>
                            </span>
                            <span>{{ $prefix }}</span>
                        </div>
                    @endif

                    @if($titleHtml !== '')
                        <h2 @if(!$isEditor) data-aos="fade-up" data-aos-duration="650" data-aos-delay="80" data-aos-once="true" data-aos-easing="ease-out-cubic" @endif>{!! $titleHtml !!}</h2>
                    @endif
                </div>

                @if($descriptionHtml !== '')
                    <div @if(!$isEditor) data-aos="fade-up" data-aos-duration="650" data-aos-delay="150" data-aos-once="true" data-aos-easing="ease-out-cubic" @endif class="wysiwyg lg:pt-3">
                        {!! $descriptionHtml !!}
                    </div>
                @endif
            </div>

            @if(!empty($features))
                <div class="mt-8 grid grid-cols-1 gap-3 sm:grid-cols-2 lg:mt-10 xl:grid-cols-4 xl:gap-4">
                    @foreach($features as $feature)
                        <div @if(!$isEditor) data-aos="fade-up" data-aos-duration="650" data-aos-delay="{{ ($loop->index % 4) * 80 }}" data-aos-once="true" data-aos-easing="ease-out-cubic" @endif>
                            <div class="group relative flex h-full min-w-0 items-center gap-3 rounded-[12px] border border-border bg-surface/50 p-4 shadow-card-soft transition-[border-color,background-color,box-shadow] duration-[900ms] ease-[cubic-bezier(0.22,1,0.36,1)] hover:border-brand/45 hover:bg-surface-alt hover:shadow-card-hover-soft motion-reduce:transition-none">
                                @if(!empty($feature['icon']['ID']))
                                    <div class="flex h-[58px] w-[58px] shrink-0 items-center justify-center rounded-full bg-brand/10 transition-transform duration-500 ease-out group-hover:scale-110 lg:h-[62px] lg:w-[62px]">
                                        {!! wp_get_attachment_image($feature['icon']['ID'], 'thumbnail', false, [
                                            'class' => 'h-[30px] w-[30px] object-contain transition-transform duration-700 ease-out group-hover:rotate-[8deg] lg:h-[32px] lg:w-[32px]',
                                            'alt' => '',
                                            'aria-hidden' => 'true',
                                            'loading' => 'lazy',
                                            'decoding' => 'async',
                                        ]) !!}
                                    </div>
                                @endif

                                <div class="flex min-w-0 flex-col items-start">
                                    @if(!empty($feature['title']))
                                        <div class="relative inline-block max-w-full pb-[5px]">
                                            <h6>{{ $feature['title'] }}</h6>
                                            <span class="absolute bottom-0 left-0 h-[2px] w-full origin-left scale-x-0 rounded-full bg-brand transition-transform duration-500 ease-out group-hover:scale-x-100"></span>
                                        </div>
                                    @endif

                                    @if(!empty($feature['description']))
                                        <p class="mt-1 mb-0 text-[13px] leading-[1.5] text-muted sm:text-[14px]">{{ $feature['description'] }}</p>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif

            @if(!empty($featuredPosts))
                <div @if(!$isEditor) data-aos="fade-up" data-aos-duration="700" data-aos-once="true" data-aos-easing="ease-out-cubic" @endif class="relative mt-9 lg:mt-12">
                    @if(count($featuredPosts) > 1)
                        <div class="swiper overflow-hidden rounded-[14px]" x-ref="featuredSlider">
                            <div class="swiper-wrapper">
                                @foreach($featuredPosts as $featuredPost)
                                    <div class="swiper-slide h-auto">
                                        @include('partials.blog-featured-card', ['featuredPost' => $featuredPost])
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <div class="absolute right-4 top-4 z-20 flex items-center gap-2 sm:right-6 sm:top-6">
                            <button type="button" x-ref="featuredPrev" aria-label="{{ esc_attr__('Poprzedni polecany artykuł', 'appandsite') }}" class="flex h-10 w-10 items-center justify-center rounded-full border border-white/35 bg-primary-dark/70 text-white backdrop-blur-sm transition-[border-color,background-color,color] duration-500 ease-out hover:border-brand hover:bg-brand hover:text-primary focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-brand">
                                <svg class="h-[18px] w-[18px]" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                    <path d="M19 12H5M11 6L5 12L11 18" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                            </button>

                            <button type="button" x-ref="featuredNext" aria-label="{{ esc_attr__('Następny polecany artykuł', 'appandsite') }}" class="flex h-10 w-10 items-center justify-center rounded-full border border-white/35 bg-primary-dark/70 text-white backdrop-blur-sm transition-[border-color,background-color,color] duration-500 ease-out hover:border-brand hover:bg-brand hover:text-primary focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-brand">
                                <svg class="h-[18px] w-[18px]" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                    <path d="M5 12H19M13 6L19 12L13 18" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                            </button>
                        </div>
                    @else
                        @php
                            $featuredPost = $featuredPosts[0];
                        @endphp

                        @include('partials.blog-featured-card', ['featuredPost' => $featuredPost])
                    @endif
                </div>
            @endif

            <div class="mt-10 border-b border-border pb-5 sm:mt-12">
                <div class="flex items-center gap-3">
                    <span class="hidden shrink-0 items-center gap-2 text-[12px] font-semibold text-primary md:inline-flex">
                        <svg class="h-[17px] w-[17px] text-brand" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                            <path d="M4 7H20M7 12H17M10 17H14" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" />
                        </svg>
                        {{ __('Kategorie:', 'appandsite') }}
                    </span>

                    <label for="blog-category-{{ $pageId }}" class="sr-only">{{ __('Wybierz kategorię bloga', 'appandsite') }}</label>
                    <select id="blog-category-{{ $pageId }}" class="w-full rounded-[8px] border border-border bg-white px-4 text-primary md:hidden" @if(!$isEditor) x-on:change="changeCategory($event.target.value)" @endif>
                        <option value="{{ esc_url($allCategoriesUrl) }}" @selected($activeCategory === '')>{{ $allCategoriesText }}</option>
                        @foreach($categories as $category)
                            <option value="{{ esc_url($category['url']) }}" @selected($activeCategory === $category['slug'])>{{ $category['name'] }}</option>
                        @endforeach
                    </select>

                    <div class="hidden min-w-0 flex-1 items-center gap-2 md:flex">
                        <button type="button" @if(!$isEditor) x-on:click="scrollCategories(-1)" x-bind:disabled="!canScrollPrev" @else disabled @endif aria-label="{{ esc_attr__('Przewiń kategorie w lewo', 'appandsite') }}" class="flex h-10 w-10 shrink-0 items-center justify-center rounded-[8px] border border-border bg-white text-primary transition-[border-color,background-color,color,opacity] duration-300 ease-out hover:border-brand hover:bg-brand/10 hover:text-brand-active disabled:cursor-default disabled:opacity-30 disabled:hover:border-border disabled:hover:bg-white">
                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                <path d="M19 12H5M11 6L5 12L11 18" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                        </button>

                        <div x-ref="categoryTrack" class="relative min-w-0 flex-1 overflow-x-auto overscroll-x-contain scroll-smooth [scrollbar-width:none] [&::-webkit-scrollbar]:hidden">
                            <div class="flex w-max min-w-full items-center gap-2">
                                <a href="{{ esc_url($allCategoriesUrl) }}" @if($activeCategory === '') aria-current="page" x-ref="activeCategory" @endif class="inline-flex min-h-10 shrink-0 items-center justify-center whitespace-nowrap rounded-[8px] border px-5 text-[13px] font-semibold transition-[border-color,background-color,color] duration-300 ease-out {{ $activeCategory === '' ? 'border-brand bg-brand text-primary' : 'border-border bg-surface text-primary hover:border-brand/50 hover:bg-brand/10 hover:text-brand-active' }}">
                                    {{ $allCategoriesText }}
                                </a>

                                @foreach($categories as $category)
                                    <a href="{{ esc_url($category['url']) }}" @if($activeCategory === $category['slug']) aria-current="page" x-ref="activeCategory" @endif class="inline-flex min-h-10 shrink-0 items-center justify-center whitespace-nowrap rounded-[8px] border px-5 text-[13px] font-semibold transition-[border-color,background-color,color] duration-300 ease-out {{ $activeCategory === $category['slug'] ? 'border-brand bg-brand text-primary' : 'border-border bg-surface text-primary hover:border-brand/50 hover:bg-brand/10 hover:text-brand-active' }}">
                                        {{ $category['name'] }}
                                    </a>
                                @endforeach
                            </div>
                        </div>

                        <button type="button" @if(!$isEditor) x-on:click="scrollCategories(1)" x-bind:disabled="!canScrollNext" @else disabled @endif aria-label="{{ esc_attr__('Przewiń kategorie w prawo', 'appandsite') }}" class="flex h-10 w-10 shrink-0 items-center justify-center rounded-[8px] border border-border bg-white text-primary transition-[border-color,background-color,color,opacity] duration-300 ease-out hover:border-brand hover:bg-brand/10 hover:text-brand-active disabled:cursor-default disabled:opacity-30 disabled:hover:border-border disabled:hover:bg-white">
                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                <path d="M5 12H19M13 6L19 12L13 18" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                        </button>
                    </div>
                </div>
            </div>

            @if(!empty($blogPosts))
                <div class="mt-7 grid grid-cols-1 gap-5 sm:grid-cols-2 xl:grid-cols-3 xl:gap-6">
                    @foreach($blogPosts as $post)
                        <div @if(!$isEditor) data-aos="fade-up" data-aos-duration="650" data-aos-delay="{{ ($loop->index % 3) * 80 }}" data-aos-once="true" data-aos-easing="ease-out-cubic" @endif class="min-w-0">
                            <article class="group relative isolate flex h-full min-w-0 flex-col rounded-[12px] border border-border bg-white shadow-card-soft transition-[border-color,box-shadow] duration-[900ms] ease-[cubic-bezier(0.22,1,0.36,1)] hover:border-brand/45 hover:shadow-card-hover-soft motion-reduce:transition-none">
                                <a href="{{ esc_url($post['url']) }}" aria-label="{{ esc_attr($post['title']) }}" class="relative block aspect-[16/9] overflow-hidden rounded-t-[11px] bg-primary-dark focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-brand">
                                    @if(!empty($post['image_id']))
                                        {!! wp_get_attachment_image($post['image_id'], 'large', false, [
                                            'class' => 'h-full w-full object-cover transition-transform duration-[1100ms] ease-[cubic-bezier(0.22,1,0.36,1)] group-hover:scale-[1.055] motion-reduce:transition-none',
                                            'alt' => $post['title'],
                                            'loading' => 'lazy',
                                            'decoding' => 'async',
                                        ]) !!}
                                    @endif
                                    <span class="pointer-events-none absolute inset-0 bg-gradient-to-t from-primary-dark/30 to-transparent"></span>
                                </a>

                                <div class="relative flex min-w-0 flex-1 flex-col p-5 sm:p-6">
                                    <div class="mb-4 flex flex-wrap items-center justify-between gap-2 text-[11px] text-muted sm:text-[12px]">
                                        @if(!empty($post['category']))
                                            <a href="{{ esc_url(\App\appandsite_blog_page_url($pageId, 1, $post['category_slug'])) }}" class="relative z-20 inline-flex items-center rounded-[6px] border border-brand/30 bg-brand/10 px-2.5 py-1 font-semibold text-brand-active transition-colors duration-300 hover:border-brand hover:bg-brand/15 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-brand">
                                                {{ $post['category'] }}
                                            </a>
                                        @endif
                                        <span>{{ $post['date'] }}</span>
                                    </div>

                                    <div class="relative z-20 mb-2 w-full min-w-0 pb-[5px]">
                                        <a href="{{ esc_url($post['url']) }}" class="relative z-20 block w-full cursor-pointer pointer-events-auto text-primary no-underline hover:text-primary hover:no-underline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-brand">
                                            <h5 class="m-0 w-full max-w-none text-wrap text-primary">
                                                {{ html_entity_decode($post['title'], ENT_QUOTES | ENT_HTML5, 'UTF-8') }}
                                            </h5>
                                        </a>
                                        <span aria-hidden="true" class="pointer-events-none absolute bottom-0 left-0 h-[2px] w-full origin-left scale-x-0 rounded-full bg-brand transition-transform duration-500 ease-out group-hover:scale-x-100"></span>
                                    </div>

                                    @if(!empty($post['excerpt']))
                                        <p class="mb-4 text-[13px] leading-[1.7] text-muted sm:text-[14px]">{{ $post['excerpt'] }}</p>
                                    @endif

                                    <div class="mt-auto flex flex-wrap items-end justify-between gap-x-3 gap-y-2 border-t border-border/70 pt-4">
                                        <a href="{{ esc_url($post['url']) }}" class="relative z-20 inline-flex w-fit cursor-pointer items-center gap-3 text-brand-active no-underline hover:text-brand-active hover:no-underline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-brand">
                                            <span class="border-b border-current pb-[2px] text-[13px] font-semibold sm:text-[14px]">{{ $postButtonText }}</span>
                                            <svg class="h-4 w-4 shrink-0" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                                <path d="M5 12H19M13 6L19 12L13 18" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                            </svg>
                                        </a>

                                        <span class="inline-flex shrink-0 items-center gap-1.5 whitespace-nowrap text-[11px] text-muted sm:text-[12px]">
                                            <svg class="h-[14px] w-[14px] text-brand-active" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                                <circle cx="12" cy="12" r="9" stroke="currentColor" stroke-width="1.8" />
                                                <path d="M12 7V12L15.5 14" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" />
                                            </svg>
                                            {{ $post['reading_time'] }} {{ __('min czytania', 'appandsite') }}
                                        </span>
                                    </div>
                                </div>
                            </article>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="mt-8 rounded-[12px] border border-border bg-surface p-8 text-center text-muted">
                    {{ __('Brak artykułów w tej kategorii.', 'appandsite') }}
                </div>
            @endif

            @if($maxPages > 1)
                <nav class="mt-10 flex flex-wrap items-center justify-center gap-2 sm:mt-12" aria-label="{{ esc_attr__('Paginacja bloga', 'appandsite') }}">
                    @if($currentPage > 1)
                        <a href="{{ esc_url(\App\appandsite_blog_page_url($pageId, $currentPage - 1, $activeCategory)) }}" rel="prev" aria-label="{{ esc_attr__('Poprzednia strona', 'appandsite') }}" class="flex h-10 w-10 items-center justify-center rounded-[8px] border border-border bg-white text-primary transition-[border-color,background-color,color] duration-300 hover:border-brand hover:bg-brand/10 hover:text-brand-active">
                            <svg class="h-[18px] w-[18px]" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                <path d="M19 12H5M11 6L5 12L11 18" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                        </a>
                    @endif

                    @php
                        $lastPage = 0;
                    @endphp

                    @foreach($pageNumbers as $pageNumber)
                        @if($lastPage && $pageNumber > $lastPage + 1)
                            <span class="px-1 text-muted" aria-hidden="true">…</span>
                        @endif

                        <a href="{{ esc_url(\App\appandsite_blog_page_url($pageId, $pageNumber, $activeCategory)) }}" @if($pageNumber === $currentPage) aria-current="page" @endif class="flex h-10 min-w-10 items-center justify-center rounded-[8px] border px-2 text-[13px] font-semibold transition-[border-color,background-color,color] duration-300 {{ $pageNumber === $currentPage ? 'border-brand bg-brand text-primary' : 'border-border bg-white text-primary hover:border-brand hover:bg-brand/10 hover:text-brand-active' }}">
                            {{ $pageNumber }}
                        </a>

                        @php
                            $lastPage = $pageNumber;
                        @endphp
                    @endforeach

                    @if($currentPage < $maxPages)
                        <a href="{{ esc_url(\App\appandsite_blog_page_url($pageId, $currentPage + 1, $activeCategory)) }}" rel="next" aria-label="{{ esc_attr__('Następna strona', 'appandsite') }}" class="flex h-10 w-10 items-center justify-center rounded-[8px] border border-border bg-white text-primary transition-[border-color,background-color,color] duration-300 hover:border-brand hover:bg-brand/10 hover:text-brand-active">
                            <svg class="h-[18px] w-[18px]" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                <path d="M5 12H19M13 6L19 12L13 18" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                        </a>
                    @endif
                </nav>
            @endif
        </x-ui.container>
    </section>
</x-block-helper.block-wrapper>
