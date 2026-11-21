@php
    $isEditor = is_admin();
    $titleHtml = wp_kses($title ?? '', ['strong' => [], 'b' => [], 'em' => [], 'i' => [], 'br' => [], 'p' => []]);
    $titleHtml = preg_replace('/<\/p>\s*<p[^>]*>/i', '<br>', $titleHtml);
    $titleHtml = preg_replace('/<\/?p\b[^>]*>/i', '', $titleHtml);
    $titleHtml = preg_replace('/<(strong|b)>/i', '<span class="text-brand">', $titleHtml);
    $titleHtml = preg_replace('/<\/(strong|b)>/i', '</span>', $titleHtml);
    $descriptionHtml = wp_kses_post($description ?? '');
    $hasTitle = trim(wp_strip_all_tags($titleHtml)) !== '';
    $hasDescription = trim(wp_strip_all_tags($descriptionHtml)) !== '';
    $buttonUrl = esc_url($button['url'] ?? '');
    $buttonText = trim((string) ($button['title'] ?? ''));
    $buttonTarget = ($button['target'] ?? '') === '_blank' ? '_blank' : '_self';
    $hasButton = $buttonUrl && $buttonText !== '';
    $hasHeader = !empty($prefix) || $hasTitle || $hasDescription || $hasButton;
@endphp

@if($hasHeader || !empty($members))
    <x-block-helper.block-wrapper :block="$block" :attributes="$attributes">
        <section class="relative overflow-x-clip bg-white">
            <div class="brand-dots-pattern pointer-events-none absolute -right-12 top-6 hidden h-[210px] w-[250px] opacity-40 lg:block" aria-hidden="true"></div>
            <x-ui.container>
                @if($hasHeader)
                    <div class="relative z-10 flex flex-col justify-between gap-6 lg:flex-row lg:items-end lg:gap-12 {{ !empty($members) ? 'mb-8 lg:mb-10' : '' }}">
                        <div class="max-w-[930px]">
                            @if(!empty($prefix))
                                <x-ui.section-prefix class="mb-4" :data-aos="$isEditor ? null : 'fade-right'" data-aos-duration="500" data-aos-once="true">{{ $prefix }}</x-ui.section-prefix>
                            @endif
                            @if($hasTitle)
                                <h2 @if(!$isEditor) data-aos="fade-up" data-aos-duration="650" data-aos-once="true" @endif class="max-w-[900px]">{!! $titleHtml !!}</h2>
                            @endif
                            @if($hasDescription)
                                <div @if(!$isEditor) data-aos="fade-up" data-aos-duration="650" data-aos-delay="80" data-aos-once="true" @endif class="wysiwyg mt-4 max-w-[830px]">{!! $descriptionHtml !!}</div>
                            @endif
                        </div>
                        @if($hasButton)
                            <a href="{{ $buttonUrl }}" target="{{ $buttonTarget }}" @if($buttonTarget === '_blank') rel="noopener noreferrer" @endif class="btn btn-outline btn-outline-brand min-h-[50px] w-full shrink-0 gap-3 px-6 text-[14px] sm:w-fit"><span>{{ $buttonText }}</span><x-ui.arrow /></a>
                        @endif
                    </div>
                @endif
                @if(!empty($members))
                    <div class="relative z-10 grid grid-cols-1 gap-5 md:grid-cols-2 xl:grid-cols-3 xl:gap-6">
                        @foreach($members as $member)
                            <article @if(!$isEditor) data-aos="fade-up" data-aos-duration="650" data-aos-delay="{{ ($loop->index % 3) * 85 }}" data-aos-once="true" @endif class="min-w-0">
                                <div class="team-card flex h-full min-w-0 flex-col rounded-[14px] border border-border bg-white p-3 shadow-card-subtle sm:p-4">
                                    @php($photo = wp_get_attachment_image($member['photoId'], 'large', false, ['class' => 'block aspect-[16/9] w-full object-cover object-center transition-icon duration-700 group-hover/photo:scale-105 group-focus-visible/photo:scale-105 motion-reduce:group-hover/photo:scale-100 motion-reduce:group-focus-visible/photo:scale-100 motion-reduce:transition-none', 'alt' => '', 'loading' => 'lazy', 'decoding' => 'async']))
                                    @if($photo)
                                        <a href="{{ esc_url($member['url']) }}" aria-label="{{ esc_attr(sprintf(__('Poznaj pracownika: %s', 'appandsite'), $member['name'])) }}" class="group/photo block overflow-hidden rounded-[10px] bg-surface-alt focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-brand">{!! $photo !!}</a>
                                    @endif
                                    <div class="flex flex-1 flex-col px-1 pb-1 pt-4 sm:px-2">
                                        <h3 class="heading-5"><a href="{{ esc_url($member['url']) }}" class="transition-colors duration-300 hover:text-brand-active focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-brand">{{ $member['name'] }}</a></h3>
                                        @if($member['position'] !== '')
                                            <div class="mt-2">
                                                <span class="group inline-flex max-w-full items-center gap-2 rounded-full bg-brand/15 px-3 py-1.5 text-[12px] font-bold leading-[1.4] text-primary transition-colors duration-500 ease-out hover:bg-gray-200 motion-reduce:transition-none sm:text-[13px]">
                                                    @if($member['positionIconId'])
                                                        {!! wp_get_attachment_image($member['positionIconId'], 'thumbnail', false, ['class' => 'icon-tilt h-4 w-4 shrink-0 object-contain', 'alt' => '', 'aria-hidden' => 'true']) !!}
                                                    @endif
                                                    <span>{{ $member['position'] }}</span>
                                                </span>
                                            </div>
                                        @endif
                                        @if($member['excerpt'] !== '')
                                            <p class="mt-3 mb-4 text-[13px] leading-[1.65] text-text sm:text-[14px]">{{ $member['excerpt'] }}</p>
                                        @endif
                                        @if($member['linkedin'] || $member['github'])
                                            <div class="mt-auto flex items-center gap-2 pt-2">
                                                @foreach(['LinkedIn' => [$member['linkedin'], $member['linkedinIconId']], 'GitHub' => [$member['github'], $member['githubIconId']]] as $label => [$url, $iconId])
                                                    @if($url)
                                                        @php($icon = wp_get_attachment_image($iconId, 'thumbnail', false, ['class' => 'icon-tilt relative z-10 h-5 w-5 object-contain', 'alt' => '', 'aria-hidden' => 'true']))
                                                        <a href="{{ esc_url($url) }}" target="_blank" rel="noopener noreferrer" aria-label="{{ $label . ': ' . $member['name'] }}" class="group/social btn btn-outline btn-icon team-social-link {{ $icon ? '' : 'min-w-[64px] w-auto px-2 text-[11px]' }}">
                                                            @if($icon)
                                                                {!! $icon !!}
                                                            @else
                                                                <span>{{ $label }}</span>
                                                            @endif
                                                        </a>
                                                    @endif
                                                @endforeach
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </article>
                        @endforeach
                    </div>
                @endif
            </x-ui.container>
        </section>
    </x-block-helper.block-wrapper>
@endif
