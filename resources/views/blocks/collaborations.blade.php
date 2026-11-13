@php
    $isEditor = is_admin();
    $items = is_array($collaborations ?? null) ? $collaborations : [];
    $useSlider = !$isEditor && count($items) > 1;
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
    $sliderLabels = [
        'prevSlideMessage' => __('Poprzednia współpraca', 'appandsite'),
        'nextSlideMessage' => __('Następna współpraca', 'appandsite'),
        'paginationBulletMessage' => __('Przejdź do współpracy {{index}}', 'appandsite'),
    ];
@endphp

@if($isEditor || !empty($items))
    <x-block-helper.block-wrapper :block="$block" :attributes="$attributes">
        <section @if($useSlider) x-data="alpCollaborations(@js($sliderLabels))" @endif class="collaborations" aria-label="{{ esc_attr__('Współprace', 'appandsite') }}">
            <x-ui.container>
                @if(!empty($prefix) || $titleHtml !== '' || $descriptionHtml !== '')
                    <div class="collaborations__heading">
                        @if(!empty($prefix))
                            <p class="collaborations__prefix"><span aria-hidden="true" class="collaborations__prefix-mark">✦</span>{{ $prefix }}</p>
                        @endif
                        @if($titleHtml !== '')
                            <h2>{!! $titleHtml !!}</h2>
                        @endif
                        @if($descriptionHtml !== '')
                            <div class="collaborations__description wysiwyg">{!! $descriptionHtml !!}</div>
                        @endif
                    </div>
                @endif

                @if(!empty($items))
                    <div @if($useSlider) x-ref="swiper" @endif class="collaborations__viewport{{ $useSlider ? ' swiper' : '' }}">
                        <div class="{{ $useSlider ? 'swiper-wrapper' : 'collaborations__list' }}">
                            @foreach($items as $collaboration)
                                <div class="collaborations__slide{{ $useSlider ? ' swiper-slide' : '' }}">
                                    @include('partials.collaboration-card', ['collaboration' => $collaboration, 'compact' => false, 'isEditor' => $isEditor])
                                </div>
                            @endforeach
                        </div>
                    </div>

                    @if($useSlider)
                        <div class="collaborations__controls">
                            <button type="button" x-ref="prev" class="btn btn-outline slider-arrow" aria-label="{{ esc_attr__('Poprzednia współpraca', 'appandsite') }}"><x-ui.arrow direction="left" /></button>
                            <div x-ref="pagination" class="collaborations__pagination swiper-pagination"></div>
                            <button type="button" x-ref="next" class="btn btn-outline slider-arrow" aria-label="{{ esc_attr__('Następna współpraca', 'appandsite') }}"><x-ui.arrow /></button>
                        </div>
                    @endif
                @elseif($isEditor)
                    <p class="collaborations__empty">{{ __('Dodaj współprace w ustawieniach globalnych, aby wyświetlić ten blok.', 'appandsite') }}</p>
                @endif
            </x-ui.container>
        </section>
    </x-block-helper.block-wrapper>
@endif
