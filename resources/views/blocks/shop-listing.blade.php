@php
    $isEditor = is_admin();
    $prefix = trim((string) ($prefix ?? ''));
    $titleHtml = wp_kses($title ?? '', ['strong' => [], 'b' => [], 'em' => [], 'i' => [], 'br' => [], 'p' => []]);
    $titleHtml = preg_replace('/<\/p>\s*<p[^>]*>/i', '<br>', $titleHtml);
    $titleHtml = preg_replace('/<\/?p\b[^>]*>/i', '', $titleHtml);
    $titleHtml = preg_replace('/<(strong|b)>/i', '<span class="text-brand">', $titleHtml);
    $titleHtml = preg_replace('/<\/(strong|b)>/i', '</span>', $titleHtml);
    $descriptionHtml = wp_kses_post($description ?? '');
    $hasTitle = trim(wp_strip_all_tags($titleHtml)) !== '';
    $hasDescription = trim(wp_strip_all_tags($descriptionHtml)) !== '';
    $hasHeader = $prefix !== '' || $hasTitle || $hasDescription;
@endphp

@if($isEditor || $hasHeader || !empty($shopItems))
    <x-block-helper.block-wrapper :block="$block" :attributes="$attributes">
        <section class="relative bg-white">
            <x-ui.container>
                @if($hasHeader)
                    <div class="max-w-[930px] {{ !empty($shopItems) ? 'mb-8 lg:mb-10' : '' }}">
                        @if($prefix !== '')
                            <x-ui.section-prefix class="mb-4" :data-aos="$isEditor ? null : 'fade-right'" data-aos-duration="500" data-aos-once="true" data-aos-easing="ease-out-cubic">{{ $prefix }}</x-ui.section-prefix>
                        @endif
                        @if($hasTitle)
                            <h2 @if(!$isEditor) data-aos="fade-up" data-aos-duration="650" data-aos-once="true" data-aos-easing="ease-out-cubic" @endif>{!! $titleHtml !!}</h2>
                        @endif
                        @if($hasDescription)
                            <div @if(!$isEditor) data-aos="fade-up" data-aos-duration="650" data-aos-delay="80" data-aos-once="true" data-aos-easing="ease-out-cubic" @endif class="wysiwyg mt-4 max-w-[830px]">{!! $descriptionHtml !!}</div>
                        @endif
                    </div>
                @endif
                @if(!empty($shopItems))
                    <div class="grid grid-cols-1 gap-5 md:grid-cols-2 xl:grid-cols-3 xl:gap-6">
                        @foreach($shopItems as $item)
                            @php
                                $orderUrl = esc_url($item['order_url']);
                                $orderTarget = ($item['order_target'] ?? '') === '_blank' ? '_blank' : '_self';
                                $itemPrice = (string) ($item['price'] ?? '');
                            @endphp
                            <div @if(!$isEditor) data-aos="fade-up" data-aos-duration="650" data-aos-delay="{{ ($loop->index % 3) * 80 }}" data-aos-once="true" data-aos-easing="ease-out-cubic" @endif class="min-w-0">
                                <article class="shop-card content-card flex h-full min-w-0 flex-col rounded-[14px] border border-border bg-white p-3 shadow-card-subtle sm:p-4">
                                    @if($item['image_id'])
                                        <a href="{{ esc_url($item['url']) }}" aria-label="{{ esc_attr(sprintf(__('Zobacz motyw: %s', 'appandsite'), $item['title'])) }}" class="shop-card__image relative block overflow-hidden rounded-[10px] bg-surface focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-brand">
                                            {!! wp_get_attachment_image($item['image_id'], 'large', false, [
                                                'class' => 'shop-card__preview block aspect-[16/10] w-full object-cover object-top transition-icon duration-700 motion-reduce:transition-none',
                                                'alt' => '',
                                                'loading' => 'lazy',
                                                'decoding' => 'async',
                                            ]) !!}
                                            @if($itemPrice !== '')
                                                <span class="shop-card__price-overlay" aria-hidden="true"><span class="shop-price shop-card__price">{{ $itemPrice }}</span></span>
                                            @endif
                                        </a>
                                    @endif
                                    <div class="flex min-w-0 flex-1 flex-col px-1 pb-1 pt-4 sm:px-2">
                                        @if($itemPrice !== '' && $item['image_id'])
                                            <span class="sr-only">{{ __('Cena:', 'appandsite') }} {{ $itemPrice }}</span>
                                        @endif
                                        <h3 class="heading-5"><a href="{{ esc_url($item['url']) }}" class="transition-colors duration-300 hover:text-brand-active focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-brand">{{ $item['title'] }}</a></h3>
                                        @if($item['description'] !== '')
                                            <p class="mt-3 text-[13px] leading-[1.65] text-text sm:text-[14px]">{{ $item['description'] }}</p>
                                        @endif
                                        @if($itemPrice !== '' && !$item['image_id'])
                                            <div class="shop-price shop-card__price-inline"><span class="sr-only">{{ __('Cena:', 'appandsite') }} </span>{{ $itemPrice }}</div>
                                        @endif
                                        <div class="mt-auto pt-5">
                                            <div class="flex flex-wrap items-center gap-3 border-t border-border/70 pt-4">
                                                <a href="{{ esc_url($item['url']) }}" class="btn btn-outline min-h-[44px] grow gap-2 px-3 text-[13px]"><span>{{ __('Zobacz motyw', 'appandsite') }}</span><x-ui.arrow /></a>
                                                @if($orderUrl)
                                                    <a href="{{ $orderUrl }}" target="{{ $orderTarget }}" @if($orderTarget === '_blank') rel="noopener noreferrer" @endif class="btn btn-primary min-h-[44px] grow gap-2 px-3 text-[13px]"><span>{{ $item['order_title'] }}</span><x-ui.arrow /></a>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </article>
                            </div>
                        @endforeach
                    </div>
                @elseif($isEditor)
                    <p class="rounded-[12px] border border-border bg-surface p-6 text-muted">{{ __('Dodaj i opublikuj motywy w sekcji „Sklep”, aby pojawiły się w listingu.', 'appandsite') }}</p>
                @endif
            </x-ui.container>
        </section>
    </x-block-helper.block-wrapper>
@endif
