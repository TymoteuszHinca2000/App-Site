@php
    $isEditor = is_admin();

    $titleHtml = wp_kses($title ?? '', ['strong' => [], 'b' => [], 'em' => [], 'i' => [], 'br' => [], 'p' => []]);
    $titleHtml = preg_replace('/<\/p>\s*<p[^>]*>/i', '<br>', $titleHtml);
    $titleHtml = preg_replace('/<\/?p\b[^>]*>/i', '', $titleHtml);
    $titleHtml = preg_replace('/<(strong|b)>/i', '<span class="text-brand">', $titleHtml);
    $titleHtml = preg_replace('/<\/(strong|b)>/i', '</span>', $titleHtml);

    $descriptionHtml = wp_kses($description ?? '', [
        'p' => [], 'strong' => [], 'b' => [], 'em' => [], 'i' => [], 'br' => [],
        'a' => ['href' => [], 'target' => [], 'rel' => []],
        'ul' => [], 'ol' => [], 'li' => [],
    ]);
@endphp

<x-block-helper.block-wrapper :block="$block" :attributes="$attributes">
    <section @if(!$isEditor) x-data="alpWhyAppSite()" @endif class="relative isolate bg-white">
        @if(!$isEditor)
            <canvas x-ref="waveTop" class="pointer-events-none absolute -right-28 -top-32 z-0 hidden h-[240px] w-[620px] max-w-none opacity-70 sm:block" aria-hidden="true"></canvas>
            <canvas x-ref="waveBottom" class="pointer-events-none absolute -bottom-32 -right-28 z-0 hidden h-[300px] w-[680px] max-w-none opacity-80 sm:block" aria-hidden="true"></canvas>
        @endif
        <div class="brand-dots-pattern pointer-events-none absolute right-[-15px] top-7 z-0 hidden h-[200px] w-[240px] animate-pulse opacity-60 [background-size:23px_23px] [animation-duration:6s] motion-reduce:animate-none lg:block" aria-hidden="true"></div>

        <x-ui.container>
            <div class="relative z-10">
                @if(!empty($prefix))
                    <x-ui.section-prefix class="mb-5" :data-aos="$isEditor ? null : 'fade-right'" data-aos-duration="500" data-aos-once="true" data-aos-easing="ease-out-cubic">{{ $prefix }}</x-ui.section-prefix>
                @endif

                @if(trim(wp_strip_all_tags($titleHtml)) !== '')
                    <h2 @if(!$isEditor) data-aos="fade-up" data-aos-duration="650" data-aos-delay="70" data-aos-once="true" @endif class="max-w-[1050px]">{!! $titleHtml !!}</h2>
                @endif

                @if(trim(wp_strip_all_tags($descriptionHtml)) !== '')
                    <div @if(!$isEditor) data-aos="fade-up" data-aos-duration="650" data-aos-delay="140" data-aos-once="true" @endif class="wysiwyg mt-10 max-w-[850px] lg:mt-12">{!! $descriptionHtml !!}</div>
                @endif

                <ul class="mt-12 grid grid-cols-1 gap-7 sm:mt-14 sm:grid-cols-2 lg:mt-16 lg:grid-cols-3 lg:gap-0">
                    @foreach($items as $item)
                        <li @if(!$isEditor) data-aos="fade-up" data-aos-duration="650" data-aos-delay="{{ 150 + $loop->index * 110 }}" data-aos-once="true" @endif class="group flex min-w-0 items-center gap-4 sm:gap-5 lg:border-r lg:border-brand/30 lg:gap-3 lg:px-4 lg:first:pl-0 lg:last:border-r-0 lg:last:pr-0 xl:gap-5 xl:px-7 2xl:gap-6 2xl:px-10">
                            <div class="flex h-[70px] w-[70px] shrink-0 items-center justify-center rounded-[18px] bg-brand/10 text-brand transition-icon group-hover:scale-105 group-hover:bg-brand/15 motion-reduce:transition-none sm:h-[76px] sm:w-[76px] lg:h-[62px] lg:w-[62px] xl:h-[76px] xl:w-[76px]">
                                @if(!empty($item['image']['ID']))
                                    {!! wp_get_attachment_image((int) $item['image']['ID'], 'thumbnail', false, ['class' => 'icon-tilt h-[41px] w-[41px] object-contain', 'alt' => '', 'aria-hidden' => 'true', 'loading' => 'lazy', 'decoding' => 'async']) !!}
                                @elseif($loop->index === 0)
                                    <svg class="icon-tilt h-[43px] w-[43px]" viewBox="0 0 48 48" fill="none" aria-hidden="true"><path d="M8 10h32v23H8zM4 39h40l-3-6H7l-3 6Z" stroke="currentColor" stroke-width="2.2" stroke-linejoin="round" stroke-linecap="round"/></svg>
                                @elseif($loop->index === 1)
                                    <svg class="icon-tilt h-[43px] w-[43px]" viewBox="0 0 48 48" fill="none" aria-hidden="true"><circle cx="24" cy="15" r="7" stroke="currentColor" stroke-width="2.2"/><path d="M10 39v-4c0-8 6-13 14-13s14 5 14 13v4M9 13a6 6 0 0 0 0 12M39 13a6 6 0 0 1 0 12M3 39v-4c0-4 2-7 6-9M45 39v-4c0-4-2-7-6-9" stroke="currentColor" stroke-width="2.2" stroke-linecap="round"/></svg>
                                @else
                                    <svg class="icon-tilt h-[43px] w-[43px]" viewBox="0 0 48 48" fill="none" aria-hidden="true"><path d="M24 3 41 10v12c0 11-7 18-17 23C14 40 7 33 7 22V10L24 3Z" stroke="currentColor" stroke-width="2.2" stroke-linejoin="round"/><path d="m16 24 6 6 11-13" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                @endif
                            </div>
                            <div class="flex min-w-0 items-center gap-3 sm:gap-4 lg:gap-2 xl:gap-4">
                                <div class="heading-3 shrink-0 whitespace-nowrap"><span @if(!$isEditor) data-count-target="{{ (int) $item['number'] }}" aria-hidden="true" @endif>{{ (int) $item['number'] }}</span><span aria-hidden="true">{{ $item['suffix'] }}</span>@if(!$isEditor)<span class="sr-only">{{ (int) $item['number'] }}{{ $item['suffix'] }}</span>@endif</div>
                                <p class="min-w-0 max-w-[220px]">{{ $item['description'] }}</p>
                            </div>
                        </li>
                    @endforeach
                </ul>

                @if((!empty($buttonPrimary['url']) && !empty($buttonPrimary['title'])) || (!empty($buttonSecondary['url']) && !empty($buttonSecondary['title'])))
                    <div @if(!$isEditor) data-aos="fade-up" data-aos-duration="650" data-aos-delay="300" data-aos-once="true" @endif class="mt-12 flex flex-col items-center justify-center gap-4 sm:mt-16 sm:flex-row sm:flex-wrap">
                        @if(!empty($buttonPrimary['url']) && !empty($buttonPrimary['title']))
                            <a href="{{ esc_url($buttonPrimary['url']) }}" target="{{ esc_attr($buttonPrimary['target'] ?? '_self') }}" @if(($buttonPrimary['target'] ?? '') === '_blank') rel="noopener noreferrer" @endif class="btn btn-primary group min-h-[56px] w-full gap-5 rounded-[9px] px-8 font-bold sm:w-auto sm:min-w-[270px]"><span>{{ $buttonPrimary['title'] }}</span><x-ui.arrow /></a>
                        @endif
                        @if(!empty($buttonSecondary['url']) && !empty($buttonSecondary['title']))
                            <a href="{{ esc_url($buttonSecondary['url']) }}" target="{{ esc_attr($buttonSecondary['target'] ?? '_self') }}" @if(($buttonSecondary['target'] ?? '') === '_blank') rel="noopener noreferrer" @endif class="btn btn-outline group min-h-[56px] w-full gap-5 rounded-[9px] px-8 font-bold sm:w-auto sm:min-w-[270px]"><span>{{ $buttonSecondary['title'] }}</span><x-ui.arrow /></a>
                        @endif
                    </div>
                @endif
            </div>
        </x-ui.container>
    </section>
</x-block-helper.block-wrapper>
