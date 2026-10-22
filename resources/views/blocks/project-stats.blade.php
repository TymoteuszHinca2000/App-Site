@php
    $isEditor = is_admin();
    $itemsCount = count($items ?? []);
@endphp

<x-block-helper.block-wrapper :block="$block" :attributes="$attributes">
    @if(!empty($items))
        <section class="border-y border-border/70 bg-white">
            <x-ui.container>
                <div class="grid grid-cols-2 gap-x-4 sm:gap-x-8 xl:grid-cols-5 xl:gap-x-6 2xl:gap-x-10">
                    @foreach($items as $item)
                        <div @if(!$isEditor) data-aos="fade-up" data-aos-duration="650" data-aos-delay="{{ $loop->index * 100 }}" data-aos-once="true" data-aos-easing="ease-out-cubic" @endif class="group flex min-w-0 flex-col items-center justify-start gap-3 border-b border-border/70 px-2 py-6 text-center sm:gap-4 sm:px-4 sm:py-7 xl:min-h-[130px] xl:flex-row xl:items-center xl:justify-start xl:gap-3 xl:border-b-0 xl:px-0 xl:py-6 xl:text-left 2xl:gap-4 {{ $itemsCount === 5 && $loop->last ? 'col-span-2 w-[calc(50%-8px)] justify-self-center border-b-0 sm:w-[calc(50%-16px)] xl:col-span-1 xl:w-full xl:justify-self-stretch' : ($loop->last ? 'border-b-0' : '') }}">
                            @if(!empty($item['image']['url']))
                                <div class="flex h-[58px] w-[58px] shrink-0 items-center justify-center rounded-full bg-brand/10 transition-transform duration-500 ease-out group-hover:scale-110 lg:h-[62px] lg:w-[62px]">
                                    <img src="{{ esc_url($item['image']['url']) }}" alt="" aria-hidden="true" loading="lazy" decoding="async" class="h-[30px] w-[30px] object-contain transition-transform duration-700 ease-out group-hover:rotate-[8deg] lg:h-[32px] lg:w-[32px]">
                                </div>
                            @endif
                            <div class="flex min-w-0 w-full max-w-[240px] flex-col items-center text-center xl:items-start xl:text-left">
                                @if(!empty($item['title']))
                                    <div class="relative inline-block max-w-full pb-[5px]">
                                        <h5 class="m-0">{{ $item['title'] }}</h5>
                                        <span class="absolute bottom-0 left-0 h-[2px] w-full origin-left scale-x-0 rounded-full bg-brand transition-transform duration-500 ease-out group-hover:scale-x-100"></span>
                                    </div>
                                @endif
                                @if(!empty($item['description']))
                                    <p class="mt-1 mb-0 text-[13px] leading-[1.5] text-muted sm:text-[14px]">{{ $item['description'] }}</p>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </x-ui.container>
        </section>
    @endif
</x-block-helper.block-wrapper>