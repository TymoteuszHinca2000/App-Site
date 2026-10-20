
@php
    $isEditor = is_admin();
    $postId = (int) (get_the_ID() ?: get_queried_object_id());
    $contentSections = array_values(is_array($sections ?? null) ? $sections : []);
    $tocLinks = [];

    $formatTitle = static function ($value) {
        $html = wp_kses((string) $value, [
            'strong' => [], 'b' => [], 'em' => [], 'i' => [], 'br' => [], 'p' => [],
        ]);

        $html = preg_replace('/<\/p>\s*<p[^>]*>/i', '<br>', $html);
        $html = preg_replace('/<\/?p\b[^>]*>/i', '', $html);
        $html = preg_replace('/<(strong|b)>/i', '<span class="text-brand">', $html);
        $html = preg_replace('/<\/(strong|b)>/i', '</span>', $html);

        return trim($html);
    };

    foreach ($tocItems ?? [] as $tocItem) {
        $number = (int) ($tocItem['section_number'] ?? 0);
        $label = trim((string) ($tocItem['label'] ?? ''));

        if ($number < 1 || $number > count($contentSections) || $label === '') {
            continue;
        }

        $tocLinks[] = [
            'label' => $label,
            'id' => 'blog-section-' . $postId . '-' . $number,
        ];
    }

    if (empty($tocLinks)) {
        foreach ($contentSections as $index => $section) {
            $label = trim((string) ($section['toc_label'] ?? ''));

            if ($label === '' && in_array($section['acf_fc_layout'] ?? '', ['text_image', 'image_compare', 'table', 'text'], true)) {
                $label = trim(wp_strip_all_tags((string) ($section['title'] ?? '')));
            }

            if ($label === '') {
                continue;
            }

            $tocLinks[] = [
                'label' => $label,
                'id' => 'blog-section-' . $postId . '-' . ($index + 1),
            ];
        }
    }

    $language = function_exists('pll_current_language') ? pll_current_language('slug') : null;
    $postDate = (string) get_post_field('post_date', $postId);

    $relatedArgs = [
        'post_type' => 'blog',
        'post_status' => 'publish',
        'posts_per_page' => 1,
        'post__not_in' => [$postId],
        'suppress_filters' => false,
    ];

    if ($language) {
        $relatedArgs['lang'] = $language;
    }

    $previousPosts = get_posts(array_merge($relatedArgs, [
        'orderby' => 'date',
        'order' => 'DESC',
        'date_query' => [
            [
                'column' => 'post_date',
                'before' => $postDate,
                'inclusive' => false,
            ],
        ],
    ]));

    $nextPosts = get_posts(array_merge($relatedArgs, [
        'orderby' => 'date',
        'order' => 'ASC',
        'date_query' => [
            [
                'column' => 'post_date',
                'after' => $postDate,
                'inclusive' => false,
            ],
        ],
    ]));

    $previousPost = $previousPosts[0] ?? null;
    $nextPost = $nextPosts[0] ?? null;

    $previousTitle = $previousPost ? html_entity_decode(get_the_title($previousPost->ID), ENT_QUOTES | ENT_HTML5, 'UTF-8') : '';
    $nextTitle = $nextPost ? html_entity_decode(get_the_title($nextPost->ID), ENT_QUOTES | ENT_HTML5, 'UTF-8') : '';

    $blogPage = get_page_by_path('blog', OBJECT, 'page');

    if ($blogPage && $language && function_exists('pll_get_post')) {
        $translatedPage = pll_get_post($blogPage->ID, $language);

        if ($translatedPage) {
            $blogPage = get_post($translatedPage);
        }
    }

    $blogUrl = $blogPage ? get_permalink($blogPage->ID) : home_url('/');
@endphp

<x-block-helper.block-wrapper :block="$block" :attributes="$attributes">
    <section @if(!$isEditor) x-data="alpBlogContent()" @endif class="relative bg-white">
        <x-ui.container>
            <div class="grid grid-cols-1 gap-8 lg:grid-cols-[240px_minmax(0,1fr)] lg:items-start lg:gap-9 xl:grid-cols-[275px_minmax(0,1fr)] xl:gap-12 2xl:gap-16">
                <aside class="min-w-0 lg:sticky lg:top-[112px] lg:self-start lg:max-h-[calc(100dvh-128px)] lg:overflow-y-auto">
                    @if(!empty($tocLinks))
                        <details class="group/toc overflow-hidden rounded-[12px] border border-border bg-white shadow-card-soft lg:hidden">
                            <summary class="flex cursor-pointer list-none items-center justify-between gap-3 p-4 text-[14px] font-semibold text-primary marker:hidden">
                                <span class="flex items-center gap-2">
                                    <svg class="h-4 w-4 text-brand" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                        <path d="M5 6H19M5 12H19M5 18H19" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" />
                                    </svg>
                                    {{ $tocTitle }}
                                </span>
                                <svg class="h-4 w-4 text-muted transition-transform duration-300 group-open/toc:rotate-180" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                    <path d="M6 9L12 15L18 9" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                            </summary>
                            <nav class="border-t border-border px-4 pb-4 pt-3" aria-label="{{ esc_attr($tocTitle) }}">
                                <ol class="space-y-3">
                                    @foreach($tocLinks as $entry)
                                        <li class="flex items-start gap-3 text-[13px] leading-[1.55]">
                                            <span class="w-5 shrink-0 font-semibold text-brand-active">{{ $loop->iteration }}.</span>
                                            <a href="#{{ esc_attr($entry['id']) }}" class="text-muted transition-colors duration-300 hover:text-brand-active">{{ $entry['label'] }}</a>
                                        </li>
                                    @endforeach
                                </ol>
                            </nav>
                        </details>

                        <div class="hidden rounded-[12px] border border-border bg-white p-5 shadow-card-soft lg:block">
                            <div class="mb-5 flex items-center gap-2 text-[14px] font-semibold text-primary">
                                <svg class="h-4 w-4 text-brand" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                    <path d="M5 6H19M5 12H19M5 18H19" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" />
                                </svg>
                                {{ $tocTitle }}
                            </div>
                            <nav aria-label="{{ esc_attr($tocTitle) }}">
                                <ol class="space-y-3">
                                    @foreach($tocLinks as $entry)
                                        <li class="flex items-start gap-3 text-[13px] leading-[1.6]">
                                            <span class="w-5 shrink-0 font-semibold text-brand-active">{{ $loop->iteration }}.</span>
                                            <a href="#{{ esc_attr($entry['id']) }}" class="text-muted transition-colors duration-300 hover:text-brand-active">{{ $entry['label'] }}</a>
                                        </li>
                                    @endforeach
                                </ol>
                            </nav>
                        </div>
                    @endif

                    @if(!empty($sidebarCards))
                        <div class="mt-4 grid gap-4 sm:grid-cols-2 lg:grid-cols-1">
                            @foreach($sidebarCards as $card)
                                <div class="group relative overflow-hidden rounded-[12px] border border-border bg-surface p-5 shadow-card-soft transition-[border-color,box-shadow,background-color] duration-[900ms] ease-[cubic-bezier(0.22,1,0.36,1)] hover:border-brand/45 hover:bg-surface-alt hover:shadow-card-hover-soft motion-reduce:transition-none">
                                    @if(!empty($card['image']['ID']))
                                        <div class="mb-4 overflow-hidden rounded-[9px]">
                                            {!! wp_get_attachment_image($card['image']['ID'], 'medium', false, [
                                                'class' => 'h-auto w-full object-cover transition-transform duration-[1100ms] ease-[cubic-bezier(0.22,1,0.36,1)] group-hover:scale-[1.035] motion-reduce:transition-none',
                                                'loading' => 'lazy',
                                                'decoding' => 'async',
                                            ]) !!}
                                        </div>
                                    @endif

                                    @if(!empty($card['title']))
                                        <div class="relative inline-block max-w-full pb-[5px]">
                                            <h6>{{ $card['title'] }}</h6>
                                            <span class="absolute bottom-0 left-0 h-[2px] w-full origin-left scale-x-0 rounded-full bg-brand transition-transform duration-500 ease-out group-hover:scale-x-100"></span>
                                        </div>
                                    @endif

                                    @if(!empty($card['description']))
                                        <p class="mt-2 mb-4 whitespace-pre-line text-[13px] leading-[1.6] text-muted">{{ trim((string) $card['description']) }}</p>
                                    @endif

                                    <div class="mt-4 flex flex-col items-start gap-2">
                                        @if(!empty($card['copy_url']))
                                            <button type="button" @if(!$isEditor) @click="copyUrl(@js($card['copy_url']), {{ $loop->index }})" @endif class="inline-flex min-h-10 items-center gap-2 rounded-[8px] border border-brand/35 bg-white px-4 text-[12px] font-semibold text-brand-active transition-[border-color,background-color,color] duration-500 ease-out hover:border-brand hover:bg-brand/10 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-brand">
                                                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                                    <rect x="8" y="8" width="12" height="12" rx="2" stroke="currentColor" stroke-width="1.8" />
                                                    <path d="M16 8V6C16 4.895 15.105 4 14 4H6C4.895 4 4 4.895 4 6V14C4 15.105 4.895 16 6 16H8" stroke="currentColor" stroke-width="1.8" />
                                                </svg>
                                                <span @if(!$isEditor) x-text="copiedIndex === {{ $loop->index }} ? @js(__('Skopiowano!', 'appandsite')) : @js($card['copy_label'] ?: __('Kopiuj link', 'appandsite'))" @endif>{{ $card['copy_label'] ?: __('Kopiuj link', 'appandsite') }}</span>
                                            </button>
                                        @endif

                                        @if(!empty($card['button']['url']) && !empty($card['button']['title']))
                                            <a href="{{ esc_url($card['button']['url']) }}" target="{{ esc_attr($card['button']['target'] ?: '_self') }}" @if(($card['button']['target'] ?? '') === '_blank') rel="noopener noreferrer" @endif class="group/btn relative isolate inline-flex min-h-[46px] w-full items-center justify-center gap-3 overflow-hidden rounded-[8px] bg-brand px-4 text-[13px] font-bold text-primary transition-colors duration-300 before:absolute before:inset-0 before:z-0 before:origin-left before:scale-x-0 before:bg-brand-active before:content-[''] before:transition-transform before:duration-500 before:ease-out hover:text-white hover:before:scale-x-100 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-brand motion-reduce:before:transition-none">
                                                <span class="relative z-10">{{ $card['button']['title'] }}</span>
                                                <svg class="relative z-10 h-4 w-4 transition-transform duration-300 group-hover/btn:translate-x-1" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                                    <path d="M5 12H19M13 6L19 12L13 18" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                                </svg>
                                            </a>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </aside>

                <div class="min-w-0">
                    <div class="space-y-11 sm:space-y-14 lg:space-y-16">
                        @foreach($contentSections as $section)
                            @php
                                $layout = $section['acf_fc_layout'] ?? '';
                                $sectionId = 'blog-section-' . $postId . '-' . ($loop->index + 1);
                                $sectionTitle = $formatTitle($section['title'] ?? '');
                                $descriptionHtml = wp_kses_post($section['description'] ?? '');
                                $hasImage = !empty($section['image']['ID']);
                                $reverse = (bool) ($section['reverse_layout'] ?? false);
                                $imageOrder = $reverse ? 'lg:order-1' : 'lg:order-2';
                                $textOrder = $reverse ? 'lg:order-2' : 'lg:order-1';
                            @endphp

                            @if($layout === 'text_image' || $layout === 'image_compare')
                                <section id="{{ esc_attr($sectionId) }}" class="scroll-mt-28" @if(!$isEditor) data-aos="fade-up" data-aos-duration="650" data-aos-once="true" @endif>
                                    <div class="grid items-start gap-6 lg:grid-cols-[minmax(0,1.2fr)_minmax(0,0.9fr)] lg:gap-8 xl:gap-10">
                                        <div class="min-w-0 {{ $textOrder }}">
                                            @if(!empty($section['prefix']))
                                                <div class="mb-3 flex items-center gap-2 text-[11px] font-semibold uppercase tracking-[0.12em] text-muted sm:text-[12px]">
                                                    <span class="flex h-[24px] w-[24px] shrink-0 items-center justify-center rounded-[6px] border border-brand/60 bg-brand/10 text-brand">
                                                        <svg class="h-3 w-3" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                                            <path d="M12 3L13.6 8.4L19 10L13.6 11.6L12 17L10.4 11.6L5 10L10.4 8.4L12 3Z" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round" />
                                                        </svg>
                                                    </span>
                                                    {{ $section['prefix'] }}
                                                </div>
                                            @endif

                                            @if($sectionTitle !== '')
                                                <h2>{!! $sectionTitle !!}</h2>
                                            @endif

                                            @if($descriptionHtml !== '')
                                                <div class="wysiwyg mt-3 sm:mt-4">{!! $descriptionHtml !!}</div>
                                            @endif
                                        </div>

                                        @if($layout === 'text_image')
                                            @if($hasImage)
                                                <figure class="group/image min-w-0 overflow-hidden rounded-[12px] border border-border bg-surface shadow-card-soft transition-[border-color,box-shadow] duration-[1000ms] ease-[cubic-bezier(0.22,1,0.36,1)] hover:border-brand/45 hover:shadow-card-hover-soft motion-reduce:transition-none {{ $imageOrder }}">
                                                    {!! wp_get_attachment_image($section['image']['ID'], 'large', false, [
                                                        'class' => 'block h-auto w-full object-cover transition-transform duration-[1100ms] ease-[cubic-bezier(0.22,1,0.36,1)] group-hover/image:scale-[1.035] motion-reduce:transition-none',
                                                        'loading' => 'lazy',
                                                        'decoding' => 'async',
                                                    ]) !!}
                                                </figure>
                                            @endif
                                        @elseif(!empty($section['before_image']['ID']) && !empty($section['after_image']['ID']))
                                            @php
                                                $beforeLabel = trim((string) ($section['before_label'] ?? '')) ?: __('Przed', 'appandsite');
                                                $afterLabel = trim((string) ($section['after_label'] ?? '')) ?: __('Po', 'appandsite');
                                            @endphp

                                            <div @if(!$isEditor) x-data="alpBlogCompare()" @endif class="group/compare min-w-0 {{ $imageOrder }}">
                                                <div class="relative isolate aspect-[4/3] w-full overflow-hidden rounded-[12px] border border-border bg-primary-dark shadow-card-soft transition-[border-color,box-shadow] duration-[1000ms] ease-[cubic-bezier(0.22,1,0.36,1)] group-hover/compare:border-brand/45 group-hover/compare:shadow-card-hover-soft focus-within:outline-2 focus-within:outline-offset-4 focus-within:outline-brand motion-reduce:transition-none sm:aspect-[16/10]">
                                                    {!! wp_get_attachment_image($section['after_image']['ID'], 'large', false, [
                                                        'class' => 'absolute inset-0 h-full w-full object-cover transition-transform duration-[1400ms] ease-[cubic-bezier(0.22,1,0.36,1)] group-hover/compare:scale-[1.025] motion-reduce:transition-none',
                                                        'loading' => 'lazy',
                                                        'decoding' => 'async',
                                                    ]) !!}

                                                    <div class="absolute inset-0 [clip-path:inset(0_50%_0_0)]" @if(!$isEditor) :style="'clip-path: inset(0 ' + (100 - position) + '% 0 0)'" @endif>
                                                        {!! wp_get_attachment_image($section['before_image']['ID'], 'large', false, [
                                                            'class' => 'h-full w-full object-cover transition-transform duration-[1400ms] ease-[cubic-bezier(0.22,1,0.36,1)] group-hover/compare:scale-[1.025] motion-reduce:transition-none',
                                                            'loading' => 'lazy',
                                                            'decoding' => 'async',
                                                        ]) !!}
                                                    </div>

                                                    <span class="pointer-events-none absolute inset-0 z-[5] bg-brand/10 opacity-0 transition-opacity duration-[1100ms] ease-[cubic-bezier(0.22,1,0.36,1)] group-hover/compare:opacity-100 motion-reduce:transition-none" aria-hidden="true"></span>

                                                    <span class="pointer-events-none absolute bottom-3 left-3 z-40 inline-flex max-w-[42%] items-center justify-center rounded-[6px] border border-white/30 bg-primary-dark/90 px-3 py-2 text-center text-[11px] font-semibold leading-[1.3] text-white shadow-card-soft backdrop-blur-sm sm:bottom-4 sm:left-4 sm:text-[12px]">
                                                        {{ $beforeLabel }}
                                                    </span>

                                                    <span class="pointer-events-none absolute bottom-3 right-3 z-40 inline-flex max-w-[42%] items-center justify-center rounded-[6px] border border-brand bg-brand px-3 py-2 text-center text-[11px] font-semibold leading-[1.3] text-primary shadow-card-soft sm:bottom-4 sm:right-4 sm:text-[12px]">
                                                        {{ $afterLabel }}
                                                    </span>

                                                    <span class="pointer-events-none absolute inset-y-0 left-1/2 z-20 w-[2px] bg-white shadow-md" @if(!$isEditor) :style="'left: ' + position + '%'" @endif aria-hidden="true"></span>

                                                    <span class="pointer-events-none absolute top-1/2 left-1/2 z-40 flex h-10 w-10 -translate-x-1/2 -translate-y-1/2 items-center justify-center rounded-full border border-white/70 bg-white text-primary shadow-card-soft transition-[box-shadow,background-color] duration-[900ms] ease-[cubic-bezier(0.22,1,0.36,1)] group-hover/compare:bg-brand group-hover/compare:shadow-card-hover-soft sm:h-12 sm:w-12" @if(!$isEditor) :style="'left: ' + position + '%'" @endif aria-hidden="true">
                                                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                                            <path d="M9 6L3 12L9 18M15 6L21 12L15 18" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                                        </svg>
                                                    </span>

                                                    <input type="range" min="0" max="100" step="1" value="50" @if(!$isEditor) x-model.number="position" @endif aria-label="{{ esc_attr__('Przesuń suwak, aby porównać dwa zdjęcia', 'appandsite') }}" class="absolute inset-0 z-30 h-full w-full cursor-ew-resize opacity-0 focus-visible:opacity-0" />
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                </section>

                            @elseif($layout === 'quote')
                                @php
                                    $quoteHtml = wp_kses_post($section['quote'] ?? '');
                                @endphp

                                @if($quoteHtml !== '')
                                    <section id="{{ esc_attr($sectionId) }}" class="scroll-mt-28" @if(!$isEditor) data-aos="fade-up" data-aos-duration="650" data-aos-once="true" @endif>
                                        <blockquote class="group relative overflow-hidden rounded-[12px] border-l-[3px] border-brand bg-surface px-6 py-6 shadow-card-soft transition-[background-color,border-color,box-shadow] duration-[950ms] ease-[cubic-bezier(0.22,1,0.36,1)] hover:border-brand-active hover:bg-surface-alt hover:shadow-card-hover-soft motion-reduce:transition-none sm:px-8 sm:py-7">
                                            <svg class="mb-3 h-8 w-8 fill-brand transition-transform duration-[1100ms] ease-[cubic-bezier(0.22,1,0.36,1)] group-hover:scale-110 group-hover:-rotate-[8deg] motion-reduce:transition-none" viewBox="0 0 24 24" aria-hidden="true">
                                                <path d="M10.5 11H6.8C7 8.9 8.2 7.6 10.4 7.1V4.8C6.3 5.4 4 8.4 4 12.9V19H10.5V11ZM20 11H16.3C16.5 8.9 17.7 7.6 19.9 7.1V4.8C15.8 5.4 13.5 8.4 13.5 12.9V19H20V11Z" />
                                            </svg>

                                            <div class="wysiwyg">{!! $quoteHtml !!}</div>

                                            @if(!empty($section['author']))
                                                <footer class="mt-4 text-[13px] font-semibold text-muted sm:text-[14px]">
                                                    @if(!empty($section['author_url']))
                                                        <a href="{{ esc_url($section['author_url']) }}" class="relative inline-block pb-1 transition-colors duration-300 hover:text-brand-active">
                                                            {{ $section['author'] }}
                                                            <span class="absolute bottom-0 left-0 h-[2px] w-full origin-left scale-x-0 bg-brand transition-transform duration-500 group-hover:scale-x-100"></span>
                                                        </a>
                                                    @else
                                                        {{ $section['author'] }}
                                                    @endif
                                                </footer>
                                            @endif
                                        </blockquote>
                                    </section>
                                @endif

                            @elseif($layout === 'table')
                                <section id="{{ esc_attr($sectionId) }}" class="scroll-mt-28" @if(!$isEditor) data-aos="fade-up" data-aos-duration="650" data-aos-once="true" @endif>
                                    @if(!empty($section['prefix']))
                                        <div class="mb-3 flex items-center gap-2 text-[11px] font-semibold uppercase tracking-[0.12em] text-muted sm:text-[12px]">
                                            <span class="flex h-[24px] w-[24px] shrink-0 items-center justify-center rounded-[6px] border border-brand/60 bg-brand/10 text-brand">
                                                <svg class="h-3 w-3" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                                    <path d="M12 3L13.6 8.4L19 10L13.6 11.6L12 17L10.4 11.6L5 10L10.4 8.4L12 3Z" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round" />
                                                </svg>
                                            </span>
                                            {{ $section['prefix'] }}
                                        </div>
                                    @endif

                                    @if($sectionTitle !== '')
                                        <h2>{!! $sectionTitle !!}</h2>
                                    @endif

                                    @if($descriptionHtml !== '')
                                        <div class="wysiwyg mt-3 sm:mt-4">{!! $descriptionHtml !!}</div>
                                    @endif

                                    @if(!empty($section['columns']))
                                        <div class="group/table relative isolate mt-5 rounded-[12px] border border-border bg-white shadow-card-soft transition-colors duration-[950ms] ease-[cubic-bezier(0.22,1,0.36,1)] hover:border-brand/45 motion-reduce:transition-none">
                                            <span aria-hidden="true" class="pointer-events-none absolute inset-0 rounded-[12px] opacity-0 shadow-card-hover-soft transition-opacity duration-[950ms] ease-[cubic-bezier(0.22,1,0.36,1)] group-hover/table:opacity-100 motion-reduce:transition-none"></span>
                                            <div class="table-scroll relative z-10 rounded-[11px]">
                                                <table>
                                                    <thead>
                                                        <tr>
                                                            @foreach($section['columns'] as $column)
                                                                <th scope="col">{{ $column['label'] ?? '' }}</th>
                                                            @endforeach
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach($section['rows'] ?? [] as $tableRow)
                                                            <tr class="transition-colors duration-500 ease-out even:bg-surface/70 hover:bg-brand/10">
                                                                @foreach($section['columns'] as $column)
                                                                    @php
                                                                        $value = $tableRow['cells'][$loop->index]['value'] ?? '';
                                                                    @endphp

                                                                    <td>
                                                                        @if($loop->first)
                                                                            <strong>{{ $value }}</strong>
                                                                        @else
                                                                            {{ $value }}
                                                                        @endif
                                                                    </td>
                                                                @endforeach
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    @endif
                                </section>

                            @elseif($layout === 'text')
                                <section id="{{ esc_attr($sectionId) }}" class="scroll-mt-28" @if(!$isEditor) data-aos="fade-up" data-aos-duration="650" data-aos-once="true" @endif>
                                    @if(!empty($section['prefix']))
                                        <div class="mb-3 flex items-center gap-2 text-[11px] font-semibold uppercase tracking-[0.12em] text-muted sm:text-[12px]">
                                            <span class="flex h-[24px] w-[24px] shrink-0 items-center justify-center rounded-[6px] border border-brand/60 bg-brand/10 text-brand">
                                                <svg class="h-3 w-3" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                                    <path d="M12 3L13.6 8.4L19 10L13.6 11.6L12 17L10.4 11.6L5 10L10.4 8.4L12 3Z" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round" />
                                                </svg>
                                            </span>
                                            {{ $section['prefix'] }}
                                        </div>
                                    @endif

                                    @if($sectionTitle !== '')
                                        <h2>{!! $sectionTitle !!}</h2>
                                    @endif

                                    @if($descriptionHtml !== '')
                                        <div class="wysiwyg mt-3 sm:mt-4">{!! $descriptionHtml !!}</div>
                                    @endif
                                </section>

                            @elseif($layout === 'mini_cta')
                                <section id="{{ esc_attr($sectionId) }}" class="scroll-mt-28" @if(!$isEditor) data-aos="fade-up" data-aos-duration="650" data-aos-once="true" @endif>
                                    <div class="group relative isolate flex flex-col gap-4 overflow-hidden rounded-[12px] border border-border bg-surface px-5 py-5 shadow-card-soft transition-[background-color,border-color,box-shadow] duration-[950ms] ease-[cubic-bezier(0.22,1,0.36,1)] hover:border-brand/45 hover:bg-surface-alt hover:shadow-card-hover-soft motion-reduce:transition-none sm:flex-row sm:items-center sm:gap-5 sm:px-6">
                                        @if(!empty($section['icon']['ID']))
                                            <div class="relative z-10 flex h-[58px] w-[58px] shrink-0 items-center justify-center rounded-full bg-brand/10 transition-transform duration-[950ms] ease-[cubic-bezier(0.22,1,0.36,1)] group-hover:scale-110 motion-reduce:transition-none lg:h-[62px] lg:w-[62px]">
                                                {!! wp_get_attachment_image($section['icon']['ID'], 'thumbnail', false, [
                                                    'class' => 'h-[30px] w-[30px] object-contain transition-transform duration-[1200ms] ease-[cubic-bezier(0.22,1,0.36,1)] group-hover:rotate-[8deg] motion-reduce:transition-none lg:h-[32px] lg:w-[32px]',
                                                    'alt' => '',
                                                    'aria-hidden' => 'true',
                                                    'loading' => 'lazy',
                                                ]) !!}
                                            </div>
                                        @endif

                                        <div class="relative z-10 min-w-0 flex-1">
                                            @if(!empty($section['title']))
                                                <div class="relative inline-block max-w-full pb-[5px]">
                                                    <h5>{{ $section['title'] }}</h5>
                                                    <span class="absolute bottom-0 left-0 h-[2px] w-full origin-left scale-x-0 rounded-full bg-brand transition-transform duration-500 ease-out group-hover:scale-x-100 motion-reduce:transition-none"></span>
                                                </div>
                                            @endif

                                            @if(!empty($section['description']))
                                                <p class="mt-1 mb-0 whitespace-pre-line text-[13px] leading-[1.6] text-muted sm:text-[14px]">{{ trim((string) $section['description']) }}</p>
                                            @endif
                                        </div>

                                        @if(!empty($section['button']['url']) && !empty($section['button']['title']))
                                            <a href="{{ esc_url($section['button']['url']) }}" target="{{ esc_attr($section['button']['target'] ?: '_self') }}" @if(($section['button']['target'] ?? '') === '_blank') rel="noopener noreferrer" @endif class="group/btn relative isolate inline-flex min-h-[46px] shrink-0 items-center justify-center gap-3 overflow-hidden rounded-[8px] bg-brand px-5 text-[13px] font-bold text-primary transition-colors duration-300 before:absolute before:inset-0 before:z-0 before:origin-left before:scale-x-0 before:bg-brand-active before:content-[''] before:transition-transform before:duration-500 before:ease-out hover:text-white hover:before:scale-x-100 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-brand motion-reduce:before:transition-none">
                                                <span class="relative z-10">{{ $section['button']['title'] }}</span>
                                                <svg class="relative z-10 h-4 w-4 transition-transform duration-300 group-hover/btn:translate-x-1" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                                    <path d="M5 12H19M13 6L19 12L13 18" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                                </svg>
                                            </a>
                                        @endif
                                    </div>
                                </section>
                            @endif
                        @endforeach
                    </div>
                </div>
            </div>

            @if($showNavigation && ($previousPost || $nextPost))
                <nav class="mt-12 grid grid-cols-2 gap-x-4 gap-y-6 border-t border-border pt-6 sm:mt-16 sm:gap-x-8 sm:pt-8" aria-label="{{ esc_attr__('Nawigacja między wpisami', 'appandsite') }}">
                    <div class="min-w-0">
                        @if($previousPost)
                            <div class="group flex max-w-full flex-col items-start gap-2 text-left">
                                <a href="{{ esc_url(get_permalink($previousPost->ID)) }}" class="inline-flex items-center gap-2 text-[12px] text-muted transition-colors duration-300 hover:text-brand-active focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-brand sm:text-[13px]">
                                    <svg class="h-4 w-4 shrink-0 text-brand transition-transform duration-300 group-hover:-translate-x-1" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                        <path d="M19 12H5M11 6L5 12L11 18" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                    </svg>
                                    <span>{{ __('Poprzedni wpis', 'appandsite') }}</span>
                                </a>
                                <h6 class="max-w-full">
                                    <a href="{{ esc_url(get_permalink($previousPost->ID)) }}" class="group/title relative inline-block max-w-full pb-[6px] text-primary transition-colors duration-300 hover:text-brand-active focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-brand">
                                        {{ $previousTitle }}
                                        <span aria-hidden="true" class="absolute bottom-0 left-0 h-[2px] w-full origin-left scale-x-0 rounded-full bg-brand transition-transform duration-500 ease-out group-hover/title:scale-x-100"></span>
                                    </a>
                                </h6>
                            </div>
                        @endif
                    </div>

                    <div class="min-w-0 text-right">
                        @if($nextPost)
                            <div class="group flex max-w-full flex-col items-end gap-2 text-right">
                                <a href="{{ esc_url(get_permalink($nextPost->ID)) }}" class="inline-flex items-center gap-2 text-[12px] text-muted transition-colors duration-300 hover:text-brand-active focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-brand sm:text-[13px]">
                                    <span>{{ __('Następny wpis', 'appandsite') }}</span>
                                    <svg class="h-4 w-4 shrink-0 text-brand transition-transform duration-300 group-hover:translate-x-1" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                        <path d="M5 12H19M13 6L19 12L13 18" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                    </svg>
                                </a>
                                <h6 class="max-w-full">
                                    <a href="{{ esc_url(get_permalink($nextPost->ID)) }}" class="group/title relative inline-block max-w-full pb-[6px] text-primary transition-colors duration-300 hover:text-brand-active focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-brand">
                                        {{ $nextTitle }}
                                        <span aria-hidden="true" class="absolute bottom-0 left-0 h-[2px] w-full origin-left scale-x-0 rounded-full bg-brand transition-transform duration-500 ease-out group-hover/title:scale-x-100"></span>
                                    </a>
                                </h6>
                            </div>
                        @endif
                    </div>

                    <div class="col-span-2 flex justify-center sm:-mt-10">
                        <a href="{{ esc_url($blogUrl) }}" aria-label="{{ esc_attr__('Wszystkie wpisy blogowe', 'appandsite') }}" class="inline-flex h-10 w-10 items-center justify-center rounded-[7px] border border-border text-brand-active transition-[border-color,background-color,color] duration-500 ease-out hover:border-brand hover:bg-brand/10 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-brand">
                            <svg class="h-[18px] w-[18px]" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                <rect x="3" y="3" width="7" height="7" rx="1.5" stroke="currentColor" stroke-width="1.8" />
                                <rect x="14" y="3" width="7" height="7" rx="1.5" stroke="currentColor" stroke-width="1.8" />
                                <rect x="3" y="14" width="7" height="7" rx="1.5" stroke="currentColor" stroke-width="1.8" />
                                <rect x="14" y="14" width="7" height="7" rx="1.5" stroke="currentColor" stroke-width="1.8" />
                            </svg>
                        </a>
                    </div>
                </nav>
            @endif
        </x-ui.container>
    </section>
</x-block-helper.block-wrapper>
