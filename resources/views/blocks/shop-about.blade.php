@php
    $isEditor = is_admin();
    $inlineHtml = static function ($value, bool $highlight = false): string {
        $html = wp_kses((string) ($value ?? ''), ['strong' => [], 'b' => [], 'em' => [], 'i' => [], 'br' => [], 'p' => []]);
        $html = preg_replace('/<\/p>\s*<p[^>]*>/i', '<br>', $html);
        $html = preg_replace('/<\/?p\b[^>]*>/i', '', $html);
        if ($highlight) {
            $html = preg_replace('/<(strong|b)>/i', '<span class="text-brand">', $html);
            $html = preg_replace('/<\/(strong|b)>/i', '</span>', $html);
        }

        return trim($html);
    };
    $hasText = static fn (string $html): bool => trim(wp_strip_all_tags($html)) !== '';
    $prefixHtml = $inlineHtml($prefix ?? '');
    $titleHtml = $inlineHtml($title ?? '', true);
    $descriptionHtml = wp_kses_post($description ?? '');
    $panelTitleHtml = $inlineHtml($panelTitle ?? '');
    $panelDescriptionHtml = wp_kses_post($panelDescription ?? '');
    $panelCaptionHtml = wp_kses_post($panelCaption ?? '');
    $panelButtonUrl = esc_url($panelButton['url'] ?? '');
    $panelButtonText = trim((string) ($panelButton['title'] ?? ''));
    $panelButtonTarget = ($panelButton['target'] ?? '') === '_blank' ? '_blank' : '_self';
    $hasPanelButton = $panelButtonUrl && $panelButtonText !== '';
    $hasHeader = $hasText($prefixHtml) || $hasText($titleHtml) || $hasText($descriptionHtml);
    $hasPanel = $hasText($panelTitleHtml) || $hasText($panelDescriptionHtml) || $hasPanelButton || $hasText($panelCaptionHtml);
    $cards = [];
    foreach (($features ?? []) as $feature) {
        if (!is_array($feature)) {
            continue;
        }
        $icon = $feature['icon'] ?? 0;
        $iconId = absint(is_array($icon) ? ($icon['ID'] ?? $icon['id'] ?? 0) : $icon);
        $cardTitle = $inlineHtml($feature['title'] ?? '');
        $cardDescription = wp_kses_post($feature['description'] ?? '');
        if ($iconId || $hasText($cardTitle) || $hasText($cardDescription)) {
            $cards[] = ['icon_id' => $iconId, 'title' => $hasText($cardTitle) ? $cardTitle : '', 'description' => $hasText($cardDescription) ? $cardDescription : ''];
        }
    }
    $hasLeft = $hasHeader || $cards !== [];
@endphp

@if(($isEditor || is_singular(['shop', 'page'])) && ($isEditor || $hasLeft || $hasPanel))
    <x-block-helper.block-wrapper :block="$block" :attributes="$attributes">
        <section class="relative">
            <x-ui.container>
                <div class="grid grid-cols-1 items-stretch gap-7 sm:gap-8 {{ $hasLeft && $hasPanel ? 'md:grid-cols-12 lg:gap-10 xl:gap-12' : '' }}">
                    @if($hasLeft)
                        <div class="flex h-full min-w-0 flex-col {{ $hasPanel ? 'md:col-span-7' : '' }}">
                            @if($hasHeader)
                                <div class="{{ $cards !== [] ? 'mb-5' : '' }}">
                                    @if($hasText($prefixHtml))
                                        <x-ui.section-prefix class="mb-4" :data-aos="$isEditor ? null : 'fade-right'" data-aos-duration="500" data-aos-once="true" data-aos-easing="ease-out-cubic">{!! $prefixHtml !!}</x-ui.section-prefix>
                                    @endif
                                    @if($hasText($titleHtml))
                                        <h2 @if(!$isEditor) data-aos="fade-up" data-aos-duration="650" data-aos-once="true" data-aos-easing="ease-out-cubic" @endif>{!! $titleHtml !!}</h2>
                                    @endif
                                    @if($hasText($descriptionHtml))
                                        <div @if(!$isEditor) data-aos="fade-up" data-aos-duration="650" data-aos-delay="80" data-aos-once="true" data-aos-easing="ease-out-cubic" @endif class="wysiwyg mt-4">{!! $descriptionHtml !!}</div>
                                    @endif
                                </div>
                            @endif
                            @if($cards !== [])
                                <div class="mt-auto rounded-[22px] bg-surface p-3">
                                    <div class="grid grid-cols-1 gap-3 sm:grid-cols-2 2xl:grid-cols-4">
                                        @foreach($cards as $card)
                                            <div @if(!$isEditor) data-aos="fade-up" data-aos-duration="650" data-aos-delay="{{ ($loop->index % 4) * 80 }}" data-aos-once="true" data-aos-easing="ease-out-cubic" @endif class="min-w-0">
                                                <x-ui.feature-card :icon-id="$card['icon_id']" :title-html="$card['title']" :description-html="$card['description']" :compact="true" />
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                        </div>
                    @endif
                    @if($hasPanel)
                        <div @if(!$isEditor) data-aos="fade-left" data-aos-duration="700" data-aos-delay="120" data-aos-once="true" data-aos-easing="ease-out-cubic" @endif class="flex h-full min-w-0 flex-col rounded-[22px] bg-surface p-6 sm:p-8 lg:p-10 {{ $hasLeft ? 'md:col-span-5' : '' }}">
                            @if($hasText($panelTitleHtml))
                                <h3 class="heading-4">{!! $panelTitleHtml !!}</h3>
                            @endif
                            @if($hasText($panelDescriptionHtml))
                                <div class="wysiwyg {{ $hasText($panelTitleHtml) ? 'mt-4' : '' }}">{!! $panelDescriptionHtml !!}</div>
                            @endif
                            @if($hasPanelButton || $hasText($panelCaptionHtml))
                                <div class="mt-auto shrink-0 {{ $hasText($panelTitleHtml) || $hasText($panelDescriptionHtml) ? 'pt-6' : '' }}">
                                    @if($hasPanelButton)
                                        <div>
                                            <a href="{{ $panelButtonUrl }}" target="{{ $panelButtonTarget }}" @if($panelButtonTarget === '_blank') rel="noopener noreferrer" @endif class="btn btn-primary min-h-[50px] w-full gap-3 px-6 text-[14px] sm:w-fit"><span>{{ $panelButtonText }}</span><x-ui.arrow /></a>
                                        </div>
                                    @endif
                                    @if($hasText($panelCaptionHtml))
                                        <div class="wysiwyg italic text-muted [&_p]:text-[14px] [&_p]:text-muted [&_li]:text-[14px] [&_li]:text-muted {{ $hasPanelButton ? 'mt-6' : '' }} {{ $hasText($panelTitleHtml) || $hasText($panelDescriptionHtml) || $hasPanelButton ? 'border-t border-border pt-4' : '' }}">{!! $panelCaptionHtml !!}</div>
                                    @endif
                                </div>
                            @endif
                        </div>
                    @endif
                </div>
            </x-ui.container>
        </section>
    </x-block-helper.block-wrapper>
@endif
