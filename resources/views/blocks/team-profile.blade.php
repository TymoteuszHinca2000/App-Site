@php
    $isEditor = is_admin();
    $heading = static function ($value): string {
        $html = wp_kses((string) $value, ['strong' => [], 'b' => [], 'em' => [], 'i' => [], 'br' => [], 'p' => []]);
        $html = preg_replace('/<\/p>\s*<p[^>]*>/i', '<br>', $html);
        $html = preg_replace('/<\/?p\b[^>]*>/i', '', $html);
        $html = preg_replace('/<(strong|b)>/i', '<span class="text-brand">', $html);

        return preg_replace('/<\/(strong|b)>/i', '</span>', $html);
    };

    $aboutHeading = $heading($aboutTitle ?? '');
    $specialtiesHeading = $heading($specialtiesTitle ?? '');
    $technologiesHeading = $heading($technologiesTitle ?? '');
    $aboutHtml = wp_kses_post($aboutDescription ?? '');
    $hasAbout = !empty($aboutPrefix) || trim(wp_strip_all_tags($aboutHeading . $aboutHtml)) !== '';
    $hasSpecialties = !empty($specialtiesPrefix) || trim(wp_strip_all_tags($specialtiesHeading)) !== '' || !empty($specialties);
    $hasTechnologies = !empty($technologiesPrefix) || trim(wp_strip_all_tags($technologiesHeading)) !== '' || !empty($technologyIds);

    $portrait = wp_get_attachment_image($photoId ?? 0, 'large', false, [
        'class' => 'block aspect-[4/5] w-full object-cover object-top',
        'alt' => $personName ?? '',
        'decoding' => 'async',
    ]);
    $contacts = [];
    if (!empty($email)) {
        $contacts[] = ['label' => __('E-mail', 'appandsite'), 'value' => $email, 'url' => 'mailto:' . $email, 'iconId' => $emailIconId, 'external' => false];
    }
    if (!empty($phoneHref)) {
        $contacts[] = ['label' => __('Telefon', 'appandsite'), 'value' => $phone, 'url' => 'tel:' . $phoneHref, 'iconId' => $phoneIconId, 'external' => false];
    }
    foreach (['LinkedIn' => [$linkedin, $linkedinIconId], 'GitHub' => [$github, $githubIconId]] as $label => [$url, $iconId]) {
        if ($url) {
            $value = preg_replace('#^https?://(www\.)?#', '', rtrim($url, '/'));
            $contacts[] = ['label' => $label, 'value' => $value, 'url' => $url, 'iconId' => $iconId, 'external' => true];
        }
    }

    $hasSidebar = $portrait || $contacts;
    $hasDetails = $hasAbout || $hasSpecialties || $hasTechnologies;
@endphp

@if($hasSidebar || $hasDetails)
    <x-block-helper.block-wrapper :block="$block" :attributes="$attributes">
        <section class="relative overflow-x-clip bg-white">
            <x-ui.container>
                <div class="grid items-start gap-8 {{ $hasSidebar && $hasDetails ? 'lg:grid-cols-[minmax(0,0.36fr)_minmax(0,0.64fr)] lg:gap-12 xl:gap-16' : '' }}">
                    @if($hasSidebar)
                        <aside @if(!$isEditor) data-aos="fade-right" data-aos-duration="650" data-aos-once="true" @endif class="min-w-0 max-w-[520px] overflow-hidden rounded-[14px] bg-primary-dark shadow-card-soft" aria-label="{{ esc_attr__('Profil i kontakt', 'appandsite') }}">
                            {!! $portrait !!}
                            @if($contacts)
                                <ul class="space-y-1 px-4 py-4 sm:px-5">
                                    @foreach($contacts as $contact)
                                        <li>
                                            <a href="{{ esc_url($contact['url']) }}" @if($contact['external']) target="_blank" rel="noopener noreferrer" @endif aria-label="{{ $contact['label'] . ': ' . $contact['value'] }}" class="group flex min-w-0 items-center gap-3 rounded-[8px] px-2 py-2 text-[13px] font-semibold leading-snug text-white transition-colors duration-500 ease-out hover:bg-white/10 focus-visible:bg-white/10 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-brand motion-reduce:transition-none sm:text-[14px]">
                                                @if($contact['iconId'])
                                                    {!! wp_get_attachment_image($contact['iconId'], 'thumbnail', false, ['class' => 'icon-tilt h-5 w-5 shrink-0 object-contain', 'alt' => '', 'aria-hidden' => 'true']) !!}
                                                @endif
                                                <span class="min-w-0 flex-1 break-words">
                                                    <span class="relative inline-block max-w-full pb-0.5">
                                                        {{ $contact['value'] }}
                                                        <span class="hover-underline" aria-hidden="true"></span>
                                                    </span>
                                                </span>
                                                <x-ui.arrow class="text-white/60 group-hover:text-brand group-focus-visible:text-brand" />
                                            </a>
                                        </li>
                                    @endforeach
                                </ul>
                            @endif
                        </aside>
                    @endif

                    @if($hasDetails)
                        <div class="min-w-0 space-y-8 lg:space-y-10">
                            @if($hasAbout)
                                <div @if(!$isEditor) data-aos="fade-up" data-aos-duration="650" data-aos-once="true" @endif class="space-y-4">
                                    @if(!empty($aboutPrefix))
                                        <x-ui.section-prefix>{{ $aboutPrefix }}</x-ui.section-prefix>
                                    @endif
                                    @if(trim(wp_strip_all_tags($aboutHeading)) !== '')
                                        <h2>{!! $aboutHeading !!}</h2>
                                    @endif
                                    @if(trim(wp_strip_all_tags($aboutHtml)) !== '')
                                        <div class="wysiwyg">{!! $aboutHtml !!}</div>
                                    @endif
                                </div>
                            @endif

                            @if($hasSpecialties)
                                <div class="space-y-4">
                                    @if(!empty($specialtiesPrefix))
                                        <x-ui.section-prefix>{{ $specialtiesPrefix }}</x-ui.section-prefix>
                                    @endif
                                    @if(trim(wp_strip_all_tags($specialtiesHeading)) !== '')
                                        <h3>{!! $specialtiesHeading !!}</h3>
                                    @endif
                                    @if(!empty($specialties))
                                        <ul class="grid grid-cols-1 gap-3 sm:grid-cols-2 xl:grid-cols-3">
                                            @foreach($specialties as $specialty)
                                                <li @if(!$isEditor) data-aos="fade-up" data-aos-duration="550" data-aos-once="true" @endif class="min-w-0">
                                                    <div class="team-card group flex h-full items-center gap-3 rounded-[12px] border border-border bg-surface p-3 shadow-card-subtle">
                                                        @if($specialty['iconId'])
                                                            <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-full bg-brand/10 transition-icon duration-700 group-hover:bg-brand/15 motion-reduce:transition-none">
                                                                {!! wp_get_attachment_image($specialty['iconId'], 'thumbnail', false, ['class' => 'icon-tilt h-6 w-6 object-contain', 'alt' => '', 'aria-hidden' => 'true', 'loading' => 'lazy']) !!}
                                                            </span>
                                                        @endif
                                                        @if($specialty['title'] !== '' || $specialty['description'] !== '')
                                                            <div class="min-w-0 break-words">
                                                                @if($specialty['title'] !== '')
                                                                    <h4 class="heading-6">{{ $specialty['title'] }}</h4>
                                                                @endif
                                                                @if($specialty['description'] !== '')
                                                                    <p class="text-[13px] leading-relaxed text-muted">{{ $specialty['description'] }}</p>
                                                                @endif
                                                            </div>
                                                        @endif
                                                    </div>
                                                </li>
                                            @endforeach
                                        </ul>
                                    @endif
                                </div>
                            @endif

                            @if($hasTechnologies)
                                <div class="space-y-4">
                                    @if(!empty($technologiesPrefix))
                                        <x-ui.section-prefix>{{ $technologiesPrefix }}</x-ui.section-prefix>
                                    @endif
                                    @if(trim(wp_strip_all_tags($technologiesHeading)) !== '')
                                        <h3>{!! $technologiesHeading !!}</h3>
                                    @endif
                                    @if(!empty($technologyIds))
                                        <div @if(!$isEditor && count($technologyIds) > 1) x-data="alpTeamStack()" @endif class="team-stack swiper">
                                            <ul class="swiper-wrapper">
                                                @foreach($technologyIds as $id)
                                                    <li class="swiper-slide group rounded-full border border-border bg-surface transition-colors duration-500 ease-out hover:border-brand/45 hover:bg-brand/10 motion-reduce:transition-none">
                                                        {!! wp_get_attachment_image($id, 'thumbnail', false, ['class' => 'icon-tilt h-1/2 w-1/2 object-contain', 'decoding' => 'async']) !!}
                                                    </li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    @endif
                                </div>
                            @endif
                        </div>
                    @endif
                </div>
            </x-ui.container>
        </section>
    </x-block-helper.block-wrapper>
@endif
