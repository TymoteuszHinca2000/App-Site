@php
    $isEditor = is_admin();
    $backgroundId = absint(is_array($background ?? null) ? ($background['ID'] ?? $background['id'] ?? 0) : ($background ?? 0));
    $prefix = trim((string) ($prefix ?? ''));
    $titleHtml = wp_kses($title ?? '', ['strong' => [], 'b' => [], 'em' => [], 'i' => [], 'br' => [], 'p' => []]);
    $titleHtml = preg_replace('/<\/p>\s*<p[^>]*>/i', '<br>', $titleHtml);
    $titleHtml = preg_replace('/<\/?p\b[^>]*>/i', '', $titleHtml);
    $titleHtml = preg_replace('/<(strong|b)>/i', '<span class="text-brand">', $titleHtml);
    $titleHtml = preg_replace('/<\/(strong|b)>/i', '</span>', $titleHtml);
    $hasTitle = trim(wp_strip_all_tags($titleHtml)) !== '';
    $descriptionHtml = wp_kses_post($description ?? '');
    $hasDescription = trim(wp_strip_all_tags($descriptionHtml)) !== '';
@endphp

@if($isEditor || $backgroundId || $prefix !== '' || $hasTitle || $hasDescription)
    <x-block-helper.block-wrapper :block="$block" :attributes="$attributes">
        <section class="relative left-1/2 w-screen -translate-x-1/2 overflow-hidden bg-primary-dark">
            @if($backgroundId)
                <div class="pointer-events-none absolute inset-0" aria-hidden="true">
                    {!! wp_get_attachment_image($backgroundId, 'full', false, [
                        'class' => 'block h-full w-full object-cover object-center',
                        'alt' => '',
                        'loading' => 'lazy',
                        'decoding' => 'async',
                    ]) !!}
                </div>
            @endif
            <div class="pointer-events-none absolute inset-0 full-cta-shade-horizontal" aria-hidden="true"></div>
            <div class="pointer-events-none absolute inset-0 full-cta-shade-vertical" aria-hidden="true"></div>

            <x-ui.container>
                <div class="relative z-10 flex min-h-[320px] items-center py-8 sm:min-h-[360px] sm:py-10 lg:min-h-[400px] lg:py-12">
                    <div class="w-full max-w-[680px]">
                        @if($prefix !== '')
                            <x-ui.section-prefix class="mb-4 !text-white/60" :data-aos="$isEditor ? null : 'fade-right'" data-aos-duration="500" data-aos-once="true" data-aos-easing="ease-out-cubic">{{ $prefix }}</x-ui.section-prefix>
                        @endif
                        @if($hasTitle)
                            <h2 @if(!$isEditor) data-aos="fade-up" data-aos-duration="650" data-aos-delay="80" data-aos-once="true" data-aos-easing="ease-out-cubic" @endif class="text-white">{!! $titleHtml !!}</h2>
                        @endif
                        @if($hasDescription)
                            <div @if(!$isEditor) data-aos="fade-up" data-aos-duration="650" data-aos-delay="150" data-aos-once="true" data-aos-easing="ease-out-cubic" @endif class="wysiwyg mt-5 text-white [&_a]:!text-white [&_p]:!text-white [&_li]:!text-white [&_strong]:!text-white [&_b]:!text-white">{!! $descriptionHtml !!}</div>
                        @endif
                    </div>
                </div>
            </x-ui.container>
        </section>
    </x-block-helper.block-wrapper>
@endif
