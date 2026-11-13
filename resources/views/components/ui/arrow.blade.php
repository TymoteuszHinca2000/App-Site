@props(['direction' => 'right'])

<svg {{ $attributes->class(['ui-arrow', 'ui-arrow--left' => $direction === 'left']) }} viewBox="0 0 24 24" fill="none" aria-hidden="true" focusable="false">
    <path d="{{ $direction === 'left' ? 'M19 12H5M11 6L5 12L11 18' : 'M5 12H19M13 6L19 12L13 18' }}" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
</svg>
