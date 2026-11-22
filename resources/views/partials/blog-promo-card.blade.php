<article class="blog-promo-card content-card group flex h-full w-full min-w-0 flex-col rounded-[16px] border border-border bg-surface p-[16px] shadow-card-soft [overflow-wrap:anywhere]">
    @if(!empty($card['image']['ID']))
        <div class="mb-3">
            {!! wp_get_attachment_image($card['image']['ID'], 'medium', false, [
                'class' => 'block h-auto max-h-10 w-auto max-w-full rounded-[9px] object-contain transition-icon duration-700 group-hover:scale-[1.035] motion-reduce:group-hover:scale-100 motion-reduce:transition-none',
                'loading' => 'lazy',
                'decoding' => 'async',
            ]) !!}
        </div>
    @endif
    @if(!empty($card['title']))
        <div class="relative inline-block max-w-full self-start pb-[5px]">
            <h3 class="heading-6">{{ $card['title'] }}</h3>
            <span class="hover-underline"></span>
        </div>
    @endif
    @if(!empty($card['description']))
        <p class="mt-2 whitespace-pre-line text-[13px] leading-[1.6] text-muted">{{ trim((string) $card['description']) }}</p>
    @endif
    @if(!empty($card['copy_url']) || (!empty($card['button']['url']) && !empty($card['button']['title'])))
        <div class="mt-auto flex flex-col items-start gap-2 pt-3">
            @if(!empty($card['copy_url']))
                <button type="button" @if(!$isEditor) @click="copyUrl(@js($card['copy_url']), {{ $cardIndex }})" @endif class="btn btn-outline btn-outline-brand w-full gap-2 font-semibold">
                    <span @if(!$isEditor) x-text="copiedIndex === {{ $cardIndex }} ? @js(__('Skopiowano!', 'appandsite')) : @js($card['copy_label'] ?: __('Kopiuj link', 'appandsite'))" @endif>{{ $card['copy_label'] ?: __('Kopiuj link', 'appandsite') }}</span>
                </button>
            @endif
            @if(!empty($card['button']['url']) && !empty($card['button']['title']))
                @php($buttonTarget = ($card['button']['target'] ?? '') === '_blank' ? '_blank' : '_self')
                <a href="{{ esc_url($card['button']['url']) }}" target="{{ $buttonTarget }}" @if($buttonTarget === '_blank') rel="noopener noreferrer" @endif class="btn btn-primary w-full gap-3 font-bold"><span>{{ $card['button']['title'] }}</span><x-ui.arrow /></a>
            @endif
        </div>
    @endif
</article>
