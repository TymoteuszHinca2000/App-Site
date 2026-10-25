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
    $hasLocation = !empty($mapLocations);
    $hasKey = !empty($mapConfig['apiKey']);
    $directionsButton = is_array($directionsButton ?? null) ? $directionsButton : [];
    $directionsUrl = trim((string) ($directionsButton['url'] ?? ''));
    $directionsTitle = trim((string) ($directionsButton['title'] ?? ''));
    $directionsTarget = ($directionsButton['target'] ?? '_self') === '_blank' ? '_blank' : '_self';
    $showDirectionsButton = $directionsUrl !== '' && $directionsTitle !== '';
@endphp

<x-block-helper.block-wrapper :block="$block" :attributes="$attributes">
    <section @if(!$isEditor && $hasLocation && $hasKey) x-data="alpGoogleMap(@js($mapConfig))" @endif class="relative bg-white">
        <x-ui.container>
            <div class="grid min-w-0 grid-cols-1 items-start gap-8 lg:grid-cols-[minmax(0,0.96fr)_minmax(0,1.04fr)] lg:gap-10 xl:gap-14">
                <div class="flex min-w-0 flex-col">
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
                        <h2 @if(!$isEditor) data-aos="fade-up" data-aos-duration="650" data-aos-delay="80" data-aos-once="true" data-aos-easing="ease-out-cubic" @endif class="w-full">{!! $titleHtml !!}</h2>
                    @endif

                    @if($descriptionHtml !== '')
                        <div @if(!$isEditor) data-aos="fade-up" data-aos-duration="650" data-aos-delay="160" data-aos-once="true" data-aos-easing="ease-out-cubic" @endif class="wysiwyg mt-4 w-full">{!! $descriptionHtml !!}</div>
                    @endif

                    @if(!empty($items))
                        <div class="mt-7 grid min-w-0 grid-cols-1 gap-3 sm:grid-cols-2 lg:mt-9 xl:grid-cols-3">
                            @foreach($items as $item)
                                <div @if(!$isEditor) data-aos="fade-up" data-aos-duration="650" data-aos-delay="{{ ($loop->index % 4) * 85 }}" data-aos-once="true" data-aos-easing="ease-out-cubic" @endif class="flex h-full min-w-0">
                                    <div class="group/card relative flex h-full w-full min-w-0 flex-col items-start gap-3 rounded-[12px] border border-border bg-white p-4 shadow-card-soft [transform:translate3d(0,0,0)] transition-[transform,border-color,box-shadow,background-color] duration-[900ms] ease-[cubic-bezier(0.22,1,0.36,1)] hover:[transform:translate3d(0,-4px,0)] hover:border-brand/40 hover:shadow-card-hover-soft motion-reduce:transform-none motion-reduce:transition-none">
                                        @if(!empty($item['icon']['ID']))
                                            <span class="flex h-[54px] w-[54px] shrink-0 items-center justify-center rounded-full bg-brand/10 transition-transform duration-700 ease-[cubic-bezier(0.22,1,0.36,1)] group-hover/card:scale-110 lg:h-[58px] lg:w-[58px] motion-reduce:transition-none">
                                                {!! wp_get_attachment_image($item['icon']['ID'], 'thumbnail', false, [
                                                    'class' => 'h-[28px] w-[28px] object-contain transition-transform duration-700 ease-[cubic-bezier(0.22,1,0.36,1)] group-hover/card:rotate-[8deg] lg:h-[30px] lg:w-[30px] motion-reduce:transition-none',
                                                    'alt' => '',
                                                    'aria-hidden' => 'true',
                                                    'loading' => 'lazy',
                                                    'decoding' => 'async',
                                                ]) !!}
                                            </span>
                                        @endif
                                        <div class="min-w-0 w-full">
                                            @if(!empty($item['title']))
                                                <div class="relative inline-block max-w-full pb-[6px]">
                                                    <h5 class="!m-0">{{ $item['title'] }}</h5>
                                                    <span aria-hidden="true" class="pointer-events-none absolute bottom-0 left-0 h-[2px] w-full origin-left scale-x-0 rounded-full bg-brand transition-transform duration-500 ease-out group-hover/card:scale-x-100 motion-reduce:transition-none"></span>
                                                </div>
                                            @endif
                                            @if(!empty($item['description']))
                                                <p class="mt-2 !mb-0 whitespace-pre-line text-[13px] leading-[1.65] text-muted sm:text-[14px]">{{ $item['description'] }}</p>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>

                <div @if(!$isEditor) data-aos="fade-left" data-aos-duration="750" data-aos-delay="100" data-aos-once="true" data-aos-easing="ease-out-cubic" @endif class="relative min-w-0 lg:self-stretch">
                    <div aria-hidden="true" class="pointer-events-none absolute -inset-5 rounded-[30px] bg-brand/10 blur-[60px] sm:-inset-8"></div>

                    <div class="relative isolate overflow-hidden rounded-[16px] border border-border bg-surface shadow-card-soft">
                        <div data-lenis-prevent-wheel @if(!$isEditor && $hasLocation && $hasKey) x-on:wheel.stop @endif class="relative h-[380px] w-full bg-surface sm:h-[470px] lg:h-[540px] xl:h-[560px]">
                            @if(!$isEditor && $hasLocation && $hasKey)
                                <div x-ref="map" class="absolute inset-0 h-full w-full" role="region" aria-label="{{ esc_attr__('Interaktywna mapa lokalizacji', 'appandsite') }}"></div>

                                <div x-show="status === 'loading'" class="pointer-events-none absolute inset-0 z-10 flex items-center justify-center bg-surface/90 text-primary" aria-live="polite">
                                    <div class="flex items-center gap-3 rounded-[10px] border border-border bg-white/90 px-5 py-4 shadow-card-soft">
                                        <span class="h-5 w-5 animate-spin rounded-full border-2 border-brand/20 border-t-brand"></span>
                                        <span class="text-[13px] font-semibold">{{ __('Ładowanie mapy…', 'appandsite') }}</span>
                                    </div>
                                </div>

                                <div x-show="status === 'error'" x-cloak class="absolute inset-0 z-10 flex items-center justify-center bg-surface p-8 text-center [&[x-cloak]]:hidden">
                                    <div class="max-w-[300px]">
                                        <p class="mb-3 text-[14px] font-bold text-primary">{{ __('Nie udało się wczytać mapy Google.', 'appandsite') }}</p>
                                        @if($showDirectionsButton)
                                            <a href="{{ esc_url($directionsUrl) }}" target="{{ esc_attr($directionsTarget) }}" @if($directionsTarget === '_blank') rel="noopener noreferrer" @endif class="inline-flex items-center gap-2 border-b border-brand text-[13px] font-semibold text-brand-active">{{ $directionsTitle }}</a>
                                        @endif
                                    </div>
                                </div>

                                <div x-show="status === 'ready'" x-cloak class="pointer-events-none absolute inset-x-4 top-4 z-10 flex items-start justify-between gap-3 [&[x-cloak]]:hidden sm:inset-x-5 sm:top-5">
                                    <div class="pointer-events-auto max-w-[calc(100%-110px)] rounded-full border border-white/80 bg-white/90 px-4 py-2 shadow-card-soft backdrop-blur-md">
                                        <span class="flex items-center gap-2 text-[10px] font-bold uppercase tracking-[0.12em] text-primary sm:text-[11px]">
                                            <span class="h-2 w-2 shrink-0 animate-pulse rounded-full bg-brand"></span>
                                            {{ __('Nasza lokalizacja', 'appandsite') }}
                                        </span>
                                    </div>
                                    <div class="pointer-events-auto flex shrink-0 flex-col gap-2">
                                        <button type="button" @click="zoom(1)" aria-label="{{ esc_attr__('Powiększ mapę', 'appandsite') }}" class="flex h-10 w-10 items-center justify-center rounded-[9px] border border-white/80 bg-white/95 text-primary shadow-card-soft transition-colors duration-300 hover:bg-brand focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-brand">
                                            <svg class="h-[17px] w-[17px]" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M12 5V19M5 12H19" stroke="currentColor" stroke-width="2" stroke-linecap="round" /></svg>
                                        </button>
                                        <button type="button" @click="zoom(-1)" aria-label="{{ esc_attr__('Pomniejsz mapę', 'appandsite') }}" class="flex h-10 w-10 items-center justify-center rounded-[9px] border border-white/80 bg-white/95 text-primary shadow-card-soft transition-colors duration-300 hover:bg-brand focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-brand">
                                            <svg class="h-[17px] w-[17px]" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M5 12H19" stroke="currentColor" stroke-width="2" stroke-linecap="round" /></svg>
                                        </button>
                                    </div>
                                </div>
                            @else
                                <div class="absolute inset-0 flex flex-col items-center justify-center gap-4 bg-[radial-gradient(circle_at_55%_45%,rgba(16,216,181,0.16),transparent_55%)] p-7 text-center">
                                    <span class="flex h-16 w-16 items-center justify-center rounded-full bg-brand/10 text-brand">
                                        <svg class="h-8 w-8" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M20 10C20 16 12 21 12 21S4 16 4 10a8 8 0 1 1 16 0Z" stroke="currentColor" stroke-width="1.8" /><circle cx="12" cy="10" r="2.5" stroke="currentColor" stroke-width="1.8" /></svg>
                                    </span>
                                    <p class="!m-0 max-w-[300px] text-[14px] font-semibold text-primary">
                                        @if(!$hasKey)
                                            {{ __('Uzupełnij klucz API w Opcje globalne → Google Maps.', 'appandsite') }}
                                        @else
                                            {{ __('Dodaj co najmniej jedną lokalizację w ustawieniach bloku.', 'appandsite') }}
                                        @endif
                                    </p>
                                </div>
                            @endif
                        </div>

                        @if($hasLocation)
                            <div class="relative flex flex-col gap-4 bg-primary-dark px-5 py-5 text-white sm:flex-row sm:items-center sm:justify-between sm:gap-6 sm:px-7 sm:py-6">
                                <div class="min-w-0 flex-1">
                                    @if(!$isEditor && $hasKey)
                                        @if(count($mapLocations) > 1)
                                            <div class="mb-2 flex flex-wrap gap-1.5">
                                                @foreach($mapLocations as $index => $location)
                                                    <button type="button" @click="selectLocation({{ $index }})" :aria-pressed="activeIndex === {{ $index }}" class="rounded-full border px-3 py-1 text-[11px] font-semibold transition-colors duration-300" :class="activeIndex === {{ $index }} ? 'border-brand bg-brand text-primary' : 'border-white/20 bg-white/5 text-white/75 hover:border-brand/60'">{{ $location['title'] }}</button>
                                                @endforeach
                                            </div>
                                        @endif
                                        <h5 class="!m-0 !text-white" x-text="activeLocation.title"></h5>
                                        <p class="mt-1 !mb-0 text-[12px] leading-[1.6] text-white/65 sm:text-[13px]" x-text="activeLocation.address"></p>
                                    @else
                                        <h5 class="!m-0 !text-white">{{ $mapLocations[0]['title'] }}</h5>
                                        <p class="mt-1 !mb-0 text-[12px] leading-[1.6] text-white/65 sm:text-[13px]">{{ $mapLocations[0]['address'] }}</p>
                                    @endif
                                </div>

                                @if($showDirectionsButton)
                                    <a href="{{ esc_url($directionsUrl) }}" target="{{ esc_attr($directionsTarget) }}" @if($directionsTarget === '_blank') rel="noopener noreferrer" @endif class="group relative isolate inline-flex min-h-[46px] shrink-0 items-center justify-center gap-3 overflow-hidden rounded-[8px] bg-brand px-5 text-[13px] font-bold text-primary no-underline before:absolute before:inset-0 before:z-0 before:origin-left before:scale-x-0 before:bg-brand-active before:transition-transform before:duration-500 before:ease-out before:content-[''] hover:before:scale-x-100 hover:text-primary focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-brand motion-reduce:before:transition-none">
                                        <span class="relative z-10">{{ $directionsTitle }}</span>
                                        <svg class="relative z-10 h-4 w-4 shrink-0" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M5 12H19M13 6L19 12L13 18" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" /></svg>
                                    </a>
                                @endif
                            </div>
                        @endif
                    </div>

                    @if(!$isEditor && $hasLocation && $hasKey)
                        <template x-ref="markerTemplate">
                            <div class="group/pin absolute z-[2] h-[54px] w-[54px] -translate-x-1/2 -translate-y-full">
                                <button type="button" data-map-marker-button class="flex h-full w-full origin-bottom cursor-pointer items-center justify-center rounded-full border-[3px] border-white bg-primary-dark text-brand shadow-[0_12px_24px_rgba(3,28,43,0.25)] ring-[6px] ring-brand/20 transition-[scale,background-color,color,box-shadow] duration-700 ease-[cubic-bezier(0.22,1,0.36,1)] group-hover/pin:scale-110 focus-visible:scale-110 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-brand motion-reduce:transition-none">
                                    <svg class="h-[25px] w-[25px]" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M19 10c0 5.3-7 10.2-7 10.2S5 15.3 5 10a7 7 0 1 1 14 0Z" stroke="currentColor" stroke-width="1.8" /><circle cx="12" cy="10" r="2.4" fill="currentColor" /></svg>
                                </button>
                            </div>
                        </template>
                    @endif
                </div>
            </div>
        </x-ui.container>
    </section>
</x-block-helper.block-wrapper>
