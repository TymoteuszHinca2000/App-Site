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
    $monitorId = is_array($monitorImage ?? null) ? (int) ($monitorImage['ID'] ?? $monitorImage['id'] ?? 0) : (int) ($monitorImage ?? 0);
    $screenId = is_array($screenImage ?? null) ? (int) ($screenImage['ID'] ?? $screenImage['id'] ?? 0) : (int) ($screenImage ?? 0);
    $monitorUrl = is_array($monitorImage ?? null) ? (string) ($monitorImage['url'] ?? '') : '';
    $screenUrl = is_array($screenImage ?? null) ? (string) ($screenImage['url'] ?? '') : '';
    $monitorIsSvg = (bool) preg_match('/\.svg(?:\?.*)?$/i', $monitorUrl);
    $hasMonitor = $monitorId > 0 || $monitorUrl !== '';
    $hasScreen = $screenId > 0 || $screenUrl !== '';
@endphp

<x-block-helper.block-wrapper :block="$block" :attributes="$attributes">
    @if($isEditor || is_singular('project'))
        <section @if(!$isEditor && $hasMonitor && $hasScreen) x-data="alpProjectScreenPreview()" @endif class="relative isolate bg-white">
            <div aria-hidden="true" class="pointer-events-none absolute inset-0 -z-10">
                <div class="absolute right-[-7%] top-[12%] h-[380px] w-[380px] rounded-full bg-brand/10 blur-[110px] motion-safe:animate-pulse lg:h-[620px] lg:w-[620px]"></div>
                <div class="absolute right-[16%] bottom-[-12%] h-[260px] w-[260px] rounded-full bg-sky-300/15 blur-[100px] lg:h-[440px] lg:w-[440px]"></div>
                <div class="absolute left-[-8%] top-[28%] h-[240px] w-[240px] rounded-full bg-primary-light/5 blur-[95px]"></div>
            </div>

            <x-ui.container>
                <div class="grid grid-cols-1 items-start gap-7 sm:gap-9 lg:grid-cols-[minmax(0,0.82fr)_minmax(0,1.18fr)] lg:gap-10 xl:gap-14">
                    <div class="relative z-10 min-w-0 lg:max-w-[570px]">
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
                            <h2 @if(!$isEditor) data-aos="fade-up" data-aos-duration="650" data-aos-delay="80" data-aos-once="true" data-aos-easing="ease-out-cubic" @endif class="m-0 w-full">{!! $titleHtml !!}</h2>
                        @endif

                        @if($descriptionHtml !== '')
                            <div @if(!$isEditor) data-aos="fade-up" data-aos-duration="650" data-aos-delay="150" data-aos-once="true" data-aos-easing="ease-out-cubic" @endif class="wysiwyg mt-5 [&_p]:!m-0 [&_p+p]:!mt-4">
                                {!! $descriptionHtml !!}
                            </div>
                        @endif

                        @if(!empty($scrollHint))
                            {{-- <div @if(!$isEditor) x-show="canScroll && !hasInteracted" x-transition.opacity.duration.700ms x-cloak @endif class="mt-7 hidden [&[x-cloak]]:hidden items-center gap-3 text-[12px] font-semibold text-muted lg:flex">
                                <span class="relative flex h-[37px] w-[37px] shrink-0 items-center justify-center rounded-full border border-brand/30 bg-brand/10 text-brand-active">
                                    <span aria-hidden="true" class="absolute inset-[-4px] rounded-full border border-brand/25 motion-safe:animate-ping"></span>
                                    <svg class="relative h-[18px] w-[18px] motion-safe:animate-bounce" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                        <rect x="7" y="2" width="10" height="20" rx="5" stroke="currentColor" stroke-width="1.8" />
                                        <path d="M12 7V11" stroke="currentColor" stroke-width="2" stroke-linecap="round" />
                                    </svg>
                                </span>
                                <span>{{ $scrollHint }}</span>
                            </div> --}}
                        @endif
                    </div>

                    <div @if(!$isEditor) data-aos="fade-left" data-aos-duration="800" data-aos-delay="170" data-aos-once="true" data-aos-easing="ease-out-cubic" @endif class="relative min-w-0">
                        @if($hasMonitor && $hasScreen)
                            <div class="pointer-events-none absolute inset-x-[12%] top-[22%] h-[72%] rounded-full bg-brand/15 blur-[70px] motion-safe:animate-pulse sm:blur-[100px]"></div>
                            <div class="relative aspect-[1397/1041] w-full drop-shadow-[0_24px_32px_rgba(6,40,59,0.18)] lg:drop-shadow-[0_36px_42px_rgba(6,40,59,0.2)]">
                                <div data-lenis-prevent class="absolute left-[1.1%] top-[2.4%] z-10 h-[72.06%] w-[97.8%] overflow-hidden bg-white">
                                    <div x-ref="screen" role="region" tabindex="0" aria-label="{{ esc_attr__('Przewijalny podgląd strony internetowej', 'appandsite') }}" class="absolute inset-0 cursor-grab touch-pan-y overflow-x-hidden overflow-y-auto overscroll-y-auto lg:overscroll-y-contain bg-white [scrollbar-width:none] focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-brand [&::-webkit-scrollbar]:hidden" @if(!$isEditor) x-bind:class="isDragging ? 'cursor-grabbing' : 'cursor-grab'" @endif>
                                        <div x-ref="screenContent" class="w-full">
                                            @if($screenId)
                                                {!! wp_get_attachment_image($screenId, 'full', false, [
                                                    'x-ref' => 'pageImage',
                                                    'class' => 'pointer-events-none block h-auto w-full max-w-none select-none',
                                                    'alt' => '',
                                                    'draggable' => 'false',
                                                    'loading' => 'lazy',
                                                    'decoding' => 'async',
                                                ]) !!}
                                            @else
                                                <img x-ref="pageImage" src="{{ esc_url($screenUrl) }}" alt="" draggable="false" loading="lazy" decoding="async" class="pointer-events-none block h-auto w-full max-w-none select-none">
                                            @endif
                                        </div>
                                    </div>

                                    @if(!$isEditor && !empty($scrollHint))
                                        <div x-show="canScroll && !hasInteracted && (isHovering || isTouchDevice)" x-transition:enter="transition-opacity duration-500 ease-out" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="transition-opacity duration-700 ease-out" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" x-cloak aria-hidden="true" class="pointer-events-none absolute inset-0 z-20 flex [&[x-cloak]]:hidden items-center justify-center overflow-hidden bg-sky-300/20 backdrop-blur-[1px]">
                                            <div class="absolute inset-0 bg-gradient-to-br from-primary/35 via-sky-300/10 to-brand/25"></div>
                                            <div class="relative mx-3 flex flex-col items-center gap-2 rounded-[10px] border border-white/40 bg-primary-dark/75 px-3 py-3 text-center shadow-[0_10px_35px_rgba(3,28,43,0.22)] backdrop-blur-sm sm:mx-4 sm:gap-3 sm:rounded-[12px] sm:px-7 sm:py-5">
                                                <span class="relative flex h-[34px] w-[34px] items-center justify-center rounded-full border border-brand/60 bg-brand/15 text-brand sm:h-[46px] sm:w-[46px]">
                                                    <span class="absolute inset-[-5px] rounded-full border border-brand/30 motion-safe:animate-ping"></span>
                                                    <svg class="relative hidden h-[23px] w-[23px] motion-safe:animate-bounce sm:block" viewBox="0 0 24 24" fill="none">
                                                        <rect x="7" y="2" width="10" height="20" rx="5" stroke="currentColor" stroke-width="1.8" />
                                                        <path d="M12 7V11" stroke="currentColor" stroke-width="2" stroke-linecap="round" />
                                                    </svg>
                                                    <svg class="relative h-[18px] w-[18px] motion-safe:animate-bounce sm:hidden" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                                        <path d="M8 13V10.5A1.5 1.5 0 0 1 11 10.5V6A1.5 1.5 0 0 1 14 6V11M14 10A1.5 1.5 0 0 1 17 10V12M17 11.5A1.5 1.5 0 0 1 20 11.5V16C20 19 17.5 21 14.5 21H12C9.5 21 8 19.5 7 17L4.5 13.8A1.5 1.5 0 0 1 6.8 11.9L8 13Z" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" />
                                                    </svg>
                                                </span>
                                                <span class="hidden max-w-[250px] text-[11px] font-bold uppercase leading-[1.5] tracking-[0.08em] text-white sm:block sm:text-[12px]">{{ $scrollHint }}</span>
                                            </div>
                                        </div>
                                    @endif
                                </div>

                                @if($monitorId && !$monitorIsSvg)
                                    {!! wp_get_attachment_image($monitorId, 'full', false, [
                                        'class' => 'pointer-events-none absolute inset-0 z-30 block h-full w-full select-none object-contain',
                                        'alt' => '',
                                        'aria-hidden' => 'true',
                                        'draggable' => 'false',
                                        'loading' => 'eager',
                                        'decoding' => 'async',
                                    ]) !!}
                                @else
                                    <img src="{{ esc_url($monitorUrl) }}" alt="" aria-hidden="true" draggable="false" class="pointer-events-none absolute inset-0 z-30 block h-full w-full select-none object-contain">
                                @endif
                            </div>

                            <div @if(!$isEditor) x-show="canScroll" x-cloak @endif class="relative z-10 -mt-1 flex [&[x-cloak]]:hidden flex-col items-center gap-3 sm:mt-1">
                                <progress @if(!$isEditor) x-bind:value="progressPercent" @endif value="0" max="100" aria-label="{{ esc_attr__('Postęp przewijania strony w monitorze', 'appandsite') }}" class="h-[4px] w-[130px] appearance-none overflow-hidden rounded-full border-0 bg-border/80 text-brand accent-brand [&::-webkit-progress-bar]:bg-border [&::-webkit-progress-value]:bg-brand [&::-moz-progress-bar]:bg-brand sm:w-[175px]"></progress>
                                @if(!empty($scrollHint))
                                    <span @if(!$isEditor) x-show="!hasInteracted" x-transition.opacity.duration.700ms @endif class="text-center text-[11px] font-semibold uppercase tracking-[0.08em] text-muted sm:text-[12px] lg:hidden">
                                        <span class="sm:hidden">{{ __('Przesuń palcem po ekranie monitora', 'appandsite') }}</span>
                                        <span class="hidden sm:inline">{{ $scrollHint }}</span>
                                    </span>
                                @endif
                            </div>
                        @elseif($isEditor)
                            <div class="rounded-[12px] border border-border bg-surface px-5 py-8 text-center text-muted">
                                {{ __('Dodaj przezroczysty monitor i pionowy zrzut strony, aby zobaczyć podgląd.', 'appandsite') }}
                            </div>
                        @endif
                    </div>
                </div>
            </x-ui.container>
        </section>
    @endif
</x-block-helper.block-wrapper>
