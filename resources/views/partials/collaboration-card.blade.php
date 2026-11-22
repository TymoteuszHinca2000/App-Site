@php
    $compact = $compact ?? false;
    $isEditor = $isEditor ?? is_admin();
    $collaboration = is_array($collaboration ?? null) ? $collaboration : [];
    $logoId = (int) ($collaboration['logo_id'] ?? 0);
    $sidebarLogoId = (int) ($collaboration['sidebar_logo_id'] ?? 0);
    $cardTitle = (string) ($compact ? ($collaboration['sidebar_title'] ?? '') : ($collaboration['title'] ?? ''));
    $cardDescription = wp_kses_post($compact ? ($collaboration['sidebar_description'] ?? '') : ($collaboration['description'] ?? ''));
    $features = is_array($collaboration['features'] ?? null) ? $collaboration['features'] : [];
    $affiliateUrl = (string) ($collaboration['affiliate_url'] ?? '');
    $affiliateLabel = (string) ($collaboration['affiliate_label'] ?? '');
    $discountCode = trim((string) ($collaboration['discount_code'] ?? ''));
    $copyLabel = trim((string) ($collaboration['copy_label'] ?? '')) ?: __('Kopiuj kod', 'appandsite');
    $hasAffiliate = $affiliateUrl !== '' && $affiliateLabel !== '' && (!$compact || $discountCode === '');
    $hasAction = $hasAffiliate || $discountCode !== '';
    $actionTitle = (string) ($collaboration['action_title'] ?? '');
    $actionDescription = (string) ($collaboration['action_description'] ?? '');
@endphp

<article @if(!$isEditor) x-data="alpCollaborationCard()" @endif class="collaboration-card{{ $compact ? ' collaboration-card--compact w-full' : '' }}{{ !$logoId ? ' collaboration-card--text-only' : '' }}">
    <div class="collaboration-card__main">
        @if($logoId && !$compact)
            <div class="collaboration-card__logo">
                {!! wp_get_attachment_image($logoId, 'medium_large', false, [
                    'class' => 'collaboration-card__logo-image',
                    'loading' => 'lazy',
                    'decoding' => 'async',
                ]) !!}
            </div>
        @endif

        <div class="collaboration-card__content">
            @if(!$compact && !empty($collaboration['prefix']))
                <x-ui.section-prefix class="mb-3">{{ $collaboration['prefix'] }}</x-ui.section-prefix>
            @endif

            @if($compact && $sidebarLogoId)
                <h3 class="collaboration-card__sidebar-brand">
                    {!! wp_get_attachment_image($sidebarLogoId, 'medium', false, [
                        'class' => 'collaboration-card__sidebar-logo',
                        'alt' => $cardTitle !== '' ? $cardTitle : (string) ($collaboration['title'] ?? ''),
                        'loading' => 'lazy',
                        'decoding' => 'async',
                    ]) !!}
                </h3>
            @elseif($cardTitle !== '')
                <h3 class="collaboration-card__title">{{ $cardTitle }}</h3>
            @endif

            @if($cardDescription !== '')
                <div class="collaboration-card__description wysiwyg">{!! $cardDescription !!}</div>
            @endif

            @if(!$compact && !empty($features))
                <ul class="collaboration-card__features">
                    @foreach($features as $feature)
                        <li class="collaboration-card__feature">
                            @if(!empty($feature['icon_id']))
                                <span class="collaboration-card__feature-icon">
                                    {!! wp_get_attachment_image((int) $feature['icon_id'], 'thumbnail', false, [
                                        'class' => 'collaboration-card__icon-image',
                                        'alt' => '',
                                        'aria-hidden' => 'true',
                                        'loading' => 'lazy',
                                        'decoding' => 'async',
                                    ]) !!}
                                </span>
                            @endif
                            <div class="collaboration-card__feature-content">
                                @if(!empty($feature['title']))
                                    <h4 class="collaboration-card__feature-title">{{ $feature['title'] }}</h4>
                                @endif
                                @if(!empty($feature['description']))
                                    <p class="collaboration-card__feature-description">{{ $feature['description'] }}</p>
                                @endif
                            </div>
                        </li>
                    @endforeach
                </ul>
            @endif
        </div>
    </div>

    @if($hasAction)
        <div class="collaboration-card__footer">
            @if(!$compact && ($actionTitle !== '' || $actionDescription !== ''))
                <div class="collaboration-card__callout-content">
                    @if($actionTitle !== '')
                        <h4 class="collaboration-card__action-title">{{ $actionTitle }}</h4>
                    @endif
                    @if($actionDescription !== '')
                        <p class="collaboration-card__action-description">{{ $actionDescription }}</p>
                    @endif
                </div>
            @endif
            <div class="collaboration-card__actions">
                @if($hasAffiliate)
                    <a href="{{ esc_url($affiliateUrl) }}" target="_blank" rel="sponsored noopener noreferrer" class="btn btn-primary collaboration-card__button swiper-no-swiping">
                        <span>{{ $affiliateLabel }}</span>
                        <x-ui.arrow />
                    </a>
                @endif

                @if($discountCode !== '')
                    <div class="collaboration-card__discount swiper-no-swiping">
                        <div class="collaboration-card__copy-row">
                            <code class="collaboration-card__code" aria-label="{{ esc_attr__('Kod rabatowy', 'appandsite') }}">{{ $discountCode }}</code>
                            <button type="button" @if($isEditor) disabled @else @click="copyCode(@js($discountCode))" @endif class="btn btn-primary collaboration-card__button collaboration-card__copy">
                                <span @if(!$isEditor) x-text="copied ? @js(__('Skopiowano', 'appandsite')) : @js($copyLabel)" @endif>{{ $copyLabel }}</span>
                            </button>
                        </div>
                        <p class="collaboration-card__copy-status" role="status" aria-live="polite" aria-atomic="true" @if(!$isEditor) x-text="copied ? @js(__('Kod skopiowany do schowka.', 'appandsite')) : (copyError ? @js(__('Nie udało się skopiować kodu. Zaznacz go i skopiuj ręcznie.', 'appandsite')) : '')" @endif></p>
                    </div>
                @endif

            </div>
        </div>
    @endif
</article>
