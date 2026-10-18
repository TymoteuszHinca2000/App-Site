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

    $descriptionHtml = wp_kses($description ?? '', [
        'p' => [],
        'strong' => [],
        'b' => [],
        'em' => [],
        'i' => [],
        'br' => [],
        'ul' => [],
        'ol' => [],
        'li' => [],
        'a' => [
            'href' => [],
            'target' => [],
            'rel' => [],
        ],
    ]);
@endphp

<x-block-helper.block-wrapper :block="$block" :attributes="$attributes">
    <section @if(!$isEditor) x-data="alpAboutCompany()" @endif class="relative isolate overflow-x-clip overflow-y-visible bg-white">
        <x-ui.container>
            <div class="relative grid grid-cols-1 items-start gap-x-8 gap-y-8 md:grid-cols-2 md:items-stretch 2xl:grid-cols-[minmax(0,1.08fr)_minmax(0,0.98fr)_minmax(0,1.08fr)] 2xl:gap-x-8 2xl:gap-y-0">
                <div class="relative z-30 flex min-w-0 flex-col items-start md:col-start-1 md:row-start-1">
                    @if(!empty($prefix))
                        <div @if(!$isEditor) data-aos="fade-right" data-aos-duration="500" data-aos-once="true" data-aos-easing="ease-out-cubic" @endif class="mb-5 flex items-center gap-3 text-[11px] font-semibold uppercase tracking-[0.14em] text-muted sm:text-[12px]"><span class="flex h-[24px] w-[24px] shrink-0 items-center justify-center rounded-[6px] border border-brand/60 bg-brand/10 text-brand"><svg class="h-[13px] w-[13px]" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M12 3L13.6 8.4L19 10L13.6 11.6L12 17L10.4 11.6L5 10L10.4 8.4L12 3Z" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/><path d="M18.5 15.5L19.2 17.8L21.5 18.5L19.2 19.2L18.5 21.5L17.8 19.2L15.5 18.5L17.8 17.8L18.5 15.5Z" stroke="currentColor" stroke-width="1.5" stroke-linejoin="round"/></svg></span><span>{{ $prefix }}</span></div>
                    @endif

                    @if($titleHtml)
                        <h2 @if(!$isEditor) data-aos="fade-up" data-aos-duration="650" data-aos-delay="80" data-aos-once="true" data-aos-easing="ease-out-cubic" @endif class="!m-0 w-full max-w-[640px] 2xl:max-w-[690px]">{!! $titleHtml !!}</h2>
                    @endif

                    @if($descriptionHtml)
                        <div @if(!$isEditor) data-aos="fade-up" data-aos-duration="650" data-aos-delay="150" data-aos-once="true" data-aos-easing="ease-out-cubic" @endif class="mt-5 w-full max-w-[480px] 2xl:max-w-[500px] [&_a]:font-semibold [&_a]:text-brand [&_a]:underline [&_a]:underline-offset-4 [&_p]:!m-0 [&_p+p]:!mt-4 [&_ul]:mt-4 [&_ol]:mt-4">{!! $descriptionHtml !!}</div>
                    @endif

                    @if(!empty($button['url']) && !empty($button['title']))
                        <div @if(!$isEditor) data-aos="fade-up" data-aos-duration="650" data-aos-delay="220" data-aos-once="true" data-aos-easing="ease-out-cubic" @endif class="mt-7">
                            <a href="{{ esc_url($button['url']) }}" target="{{ esc_attr($button['target'] ?: '_self') }}" @if(($button['target'] ?? '') === '_blank') rel="noopener noreferrer" @endif class="group relative isolate inline-flex min-h-[52px] items-center justify-center gap-4 overflow-hidden rounded-[8px] bg-primary px-7 text-[14px] font-bold text-white transition-colors duration-300 before:absolute before:inset-0 before:z-0 before:origin-left before:scale-x-0 before:bg-brand before:content-[''] before:transition-transform before:duration-300 before:ease-out hover:text-primary hover:before:scale-x-100 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-brand motion-reduce:before:transition-none"><span class="relative z-10">{{ $button['title'] }}</span><svg class="relative z-10 h-4 w-4 transition-transform duration-300 group-hover:translate-x-1 motion-reduce:transition-none" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M5 12H19M13 6L19 12L13 18" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg></a>
                        </div>
                    @endif
                </div>

                @if(!empty($image['ID']) || !empty($items))
                    <div class="relative z-10 flex min-w-0 items-end justify-center self-end md:col-start-2 md:row-start-1 2xl:self-stretch">
                        @if(!empty($image['ID']))
                            <svg class="pointer-events-none absolute left-[12%] top-1/2 -z-10 h-[128%] w-[155%] -translate-y-1/2 sm:left-[14%] sm:w-[165%] md:left-[10%] md:h-[132%] md:w-[170%] 2xl:left-[8%] 2xl:h-[146%] 2xl:w-[260%]" viewBox="-30 -30 960 860" preserveAspectRatio="none" aria-hidden="true" focusable="false"><path x-ref="blobPath" class="fill-decor-soft" d="M 112 320 C 45 260 95 116 230 110 C 339 104 395 30 515 86 C 623 135 624 196 755 193 C 890 189 901 325 841 416 C 797 482 884 604 756 665 C 655 718 557 635 430 715 C 328 782 243 645 151 613 C 58 582 27 428 112 320 Z"></path></svg>
                            <div class="pointer-events-none absolute left-[2%] top-[18%] z-[1] h-[54%] w-[82%] animate-pulse opacity-60 [animation-duration:6s] brand-dots-pattern [background-size:15px_15px] motion-reduce:animate-none sm:left-[6%] sm:w-[84%] md:left-[4%] md:w-[92%] 2xl:left-[-2%] 2xl:w-[122%]"></div>
                            <div @if(!$isEditor) data-aos="fade-up" data-aos-duration="750" data-aos-delay="120" data-aos-once="true" data-aos-easing="ease-out-cubic" @endif class="pointer-events-none relative z-10 -my-[10px] flex w-full max-w-[360px] items-end justify-center self-end sm:max-w-[440px] md:max-w-[520px] 2xl:absolute 2xl:inset-x-[-10%] 2xl:top-[-10px] 2xl:bottom-[-10px] 2xl:my-0 2xl:w-[120%] 2xl:max-w-none 2xl:-translate-y-[50px]">
                                {!! wp_get_attachment_image($image['ID'], 'large', false, ['class' => 'block h-auto max-h-[420px] w-full object-contain object-bottom sm:max-h-[470px] md:max-h-[520px] 2xl:h-full 2xl:max-h-none', 'loading' => $isEditor ? 'eager' : 'lazy', 'decoding' => 'async']) !!}
                            </div>
                        @endif
                    </div>
                @endif

                @if(!empty($items))
                    <div class="relative z-20 grid min-w-0 grid-cols-1 gap-4 md:col-span-2 md:col-start-1 md:row-start-2 md:grid-cols-2 lg:grid-cols-3 lg:gap-5 2xl:col-span-1 2xl:col-start-3 2xl:row-start-1 2xl:self-center 2xl:grid-cols-1 2xl:gap-5">
                        @foreach($items as $item)
                            <div @if(!$isEditor) data-aos="fade-up" data-aos-duration="650" data-aos-delay="{{ 180 + ($loop->index * 100) }}" data-aos-once="true" data-aos-easing="ease-out-cubic" @endif class="min-w-0 2xl:w-full">
                                <div class="group relative flex h-full min-w-0 items-center gap-4 rounded-[16px] border border-border bg-white/95 p-4 shadow-card-soft transition-[box-shadow,border-color,background-color] duration-700 ease-[cubic-bezier(0.22,1,0.36,1)] hover:border-brand/45 hover:bg-white hover:shadow-card-hover sm:p-5 2xl:gap-4 2xl:p-5">
                                    @if(!empty($item['image']['url']))
                                        <div class="flex h-[58px] w-[58px] shrink-0 items-center justify-center rounded-full bg-brand/10 transition-[transform,background-color] duration-[950ms] ease-[cubic-bezier(0.22,1,0.36,1)] group-hover:scale-110 group-hover:bg-brand/15 motion-reduce:transition-none 2xl:h-[60px] 2xl:w-[60px]"><img src="{{ esc_url($item['image']['url']) }}" alt="" aria-hidden="true" loading="lazy" decoding="async" class="h-[30px] w-[30px] object-contain transition-transform duration-[1500ms] ease-[cubic-bezier(0.22,1,0.36,1)] group-hover:rotate-[10deg] motion-reduce:transition-none 2xl:h-[32px] 2xl:w-[32px]"></div>
                                    @endif

                                    <div class="min-w-0 flex-1">
                                        @if(!empty($item['title']))
                                            <div class="relative inline-block max-w-full pb-[5px]"><h3 class="!m-0 !text-[16px] !font-bold !leading-[1.35] sm:!text-[17px] 2xl:!text-[18px]">{{ $item['title'] }}</h3><span class="absolute bottom-0 left-0 h-[2px] w-full origin-left scale-x-0 rounded-full bg-brand transition-transform duration-500 ease-out group-hover:scale-x-100 motion-reduce:transition-none"></span></div>
                                        @endif

                                        @if(!empty($item['description']))
                                            <p class="mt-1 !mb-0 !text-[13px] !leading-[1.5] text-muted sm:!text-[14px]">{{ $item['description'] }}</p>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </x-ui.container>
    </section>
</x-block-helper.block-wrapper>