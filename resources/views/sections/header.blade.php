@php
    $headerData = get_field(
        'header',
        'option'
    );

    $headerData = is_array($headerData)
        ? $headerData
        : [];

    $logo =
        $headerData['logo']
        ?? null;

    $headerButton =
        $headerData['button']
        ?? null;

    /*
    |--------------------------------------------------------------------------
    | Strona główna
    |--------------------------------------------------------------------------
    */

    $homeUrl =
        function_exists('pll_home_url')
            ? pll_home_url()
            : home_url('/');

    /*
    |--------------------------------------------------------------------------
    | Polylang
    |--------------------------------------------------------------------------
    */

    $languages = [];

    if (
        function_exists(
            'pll_the_languages'
        )
    ) {
        $languages = pll_the_languages([
            'raw' => 1,
            'hide_if_empty' => 0,
            'hide_current' => 0,
        ]);
    }

    $languages = is_array($languages)
        ? $languages
        : [];

    $currentLanguage = null;

    foreach ($languages as $language) {
        if (
            !empty(
                $language['current_lang']
            )
        ) {
            $currentLanguage = $language;

            break;
        }
    }

    if (
        !$currentLanguage
        && !empty($languages)
    ) {
        $currentLanguage = reset(
            $languages
        );
    }
@endphp

<header
    x-data="alpHeader()"
    x-bind:class="
        scrolled || mobileOpen
            ? 'bg-primary/95 shadow-[0_8px_32px_rgba(0,0,0,0.16)] backdrop-blur-xl'
            : 'bg-transparent'
    "
    x-on:keydown.escape.window="closeMenus()"
    class="header group/header fixed left-0 top-0 z-[100] w-full
        transition-[background-color,box-shadow,backdrop-filter]
        duration-300
        ease-out
        [body.admin-bar_&]:top-[32px]
        max-[782px]:[body.admin-bar_&]:top-[46px]"
>

    <div
        class="mx-auto grid w-full max-w-[1672px]
            grid-cols-[minmax(0,1fr)_auto]
            items-center
            gap-4
            px-4
            py-4
            sm:px-6
            xl:grid-cols-[minmax(0,1fr)_auto_minmax(0,1fr)]
            xl:gap-5
            xl:px-8
            2xl:gap-8"
    >

        {{-- LOGO --}}
        <div
            class="relative z-20 flex min-w-0 items-center justify-start"
        >

            <a
                href="{{ esc_url($homeUrl) }}"
                aria-label="{{ esc_attr(get_bloginfo('name') . ' - strona główna') }}"
                class="inline-flex max-w-full items-center
                    transition-opacity
                    duration-300
                    hover:opacity-80
                    focus-visible:rounded-sm
                    focus-visible:outline-2
                    focus-visible:outline-offset-4
                    focus-visible:outline-brand"
            >

                @if(!empty($logo['ID']))

                    {!! wp_get_attachment_image(
                        $logo['ID'],
                        'full',
                        false,
                        [
                            'class' => 'block h-auto max-h-[38px] w-auto max-w-[150px] object-contain sm:max-h-[42px] sm:max-w-[165px] xl:max-h-[46px] xl:max-w-[180px]',
                            'alt' => get_bloginfo('name'),
                            'loading' => 'eager',
                            'decoding' => 'async',
                        ]
                    ) !!}

                @else

                    <span
                        class="text-[23px] font-extrabold tracking-[-0.04em] text-white"
                    >
                        {{ get_bloginfo('name') }}
                    </span>

                @endif

            </a>

        </div>

        {{-- DESKTOP MENU --}}
        <nav
            aria-label="Menu główne"
            class="relative z-20 hidden xl:block"
        >

            {!! wp_nav_menu([
                'theme_location' => 'primary_navigation',
                'container' => false,
                'menu_id' => 'header-navigation-desktop',
                'menu_class' => 'flex items-center justify-center gap-4 2xl:gap-7',
                'depth' => 1,
                'fallback_cb' => false,
                'header_variant' => 'desktop',
                'echo' => false,
            ]) !!}

        </nav>

        {{-- DESKTOP RIGHT --}}
        <div
            class="relative z-20 hidden items-center justify-end gap-3 xl:flex 2xl:gap-4"
        >

            {{-- BUTTON --}}
            @if(
                !empty($headerButton['url'])
                && !empty($headerButton['title'])
            )

                <a
                    href="{{ esc_url($headerButton['url']) }}"
                    target="{{ esc_attr($headerButton['target'] ?: '_self') }}"
                    @if(($headerButton['target'] ?? '') === '_blank')
                        rel="noopener noreferrer"
                    @endif
                    class="group relative isolate inline-flex min-h-[52px] shrink-0
                        items-center
                        justify-center
                        gap-4
                        overflow-hidden
                        rounded-[8px]
                        bg-brand
                        px-7
                        text-[14px]
                        font-bold
                        whitespace-nowrap
                        text-primary

                        transition-colors
                        duration-300

                        before:absolute
                        before:inset-0
                        before:z-0
                        before:origin-left
                        before:scale-x-0
                        before:bg-brand-active
                        before:content-['']
                        before:transition-transform
                        before:duration-300
                        before:ease-out

                        hover:text-white
                        hover:before:scale-x-100

                        focus-visible:outline-2
                        focus-visible:outline-offset-2
                        focus-visible:outline-brand

                        motion-reduce:before:transition-none"
                >

                    <span class="relative z-10">
                        {{ $headerButton['title'] }}
                    </span>

                    <svg
                        class="relative z-10 h-4 w-4 shrink-0
                            transition-transform
                            duration-300
                            group-hover:translate-x-1
                            motion-reduce:transition-none"
                        viewBox="0 0 24 24"
                        fill="none"
                        aria-hidden="true"
                    >
                        <path
                            d="M5 12H19M13 6L19 12L13 18"
                            stroke="currentColor"
                            stroke-width="2"
                            stroke-linecap="round"
                            stroke-linejoin="round"
                        />
                    </svg>

                </a>

            @endif

            {{-- LANGUAGE --}}
            @if(
                count($languages) > 1
                && $currentLanguage
            )

                <div
                    x-on:click.outside="languageOpen = false"
                    class="relative shrink-0"
                >

                    <button
                        type="button"
                        x-on:click="languageOpen = !languageOpen"
                        x-bind:aria-expanded="languageOpen.toString()"
                        aria-haspopup="true"
                        aria-label="Wybierz język"
                        class="flex min-h-[52px] items-center justify-center gap-3
                            rounded-[8px]
                            border
                            border-white/20
                            bg-white/[0.05]
                            px-4
                            text-[14px]
                            font-semibold
                            text-white

                            transition-colors
                            duration-300

                            hover:border-brand/60
                            hover:bg-white/10

                            focus-visible:outline-2
                            focus-visible:outline-offset-2
                            focus-visible:outline-brand

                            2xl:px-5"
                    >

                        @if(!empty($currentLanguage['flag']))

                            <img
                                src="{{ esc_url($currentLanguage['flag']) }}"
                                alt=""
                                aria-hidden="true"
                                width="24"
                                height="16"
                                class="block h-[16px] w-[24px] shrink-0 rounded-[2px] object-cover"
                            >

                        @endif

                        <span>
                            {{ strtoupper($currentLanguage['slug'] ?? '') }}
                        </span>

                        <svg
                            x-bind:class="
                                languageOpen
                                    ? 'rotate-180'
                                    : ''
                            "
                            class="h-4 w-4 shrink-0 transition-transform duration-300"
                            viewBox="0 0 24 24"
                            fill="none"
                            aria-hidden="true"
                        >
                            <path
                                d="M6 9L12 15L18 9"
                                stroke="currentColor"
                                stroke-width="2"
                                stroke-linecap="round"
                                stroke-linejoin="round"
                            />
                        </svg>

                    </button>

                    {{-- LANGUAGE DROPDOWN --}}
                    <div
                        x-cloak
                        x-show="languageOpen"

                        x-transition:enter="transition ease-out duration-200"
                        x-transition:enter-start="opacity-0 -translate-y-2"
                        x-transition:enter-end="opacity-100 translate-y-0"

                        x-transition:leave="transition ease-in duration-150"
                        x-transition:leave-start="opacity-100 translate-y-0"
                        x-transition:leave-end="opacity-0 -translate-y-2"

                        class="absolute right-0 top-[calc(100%+12px)] z-50
                            min-w-[175px]
                            overflow-hidden
                            rounded-[10px]
                            border
                            border-white/10
                            bg-primary
                            p-2
                            shadow-[0_12px_36px_rgba(0,0,0,0.25)]
                            [&[x-cloak]]:hidden"
                    >

                        @foreach($languages as $language)

                            @if(!empty($language['url']))

                                <a
                                    href="{{ esc_url($language['url']) }}"
                                    hreflang="{{ esc_attr($language['slug'] ?? '') }}"
                                    lang="{{ esc_attr($language['locale'] ?? $language['slug'] ?? '') }}"
                                    @if(!empty($language['current_lang']))
                                        aria-current="page"
                                    @endif
                                    class="flex items-center gap-3 rounded-[7px]
                                        px-3
                                        py-3
                                        text-[14px]
                                        font-semibold

                                        transition-colors
                                        duration-300

                                        hover:bg-white/10
                                        hover:text-brand

                                        {{ !empty($language['current_lang'])
                                            ? 'bg-white/[0.06] text-brand'
                                            : 'text-white/85'
                                        }}"
                                >

                                    @if(!empty($language['flag']))

                                        <img
                                            src="{{ esc_url($language['flag']) }}"
                                            alt=""
                                            aria-hidden="true"
                                            width="24"
                                            height="16"
                                            class="block h-[16px] w-[24px] shrink-0 rounded-[2px] object-cover"
                                        >

                                    @endif

                                    <span>
                                        {{ $language['name'] ?? strtoupper($language['slug'] ?? '') }}
                                    </span>

                                    @if(!empty($language['current_lang']))

                                        <svg
                                            class="ml-auto h-4 w-4 shrink-0 text-brand"
                                            viewBox="0 0 24 24"
                                            fill="none"
                                            aria-hidden="true"
                                        >
                                            <path
                                                d="M5 12L10 17L19 7"
                                                stroke="currentColor"
                                                stroke-width="2"
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                            />
                                        </svg>

                                    @endif

                                </a>

                            @endif

                        @endforeach

                    </div>

                </div>

            @endif

        </div>

        {{-- MOBILE MENU BUTTON --}}
        <button
            type="button"
            x-on:click="toggleMobile()"
            x-bind:aria-expanded="mobileOpen.toString()"
            aria-controls="header-mobile-panel"
            aria-label="Otwórz lub zamknij menu"
            class="relative z-20 flex h-[48px] w-[48px] items-center justify-center
                rounded-[8px]
                border
                border-white/20
                bg-white/10
                text-white

                transition-colors
                duration-300

                hover:border-brand
                hover:text-brand

                focus-visible:outline-2
                focus-visible:outline-offset-2
                focus-visible:outline-brand

                xl:hidden"
        >

            <svg
                x-show="!mobileOpen"
                class="h-[24px] w-[24px]"
                viewBox="0 0 24 24"
                fill="none"
                aria-hidden="true"
            >
                <path
                    d="M4 7H20M4 12H20M4 17H20"
                    stroke="currentColor"
                    stroke-width="2"
                    stroke-linecap="round"
                />
            </svg>

            <svg
                x-cloak
                x-show="mobileOpen"
                class="h-[24px] w-[24px] [&[x-cloak]]:hidden"
                viewBox="0 0 24 24"
                fill="none"
                aria-hidden="true"
            >
                <path
                    d="M5 5L19 19M19 5L5 19"
                    stroke="currentColor"
                    stroke-width="2"
                    stroke-linecap="round"
                />
            </svg>

        </button>

    </div>

    {{-- MOBILE PANEL --}}
    <div
        id="header-mobile-panel"

        x-cloak
        x-show="mobileOpen"

        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0 translate-x-full"
        x-transition:enter-end="opacity-100 translate-x-0"

        x-transition:leave="transition ease-in duration-250"
        x-transition:leave-start="opacity-100 translate-x-0"
        x-transition:leave-end="opacity-0 translate-x-full"

        class="fixed inset-0 z-10
            h-[100dvh]
            overflow-y-auto
            bg-primary
            pt-[80px]
            [&[x-cloak]]:hidden
            [body.admin-bar_&]:pt-[112px]
            max-[782px]:[body.admin-bar_&]:pt-[126px]
            xl:hidden"
    >

        <div
            class="mx-auto flex min-h-full max-w-[1672px] flex-col px-6 pb-8 pt-5"
        >

            {{-- MOBILE NAV --}}
            <nav
                aria-label="Menu mobilne"
                x-on:click="closeMenus()"
            >

                {!! wp_nav_menu([
                    'theme_location' => 'primary_navigation',
                    'container' => false,
                    'menu_id' => 'header-navigation-mobile',
                    'menu_class' => 'flex flex-col gap-1',
                    'depth' => 1,
                    'fallback_cb' => false,
                    'header_variant' => 'mobile',
                    'echo' => false,
                ]) !!}

            </nav>

            {{-- MOBILE BUTTON --}}
            @if(
                !empty($headerButton['url'])
                && !empty($headerButton['title'])
            )

                <a
                    href="{{ esc_url($headerButton['url']) }}"
                    target="{{ esc_attr($headerButton['target'] ?: '_self') }}"
                    @if(($headerButton['target'] ?? '') === '_blank')
                        rel="noopener noreferrer"
                    @endif
                    class="group relative isolate mt-5 flex min-h-[52px] w-full
                        items-center
                        justify-center
                        gap-4
                        overflow-hidden
                        rounded-[8px]
                        bg-brand
                        px-7
                        text-[14px]
                        font-bold
                        text-primary

                        transition-colors
                        duration-300

                        before:absolute
                        before:inset-0
                        before:z-0
                        before:origin-left
                        before:scale-x-0
                        before:bg-brand-active
                        before:content-['']
                        before:transition-transform
                        before:duration-300
                        before:ease-out

                        hover:text-white
                        hover:before:scale-x-100

                        focus-visible:outline-2
                        focus-visible:outline-offset-2
                        focus-visible:outline-brand

                        motion-reduce:before:transition-none"
                >

                    <span class="relative z-10">
                        {{ $headerButton['title'] }}
                    </span>

                    <svg
                        class="relative z-10 h-4 w-4
                            transition-transform
                            duration-300
                            group-hover:translate-x-1
                            motion-reduce:transition-none"
                        viewBox="0 0 24 24"
                        fill="none"
                        aria-hidden="true"
                    >
                        <path
                            d="M5 12H19M13 6L19 12L13 18"
                            stroke="currentColor"
                            stroke-width="2"
                            stroke-linecap="round"
                            stroke-linejoin="round"
                        />
                    </svg>

                </a>

            @endif

            {{-- MOBILE LANGUAGE --}}
            @if(
                count($languages) > 1
                && $currentLanguage
            )

                <div
                    class="mt-5 border-t border-white/10 pt-4"
                >

                    <button
                        type="button"
                        x-on:click="mobileLanguageOpen = !mobileLanguageOpen"
                        x-bind:aria-expanded="mobileLanguageOpen.toString()"
                        class="flex min-h-[52px] w-full items-center justify-between
                            gap-3
                            rounded-[8px]
                            py-2
                            text-[14px]
                            font-semibold
                            text-white"
                    >

                        <span class="flex items-center gap-3">

                            @if(!empty($currentLanguage['flag']))

                                <img
                                    src="{{ esc_url($currentLanguage['flag']) }}"
                                    alt=""
                                    aria-hidden="true"
                                    width="25"
                                    height="17"
                                    class="block h-[17px] w-[25px] shrink-0 rounded-[2px] object-cover"
                                >

                            @endif

                            <span>
                                {{ __('Język:', 'appandsite') }}
                                {{ strtoupper($currentLanguage['slug'] ?? '') }}
                            </span>

                        </span>

                        <svg
                            x-bind:class="
                                mobileLanguageOpen
                                    ? 'rotate-180'
                                    : ''
                            "
                            class="h-4 w-4 transition-transform duration-300"
                            viewBox="0 0 24 24"
                            fill="none"
                            aria-hidden="true"
                        >
                            <path
                                d="M6 9L12 15L18 9"
                                stroke="currentColor"
                                stroke-width="2"
                                stroke-linecap="round"
                                stroke-linejoin="round"
                            />
                        </svg>

                    </button>

                    <div
                        x-cloak
                        x-show="mobileLanguageOpen"
                        x-transition.opacity.duration.200ms
                        class="mt-2 flex flex-col gap-1 [&[x-cloak]]:hidden"
                    >

                        @foreach($languages as $language)

                            @if(!empty($language['url']))

                                <a
                                    href="{{ esc_url($language['url']) }}"
                                    hreflang="{{ esc_attr($language['slug'] ?? '') }}"
                                    lang="{{ esc_attr($language['locale'] ?? $language['slug'] ?? '') }}"
                                    @if(!empty($language['current_lang']))
                                        aria-current="page"
                                    @endif
                                    class="flex items-center gap-3 rounded-[8px]
                                        px-3
                                        py-3
                                        text-[14px]
                                        font-semibold

                                        transition-colors
                                        duration-300

                                        hover:bg-white/10

                                        {{ !empty($language['current_lang'])
                                            ? 'bg-white/[0.06] text-brand'
                                            : 'text-white/85 hover:text-brand'
                                        }}"
                                >

                                    @if(!empty($language['flag']))

                                        <img
                                            src="{{ esc_url($language['flag']) }}"
                                            alt=""
                                            aria-hidden="true"
                                            width="25"
                                            height="17"
                                            class="block h-[17px] w-[25px] shrink-0 rounded-[2px] object-cover"
                                        >

                                    @endif

                                    <span>
                                        {{ $language['name'] ?? strtoupper($language['slug'] ?? '') }}
                                    </span>

                                </a>

                            @endif

                        @endforeach

                    </div>

                </div>

            @endif

        </div>

    </div>

</header>