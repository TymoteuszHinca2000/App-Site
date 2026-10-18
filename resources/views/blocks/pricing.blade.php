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

    $priceTitleHtml = wp_kses($price_title ?? '', [
        'strong' => [],
        'b' => [],
        'em' => [],
        'i' => [],
        'br' => [],
        'p' => [],
    ]);

    $priceTitleHtml = preg_replace(
        '/<\/p>\s*<p[^>]*>/i',
        '<br>',
        $priceTitleHtml
    );

    $priceTitleHtml = preg_replace(
        '/<\/?p\b[^>]*>/i',
        '',
        $priceTitleHtml
    );

    $priceTitleHtml = trim($priceTitleHtml);

    $priceContentHtml = wp_kses_post(
        $price_content ?? ''
    );
@endphp

<x-block-helper.block-wrapper :block="$block" :attributes="$attributes">
    <section class="relative left-1/2 w-screen -translate-x-1/2 overflow-hidden bg-primary-dark">

        <div class="absolute inset-0 pricing-section-ambient"></div>

        <x-ui.container>

            <div class="relative z-10 grid gap-8 py-10 sm:py-12 md:py-14 lg:grid-cols-[minmax(0,1fr)_minmax(380px,0.8fr)] lg:items-center lg:gap-14 lg:py-16 xl:gap-20 xl:py-20">

                <div class="max-w-[680px]">

                    @if(!empty($prefix))

                        <div
                            @if(!$isEditor)
                                data-aos="fade-right"
                                data-aos-duration="500"
                                data-aos-once="true"
                                data-aos-easing="ease-out-cubic"
                            @endif
                            class="mb-4 flex items-center gap-3 text-[11px] font-semibold uppercase tracking-[0.14em] text-white/65 sm:mb-5 sm:text-[12px]"
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
                            class="!m-0 max-w-[680px] !text-white"
                        >
                            {!! $titleHtml !!}
                        </h2>

                    @endif

                    @if(!empty($descriptionHtml))

                        <div
                            @if(!$isEditor)
                                data-aos="fade-up"
                                data-aos-duration="650"
                                data-aos-delay="140"
                                data-aos-once="true"
                                data-aos-easing="ease-out-cubic"
                            @endif
                            class="wysiwyg mt-4 max-w-[620px] text-white/75 sm:mt-5 [&_a]:!text-brand [&_li]:!text-white/75 [&_p]:!m-0 [&_p]:!text-white/75 [&_p+p]:!mt-4 [&_strong]:!text-white"
                        >
                            {!! $descriptionHtml !!}
                        </div>

                    @endif

                    @if(!empty($button['url']) && !empty($button['title']))

                        <div
                            @if(!$isEditor)
                                data-aos="fade-up"
                                data-aos-duration="650"
                                data-aos-delay="200"
                                data-aos-once="true"
                                data-aos-easing="ease-out-cubic"
                            @endif
                            class="mt-6 sm:mt-8"
                        >

                            <a
                                href="{{ esc_url($button['url']) }}"
                                target="{{ esc_attr($button['target'] ?: '_self') }}"
                                @if(($button['target'] ?? '') === '_blank')
                                    rel="noopener noreferrer"
                                @endif
                                class="group relative isolate inline-flex min-h-[46px] items-center justify-center gap-3 overflow-hidden rounded-[8px] bg-brand px-5 text-[13px] font-bold text-primary transition-colors duration-300 before:absolute before:inset-0 before:z-0 before:origin-left before:scale-x-0 before:bg-brand-active before:content-[''] before:transition-transform before:duration-300 before:ease-out hover:text-white hover:before:scale-x-100 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-brand motion-reduce:before:transition-none sm:min-h-[52px] sm:gap-4 sm:px-7 sm:text-[14px]"
                            >

                                <span class="relative z-10">
                                    {{ $button['title'] }}
                                </span>

                                <svg
                                    class="relative z-10 h-4 w-4"
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

                <div
                    @if(!$isEditor)
                        data-aos="fade-left"
                        data-aos-duration="700"
                        data-aos-delay="120"
                        data-aos-once="true"
                        data-aos-easing="ease-out-cubic"
                    @endif
                    class="group relative overflow-hidden rounded-[14px] border border-white/20 bg-primary-light/80 p-5 shadow-[0_18px_50px_rgba(0,0,0,0.12)] backdrop-blur-sm sm:p-7 lg:p-8"
                >

                    <div class="pointer-events-none absolute inset-0 z-0 bg-brand/[0.045] opacity-0 transition-opacity duration-[1000ms] ease-[cubic-bezier(0.22,1,0.36,1)] group-hover:opacity-100"></div>

                    <div class="pointer-events-none absolute inset-0 z-0 rounded-[14px] border border-brand opacity-0 transition-opacity duration-[1000ms] ease-[cubic-bezier(0.22,1,0.36,1)] group-hover:opacity-100"></div>

                    <div class="pointer-events-none absolute inset-0 z-0 opacity-0 transition-opacity duration-[1200ms] ease-[cubic-bezier(0.22,1,0.36,1)] group-hover:opacity-100">
                        <div class="absolute right-[-10%] top-[-45%] h-[380px] w-[380px] rounded-full bg-brand/15 blur-[110px]"></div>
                    </div>

                    <div class="pointer-events-none absolute bottom-[-45%] left-[-20%] z-0 h-[280px] w-[280px] rounded-full bg-brand/[0.05] opacity-0 blur-[100px] transition-opacity duration-[1200ms] ease-[cubic-bezier(0.22,1,0.36,1)] group-hover:opacity-100"></div>

                    <div class="pointer-events-none absolute inset-x-0 top-0 z-10 h-px bg-gradient-to-r from-transparent via-brand/30 to-transparent opacity-60 transition-[opacity] duration-[1000ms] ease-[cubic-bezier(0.22,1,0.36,1)] group-hover:opacity-100"></div>

                    <div class="pointer-events-none absolute right-4 top-4 z-[2] sm:right-6 sm:top-6 lg:right-7 lg:top-7">

                        <div class="relative flex h-[125px] w-[125px] rotate-[30deg] items-center justify-center transition-transform duration-[1100ms] ease-[cubic-bezier(0.22,1,0.36,1)] group-hover:rotate-[38deg] group-hover:scale-[1.07] motion-reduce:transition-none sm:h-[150px] sm:w-[150px] lg:h-[165px] lg:w-[165px]">

                            <svg
                                class="absolute inset-0 h-full w-full text-brand/25 transition-[color,filter] duration-[1000ms] ease-[cubic-bezier(0.22,1,0.36,1)] group-hover:text-brand/40 pricing-stamp-symbol"
                                viewBox="0 0 200 200"
                                fill="currentColor"
                                aria-hidden="true"
                            >
                                <path d="M100 8L118 32L148 20L151 50L181 53L169 80L192 100L169 120L181 147L151 150L148 180L118 168L100 192L82 168L52 180L49 150L19 147L31 120L8 100L31 80L19 53L49 50L52 20L82 32L100 8Z" />
                            </svg>

                            <span class="relative z-10 text-center font-caveat text-[20px] font-semibold uppercase leading-[0.9] tracking-[0.05em] text-white transition-[color,text-shadow] duration-[1000ms] ease-[cubic-bezier(0.22,1,0.36,1)] group-hover:drop-shadow-[0_0_8px_rgba(255,255,255,0.30)] sm:text-[22px] lg:text-[24px]">
                                TOP<br>OFFER
                            </span>

                        </div>

                    </div>

                    <div class="relative z-10">

                        @if(!empty($price_prefix))

                            <div class="mb-2 text-[14px] font-semibold text-brand sm:text-[15px]">
                                {{ $price_prefix }}
                            </div>

                        @endif

                        @if(!empty($priceTitleHtml))

                            <h3 class="!m-0 max-w-[70%] !text-[22px] !font-bold !leading-[1.25] !text-white sm:!text-[26px] lg:!text-[28px]">
                                {!! $priceTitleHtml !!}
                            </h3>

                        @endif

                        @if(!empty($priceContentHtml))

                            <div class="wysiwyg mt-5 text-white/80 sm:mt-6 [&_a]:!text-brand [&_ol]:!m-0 [&_ol]:!p-0 [&_p]:!m-0 [&_strong]:!text-white [&_ul]:!m-0 [&_ul]:!flex [&_ul]:!list-none [&_ul]:!flex-col [&_ul]:!gap-3 [&_ul]:!p-0 [&_li]:!relative [&_li]:!m-0 [&_li]:!pl-6 [&_li]:!text-[13px] [&_li]:!leading-[1.55] [&_li]:!text-white/80 sm:[&_li]:!text-[14px] [&_li]:before:absolute [&_li]:before:left-0 [&_li]:before:top-[0.5em] [&_li]:before:h-[9px] [&_li]:before:w-[9px] [&_li]:before:rounded-full [&_li]:before:border-2 [&_li]:before:border-brand [&_li]:before:content-['']">
                                {!! $priceContentHtml !!}
                            </div>

                        @endif

                    </div>

                </div>

            </div>

        </x-ui.container>

    </section>
</x-block-helper.block-wrapper>