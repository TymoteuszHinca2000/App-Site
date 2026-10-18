@php
    $footerData = get_field(
        'footer',
        'option'
    );

    $footerData = is_array($footerData)
        ? $footerData
        : [];

    $logo =
        $footerData['logo']
        ?? null;

    $description =
        $footerData['description']
        ?? '';

    $socialMedia =
        $footerData['social_media']
        ?? [];

    $copyright =
        $footerData['copyright']
        ?? '';

    $privacyPolicy =
        $footerData['privacy_policy']
        ?? null;

    $currentLanguage =
        function_exists('pll_current_language')
            ? pll_current_language('slug')
            : 'pl';

    $homeUrl =
        function_exists('pll_home_url')
            ? pll_home_url()
            : home_url('/');

    $socialLabels = [
        'facebook' => 'Facebook',
        'instagram' => 'Instagram',
        'linkedin' => 'LinkedIn',
        'youtube' => 'YouTube',
        'tiktok' => 'TikTok',
        'x' => 'X',
    ];

    $privacyFallbackTitle =
        $currentLanguage === 'en'
            ? 'Privacy Policy'
            : 'Polityka prywatności';
@endphp

<footer class="relative overflow-hidden bg-primary-dark text-white">

    
    <div
        aria-hidden="true"
        class="pointer-events-none absolute inset-0
            footer-brand-ambient"
    ></div>

    <div class="relative z-10">

        
        <x-ui.container>

            <div
                class="grid gap-8 py-8
                    lg:grid-cols-[minmax(240px,1fr)_auto_minmax(220px,1fr)]
                    lg:items-start
                    lg:gap-12
                    lg:py-10
                    xl:gap-16"
            >

                
                <div
                    class="mx-auto flex max-w-[320px] flex-col items-center text-center
                        lg:mx-0
                        lg:items-start
                        lg:text-left"
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
                                    'class' => 'block h-auto max-h-[44px] w-auto max-w-[190px] object-contain',
                                    'alt' => get_bloginfo('name'),
                                    'loading' => 'lazy',
                                    'decoding' => 'async',
                                ]
                            ) !!}

                        @else

                            <span
                                class="text-[24px] font-extrabold tracking-[-0.04em] text-white"
                            >
                                {{ get_bloginfo('name') }}
                            </span>

                        @endif

                    </a>

                    @if(!empty($description))

                        <div
                            class="mt-4 max-w-[290px]
                                text-center
                                text-[13px]
                                leading-[1.65]
                                text-white/60
                                lg:text-left
                                [&_a]:font-semibold
                                [&_a]:text-brand
                                [&_a]:underline
                                [&_a]:underline-offset-4
                                [&_br]:block"
                        >
                            {!! wp_kses_post($description) !!}
                        </div>

                    @endif

                </div>

                
                <div
                    class="flex items-center justify-center
                        lg:pt-1"
                >

                    <nav
                        aria-label="{{ esc_attr__('Menu stopki', 'appandsite') }}"
                        class="w-full"
                    >

                        {!! wp_nav_menu([
                            'theme_location' => 'footer_navigation',
                            'container' => false,
                            'menu_id' => 'footer-navigation',
                            'menu_class' => 'flex flex-wrap items-center justify-center gap-x-6 gap-y-1 lg:justify-center xl:gap-x-7',
                            'depth' => 1,
                            'fallback_cb' => false,
                            'echo' => false,
                        ]) !!}

                    </nav>

                </div>

                
                @if(!empty($socialMedia))

                    <div
                        class="flex flex-wrap items-center justify-center gap-2
                            lg:justify-end
                            lg:pt-1"
                    >

                        @foreach($socialMedia as $social)

                            @php
                                $type =
                                    $social['type']
                                    ?? '';

                                $socialLink =
                                    $social['link']
                                    ?? null;

                                $socialLabel =
                                    $socialLabels[$type]
                                    ?? ucfirst($type);
                            @endphp

                            @if(
                                !empty($socialLink['url'])
                                && !empty($type)
                            )

                                <a
                                    href="{{ esc_url($socialLink['url']) }}"
                                    target="{{ esc_attr($socialLink['target'] ?: '_blank') }}"
                                    @if(($socialLink['target'] ?? '_blank') === '_blank')
                                        rel="noopener noreferrer"
                                    @endif
                                    aria-label="{{ esc_attr($socialLabel) }}"
                                    class="group flex h-[40px] w-[40px] shrink-0 items-center justify-center
                                        rounded-[8px]
                                        text-white/85
                                        transition-[color,background-color]
                                        duration-300
                                        hover:bg-white/[0.06]
                                        hover:text-brand
                                        focus-visible:outline-2
                                        focus-visible:outline-offset-3
                                        focus-visible:outline-brand"
                                >

                                    
                                    @if($type === 'facebook')

                                        <svg
                                            class="h-[18px] w-[18px]"
                                            viewBox="0 0 24 24"
                                            fill="currentColor"
                                            aria-hidden="true"
                                        >
                                            <path
                                                d="M13.7 21V12.8H16.5L16.9 9.6H13.7V7.55C13.7 6.62 13.96 5.99 15.31 5.99H17V3.13C16.71 3.09 15.7 3 14.53 3C12.08 3 10.4 4.49 10.4 7.23V9.6H7.63V12.8H10.4V21H13.7Z"
                                            />
                                        </svg>

                                    
                                    @elseif($type === 'instagram')

                                        <svg
                                            class="h-[19px] w-[19px]"
                                            viewBox="0 0 24 24"
                                            fill="none"
                                            aria-hidden="true"
                                        >
                                            <rect
                                                x="3.5"
                                                y="3.5"
                                                width="17"
                                                height="17"
                                                rx="5"
                                                stroke="currentColor"
                                                stroke-width="2"
                                            />

                                            <circle
                                                cx="12"
                                                cy="12"
                                                r="4"
                                                stroke="currentColor"
                                                stroke-width="2"
                                            />

                                            <circle
                                                cx="17.5"
                                                cy="6.7"
                                                r="1.1"
                                                fill="currentColor"
                                            />
                                        </svg>

                                    
                                    @elseif($type === 'linkedin')

                                        <svg
                                            class="h-[18px] w-[18px]"
                                            viewBox="0 0 24 24"
                                            fill="currentColor"
                                            aria-hidden="true"
                                        >
                                            <path
                                                d="M6.4 8.2H3.2V20H6.4V8.2ZM4.8 3C3.8 3 3 3.82 3 4.82C3 5.82 3.8 6.64 4.8 6.64C5.8 6.64 6.6 5.82 6.6 4.82C6.6 3.82 5.8 3 4.8 3ZM20.8 13.23C20.8 9.67 18.9 8 16.36 8C14.31 8 13.39 9.13 12.88 9.92V8.2H9.68V20H12.88V14.15C12.88 12.61 13.17 11.12 15.08 11.12C16.96 11.12 16.98 12.88 16.98 14.25V20H20.18L20.8 13.23Z"
                                            />
                                        </svg>

                                    
                                    @elseif($type === 'youtube')

                                        <svg
                                            class="h-[20px] w-[20px]"
                                            viewBox="0 0 24 24"
                                            fill="none"
                                            aria-hidden="true"
                                        >
                                            <path
                                                d="M21.2 7.15C20.98 6.32 20.34 5.67 19.51 5.45C18.02 5.05 12 5.05 12 5.05C12 5.05 5.98 5.05 4.49 5.45C3.66 5.67 3.02 6.32 2.8 7.15C2.4 8.65 2.4 12 2.4 12C2.4 12 2.4 15.35 2.8 16.85C3.02 17.68 3.66 18.33 4.49 18.55C5.98 18.95 12 18.95 12 18.95C12 18.95 18.02 18.95 19.51 18.55C20.34 18.33 20.98 17.68 21.2 16.85C21.6 15.35 21.6 12 21.6 12C21.6 12 21.6 8.65 21.2 7.15Z"
                                                fill="currentColor"
                                            />

                                            <path
                                                d="M10 15.15V8.85L15.45 12L10 15.15Z"
                                                class="fill-primary-dark"
                                            />
                                        </svg>

                                    
                                    @elseif($type === 'tiktok')

                                        <svg
                                            class="h-[18px] w-[18px]"
                                            viewBox="0 0 24 24"
                                            fill="currentColor"
                                            aria-hidden="true"
                                        >
                                            <path
                                                d="M15.2 3C15.47 4.55 16.35 5.76 17.66 6.62C18.51 7.18 19.47 7.48 20.5 7.52V10.63C18.58 10.57 16.83 9.97 15.29 8.82V15.28C15.29 18.66 12.55 21.4 9.17 21.4C5.79 21.4 3.05 18.66 3.05 15.28C3.05 11.9 5.79 9.16 9.17 9.16C9.53 9.16 9.88 9.19 10.22 9.25V12.44C9.89 12.32 9.54 12.26 9.17 12.26C7.18 12.26 6.15 13.71 6.15 15.28C6.15 16.95 7.5 18.3 9.17 18.3C10.84 18.3 12.19 16.95 12.19 15.28V3H15.2Z"
                                            />
                                        </svg>

                                    
                                    @elseif($type === 'x')

                                        <svg
                                            class="h-[17px] w-[17px]"
                                            viewBox="0 0 24 24"
                                            fill="currentColor"
                                            aria-hidden="true"
                                        >
                                            <path
                                                d="M18.9 3H22L15.23 10.74L23.2 21H16.96L12.08 14.62L6.5 21H3.4L10.63 12.74L3 3H9.4L13.81 8.83L18.9 3ZM17.81 19.2H19.53L8.46 4.7H6.61L17.81 19.2Z"
                                            />
                                        </svg>

                                    @endif

                                </a>

                            @endif

                        @endforeach

                    </div>

                @endif

            </div>

        </x-ui.container>

        
        <div class="border-t border-white/10">

            <x-ui.container>

                <div
                    class="flex flex-col items-center justify-center gap-3 py-5
                        text-center
                        text-[12px]
                        leading-[1.5]
                        text-white/50

                        sm:flex-row
                        sm:justify-between
                        sm:text-left"
                >

                    
                    <div class="text-center sm:text-left">

                        @if(!empty($copyright))

                            <span>
                                {{ $copyright }}
                            </span>

                        @else

                            <span>
                                &copy; {{ date('Y') }}
                                {{ get_bloginfo('name') }}.
                                {{ __('Wszelkie prawa zastrzeżone.', 'appandsite') }}
                            </span>

                        @endif

                    </div>

                    
                    @if(!empty($privacyPolicy['url']))

                        <a
                            href="{{ esc_url($privacyPolicy['url']) }}"
                            target="{{ esc_attr($privacyPolicy['target'] ?: '_self') }}"
                            @if(($privacyPolicy['target'] ?? '') === '_blank')
                                rel="noopener noreferrer"
                            @endif
                            class="relative w-fit pb-[3px]
                                text-center
                                text-white/60
                                transition-colors
                                duration-300

                                after:absolute
                                after:bottom-0
                                after:left-0
                                after:h-px
                                after:w-full
                                after:origin-left
                                after:scale-x-0
                                after:bg-brand
                                after:transition-transform
                                after:duration-300
                                after:ease-out

                                hover:text-brand
                                hover:after:scale-x-100

                                focus-visible:outline-2
                                focus-visible:outline-offset-4
                                focus-visible:outline-brand

                                sm:text-left"
                        >
                            {{ !empty($privacyPolicy['title'])
                                ? $privacyPolicy['title']
                                : $privacyFallbackTitle
                            }}
                        </a>

                    @endif

                </div>

            </x-ui.container>

        </div>

    </div>

</footer>