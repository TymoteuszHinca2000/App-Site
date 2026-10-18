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

    $dotCountDesktop = 18;
    $dotCountMobile = 14;

    $dotDelay = 120;
@endphp

<x-block-helper.block-wrapper :block="$block" :attributes="$attributes">
    <section class="relative bg-white">
            <x-ui.container>

                <div class="w-full">

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
                            class="wysiwyg mt-5 w-full [&_p]:!m-0 [&_p+p]:!mt-4"
                        >
                            {!! $descriptionHtml !!}
                        </div>
                    @endif

                </div>

                @if(!empty($steps))
                    <div class="mt-9 grid grid-cols-1 gap-y-8 xl:grid-cols-4 xl:gap-x-8 xl:gap-y-0">

                        @foreach($steps as $step)
                            @php
                                $number = str_pad(
                                    (string) $loop->iteration,
                                    2,
                                    '0',
                                    STR_PAD_LEFT
                                );

                                $isLast = $loop->last;
                            @endphp

                            <div
                                @if(!$isEditor)
                                    data-aos="fade-up"
                                    data-aos-duration="650"
                                    data-aos-delay="{{ $loop->index * 90 }}"
                                    data-aos-once="true"
                                    data-aos-easing="ease-out-cubic"
                                @endif
                                class="min-w-0"
                            >
                                <div class="group relative min-w-0 pb-2 xl:pb-0">

                                    @if(!$isLast)
                                        <div
                                            class="steps-dots pointer-events-none absolute bottom-[-28px] left-[26px] top-[64px] z-0 flex w-[4px] -translate-x-1/2 flex-col items-center justify-between xl:hidden"
                                            aria-hidden="true"
                                        >
                                            @for($dotIndex = 0; $dotIndex < $dotCountMobile; $dotIndex++)
                                                <span
                                                    class="steps-dot h-[4px] w-[4px] shrink-0 rounded-full bg-brand"
                                                    style="animation-delay: {{ $dotIndex * $dotDelay }}ms;"
                                                ></span>
                                            @endfor
                                        </div>

                                        <div
                                            class="steps-dots pointer-events-none absolute left-[74px] right-[-12px] top-[25px] z-0 hidden items-center justify-between xl:flex"
                                            aria-hidden="true"
                                        >
                                            @for($dotIndex = 0; $dotIndex < $dotCountDesktop; $dotIndex++)
                                                <span
                                                    class="steps-dot h-[4px] w-[4px] shrink-0 rounded-full bg-brand"
                                                    style="animation-delay: {{ $dotIndex * $dotDelay }}ms;"
                                                ></span>
                                            @endfor
                                        </div>
                                    @endif

                                    <div class="flex items-start gap-5 xl:block">

                                        <div
                                            class="relative z-20 flex h-[54px] w-[54px] shrink-0 items-center justify-center rounded-full bg-brand
                                                shadow-step-brand
                                                transition-transform
                                                duration-500
                                                ease-out
                                                group-hover:scale-110
                                                motion-reduce:transform-none
                                                motion-reduce:transition-none"
                                        >
                                            <span
                                                class="block text-[13px] font-bold text-white
                                                    transition-transform
                                                    duration-700
                                                    ease-out
                                                    group-hover:rotate-[8deg]
                                                    motion-reduce:transform-none
                                                    motion-reduce:transition-none"
                                            >
                                                {{ $number }}
                                            </span>
                                        </div>

                                        <div class="min-w-0 flex-1 pt-1 xl:mt-5 xl:pt-0">

                                            @if(!empty($step['title']))
                                                <div class="relative inline-block max-w-full pb-[5px]">

                                                    <h3 class="!m-0 !text-[16px] !font-bold !leading-[1.35] sm:!text-[17px]">
                                                        {{ $step['title'] }}
                                                    </h3>

                                                    <span
                                                        class="absolute bottom-0 left-0 h-[2px] w-full origin-left scale-x-0 rounded-full bg-brand
                                                            transition-transform
                                                            duration-500
                                                            ease-out
                                                            group-hover:scale-x-100
                                                            motion-reduce:transition-none"
                                                    ></span>

                                                </div>
                                            @endif

                                            @if(!empty($step['description']))
                                                <p class="mt-2 !mb-0 !text-[14px] !leading-[1.65] text-muted sm:!text-[15px]">
                                                    {!! wp_kses_post($step['description']) !!}
                                                </p>
                                            @endif

                                        </div>

                                    </div>

                                </div>
                            </div>
                        @endforeach

                    </div>
                @endif

            </x-ui.container>
        </section>
</x-block-helper.block-wrapper>