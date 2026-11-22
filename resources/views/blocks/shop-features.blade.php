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
    $prefixHtml = $inlineHtml($prefix ?? '');
    $titleHtml = $inlineHtml($title ?? '', true);
    $descriptionHtml = wp_kses_post($description ?? '');
    $hasPrefix = trim(wp_strip_all_tags($prefixHtml)) !== '';
    $hasTitle = trim(wp_strip_all_tags($titleHtml)) !== '';
    $hasDescription = trim(wp_strip_all_tags($descriptionHtml)) !== '';
    $hasHeader = $hasPrefix || $hasTitle || $hasDescription;
    $cards = [];
    foreach (($features ?? []) as $feature) {
        if (!is_array($feature)) {
            continue;
        }
        $icon = $feature['icon'] ?? 0;
        $iconId = absint(is_array($icon) ? ($icon['ID'] ?? $icon['id'] ?? 0) : $icon);
        $cardTitle = $inlineHtml($feature['title'] ?? '');
        $cardDescription = wp_kses_post($feature['description'] ?? '');
        $hasCardTitle = trim(wp_strip_all_tags($cardTitle)) !== '';
        $hasCardDescription = trim(wp_strip_all_tags($cardDescription)) !== '';
        if ($iconId || $hasCardTitle || $hasCardDescription) {
            $cards[] = ['icon_id' => $iconId, 'title' => $hasCardTitle ? $cardTitle : '', 'description' => $hasCardDescription ? $cardDescription : ''];
        }
    }
@endphp

@if(($isEditor || is_singular(['shop', 'page'])) && ($isEditor || $hasHeader || $cards !== []))
    <x-block-helper.block-wrapper :block="$block" :attributes="$attributes">
        <section class="relative">
            <x-ui.container>
                <div class="rounded-[22px] bg-surface p-5 sm:p-8 lg:p-10">
                    @if($hasHeader)
                        <div class="max-w-[1100px] {{ $cards !== [] ? 'mb-6 lg:mb-8' : '' }}">
                            @if($hasPrefix)
                                <x-ui.section-prefix class="mb-4" :data-aos="$isEditor ? null : 'fade-right'" data-aos-duration="500" data-aos-once="true" data-aos-easing="ease-out-cubic">{!! $prefixHtml !!}</x-ui.section-prefix>
                            @endif
                            @if($hasTitle)
                                <h2 @if(!$isEditor) data-aos="fade-up" data-aos-duration="650" data-aos-once="true" data-aos-easing="ease-out-cubic" @endif>{!! $titleHtml !!}</h2>
                            @endif
                            @if($hasDescription)
                                <div @if(!$isEditor) data-aos="fade-up" data-aos-duration="650" data-aos-delay="80" data-aos-once="true" data-aos-easing="ease-out-cubic" @endif class="wysiwyg mt-4">{!! $descriptionHtml !!}</div>
                            @endif
                        </div>
                    @endif
                    @if($cards !== [])
                        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">
                            @foreach($cards as $card)
                                <div @if(!$isEditor) data-aos="fade-up" data-aos-duration="650" data-aos-delay="{{ ($loop->index % 4) * 80 }}" data-aos-once="true" data-aos-easing="ease-out-cubic" @endif class="min-w-0">
                                    <x-ui.feature-card :icon-id="$card['icon_id']" :title-html="$card['title']" :description-html="$card['description']" />
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </x-ui.container>
        </section>
    </x-block-helper.block-wrapper>
@endif
