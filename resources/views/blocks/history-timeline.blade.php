
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
    $validItems = array_values(array_filter($items ?? [], fn ($item) => !empty($item['title']) || !empty($item['description']) || !empty($item['date']) || !empty($item['icon']['ID'])));
@endphp

<x-block-helper.block-wrapper :block="$block" :attributes="$attributes">
    @if(!empty($validItems))
        <section class="py-14 sm:py-16 lg:py-20">
            <x-ui.container>
                <div class="grid gap-8 lg:grid-cols-12 lg:gap-10">
                    <div class="lg:col-span-7">
                        @if(!empty($prefix))
                            <div @if(!$isEditor) data-aos="fade-right" data-aos-duration="550" data-aos-once="true" data-aos-easing="ease-out-cubic" @endif class="mb-4 flex items-center gap-3 text-[11px] font-semibold uppercase tracking-[0.14em] text-muted sm:text-[12px]">
                                <span class="flex h-[26px] w-[26px] shrink-0 items-center justify-center rounded-[8px] border border-brand/30 bg-brand/10 text-brand">
                                    <svg class="h-[13px] w-[13px]" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                        <path d="M12 8V12L15 15" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" />
                                        <path d="M22 12C22 17.523 17.523 22 12 22C6.477 22 2 17.523 2 12C2 6.477 6.477 2 12 2C17.523 2 22 6.477 22 12Z" stroke="currentColor" stroke-width="1.8" />
                                    </svg>
                                </span>
                                <span>{{ $prefix }}</span>
                            </div>
                        @endif
                        @if(!empty($titleHtml))
                            <h2 @if(!$isEditor) data-aos="fade-up" data-aos-duration="650" data-aos-delay="60" data-aos-once="true" data-aos-easing="ease-out-cubic" @endif class="max-w-[760px]">{!! $titleHtml !!}</h2>
                        @endif
                    </div>
                    <div class="lg:col-span-5">
                        @if(!empty($descriptionHtml))
                            <div @if(!$isEditor) data-aos="fade-left" data-aos-duration="650" data-aos-delay="120" data-aos-once="true" data-aos-easing="ease-out-cubic" @endif class="wysiwyg max-w-[560px] lg:ml-auto [&_p]:text-muted [&_p]:leading-[1.75]">{!! $descriptionHtml !!}</div>
                        @endif
                    </div>
                </div>

                <div class="relative mt-12 sm:mt-14 lg:mt-16">
                    <div class="absolute bottom-0 left-[28px] top-0 w-px bg-border/70 lg:left-1/2 lg:-translate-x-1/2"></div>
                    <div class="absolute bottom-0 left-[28px] top-0 w-px bg-[linear-gradient(180deg,rgba(38,221,172,0.08)_0%,rgba(38,221,172,0.7)_30%,rgba(79,136,255,0.55)_68%,rgba(38,221,172,0.1)_100%)] lg:left-1/2 lg:-translate-x-1/2"></div>

                    <div class="space-y-6 sm:space-y-8 lg:space-y-10">
                        @foreach($validItems as $item)
                            @php
                                $isEven = $loop->iteration % 2 === 0;
                                $cardAos = $isEven ? 'fade-left' : 'fade-right';
                                $desktopCardSide = $isEven ? 'lg:col-start-8 lg:text-left' : 'lg:col-start-1 lg:text-left';
                                $desktopSpacer = $isEven ? 'lg:col-start-1 lg:col-span-5' : 'lg:col-start-8 lg:col-span-5';
                            @endphp

                            <div class="group relative grid grid-cols-[56px_minmax(0,1fr)] gap-x-5 lg:grid-cols-12 lg:gap-x-0">
                                <div class="relative flex justify-center lg:col-start-6 lg:col-span-2">
                                    <div class="absolute left-1/2 top-[18px] h-[18px] w-[18px] -translate-x-1/2 rounded-full border-2 border-brand bg-white shadow-[0_0_0_5px_rgba(38,221,172,0.12)] transition-all duration-500 ease-out group-hover:scale-110 group-hover:shadow-[0_0_0_8px_rgba(38,221,172,0.18)]">
                                        <span class="absolute inset-[3px] rounded-full bg-brand transition-transform duration-500 ease-out group-hover:scale-110"></span>
                                    </div>
                                    <span class="absolute left-1/2 top-[18px] h-[18px] w-[18px] -translate-x-1/2 rounded-full bg-brand/20 opacity-0 blur-[12px] transition-all duration-500 ease-out group-hover:opacity-100"></span>
                                </div>

                                <div @if(!$isEditor) data-aos="fade-up" data-aos-duration="700" data-aos-delay="{{ $loop->index * 90 }}" data-aos-once="true" data-aos-easing="ease-out-cubic" @endif class="min-w-0 lg:hidden">
                                    <div class="relative overflow-hidden rounded-[20px] border border-border bg-surface p-5 shadow-card-soft transition-all duration-700 ease-[cubic-bezier(0.22,1,0.36,1)] hover:-translate-y-1 hover:border-brand/40 hover:shadow-card-hover-soft motion-reduce:transition-none sm:p-6">
                                        <span class="pointer-events-none absolute inset-x-0 top-0 h-[2px] bg-[linear-gradient(90deg,rgba(38,221,172,0)_0%,rgba(38,221,172,0.8)_50%,rgba(38,221,172,0)_100%)] opacity-0 transition-opacity duration-700 group-hover:opacity-100"></span>
                                        @if(!empty($item['date']))
                                            <div class="mb-4">
                                                <span class="inline-flex rounded-full border border-brand/20 bg-brand/10 px-3 py-[7px] text-[12px] font-semibold leading-none text-brand sm:text-[13px]">{{ $item['date'] }}</span>
                                            </div>
                                        @endif
                                        <div class="flex items-start gap-4">
                                            @if(!empty($item['icon']['ID']))
                                                <div class="flex h-[58px] w-[58px] shrink-0 items-center justify-center rounded-full bg-brand/10 transition-transform duration-500 ease-out group-hover:scale-110 sm:h-[62px] sm:w-[62px]">
                                                    {!! wp_get_attachment_image($item['icon']['ID'], 'thumbnail', false, [
                                                        'class' => 'h-[30px] w-[30px] object-contain transition-transform duration-700 ease-out group-hover:rotate-[8deg] sm:h-[32px] sm:w-[32px]',
                                                        'alt' => '',
                                                        'aria-hidden' => 'true',
                                                        'loading' => 'lazy',
                                                        'decoding' => 'async',
                                                    ]) !!}
                                                </div>
                                            @endif
                                            <div class="min-w-0 flex-1">
                                                @if(!empty($item['title']))
                                                    <div class="relative inline-block max-w-full pb-[5px]">
                                                        <h4 class="m-0">{{ $item['title'] }}</h4>
                                                        <span class="absolute bottom-0 left-0 h-[2px] w-full origin-left scale-x-0 rounded-full bg-brand transition-transform duration-500 ease-out group-hover:scale-x-100"></span>
                                                    </div>
                                                @endif
                                                @if(!empty($item['description']))
                                                    <p class="mt-2 mb-0 text-[15px] leading-[1.7] text-muted">{{ $item['description'] }}</p>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div @if(!$isEditor) data-aos="{{ $cardAos }}" data-aos-duration="750" data-aos-delay="{{ $loop->index * 90 }}" data-aos-once="true" data-aos-easing="ease-out-cubic" @endif class="hidden lg:col-span-5 lg:block {{ $desktopCardSide }}">
                                    <div class="relative overflow-hidden rounded-[22px] border border-border bg-surface p-7 shadow-card-soft transition-all duration-700 ease-[cubic-bezier(0.22,1,0.36,1)] hover:-translate-y-1 hover:border-brand/40 hover:shadow-card-hover-soft motion-reduce:transition-none xl:p-8">
                                        <span class="pointer-events-none absolute inset-x-0 top-0 h-[2px] bg-[linear-gradient(90deg,rgba(38,221,172,0)_0%,rgba(38,221,172,0.85)_50%,rgba(38,221,172,0)_100%)] opacity-0 transition-opacity duration-700 group-hover:opacity-100"></span>
                                        <span class="pointer-events-none absolute top-[30px] h-px w-[44px] bg-[linear-gradient(90deg,rgba(38,221,172,0.55)_0%,rgba(38,221,172,0)_100%)] {{ $isEven ? 'right-full mr-5 rotate-180' : 'left-full ml-5' }}"></span>
                                        @if(!empty($item['date']))
                                            <div class="mb-4">
                                                <span class="inline-flex rounded-full border border-brand/20 bg-brand/10 px-3 py-[7px] text-[12px] font-semibold leading-none text-brand xl:text-[13px]">{{ $item['date'] }}</span>
                                            </div>
                                        @endif
                                        <div class="flex items-start gap-4 xl:gap-5">
                                            @if(!empty($item['icon']['ID']))
                                                <div class="flex h-[62px] w-[62px] shrink-0 items-center justify-center rounded-full bg-brand/10 transition-transform duration-500 ease-out group-hover:scale-110">
                                                    {!! wp_get_attachment_image($item['icon']['ID'], 'thumbnail', false, [
                                                        'class' => 'h-[32px] w-[32px] object-contain transition-transform duration-700 ease-out group-hover:rotate-[8deg]',
                                                        'alt' => '',
                                                        'aria-hidden' => 'true',
                                                        'loading' => 'lazy',
                                                        'decoding' => 'async',
                                                    ]) !!}
                                                </div>
                                            @endif
                                            <div class="min-w-0 flex-1">
                                                @if(!empty($item['title']))
                                                    <div class="relative inline-block max-w-full pb-[5px]">
                                                        <h4 class="m-0">{{ $item['title'] }}</h4>
                                                        <span class="absolute bottom-0 left-0 h-[2px] w-full origin-left scale-x-0 rounded-full bg-brand transition-transform duration-500 ease-out group-hover:scale-x-100"></span>
                                                    </div>
                                                @endif
                                                @if(!empty($item['description']))
                                                    <p class="mt-2 mb-0 text-[15px] leading-[1.75] text-muted xl:text-[16px]">{{ $item['description'] }}</p>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="hidden {{ $desktopSpacer }} lg:block"></div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </x-ui.container>
        </section>
    @endif
</x-block-helper.block-wrapper>
