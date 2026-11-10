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
    $contactDescriptionHtml = nl2br(esc_html($contactDescription ?? ''));

    $formHtml = '';
    $formError = '';
    $selectedForm = $contactFormReference ?? null;
    $formReference = '';
    $blockData = [];

    if (is_array($block)) {
        $blockData = $block['data'] ?? ($block['attrs']['data'] ?? []);
    } elseif (is_object($block)) {
        $blockData = $block->data ?? [];
        if (empty($blockData) && isset($block->attributes) && is_array($block->attributes)) {
            $blockData = $block->attributes['data'] ?? [];
        }
    }

    if (empty($selectedForm) && is_array($blockData)) {
        $selectedForm = $blockData['contact_form_id'] ?? null;
    }

    if (empty($selectedForm) && function_exists('get_field')) {
        $selectedForm = get_field('contact_form_id', false, false);
    }

    if (is_array($selectedForm)) {
        $selectedForm = $selectedForm['value'] ?? $selectedForm['ID'] ?? $selectedForm['id'] ?? '';
    }

    if ($selectedForm instanceof \WP_Post) {
        $selectedForm = $selectedForm->ID;
    } elseif (is_object($selectedForm) && method_exists($selectedForm, 'id')) {
        $selectedForm = $selectedForm->id();
    }

    if (is_scalar($selectedForm)) {
        $formReference = trim((string) $selectedForm);
    }

    if (preg_match('/\[contact-form-7\b[^\]]*\bid=["\']?([a-f0-9]+)["\']?/i', $formReference, $formMatch)) {
        $formReference = $formMatch[1];
    } elseif (preg_match('/\(ID:\s*(\d+)\)\s*$/i', $formReference, $formMatch)) {
        $formReference = $formMatch[1];
    }

    if ($formReference === '') {
        $formError = 'Nie odczytano wyboru formularza z ACF. Zapisz/zaaktualizuj stronę po jego wybraniu.';
    } elseif (!class_exists('WPCF7_ContactForm') || !shortcode_exists('contact-form-7')) {
        $formError = 'Contact Form 7 nie jest aktywny.';
    } else {
        $contactForm = null;

        if (ctype_digit($formReference)) {
            $contactForm = \WPCF7_ContactForm::get_instance((int) $formReference);
        } elseif (preg_match('/^[a-f0-9]{7,}$/i', $formReference) && function_exists('wpcf7_get_contact_form_by_hash')) {
            $contactForm = wpcf7_get_contact_form_by_hash(strtolower($formReference));
        }

        if ($contactForm) {
            $formShortcode = trim((string) $contactForm->shortcode());
            $formShortcode = preg_replace('/\]$/', ' html_class="appandsite-contact-form"]', $formShortcode);
            $formHtml = do_shortcode($formShortcode);
            $formTags = new \WP_HTML_Tag_Processor($formHtml);

            while ($formTags->next_tag(['class_name' => 'app-contact-submit-wrap'])) {
                $formTags->add_class('btn');
                $formTags->add_class('btn-primary');
            }

            $formHtml = $formTags->get_updated_html();

            if (trim($formHtml) === '' || str_contains($formHtml, 'wpcf7-contact-form-not-found')) {
                $formHtml = '';
                $formError = 'CF7 nie zwrócił prawidłowego formularza dla wybranej pozycji.';
            }
        } else {
            $formError = 'Nie znaleziono formularza CF7 dla identyfikatora: ' . $formReference . '.';
        }
    }

    $quickLink = is_array($quickButton ?? null) ? $quickButton : [];
    $quickUrl = trim((string) ($quickLink['url'] ?? ''));
    $quickLabel = trim((string) ($quickLink['title'] ?? ''));
    $quickTarget = ($quickLink['target'] ?? '') === '_blank' ? '_blank' : '_self';
    $showQuick = !empty($quickIcon) || !empty($quickTitle) || !empty($quickDescription) || ($quickUrl !== '' && $quickLabel !== '');
@endphp

<x-block-helper.block-wrapper :block="$block" :attributes="$attributes">
    <section @if(!$isEditor) x-data="alpContactSection()" @endif class="relative bg-white">
        <x-ui.container>
            <div class="grid min-w-0 grid-cols-1 items-start gap-10 lg:grid-cols-5 lg:gap-12 xl:gap-16">
                <div class="min-w-0 lg:col-span-3">
                    @if(!empty($prefix))
                        <div @if(!$isEditor) data-aos="fade-right" data-aos-duration="500" data-aos-once="true" data-aos-easing="ease-out-cubic" @endif class="mb-5 flex items-center gap-3 text-[11px] font-semibold uppercase tracking-[0.14em] text-muted sm:text-[12px]">
                            <span class="flex h-[24px] w-[24px] shrink-0 items-center justify-center rounded-[6px] border border-brand/60 bg-brand/10 text-brand">
                                <svg class="h-[13px] w-[13px]" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                    <path d="M12 3L13.6 8.4L19 10L13.6 11.6L12 17L10.4 11.6L5 10L10.4 8.4L12 3Z" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round" />
                                    <path d="M18.5 15.5L19.2 17.8L21.5 18.5L19.2 19.2L18.5 21.5L17.8 19.2L15.5 18.5Z" stroke="currentColor" stroke-width="1.5" stroke-linejoin="round" />
                                </svg>
                            </span>
                            <span>{{ $prefix }}</span>
                        </div>
                    @endif

                    @if($titleHtml !== '')
                        <h2 @if(!$isEditor) data-aos="fade-up" data-aos-duration="650" data-aos-delay="80" data-aos-once="true" data-aos-easing="ease-out-cubic" @endif class="w-full">{!! $titleHtml !!}</h2>
                    @endif

                    @if($descriptionHtml !== '')
                        <div @if(!$isEditor) data-aos="fade-up" data-aos-duration="650" data-aos-delay="150" data-aos-once="true" data-aos-easing="ease-out-cubic" @endif class="wysiwyg mt-5 max-w-[690px] [&_p]:!m-0 [&_p+p]:!mt-3">
                            {!! $descriptionHtml !!}
                        </div>
                    @endif

                    <div @if(!$isEditor) data-aos="fade-up" data-aos-duration="700" data-aos-delay="200" data-aos-once="true" data-aos-easing="ease-out-cubic" @endif class="mt-8 min-w-0 sm:mt-9 [&_.app-contact-fields]:grid [&_.app-contact-fields]:grid-cols-1 sm:[&_.app-contact-fields]:grid-cols-2 [&_.app-contact-fields]:gap-x-5 [&_.app-contact-fields]:gap-y-5 [&_.app-contact-field]:flex [&_.app-contact-field]:min-w-0 [&_.app-contact-field]:flex-col [&_.app-contact-field]:gap-2 [&_.app-contact-field_p]:contents [&_.app-contact-field_br]:hidden [&_.app-contact-full]:col-span-full [&_.app-contact-label]:!m-0 [&_.app-contact-label]:block [&_.app-contact-label]:!text-[13px] [&_.app-contact-label]:!font-medium [&_.app-contact-label]:!leading-[1.4] [&_.app-contact-label]:!text-text [&_.app-contact-label_strong]:!text-brand-active [&_.wpcf7-form-control-wrap]:block [&_.wpcf7-form-control-wrap]:w-full [&_.app-contact-message]:!min-h-[160px] [&_.app-contact-message]:!h-[170px] [&_.app-contact-message]:!overflow-y-auto [&_.app-contact-consent]:mt-5 [&_.app-contact-consent]:text-[12px] [&_.app-contact-consent]:leading-[1.7] [&_.app-contact-consent]:text-muted [&_.app-contact-consent_p]:!m-0 [&_.app-contact-consent_br]:hidden [&_.app-contact-consent_a]:font-medium [&_.app-contact-consent_a]:text-brand-active [&_.app-contact-consent_a]:underline [&_.app-contact-consent_a]:underline-offset-4 [&_.wpcf7-list-item]:!m-0 [&_.wpcf7-list-item_label]:text-muted [&_.app-contact-actions]:mt-6 [&_.app-contact-actions]:flex [&_.app-contact-actions]:flex-col [&_.app-contact-actions]:items-stretch [&_.app-contact-actions]:gap-4 sm:[&_.app-contact-actions]:flex-row sm:[&_.app-contact-actions]:items-center sm:[&_.app-contact-actions]:justify-between [&_.app-contact-actions_p]:contents [&_.app-contact-actions_br]:hidden [&_.app-contact-submit-wrap]:relative [&_.app-contact-submit-wrap]:isolate [&_.app-contact-submit-wrap]:inline-flex [&_.app-contact-submit-wrap]:min-h-[52px] [&_.app-contact-submit-wrap]:w-fit [&_.app-contact-submit-wrap]:max-w-full [&_.app-contact-submit-wrap]:self-start [&_.app-contact-submit-wrap]:shrink-0 [&_.app-contact-submit-wrap]:items-center [&_.app-contact-submit-wrap]:overflow-hidden [&_.app-contact-submit-wrap]:rounded-[8px] [&_.app-contact-submit-wrap_p]:contents [&_.app-contact-submit-wrap_br]:hidden [&_.app-contact-submit]:relative [&_.app-contact-submit]:z-10 [&_.app-contact-submit]:!m-0 [&_.app-contact-submit]:!h-[52px] [&_.app-contact-submit]:!w-auto [&_.app-contact-submit]:max-w-full [&_.app-contact-submit]:!cursor-pointer [&_.app-contact-submit]:!border-0 [&_.app-contact-submit]:!rounded-[8px] [&_.app-contact-submit]:!bg-transparent [&_.app-contact-submit]:!px-6 [&_.app-contact-submit]:!pr-14 [&_.app-contact-submit]:!text-[14px] [&_.app-contact-submit]:!font-bold [&_.app-contact-submit]:!text-current [&_.app-contact-submit]:!shadow-none [&_.app-contact-submit]:!transition-none [&_.app-contact-submit-wrap_svg]:pointer-events-none [&_.app-contact-submit-wrap_svg]:absolute [&_.app-contact-submit-wrap_svg]:right-5 [&_.app-contact-submit-wrap_svg]:z-10 [&_.app-contact-submit-wrap_svg]:h-[16px] [&_.app-contact-submit-wrap_svg]:w-[16px] [&_.app-contact-submit-wrap_svg]:transition-transform [&_.app-contact-submit-wrap_svg]:duration-300 [&_.wpcf7-not-valid-tip]:mt-1 [&_.wpcf7-not-valid-tip]:text-[12px] [&_.wpcf7-response-output]:!mx-0 [&_.wpcf7-response-output]:!mb-0 [&_.wpcf7-response-output]:!mt-4 [&_.wpcf7-response-output]:rounded-[8px] [&_.wpcf7-response-output]:!border-border [&_.wpcf7-response-output]:!p-3 [&_.app-contact-submit-wrap_.wpcf7-spinner]:!absolute [&_.app-contact-submit-wrap_.wpcf7-spinner]:right-4 [&_.app-contact-submit-wrap_.wpcf7-spinner]:top-1/2 [&_.app-contact-submit-wrap_.wpcf7-spinner]:z-20 [&_.app-contact-submit-wrap_.wpcf7-spinner]:!m-0 [&_.app-contact-submit-wrap_.wpcf7-spinner]:-translate-y-1/2 [&_.wpcf7-form.submitting_.app-contact-submit-wrap_svg]:invisible [&_.app-contact-submit-wrap]:!p-0">
                        @if($formHtml !== '')
                            {!! $formHtml !!}
                        @elseif(current_user_can('manage_options'))
                            <div class="rounded-[12px] border border-border bg-surface p-5 text-[14px] text-muted">
                                <strong class="mb-2 block text-primary">Nie można wyświetlić formularza CF7</strong>
                                <p class="m-0">{{ $formError }}</p>
                                <p class="mt-2 mb-0">{{ __('ID wybrane w bloku:', 'appandsite') }} {{ $formReference !== '' ? $formReference : __('brak', 'appandsite') }}</p>
                            </div>
                        @else
                            <p class="text-muted">{{ __('Formularz kontaktowy jest chwilowo niedostępny.', 'appandsite') }}</p>
                        @endif
                    </div>

                    @if(!empty($formNote))
                        <div x-ref="formNote" class="mt-5 flex items-center gap-2 text-[12px] leading-[1.5] text-muted sm:text-[13px] sm:[&.app-contact-note-in-actions]:ml-auto sm:[&.app-contact-note-in-actions]:text-right [&.app-contact-note-in-actions]:!mt-0">
                            <svg class="h-[16px] w-[16px] shrink-0 text-brand-active" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                <circle cx="12" cy="12" r="9" stroke="currentColor" stroke-width="1.8" />
                                <path d="M12 7V12L15 14" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" />
                            </svg>
                            <span>{{ $formNote }}</span>
                        </div>
                    @endif
                </div>

                <div class="flex min-w-0 flex-col gap-4 lg:col-span-2">
                    <div @if(!$isEditor) data-aos="fade-left" data-aos-duration="650" data-aos-delay="120" data-aos-once="true" data-aos-easing="ease-out-cubic" @endif class="rounded-[14px] border border-border bg-gradient-to-br from-surface via-white to-surface-alt/70 p-5 shadow-card-soft sm:p-7 lg:p-6 xl:p-8">
                        @if(!empty($contactTitle))
                            <h3 class="heading-5">{{ $contactTitle }}</h3>
                        @endif

                        @if(!empty($contactDescription))
                            <p class="mt-2 !text-[13px] !leading-[1.65] text-muted sm:!text-[14px]">{!! $contactDescriptionHtml !!}</p>
                        @endif

                        @if(!empty($contactItems))
                            <div class="mt-6 flex flex-col gap-5">
                                @foreach($contactItems as $item)
                                    <div class="group flex min-w-0 items-start gap-4">
                                        @if(!empty($item['icon']['ID']))
                                            <span class="flex h-[52px] w-[52px] shrink-0 items-center justify-center rounded-full bg-brand/10 transition-transform duration-500 ease-out group-hover:scale-110">
                                                {!! wp_get_attachment_image((int) $item['icon']['ID'], 'thumbnail', false, ['class' => 'icon-tilt h-[27px] w-[27px] object-contain', 'alt' => '', 'aria-hidden' => 'true', 'loading' => 'lazy']) !!}
                                            </span>
                                        @endif

                                        <div class="min-w-0 flex-1 pt-0.5">
                                            @if(!empty($item['title']))
                                                <div class="relative inline-block max-w-full pb-[4px]">
                                                    <h4 class="heading-6 !m-0">{{ $item['title'] }}</h4>
                                                    <span aria-hidden="true" class="hover-underline"></span>
                                                </div>
                                            @endif

                                            @if(!empty($item['description']))
                                                @if(!empty($item['href']))
                                                    <a href="{{ esc_url($item['href']) }}" class="mt-1 block w-fit max-w-full break-words whitespace-pre-line text-[13px] leading-[1.6] text-text no-underline transition-colors duration-300 hover:text-brand-active sm:text-[14px]">{{ $item['description'] }}</a>
                                                @else
                                                    <p class="mt-1 !text-[13px] !leading-[1.6] whitespace-pre-line text-muted sm:!text-[14px]">{{ $item['description'] }}</p>
                                                @endif
                                            @endif
                                        </div>

                                        @if(!empty($item['copy']) && !$isEditor)
                                            <button type="button" data-copy="{{ esc_attr($item['copy']) }}" x-on:click="copyText($el.dataset.copy, {{ $loop->index }})" x-bind:aria-label="copiedIndex === {{ $loop->index }} ? '{{ esc_attr__('Skopiowano', 'appandsite') }}' : '{{ esc_attr__('Kopiuj dane kontaktowe', 'appandsite') }}'" class="mt-2 flex h-9 w-9 shrink-0 items-center justify-center rounded-[8px] text-brand-active transition-[background-color,transform] duration-300 ease-out hover:bg-brand/10 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-brand">
                                                <svg x-show="copiedIndex !== {{ $loop->index }}" class="h-[17px] w-[17px]" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                                    <rect x="8" y="8" width="11" height="12" rx="2" stroke="currentColor" stroke-width="1.7" />
                                                    <path d="M16 8V6C16 4.89543 15.1046 4 14 4H6C4.89543 4 4 4.89543 4 6V15C4 16.1046 4.89543 17 6 17H8" stroke="currentColor" stroke-width="1.7" />
                                                </svg>
                                                <svg x-show="copiedIndex === {{ $loop->index }}" x-cloak class="h-[17px] w-[17px]" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                                    <path d="M5 12L10 17L19 7" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                                </svg>
                                            </button>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        @endif

                        @if(!empty($socialItems))
                            <div class="mt-7">
                                @if(!empty($socialTitle))
                                    <h4 class="heading-6 mb-3">{{ $socialTitle }}</h4>
                                @endif

                                <div class="flex flex-wrap gap-3">
                                    @foreach($socialItems as $social)
                                        @php
                                            $socialLink = $social['link'];
                                            $socialTarget = ($socialLink['target'] ?? '') === '_blank' ? '_blank' : '_self';
                                        @endphp
                                        <a href="{{ esc_url($socialLink['url']) }}" target="{{ esc_attr($socialTarget) }}" @if($socialTarget === '_blank') rel="noopener noreferrer" @endif aria-label="{{ esc_attr($socialLink['title'] ?: __('Profil społecznościowy', 'appandsite')) }}" class="group/social flex h-[48px] w-[48px] items-center justify-center rounded-[10px] border border-border bg-white/80 shadow-card-subtle transition-[box-shadow,border-color,background-color] duration-[700ms] ease-[cubic-bezier(0.22,1,0.36,1)] hover:border-brand/50 hover:bg-brand/10 hover:shadow-card-hover-soft focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-brand">
                                            @if(!empty($social['icon']['ID']))
                                                {!! wp_get_attachment_image((int) $social['icon']['ID'], 'thumbnail', false, ['class' => 'icon-tilt h-[23px] w-[23px] object-contain group-hover/social:scale-110 motion-reduce:transition-none', 'alt' => '', 'aria-hidden' => 'true', 'loading' => 'lazy']) !!}
                                            @else
                                                <svg class="icon-tilt h-5 w-5 text-brand-active group-hover/social:scale-110 motion-reduce:transition-none" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                                    <path d="M10 14L14 10M8 16H6C3 16 3 13 5 11L8 8C10 6 13 7 14 8M16 8H18C21 8 21 11 19 13L16 16C14 18 11 17 10 16" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" />
                                                </svg>
                                            @endif
                                        </a>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>

                    @if($showQuick)
                        <div @if(!$isEditor) data-aos="fade-up" data-aos-duration="700" data-aos-delay="200" data-aos-once="true" data-aos-easing="ease-out-cubic" @endif class="relative overflow-hidden rounded-[13px] border border-primary/20 bg-primary-dark p-5 shadow-card-soft sm:p-6">
                            <div aria-hidden="true" class="pointer-events-none absolute -right-16 -bottom-20 h-[190px] w-[190px] rounded-full bg-brand/15 blur-[55px]"></div>
                            <div class="relative flex flex-col items-start gap-5 sm:flex-row sm:items-center sm:justify-between sm:gap-4">
                                <div class="flex min-w-0 items-center gap-3">
                                    @if(!empty($quickIcon['ID']))
                                        <span class="flex h-[50px] w-[50px] shrink-0 items-center justify-center rounded-full bg-brand/10 ring-1 ring-brand/40">
                                            {!! wp_get_attachment_image((int) $quickIcon['ID'], 'thumbnail', false, ['class' => 'h-[29px] w-[29px] object-contain', 'alt' => '', 'aria-hidden' => 'true', 'loading' => 'lazy']) !!}
                                        </span>
                                    @endif
                                    <div class="min-w-0">
                                        @if(!empty($quickTitle))
                                            <h3 class="heading-6 !m-0 !text-white">{{ $quickTitle }}</h3>
                                        @endif
                                        @if(!empty($quickDescription))
                                            <p class="mt-1 !text-[13px] !leading-[1.5] !text-white/75">{{ $quickDescription }}</p>
                                        @endif
                                    </div>
                                </div>

                                @if($quickUrl !== '' && $quickLabel !== '')
                                    <a href="{{ esc_url($quickUrl) }}" target="{{ esc_attr($quickTarget) }}" @if($quickTarget === '_blank') rel="noopener noreferrer" @endif class="btn btn-brand-outline group/button min-h-[45px] w-full shrink-0 gap-3 rounded-[8px] border border-white/35 px-4 text-[12px] font-semibold hover:border-brand focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-brand sm:w-auto">
                                        <span class="relative z-10">{{ $quickLabel }}</span>
                                        <svg class="relative z-10 h-[16px] w-[16px] shrink-0 transition-transform duration-300 group-hover/button:translate-x-1 motion-reduce:transition-none" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                            <path d="M5 12H19M13 6L19 12L13 18" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                        </svg>
                                    </a>
                                @endif
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </x-ui.container>
    </section>
</x-block-helper.block-wrapper>
