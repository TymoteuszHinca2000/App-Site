@php
    $isEditor = is_admin();
    $prefix = trim((string) ($prefix ?? ''));
    $titleHtml = wp_kses($title ?? '', ['strong' => [], 'b' => [], 'em' => [], 'i' => [], 'br' => [], 'p' => []]);
    $titleHtml = preg_replace('/<\/p>\s*<p[^>]*>/i', '<br>', $titleHtml);
    $titleHtml = preg_replace('/<\/?p\b[^>]*>/i', '', $titleHtml);
    $titleHtml = preg_replace('/<(strong|b)>/i', '<span class="text-brand">', $titleHtml);
    $titleHtml = preg_replace('/<\/(strong|b)>/i', '</span>', $titleHtml);
    $hasTitle = trim(wp_strip_all_tags($titleHtml)) !== '';
    $descriptionHtml = wp_kses_post($description ?? '');
    $hasDescription = trim(wp_strip_all_tags($descriptionHtml)) !== '';
    $priceText = sanitize_text_field((string) ($price ?? ''));
    $hasPrice = $priceText !== '';
    $orderUrl = esc_url($orderButton['url'] ?? '');
    $orderText = (string) ($orderButton['title'] ?? '');
    $orderTarget = ($orderButton['target'] ?? '') === '_blank' ? '_blank' : '_self';
    $secondaryUrl = esc_url($buttonSecondary['url'] ?? '');
    $secondaryText = trim((string) ($buttonSecondary['title'] ?? ''));
    $secondaryTarget = ($buttonSecondary['target'] ?? '') === '_blank' ? '_blank' : '_self';
    $hasSecondary = $secondaryUrl && $secondaryText !== '';
    $hasContent = $prefix !== '' || $hasTitle || $hasDescription || $hasPrice || $orderUrl || $hasSecondary;
@endphp

@if(($isEditor || is_singular(['shop', 'page'])) && ($isEditor || $hasContent))
    <x-block-helper.block-wrapper :block="$block" :attributes="$attributes">
        <section class="relative">
            <x-ui.container>
                <div class="shop-cta consultation-gradient relative isolate overflow-hidden rounded-[22px] px-6 py-7 sm:p-8 lg:px-10 lg:py-9">
                    <div class="shop-cta__shape pointer-events-none absolute right-[10%] top-1/2 -z-10 h-[260px] w-[260px] -translate-y-1/2 rotate-45 rounded-[22px] border border-brand/10 bg-brand/5" aria-hidden="true"></div>
                    <div class="shop-cta__layout relative {{ $hasPrice ? 'shop-cta__layout--priced' : '' }}">
                        @if($prefix !== '' || $hasTitle || $hasDescription)
                            <div class="shop-cta__content min-w-0 max-w-[930px]">
                                @if($prefix !== '')
                                    <x-ui.section-prefix class="mb-4 !text-white/60" :data-aos="$isEditor ? null : 'fade-right'" data-aos-duration="500" data-aos-once="true" data-aos-easing="ease-out-cubic">{{ $prefix }}</x-ui.section-prefix>
                                @endif
                                @if($hasTitle)
                                    <h2 @if(!$isEditor) data-aos="fade-up" data-aos-duration="650" data-aos-once="true" data-aos-easing="ease-out-cubic" @endif class="text-white">{!! $titleHtml !!}</h2>
                                @endif
                                @if($hasDescription)
                                    <div @if(!$isEditor) data-aos="fade-up" data-aos-duration="650" data-aos-delay="80" data-aos-once="true" data-aos-easing="ease-out-cubic" @endif class="wysiwyg mt-4 text-white [&_p]:!text-white [&_li]:!text-white [&_a]:!text-white [&_strong]:!text-white [&_b]:!text-white">{!! $descriptionHtml !!}</div>
                                @endif
                            </div>
                        @endif
                        @if($hasPrice)
                            <div @if(!$isEditor) data-aos="fade-up" data-aos-duration="650" data-aos-delay="120" data-aos-once="true" data-aos-easing="ease-out-cubic" @endif class="shop-price shop-cta__price"><span class="sr-only">{{ __('Cena:', 'appandsite') }} </span>{{ $priceText }}</div>
                        @endif
                        @if($orderUrl || $hasSecondary)
                            <div @if(!$isEditor) data-aos="fade-up" data-aos-duration="650" data-aos-delay="160" data-aos-once="true" data-aos-easing="ease-out-cubic" @endif class="flex w-full flex-col gap-3 sm:flex-row sm:flex-wrap lg:w-auto lg:shrink-0">
                                @if($orderUrl)
                                    <a href="{{ $orderUrl }}" target="{{ $orderTarget }}" @if($orderTarget === '_blank') rel="noopener noreferrer" @endif class="btn btn-primary min-h-[50px] w-full gap-3 px-6 text-[14px] sm:w-auto"><span>{{ $orderText }}</span><x-ui.arrow /></a>
                                @endif
                                @if($hasSecondary)
                                    <a href="{{ $secondaryUrl }}" target="{{ $secondaryTarget }}" @if($secondaryTarget === '_blank') rel="noopener noreferrer" @endif class="btn btn-light-outline min-h-[50px] w-full gap-3 border-white/30 bg-white/[0.02] px-6 text-[14px] sm:w-auto"><span>{{ $secondaryText }}</span><x-ui.arrow /></a>
                                @endif
                            </div>
                        @endif
                    </div>
                </div>
            </x-ui.container>
        </section>
    </x-block-helper.block-wrapper>
@endif
