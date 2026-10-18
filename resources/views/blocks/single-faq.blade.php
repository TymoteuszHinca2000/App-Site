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

    $blockId = sanitize_html_class(
        wp_unique_id('single-faq-')
    );

    $leftItems = [];
    $rightItems = [];

    foreach ($items ?? [] as $index => $item) {
        $item['_index'] = $index;

        if ($index % 2 === 0) {
            $leftItems[] = $item;
        } else {
            $rightItems[] = $item;
        }
    }
@endphp

<x-block-helper.block-wrapper :block="$block" :attributes="$attributes">
    <section
        @if(!$isEditor)
            x-data="alpSingleFaq()"
        @endif
        class="relative bg-white"
    >
        <x-ui.container>

            <div class="flex flex-col gap-6 lg:flex-row lg:items-end lg:justify-between lg:gap-12">

                <div class="w-full max-w-[760px]">

                    @if(!empty($prefix))
                        <div
                            @if(!$isEditor)
                                data-aos="fade-right"
                                data-aos-duration="500"
                                data-aos-once="true"
                                data-aos-easing="ease-out-cubic"
                            @endif
                            class="mb-5 flex items-center gap-3 text-[11px] font-semibold uppercase tracking-[0.14em] text-muted sm:text-[12px]"
                        >
                            <span class="flex h-[24px] w-[24px] shrink-0 items-center justify-center rounded-[6px] border border-brand/60 bg-brand/10 text-brand">
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
                            class="wysiwyg mt-5 max-w-[680px] [&_p]:!m-0 [&_p+p]:!mt-4"
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
                            data-aos="fade-up"
                            data-aos-duration="650"
                            data-aos-delay="220"
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
                            class="group relative isolate inline-flex min-h-[52px] items-center justify-center gap-4 overflow-hidden rounded-[8px] bg-primary px-7 text-[14px] font-bold text-white
                                transition-colors
                                duration-300
                                before:absolute
                                before:inset-0
                                before:z-0
                                before:origin-left
                                before:scale-x-0
                                before:bg-brand
                                before:content-['']
                                before:transition-transform
                                before:duration-300
                                before:ease-out
                                hover:text-primary
                                hover:before:scale-x-100
                                focus-visible:outline-2
                                focus-visible:outline-offset-2
                                focus-visible:outline-brand
                                motion-reduce:before:transition-none"
                        >
                            <span class="relative z-10">
                                {{ $button['title'] }}
                            </span>

                            <svg
                                class="relative z-10 h-4 w-4 transition-transform duration-300 group-hover:translate-x-1 motion-reduce:transition-none"
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

            @if(!empty($items))

                <div class="mt-8 space-y-3 lg:hidden">

                    @foreach($items as $item)
                        @php
                            $faqIndex = $loop->index;

                            $answerId =
                                $blockId .
                                '-answer-mobile-' .
                                $faqIndex;
                        @endphp

                        <div
                            @if(!$isEditor)
                                data-aos="fade-up"
                                data-aos-duration="600"
                                data-aos-delay="{{ 180 + ($faqIndex * 60) }}"
                                data-aos-once="true"
                                data-aos-easing="ease-out-cubic"
                            @endif
                            class="overflow-hidden rounded-[16px] border border-border bg-white/95
                                shadow-card-soft
                                transition-[box-shadow,border-color,background-color]
                                duration-700
                                ease-[cubic-bezier(0.22,1,0.36,1)]
                                hover:border-brand/45
                                hover:bg-white
                                hover:shadow-card-hover"
                        >
                            <button
                                type="button"
                                @if(!$isEditor)
                                    @click="toggleFaq({{ $faqIndex }})"
                                    :aria-expanded="isOpen({{ $faqIndex }})"
                                @endif
                                aria-controls="{{ $answerId }}"
                                class="group flex min-h-[68px] w-full items-center gap-4 px-4 py-4 text-left sm:px-5"
                            >
                                <span
                                    class="flex h-[34px] w-[34px] shrink-0 items-center justify-center rounded-full bg-brand/10 text-[15px] font-bold leading-none text-brand
                                        transition-[transform,background-color]
                                        duration-[700ms]
                                        ease-[cubic-bezier(0.22,1,0.36,1)]
                                        group-hover:rotate-[8deg]
                                        group-hover:scale-110
                                        group-hover:bg-brand/15
                                        motion-reduce:transform-none
                                        motion-reduce:transition-none"
                                    aria-hidden="true"
                                >
                                    ?
                                </span>

                                <span class="min-w-0 flex-1 text-[14px] font-semibold leading-[1.45] text-primary sm:text-[15px]">
                                    {{ $item['question'] ?? '' }}
                                </span>

                                <span
                                    @if(!$isEditor)
                                        :class="isOpen({{ $faqIndex }})
                                            ? 'rotate-45 text-brand'
                                            : 'rotate-0 text-primary'"
                                    @endif
                                    class="flex h-[30px] w-[30px] shrink-0 items-center justify-center rounded-full
                                        transition-[transform,color]
                                        duration-500
                                        ease-[cubic-bezier(0.22,1,0.36,1)]"
                                >
                                    <svg
                                        class="h-[17px] w-[17px]"
                                        viewBox="0 0 24 24"
                                        fill="none"
                                        aria-hidden="true"
                                    >
                                        <path
                                            d="M12 5V19M5 12H19"
                                            stroke="currentColor"
                                            stroke-width="2"
                                            stroke-linecap="round"
                                        />
                                    </svg>
                                </span>
                            </button>

                            <div
                                id="{{ $answerId }}"
                                @if(!$isEditor)
                                    :class="isOpen({{ $faqIndex }})
                                        ? 'grid-rows-[1fr] opacity-100'
                                        : 'grid-rows-[0fr] opacity-0'"
                                @endif
                                class="grid
                                    transition-[grid-template-rows,opacity]
                                    duration-500
                                    ease-[cubic-bezier(0.22,1,0.36,1)]"
                            >
                                <div class="overflow-hidden">
                                    <div class="px-4 pb-5 sm:px-5">
                                        <div class="ml-[50px] border-t border-border pt-4">

                                            @if(!empty($item['answer']))
                                                <div class="wysiwyg text-[14px] sm:text-[15px]">
                                                    {!! wp_kses_post($item['answer']) !!}
                                                </div>
                                            @endif

                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>
                    @endforeach

                </div>

                <div class="mt-8 hidden grid-cols-2 items-start gap-x-6 lg:grid">

                    <div class="space-y-3">

                        @foreach($leftItems as $item)
                            @php
                                $faqIndex = $item['_index'];

                                $answerId =
                                    $blockId .
                                    '-answer-left-' .
                                    $faqIndex;
                            @endphp

                            <div
                                @if(!$isEditor)
                                    data-aos="fade-up"
                                    data-aos-duration="600"
                                    data-aos-delay="{{ 180 + ($faqIndex * 60) }}"
                                    data-aos-once="true"
                                    data-aos-easing="ease-out-cubic"
                                @endif
                                class="overflow-hidden rounded-[16px] border border-border bg-white/95
                                    shadow-card-soft
                                    transition-[box-shadow,border-color,background-color]
                                    duration-700
                                    ease-[cubic-bezier(0.22,1,0.36,1)]
                                    hover:border-brand/45
                                    hover:bg-white
                                    hover:shadow-card-hover"
                            >
                                <button
                                    type="button"
                                    @if(!$isEditor)
                                        @click="toggleFaq({{ $faqIndex }})"
                                        :aria-expanded="isOpen({{ $faqIndex }})"
                                    @endif
                                    aria-controls="{{ $answerId }}"
                                    class="group flex min-h-[68px] w-full items-center gap-4 px-5 py-4 text-left"
                                >
                                    <span
                                        class="flex h-[34px] w-[34px] shrink-0 items-center justify-center rounded-full bg-brand/10 text-[15px] font-bold leading-none text-brand
                                            transition-[transform,background-color]
                                            duration-[700ms]
                                            ease-[cubic-bezier(0.22,1,0.36,1)]
                                            group-hover:rotate-[8deg]
                                            group-hover:scale-110
                                            group-hover:bg-brand/15
                                            motion-reduce:transform-none
                                            motion-reduce:transition-none"
                                        aria-hidden="true"
                                    >
                                        ?
                                    </span>

                                    <span class="min-w-0 flex-1 text-[15px] font-semibold leading-[1.45] text-primary">
                                        {{ $item['question'] ?? '' }}
                                    </span>

                                    <span
                                        @if(!$isEditor)
                                            :class="isOpen({{ $faqIndex }})
                                                ? 'rotate-45 text-brand'
                                                : 'rotate-0 text-primary'"
                                        @endif
                                        class="flex h-[30px] w-[30px] shrink-0 items-center justify-center rounded-full
                                            transition-[transform,color]
                                            duration-500
                                            ease-[cubic-bezier(0.22,1,0.36,1)]"
                                    >
                                        <svg
                                            class="h-[17px] w-[17px]"
                                            viewBox="0 0 24 24"
                                            fill="none"
                                            aria-hidden="true"
                                        >
                                            <path
                                                d="M12 5V19M5 12H19"
                                                stroke="currentColor"
                                                stroke-width="2"
                                                stroke-linecap="round"
                                            />
                                        </svg>
                                    </span>
                                </button>

                                <div
                                    id="{{ $answerId }}"
                                    @if(!$isEditor)
                                        :class="isOpen({{ $faqIndex }})
                                            ? 'grid-rows-[1fr] opacity-100'
                                            : 'grid-rows-[0fr] opacity-0'"
                                    @endif
                                    class="grid
                                        transition-[grid-template-rows,opacity]
                                        duration-500
                                        ease-[cubic-bezier(0.22,1,0.36,1)]"
                                >
                                    <div class="overflow-hidden">
                                        <div class="px-5 pb-5">
                                            <div class="ml-[50px] border-t border-border pt-4">

                                                @if(!empty($item['answer']))
                                                    <div class="wysiwyg text-[14px]">
                                                        {!! wp_kses_post($item['answer']) !!}
                                                    </div>
                                                @endif

                                            </div>
                                        </div>
                                    </div>
                                </div>

                            </div>
                        @endforeach

                    </div>

                    <div class="space-y-3">

                        @foreach($rightItems as $item)
                            @php
                                $faqIndex = $item['_index'];

                                $answerId =
                                    $blockId .
                                    '-answer-right-' .
                                    $faqIndex;
                            @endphp

                            <div
                                @if(!$isEditor)
                                    data-aos="fade-up"
                                    data-aos-duration="600"
                                    data-aos-delay="{{ 180 + ($faqIndex * 60) }}"
                                    data-aos-once="true"
                                    data-aos-easing="ease-out-cubic"
                                @endif
                                class="overflow-hidden rounded-[16px] border border-border bg-white/95
                                    shadow-card-soft
                                    transition-[box-shadow,border-color,background-color]
                                    duration-700
                                    ease-[cubic-bezier(0.22,1,0.36,1)]
                                    hover:border-brand/45
                                    hover:bg-white
                                    hover:shadow-card-hover"
                            >
                                <button
                                    type="button"
                                    @if(!$isEditor)
                                        @click="toggleFaq({{ $faqIndex }})"
                                        :aria-expanded="isOpen({{ $faqIndex }})"
                                    @endif
                                    aria-controls="{{ $answerId }}"
                                    class="group flex min-h-[68px] w-full items-center gap-4 px-5 py-4 text-left"
                                >
                                    <span
                                        class="flex h-[34px] w-[34px] shrink-0 items-center justify-center rounded-full bg-brand/10 text-[15px] font-bold leading-none text-brand
                                            transition-[transform,background-color]
                                            duration-[700ms]
                                            ease-[cubic-bezier(0.22,1,0.36,1)]
                                            group-hover:rotate-[8deg]
                                            group-hover:scale-110
                                            group-hover:bg-brand/15
                                            motion-reduce:transform-none
                                            motion-reduce:transition-none"
                                        aria-hidden="true"
                                    >
                                        ?
                                    </span>

                                    <span class="min-w-0 flex-1 text-[15px] font-semibold leading-[1.45] text-primary">
                                        {{ $item['question'] ?? '' }}
                                    </span>

                                    <span
                                        @if(!$isEditor)
                                            :class="isOpen({{ $faqIndex }})
                                                ? 'rotate-45 text-brand'
                                                : 'rotate-0 text-primary'"
                                        @endif
                                        class="flex h-[30px] w-[30px] shrink-0 items-center justify-center rounded-full
                                            transition-[transform,color]
                                            duration-500
                                            ease-[cubic-bezier(0.22,1,0.36,1)]"
                                    >
                                        <svg
                                            class="h-[17px] w-[17px]"
                                            viewBox="0 0 24 24"
                                            fill="none"
                                            aria-hidden="true"
                                        >
                                            <path
                                                d="M12 5V19M5 12H19"
                                                stroke="currentColor"
                                                stroke-width="2"
                                                stroke-linecap="round"
                                            />
                                        </svg>
                                    </span>
                                </button>

                                <div
                                    id="{{ $answerId }}"
                                    @if(!$isEditor)
                                        :class="isOpen({{ $faqIndex }})
                                            ? 'grid-rows-[1fr] opacity-100'
                                            : 'grid-rows-[0fr] opacity-0'"
                                    @endif
                                    class="grid
                                        transition-[grid-template-rows,opacity]
                                        duration-500
                                        ease-[cubic-bezier(0.22,1,0.36,1)]"
                                >
                                    <div class="overflow-hidden">
                                        <div class="px-5 pb-5">
                                            <div class="ml-[50px] border-t border-border pt-4">

                                                @if(!empty($item['answer']))
                                                    <div class="wysiwyg text-[14px]">
                                                        {!! wp_kses_post($item['answer']) !!}
                                                    </div>
                                                @endif

                                            </div>
                                        </div>
                                    </div>
                                </div>

                            </div>
                        @endforeach

                    </div>

                </div>

            @endif

        </x-ui.container>
    </section>
</x-block-helper.block-wrapper>