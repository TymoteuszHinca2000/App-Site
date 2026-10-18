@php
    $isEditor = is_admin();

    $titleHtml = wp_kses($title ?? '', [
        'p' => [],
        'strong' => [],
        'b' => [],
        'em' => [],
        'i' => [],
        'br' => [],
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

    $titleHtml = str_replace(
        ['<strong>', '</strong>', '<b>', '</b>'],
        [
            '<span class="text-brand">',
            '</span>',
            '<span class="text-brand">',
            '</span>',
        ],
        $titleHtml
    );

    $descriptionHtml = wp_kses($description ?? '', [
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
    ]);

    $firstRotatingText = wp_kses(
        $rotatingTexts[0]['text'] ?? '',
        [
            'br' => [],
        ]
    );

    $captionName = '';
    $captionRole = '';

    if (!empty($imageCaption)) {
        $captionRaw = trim(
            wp_strip_all_tags($imageCaption)
        );

        $captionLines = preg_split(
            '/\r\n|\r|\n/',
            $captionRaw
        );

        $captionLines = array_values(
            array_filter(
                array_map(
                    'trim',
                    $captionLines
                )
            )
        );

        if (count($captionLines) >= 2) {
            $captionName = array_shift(
                $captionLines
            );

            $captionRole = implode(
                ' ',
                $captionLines
            );
        } elseif (!empty($captionLines[0])) {
            $captionSingle = $captionLines[0];

            if (
                preg_match(
                    '/^(.+?)\s+(Właściciel|Właścicielka|Założyciel|Założycielka|Founder|Owner)\b(.*)$/u',
                    $captionSingle,
                    $matches
                )
            ) {
                $captionName = trim(
                    $matches[1]
                );

                $captionRole = trim(
                    $matches[2] . $matches[3]
                );
            } else {
                $captionName = $captionSingle;
            }
        }
    }
@endphp

<x-block-helper.block-wrapper :block="$block" :attributes="$attributes">
    <section>
        <x-ui.container>
            <div
                @if(!$isEditor)
                    x-data="alpConsultation(@js($rotatingTexts), 3000)"
                    x-ref="root"
                @endif
                class="relative"
            >
                <div
                    class="absolute inset-0 overflow-hidden rounded-[22px] consultation-gradient"
                ></div>

                <div
                    class="pointer-events-none absolute left-[32%] top-[54%] z-0 hidden h-[330px] w-[330px] -translate-y-1/2 rotate-45 border border-white/[0.025] xl:block"
                ></div>

                <div class="relative z-10 hidden min-h-[390px] grid-cols-[56%_44%] xl:grid">

                    <div class="relative z-30 flex flex-col justify-center p-10 2xl:p-12">

                        @if($prefix)
                            <div
                                @if(!$isEditor)
                                    data-aos="fade-right"
                                    data-aos-duration="500"
                                    data-aos-delay="0"
                                    data-aos-once="true"
                                    data-aos-easing="ease-out-cubic"
                                @endif
                                class="mb-4 flex items-center gap-3 text-[12px] font-semibold uppercase tracking-[0.12em] text-white/60"
                            >
                                <span
                                    class="flex h-[24px] w-[24px] shrink-0 items-center justify-center rounded-[6px] border border-brand/60 bg-brand/10 text-brand"
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
                                            d="M18.5 15.5L19.2 17.8L21.5 18.5L19.2 19.2L18.5 21.5L17.8 19.2L15.5 18.5Z"
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

                        @if($titleHtml)
                            <h2
                                @if(!$isEditor)
                                    data-aos="fade-up"
                                    data-aos-duration="650"
                                    data-aos-delay="80"
                                    data-aos-once="true"
                                    data-aos-easing="ease-out-cubic"
                                @endif
                                class="w-full max-w-[840px] text-[44px] font-extrabold leading-[1.06] tracking-[-0.045em] text-white 2xl:max-w-[920px] 2xl:text-[48px]"
                            >
                                {!! $titleHtml !!}
                            </h2>
                        @endif

                        @if($descriptionHtml)
                            <div
                                @if(!$isEditor)
                                    data-aos="fade-up"
                                    data-aos-duration="650"
                                    data-aos-delay="160"
                                    data-aos-once="true"
                                    data-aos-easing="ease-out-cubic"
                                @endif
                                class="mt-4 max-w-[500px] text-[15px] font-normal leading-[1.65] text-white 2xl:text-[16px]
                                    [&_p]:!m-0
                                    [&_p]:!text-white
                                    [&_p+p]:!mt-3
                                    [&_strong]:!text-white
                                    [&_b]:!text-white
                                    [&_li]:!text-white
                                    [&_a]:!text-white"
                            >
                                {!! $descriptionHtml !!}
                            </div>
                        @endif

                        @if($buttonPrimary || $buttonSecondary)
                            <div
                                @if(!$isEditor)
                                    data-aos="fade-up"
                                    data-aos-duration="650"
                                    data-aos-delay="240"
                                    data-aos-once="true"
                                    data-aos-easing="ease-out-cubic"
                                @endif
                                class="mt-6 flex flex-wrap items-center gap-4"
                            >
                                @if(
                                    !empty($buttonPrimary['url'])
                                    && !empty($buttonPrimary['title'])
                                )
                                    <a
                                        href="{{ esc_url($buttonPrimary['url']) }}"
                                        target="{{ esc_attr($buttonPrimary['target'] ?: '_self') }}"
                                        @if(($buttonPrimary['target'] ?? '') === '_blank')
                                            rel="noopener noreferrer"
                                        @endif
                                        class="group relative isolate inline-flex min-h-[50px] items-center justify-center gap-4 overflow-hidden rounded-[8px] bg-brand px-6 text-[14px] font-bold text-primary transition-colors duration-300
                                            before:absolute
                                            before:inset-0
                                            before:z-0
                                            before:origin-left
                                            before:scale-x-0
                                            before:bg-brand-active
                                            before:content-['']
                                            before:transition-transform
                                            before:duration-300
                                            before:ease-out
                                            hover:text-white
                                            hover:before:scale-x-100"
                                    >
                                        <span class="relative z-10">
                                            {{ $buttonPrimary['title'] }}
                                        </span>

                                        <svg
                                            class="relative z-10 h-4 w-4 transition-transform duration-300 group-hover:translate-x-1"
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
                                @endif

                                @if(
                                    !empty($buttonSecondary['url'])
                                    && !empty($buttonSecondary['title'])
                                )
                                    <a
                                        href="{{ esc_url($buttonSecondary['url']) }}"
                                        target="{{ esc_attr($buttonSecondary['target'] ?: '_self') }}"
                                        @if(($buttonSecondary['target'] ?? '') === '_blank')
                                            rel="noopener noreferrer"
                                        @endif
                                        class="group relative isolate inline-flex min-h-[50px] items-center justify-center gap-4 overflow-hidden rounded-[8px] border border-white/30 bg-white/[0.02] px-6 text-[14px] font-bold text-white transition-colors duration-300
                                            before:absolute
                                            before:inset-0
                                            before:z-0
                                            before:origin-left
                                            before:scale-x-0
                                            before:bg-white/10
                                            before:content-['']
                                            before:transition-transform
                                            before:duration-300
                                            before:ease-out
                                            hover:border-brand
                                            hover:before:scale-x-100"
                                    >
                                        <span class="relative z-10">
                                            {{ $buttonSecondary['title'] }}
                                        </span>

                                        <svg
                                            class="relative z-10 h-4 w-4 transition-transform duration-300 group-hover:translate-x-1"
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
                                @endif
                            </div>
                        @endif

                    </div>

                    <div class="relative z-20 min-h-[390px]">

                        @if(!empty($image['ID']))
                            <div
                                @if(!$isEditor)
                                    data-aos="fade-left"
                                    data-aos-duration="850"
                                    data-aos-delay="120"
                                    data-aos-once="true"
                                    data-aos-easing="ease-out-cubic"
                                @endif
                                class="pointer-events-none absolute inset-0 z-10"
                            >
                                {!! wp_get_attachment_image(
                                    $image['ID'],
                                    'large',
                                    false,
                                    [
                                        'class' => 'pointer-events-none absolute bottom-0 left-[-20%] h-auto max-h-[clamp(420px,31vw,490px)] w-auto max-w-none object-contain object-bottom 2xl:left-[-18%]',
                                        'loading' => $isEditor ? 'eager' : 'lazy',
                                        'decoding' => 'async',
                                    ]
                                ) !!}
                            </div>
                        @endif

                        @if(!empty($rotatingTexts))
                            <div
                                @if(!$isEditor)
                                    data-aos="fade-left"
                                    data-aos-duration="700"
                                    data-aos-delay="430"
                                    data-aos-once="true"
                                    data-aos-easing="ease-out-cubic"
                                @endif
                                class="absolute right-[42px] top-[70px] z-30 w-[260px] 2xl:right-[54px] 2xl:w-[280px]"
                            >
                                <div
                                    @if(!$isEditor)
                                        x-ref="noteDesktop"
                                    @endif
                                    class="flex items-center gap-2 text-white"
                                >
                                    <div class="shrink-0 text-white">
                                        <svg
                                            class="h-auto w-[78px] 2xl:w-[84px]"
                                            viewBox="0 0 120 80"
                                            fill="none"
                                            aria-hidden="true"
                                        >
                                            <path
                                                d="M112 9C91 10 71 18 55 30C43 39 34 49 28 62"
                                                stroke="currentColor"
                                                stroke-width="2.3"
                                                stroke-linecap="round"
                                            />

                                            <path
                                                d="M28 62L30 48M28 62L42 59"
                                                stroke="currentColor"
                                                stroke-width="2.3"
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                            />
                                        </svg>
                                    </div>

                                    <div
                                        class="font-caveat text-[32px] font-medium leading-[1.02] tracking-normal text-white 2xl:text-[35px]"
                                        @if(!$isEditor)
                                            x-html="currentText"
                                        @endif
                                    >
                                        {!! $firstRotatingText !!}
                                    </div>
                                </div>
                            </div>
                        @endif

                        @if($captionName || $captionRole)
                            <div
                                @if(!$isEditor)
                                    data-aos="fade-left"
                                    data-aos-duration="650"
                                    data-aos-delay="350"
                                    data-aos-once="true"
                                    data-aos-easing="ease-out-cubic"
                                @endif
                                class="absolute bottom-[30px] right-[18px] z-30 flex max-w-[270px] items-start gap-3 2xl:right-[24px]"
                            >
                                <span class="flex h-[18px] w-6 shrink-0 items-center">
                                    <span class="block h-[2px] w-full bg-brand"></span>
                                </span>

                                <div class="min-w-0">
                                    @if($captionName)
                                        <div class="text-[12px] font-medium leading-[18px] text-white/85">
                                            {{ $captionName }}
                                        </div>
                                    @endif

                                    @if($captionRole)
                                        <div class="mt-[1px] text-[12px] font-medium leading-[16px] text-white/70">
                                            {{ $captionRole }}
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endif

                    </div>
                </div>

                <div class="relative z-10 xl:hidden">

                    <div class="relative z-20 w-full p-6 pb-0 sm:p-8 sm:pb-0 lg:p-10 lg:pb-0">

                        @if($prefix)
                            <div
                                @if(!$isEditor)
                                    data-aos="fade-right"
                                    data-aos-duration="500"
                                    data-aos-delay="0"
                                    data-aos-once="true"
                                    data-aos-easing="ease-out-cubic"
                                @endif
                                class="mb-4 flex items-center gap-3 text-[11px] font-semibold uppercase tracking-[0.12em] text-white/60 sm:text-[12px]"
                            >
                                <span
                                    class="flex h-[24px] w-[24px] shrink-0 items-center justify-center rounded-[6px] border border-brand/60 bg-brand/10 text-brand"
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
                                            d="M18.5 15.5L19.2 17.8L21.5 18.5L19.2 19.2L18.5 21.5L17.8 19.2L15.5 18.5Z"
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

                        @if($titleHtml)
                            <h2
                                @if(!$isEditor)
                                    data-aos="fade-up"
                                    data-aos-duration="650"
                                    data-aos-delay="80"
                                    data-aos-once="true"
                                    data-aos-easing="ease-out-cubic"
                                @endif
                                class="w-full text-[30px] font-extrabold leading-[1.06] tracking-[-0.045em] text-white sm:text-[34px] md:text-[38px] lg:text-[42px]"
                            >
                                {!! $titleHtml !!}
                            </h2>
                        @endif

                        @if($descriptionHtml)
                            <div
                                @if(!$isEditor)
                                    data-aos="fade-up"
                                    data-aos-duration="650"
                                    data-aos-delay="160"
                                    data-aos-once="true"
                                    data-aos-easing="ease-out-cubic"
                                @endif
                                class="mt-4 w-full text-[14px] font-normal leading-[1.65] text-white sm:text-[15px] lg:text-[16px]
                                    [&_p]:!m-0
                                    [&_p]:!text-white
                                    [&_p+p]:!mt-3
                                    [&_strong]:!text-white
                                    [&_b]:!text-white
                                    [&_li]:!text-white
                                    [&_a]:!text-white"
                            >
                                {!! $descriptionHtml !!}
                            </div>
                        @endif

                        @if($buttonPrimary || $buttonSecondary)
                            <div
                                @if(!$isEditor)
                                    data-aos="fade-up"
                                    data-aos-duration="650"
                                    data-aos-delay="240"
                                    data-aos-once="true"
                                    data-aos-easing="ease-out-cubic"
                                @endif
                                class="mt-6 flex w-full flex-col gap-3 sm:flex-row sm:flex-wrap sm:items-center sm:justify-start sm:gap-4"
                            >
                                @if(
                                    !empty($buttonPrimary['url'])
                                    && !empty($buttonPrimary['title'])
                                )
                                    <a
                                        href="{{ esc_url($buttonPrimary['url']) }}"
                                        target="{{ esc_attr($buttonPrimary['target'] ?: '_self') }}"
                                        @if(($buttonPrimary['target'] ?? '') === '_blank')
                                            rel="noopener noreferrer"
                                        @endif
                                        class="group relative isolate inline-flex min-h-[52px] w-full items-center justify-center gap-5 overflow-hidden rounded-[8px] bg-brand px-7 text-[14px] font-bold text-primary transition-colors duration-300
                                            before:absolute
                                            before:inset-0
                                            before:z-0
                                            before:origin-left
                                            before:scale-x-0
                                            before:bg-brand-active
                                            before:content-['']
                                            before:transition-transform
                                            before:duration-300
                                            before:ease-out
                                            hover:text-white
                                            hover:before:scale-x-100
                                            sm:w-auto
                                            sm:min-w-[235px]"
                                    >
                                        <span class="relative z-10">
                                            {{ $buttonPrimary['title'] }}
                                        </span>

                                        <svg
                                            class="relative z-10 h-4 w-4 transition-transform duration-300 group-hover:translate-x-1"
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
                                @endif

                                @if(
                                    !empty($buttonSecondary['url'])
                                    && !empty($buttonSecondary['title'])
                                )
                                    <a
                                        href="{{ esc_url($buttonSecondary['url']) }}"
                                        target="{{ esc_attr($buttonSecondary['target'] ?: '_self') }}"
                                        @if(($buttonSecondary['target'] ?? '') === '_blank')
                                            rel="noopener noreferrer"
                                        @endif
                                        class="group relative isolate inline-flex min-h-[52px] w-full items-center justify-center gap-5 overflow-hidden rounded-[8px] border border-white/30 bg-white/[0.02] px-7 text-[14px] font-bold text-white transition-colors duration-300
                                            before:absolute
                                            before:inset-0
                                            before:z-0
                                            before:origin-left
                                            before:scale-x-0
                                            before:bg-white/10
                                            before:content-['']
                                            before:transition-transform
                                            before:duration-300
                                            before:ease-out
                                            hover:border-brand
                                            hover:before:scale-x-100
                                            sm:w-auto
                                            sm:min-w-[215px]"
                                    >
                                        <span class="relative z-10">
                                            {{ $buttonSecondary['title'] }}
                                        </span>

                                        <svg
                                            class="relative z-10 h-4 w-4 transition-transform duration-300 group-hover:translate-x-1"
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
                                @endif
                            </div>
                        @endif

                    </div>

                    <div
                        class="relative isolate -mt-[64px] h-[clamp(345px,112vw,430px)] sm:-mt-[48px] sm:h-[440px] md:-mt-[32px] md:h-[485px] lg:mt-0 lg:h-[525px]"
                    >
                        @if(!empty($image['ID']))
                            <div
                                @if(!$isEditor)
                                    data-aos="fade-left"
                                    data-aos-duration="850"
                                    data-aos-delay="120"
                                    data-aos-once="true"
                                    data-aos-easing="ease-out-cubic"
                                @endif
                                class="pointer-events-none absolute inset-0 z-20"
                            >
                                {!! wp_get_attachment_image(
                                    $image['ID'],
                                    'large',
                                    false,
                                    [
                                        'class' => 'pointer-events-none absolute bottom-0 right-[-5%] h-[110%] w-[88%] max-w-none object-contain object-bottom sm:right-[-2%] sm:h-[112%] sm:w-[80%] md:right-0 md:h-[112%] md:w-[76%] lg:h-[114%] lg:w-[74%]',
                                        'loading' => $isEditor ? 'eager' : 'lazy',
                                        'decoding' => 'async',
                                    ]
                                ) !!}
                            </div>
                        @endif

                        @if(!empty($rotatingTexts))
                            <div
                                @if(!$isEditor)
                                    data-aos="fade-right"
                                    data-aos-duration="700"
                                    data-aos-delay="430"
                                    data-aos-once="true"
                                    data-aos-easing="ease-out-cubic"
                                @endif
                                class="absolute left-6 top-[24%] z-30 w-[48%] sm:left-8 sm:top-[27%] sm:w-[42%] md:top-[29%] md:w-[38%] lg:left-10 lg:w-[36%]"
                            >
                                <div
                                    @if(!$isEditor)
                                        x-ref="noteResponsive"
                                    @endif
                                    class="flex items-center justify-start gap-1.5 text-white sm:gap-2"
                                >
                                    <div
                                        class="min-w-0 break-words text-left font-caveat text-[25px] font-medium leading-[1.04] tracking-normal text-white sm:text-[29px] md:text-[33px] lg:text-[36px]"
                                        @if(!$isEditor)
                                            x-html="currentText"
                                        @endif
                                    >
                                        {!! $firstRotatingText !!}
                                    </div>

                                    <div class="shrink-0 scale-x-[-1] text-white">
                                        <svg
                                            class="h-auto w-[46px] sm:w-[56px] md:w-[65px] lg:w-[75px]"
                                            viewBox="0 0 120 80"
                                            fill="none"
                                            aria-hidden="true"
                                        >
                                            <path
                                                d="M112 9C91 10 71 18 55 30C43 39 34 49 28 62"
                                                stroke="currentColor"
                                                stroke-width="2.3"
                                                stroke-linecap="round"
                                            />

                                            <path
                                                d="M28 62L30 48M28 62L42 59"
                                                stroke="currentColor"
                                                stroke-width="2.3"
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                            />
                                        </svg>
                                    </div>
                                </div>
                            </div>
                        @endif

                        @if($captionName || $captionRole)
                            <div
                                @if(!$isEditor)
                                    data-aos="fade-right"
                                    data-aos-duration="650"
                                    data-aos-delay="350"
                                    data-aos-once="true"
                                    data-aos-easing="ease-out-cubic"
                                @endif
                                class="absolute bottom-7 left-6 z-30 flex max-w-[43%] items-start gap-2 sm:bottom-9 sm:left-8 sm:max-w-[40%] md:bottom-10 md:max-w-[35%] lg:bottom-12 lg:left-10"
                            >
                                <div class="min-w-0 text-right">
                                    @if($captionName)
                                        <div class="text-[10px] font-medium leading-[16px] text-white/85 sm:text-[11px] sm:leading-[18px] md:text-[12px]">
                                            {{ $captionName }}
                                        </div>
                                    @endif

                                    @if($captionRole)
                                        <div class="mt-[1px] text-[10px] font-medium leading-[15px] text-white/70 sm:text-[11px] sm:leading-[16px] md:text-[12px]">
                                            {{ $captionRole }}
                                        </div>
                                    @endif
                                </div>

                                <span class="mt-[7px] block h-[2px] w-5 shrink-0 bg-brand sm:w-6"></span>
                            </div>
                        @endif

                    </div>
                </div>

            </div>
        </x-ui.container>
    </section>
</x-block-helper.block-wrapper>