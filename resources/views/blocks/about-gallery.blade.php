
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

    $validGallery = array_values(array_filter($galleryItems ?? [], function ($item) {
        return !empty($item['image']['ID']) && wp_get_attachment_image_url($item['image']['ID'], 'full');
    }));

    $galleryCount = count($validGallery);
    $hasLightbox = $galleryCount > 1;
    $reverseLayout = (bool) ($reverseLayout ?? false);
    $compactGallery = $galleryCount <= 3;

    $desktopLayout = $galleryCount > 0
        ? 'min-[1401px]:grid-cols-2 min-[1401px]:grid-rows-[auto_minmax(0,1fr)] min-[1401px]:min-h-[600px] min-[1401px]:gap-y-0'
        : '';

    $contentColumn = $galleryCount > 0
        ? ($reverseLayout ? 'min-[1401px]:col-start-2' : 'min-[1401px]:col-start-1')
        : '';

    $galleryColumn = $reverseLayout ? 'min-[1401px]:col-start-1' : 'min-[1401px]:col-start-2';

    $featuresPosition = $galleryCount > 0
        ? 'min-[1401px]:row-start-2 min-[1401px]:self-end min-[1401px]:pt-10'
        : '';

    if ($galleryCount > 1) {
        $featuresPosition .= ' min-[1401px]:relative min-[1401px]:bottom-9';
    }

    $contentAos = $reverseLayout ? 'fade-left' : 'fade-right';
    $galleryAos = $reverseLayout ? 'fade-right' : 'fade-left';

    $galleryHeightClass = $compactGallery
        ? 'sm:h-[480px] md:h-[540px] lg:h-[580px] xl:h-[600px] min-[1401px]:h-auto min-[1401px]:min-h-[600px]'
        : 'sm:min-h-[480px] min-[1401px]:min-h-[600px]';

    $galleryGridClass = $compactGallery
        ? 'sm:absolute sm:inset-0 sm:grid-rows-2'
        : 'sm:auto-rows-[minmax(210px,1fr)]';
@endphp

<x-block-helper.block-wrapper :block="$block" :attributes="$attributes">
    <section class="relative bg-white">
        <x-ui.container>
            <div class="grid grid-cols-1 items-stretch gap-x-0 gap-y-5 sm:gap-y-8 lg:gap-y-10 min-[1401px]:gap-x-10 min-[1600px]:gap-x-14 min-[1800px]:gap-x-20 {{ $desktopLayout }}">
                <div class="min-w-0 min-[1401px]:row-start-1 {{ $contentColumn }}">
                    @if(!empty($prefix))
                        <div @if(!$isEditor) data-aos="{{ $contentAos }}" data-aos-duration="550" data-aos-once="true" data-aos-easing="ease-out-cubic" @endif class="mb-4 flex items-center gap-3 text-[11px] font-semibold uppercase tracking-[0.14em] text-muted sm:mb-5 sm:text-[12px]">
                            <span class="flex h-[24px] w-[24px] shrink-0 items-center justify-center rounded-[6px] border border-brand/60 bg-brand/10 text-brand">
                                <svg class="h-[13px] w-[13px]" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                    <path d="M12 3L13.6 8.4L19 10L13.6 11.6L12 17L10.4 11.6L5 10L10.4 8.4L12 3Z" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round" />
                                    <path d="M18.5 15.5L19.2 17.8L21.5 18.5L19.2 19.2L18.5 21.5L17.8 19.2L15.5 18.5L17.8 17.8L18.5 15.5Z" stroke="currentColor" stroke-width="1.5" stroke-linejoin="round" />
                                </svg>
                            </span>
                            <span>{{ $prefix }}</span>
                        </div>
                    @endif

                    @if(!empty($titleHtml))
                        <h2 @if(!$isEditor) data-aos="fade-up" data-aos-duration="650" data-aos-delay="80" data-aos-once="true" data-aos-easing="ease-out-cubic" @endif>{!! $titleHtml !!}</h2>
                    @endif

                    @if(!empty($descriptionHtml))
                        <div @if(!$isEditor) data-aos="fade-up" data-aos-duration="650" data-aos-delay="140" data-aos-once="true" data-aos-easing="ease-out-cubic" @endif class="wysiwyg mt-4 sm:mt-5">{!! $descriptionHtml !!}</div>
                    @endif
                </div>

                @if(!empty($features))
                    <div class="grid min-w-0 grid-cols-2 gap-x-4 gap-y-6 sm:grid-cols-4 sm:gap-x-6 lg:gap-x-8 {{ $featuresPosition }} {{ $contentColumn }}">
                        @foreach($features as $feature)
                            <div @if(!$isEditor) data-aos="fade-up" data-aos-duration="600" data-aos-delay="{{ ($loop->index % 4) * 70 }}" data-aos-once="true" data-aos-easing="ease-out-cubic" @endif class="group flex min-w-0 flex-col items-start">
                                @if(!empty($feature['icon']['ID']))
                                    <div class="mb-3 flex h-[58px] w-[58px] shrink-0 items-center justify-center rounded-full bg-brand/10 transition-transform duration-500 ease-out group-hover:scale-110 lg:h-[62px] lg:w-[62px]">
                                        {!! wp_get_attachment_image($feature['icon']['ID'], 'thumbnail', false, [
                                            'class' => 'h-[30px] w-[30px] object-contain transition-transform duration-700 ease-out group-hover:rotate-[8deg] lg:h-[32px] lg:w-[32px]',
                                            'alt' => '',
                                            'aria-hidden' => 'true',
                                            'loading' => 'lazy',
                                            'decoding' => 'async',
                                        ]) !!}
                                    </div>
                                @endif
                                <div class="flex w-full min-w-0 flex-col items-start gap-1">
                                    @if(!empty($feature['title']))
                                        <div class="relative inline-block max-w-full pb-[5px]">
                                            <h6>{{ $feature['title'] }}</h6>
                                            <span class="absolute bottom-0 left-0 h-[2px] w-full origin-left scale-x-0 rounded-full bg-brand transition-transform duration-500 ease-out group-hover:scale-x-100"></span>
                                        </div>
                                    @endif
                                    @if(!empty($feature['description']))
                                        <small class="block whitespace-pre-line text-muted">{{ trim((string) $feature['description']) }}</small>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif

                @if($galleryCount > 0)
                    <div @if(!$isEditor && $hasLightbox) x-data="alpAboutGallery()" @endif @if(!$isEditor) data-aos="{{ $galleryAos }}" data-aos-duration="750" data-aos-delay="100" data-aos-once="true" data-aos-easing="ease-out-cubic" @endif class="relative min-w-0 self-stretch min-[1401px]:row-start-1 min-[1401px]:row-span-2 {{ $galleryColumn }} {{ $galleryHeightClass }}">
                        <div class="grid grid-cols-1 gap-3 sm:grid-cols-2 sm:gap-4 {{ $galleryGridClass }}">
                            @foreach($validGallery as $index => $card)
                                @php
                                    $imageId = (int) $card['image']['ID'];
                                    $photoUrl = wp_get_attachment_image_url($imageId, 'full');
                                    $photoAlt = get_post_meta($imageId, '_wp_attachment_image_alt', true) ?: ($card['title'] ?? '');
                                    $cardTitle = trim((string) ($card['title'] ?? ''));
                                    $cardDescription = trim((string) ($card['description'] ?? ''));
                                    $cardHasDescription = $cardDescription !== '';
                                    $cardHasContent = !empty($card['icon']['ID']) || $cardTitle !== '' || $cardHasDescription;

                                    if ($index === 0) {
                                        $cardGridClass = $galleryCount === 1
                                            ? 'aspect-[4/5] sm:col-span-2 sm:row-span-2 sm:aspect-auto'
                                            : 'aspect-[4/5] sm:row-span-2 sm:aspect-auto';
                                    } elseif ($galleryCount === 2 && $index === 1) {
                                        $cardGridClass = 'aspect-[16/10] sm:row-span-2 sm:aspect-auto';
                                    } else {
                                        $cardGridClass = 'aspect-[16/10] sm:aspect-auto';
                                    }

                                    $cardClasses = 'group/photo relative block min-h-0 min-w-0 overflow-hidden rounded-[14px] border border-border bg-surface shadow-card-soft transition-[border-color,box-shadow] duration-500 hover:border-brand/50 hover:shadow-card-hover-soft';
                                    $cardClasses .= $hasLightbox ? ' focus-visible:outline-2 focus-visible:outline-offset-4 focus-visible:outline-brand' : '';
                                    $cardClasses .= ' ' . $cardGridClass;
                                @endphp

                                @if($hasLightbox)
                                    <a href="{{ esc_url($photoUrl) }}" data-about-gallery-image @if(!$isEditor) @click.prevent="open({{ $index }})" @endif aria-label="{{ esc_attr($cardTitle ?: __('Otwórz zdjęcie', 'appandsite')) }}" class="{{ $cardClasses }}">
                                @else
                                    <div class="{{ $cardClasses }}">
                                @endif
                                    {!! wp_get_attachment_image($imageId, 'large', false, [
                                        'class' => 'absolute inset-0 h-full w-full object-cover transition-transform duration-[1000ms] ease-out group-hover/photo:scale-[1.055]',
                                        'alt' => $photoAlt,
                                        'loading' => 'lazy',
                                        'decoding' => 'async',
                                    ]) !!}

                                    <span class="pointer-events-none absolute inset-0 bg-gradient-to-t from-primary-dark/95 via-primary-dark/20 to-transparent"></span>

                                    @if($hasLightbox)
                                        <span class="pointer-events-none absolute right-3 top-3 grid h-9 w-9 place-items-center rounded-full border border-white/35 bg-primary-dark/40 text-white backdrop-blur-sm transition-[background-color,border-color] duration-500 group-hover/photo:border-brand group-hover/photo:bg-brand/30 sm:right-4 sm:top-4 sm:h-10 sm:w-10">
                                            <svg class="h-[18px] w-[18px]" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                                <path d="M9 4H4V9M15 4H20V9M4 15V20H9M20 15V20H15" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" />
                                            </svg>
                                        </span>
                                    @endif

                                    @if($cardHasContent)
                                        <span class="pointer-events-none absolute inset-x-0 bottom-0 flex {{ $cardHasDescription ? 'items-start' : 'items-center' }} gap-2 p-4 sm:gap-3 sm:p-4 xl:p-5">
                                            @if(!empty($card['icon']['ID']))
                                                <span class="{{ $cardHasDescription ? 'mt-0.5' : '' }} flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-brand/20 sm:h-9 sm:w-9">
                                                    {!! wp_get_attachment_image($card['icon']['ID'], 'thumbnail', false, [
                                                        'class' => 'h-[18px] w-[18px] object-contain sm:h-[20px] sm:w-[20px]',
                                                        'alt' => '',
                                                        'aria-hidden' => 'true',
                                                        'loading' => 'lazy',
                                                        'decoding' => 'async',
                                                    ]) !!}
                                                </span>
                                            @endif
                                            <span class="flex min-w-0 flex-col gap-1">
                                                @if($cardTitle !== '')
                                                    <span class="block text-[13px] font-semibold leading-[1.4] text-white sm:text-[14px] xl:text-[15px]">{{ $cardTitle }}</span>
                                                @endif
                                                @if($cardHasDescription)
                                                    <small class="block whitespace-pre-line text-white/80">{{ $cardDescription }}</small>
                                                @endif
                                            </span>
                                        </span>
                                    @endif

                                @if($hasLightbox)
                                    </a>
                                @else
                                    </div>
                                @endif
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>
        </x-ui.container>
    </section>
</x-block-helper.block-wrapper>
