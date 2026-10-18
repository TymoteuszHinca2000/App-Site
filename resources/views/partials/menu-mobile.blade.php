@if (! empty($menuMobile['left']))
    <div
        class="js-menu-mobile pointer-events-none fixed top-0 left-0 z-999998 flex h-[100vh] w-full items-center justify-center bg-white px-4 opacity-0 transition-all duration-500 group-[.menu-open]/body:pointer-events-auto group-[.menu-open]/body:opacity-100"
    >
        <div class="absolute top-4 right-4">
            @include('partials.menu-toggler')
        </div>

        <nav class="flex h-full w-full flex-col justify-center overflow-hidden py-21">
            <ul class="flex flex-col gap-4 overflow-auto text-center text-xl">
                @if (! empty($menuMobile['left']))
                    @foreach ($menuMobile['left'] as $menuMobileItem)
                        <li>
                            <a
                                class=""
                                href="{{ esc_url($menuMobileItem['item_link']['url']) }}"
                                target="{{ esc_attr($menuMobileItem['item_link']['target']) }}"
                            >
                                {{ esc_html($menuMobileItem['item_link']['title']) }}
                            </a>
                        </li>
                    @endforeach
                @endif
            </ul>
        </nav>
    </div>
@endif
