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

    $titleHtml = preg_replace(
        '/<\/p>\s*<p[^>]*>/i',
        '<br>',
        $titleHtml
    );

    $titleHtml = preg_replace(
        '/<\/?p\b[^>]*>/i',
        '',
        $titleHtml
    );

    $titleHtml = preg_replace(
        '/<(strong|b)>/i',
        '<span class="text-brand">',
        $titleHtml
    );

    $titleHtml = preg_replace(
        '/<\/(strong|b)>/i',
        '</span>',
        $titleHtml
    );

    $titleHtml = trim($titleHtml);

    $descriptionHtml = wp_kses_post(
        $description ?? ''
    );
@endphp

<x-block-helper.block-wrapper :block="$block" :attributes="$attributes">
    @if($isEditor || is_singular('offer'))
        <section class="relative bg-white">
            <x-ui.container>
                <div class="w-full">

                    @if(!empty($prefix))
                        <div
                            @if(!$isEditor)
                                data-aos="fade-right"
                                data-aos-duration="500"
                                data-aos-once="true"
                                data-aos-easing="ease-out-cubic"
                            @endif
                            class="mb-5 flex items-center gap-3 text-[11px] font-semibold uppercase tracking-[0.14em] text-muted sm:text-[12px]"
                        >
                            <span class="flex h-[24px] w-[24px] shrink-0 items-center justify-center rounded-[6px] border border-brand/60 bg-brand/10 text-brand">
                                <svg
                                    class="h-[13px] w-[13px]"
                                    viewBox="0 0 24 24"
                                    fill="none"
                                    aria-hidden="true"
                                >
                                    <path
                                        d="M12 3L13.6 8.4L19 10L13.6 11.6L12 17L10.4 11.6L5 10L10.4 8.4L12 3Z"
                                        stroke="currentColor"
                                        stroke-width="1.8"
                                        stroke-linejoin="round"
                                    />

                                    <path
                                        d="M18.5 15.5L19.2 17.8L21.5 18.5L19.2 19.2L18.5 21.5L17.8 19.2L15.5 18.5L17.8 17.8L18.5 15.5Z"
                                        stroke="currentColor"
                                        stroke-width="1.5"
                                        stroke-linejoin="round"
                                    />
                                </svg>
                            </span>

                            <span>{{ $prefix }}</span>
                        </div>
                    @endif

                    @if(!empty($titleHtml))
                        <h2
                            @if(!$isEditor)
                                data-aos="fade-up"
                                data-aos-duration="650"
                                data-aos-delay="80"
                                data-aos-once="true"
                                data-aos-easing="ease-out-cubic"
                            @endif
                            class="!m-0 w-full"
                        >
                            {!! $titleHtml !!}
                        </h2>
                    @endif

                    @if(!empty($descriptionHtml))
                        <div
                            @if(!$isEditor)
                                data-aos="fade-up"
                                data-aos-duration="650"
                                data-aos-delay="150"
                                data-aos-once="true"
                                data-aos-easing="ease-out-cubic"
                            @endif
                            class="wysiwyg mt-5 w-full [&_p]:!m-0 [&_p+p]:!mt-4"
                        >
                            {!! $descriptionHtml !!}
                        </div>
                    @endif

                    @if(!empty($rows))
                        <div
                            @if(!$isEditor)
                                data-aos="fade-up"
                                data-aos-duration="700"
                                data-aos-delay="220"
                                data-aos-once="true"
                                data-aos-easing="ease-out-cubic"
                            @endif
                            class="mt-7 overflow-hidden rounded-[12px] border border-border shadow-card-soft"
                        >
                            <div class="table-scroll">
                                <table>
                                    <thead>
                                        <tr>
                                            <th class="w-[32%]">
                                                {{ $column1 ?: 'Element' }}
                                            </th>

                                            <th class="w-[50%]">
                                                {{ $column2 ?: 'Opis' }}
                                            </th>

                                            <th class="w-[18%] text-center">
                                                {{ $column3 ?: 'W standardzie' }}
                                            </th>
                                        </tr>
                                    </thead>

                                    <tbody>
                                        @foreach($rows as $row)
                                            <tr>
                                                <td>
                                                    @if(!empty($row['element']))
                                                        <strong>
                                                            {{ $row['element'] }}
                                                        </strong>
                                                    @endif
                                                </td>

                                                <td>
                                                    @if(!empty($row['description']))
                                                        {!! wp_kses_post($row['description']) !!}
                                                    @endif
                                                </td>

                                                <td class="text-center">
                                                    @if(!empty($row['included']))
                                                        <span class="inline-flex items-center justify-center text-brand">
                                                            <svg
                                                                class="h-[20px] w-[20px]"
                                                                viewBox="0 0 24 24"
                                                                fill="none"
                                                                aria-hidden="true"
                                                            >
                                                                <path
                                                                    d="M5 12.5L9.5 17L19 7.5"
                                                                    stroke="currentColor"
                                                                    stroke-width="2.6"
                                                                    stroke-linecap="round"
                                                                    stroke-linejoin="round"
                                                                />
                                                            </svg>
                                                        </span>
                                                    @else
                                                        <span class="text-[14px] font-medium text-muted">
                                                            {{ $row['status'] ?: __('Opcjonalnie', 'appandsite') }}
                                                        </span>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    @endif

                </div>
            </x-ui.container>
        </section>
    @endif
</x-block-helper.block-wrapper>