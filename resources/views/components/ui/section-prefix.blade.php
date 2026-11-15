<div {{ $attributes->class(['flex items-center gap-3 text-[11px] font-semibold uppercase tracking-[0.14em] text-muted sm:text-[12px]']) }}>
    <span class="flex h-[24px] w-[24px] shrink-0 items-center justify-center rounded-[6px] border border-brand/60 bg-brand/10 text-brand" aria-hidden="true">
        <svg class="h-[13px] w-[13px]" viewBox="0 0 24 24" fill="none" focusable="false">
            <path d="M12 3L13.6 8.4L19 10L13.6 11.6L12 17L10.4 11.6L5 10L10.4 8.4L12 3Z" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round" />
            <path d="M18.5 15.5L19.2 17.8L21.5 18.5L19.2 19.2L18.5 21.5L17.8 19.2L15.5 18.5L17.8 17.8L18.5 15.5Z" stroke="currentColor" stroke-width="1.5" stroke-linejoin="round" />
        </svg>
    </span>
    <span>{{ $slot }}</span>
</div>
