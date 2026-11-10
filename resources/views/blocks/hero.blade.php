<x-block-helper.block-wrapper :block="$block" :attributes="$attributes">
    <section class="relative bg-white">
        <x-ui.container>
            @if(!empty($title))
                <h2 class="!m-0 text-primary">
                    {{ $title }}
                </h2>
            @endif

            @if(!empty($image['url']))
                <div class="mt-6 overflow-hidden rounded-[14px] border border-border bg-surface">
                    {!! wp_get_attachment_image((int) ($image['ID'] ?? $image['id'] ?? 0), 'large', false, ['class' => 'block h-auto w-full object-cover', 'alt' => $image['alt'] ?? '', 'loading' => 'lazy', 'decoding' => 'async']) !!}
                </div>
            @endif
        </x-ui.container>
    </section>
</x-block-helper.block-wrapper>
