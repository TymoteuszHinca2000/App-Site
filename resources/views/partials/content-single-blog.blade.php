
@php
    $postId = (int) get_the_ID();
    $hasBlogBanner = has_block('acf/blog-banner', $postId);
    $readingTime = (int) get_field('blog_reading_time', $postId);

    if ($readingTime <= 0) {
        $contentText = wp_strip_all_tags(strip_shortcodes((string) get_post_field('post_content', $postId)));
        preg_match_all('/[\p{L}\p{N}]+/u', $contentText, $matches);
        $readingTime = max(1, (int) ceil(count($matches[0] ?? []) / 200));
    }

    $categoryTerms = get_the_terms($postId, 'blog_category');
    $postCategories = !is_wp_error($categoryTerms) && !empty($categoryTerms) ? $categoryTerms : [];
@endphp

<article @php(post_class('bg-white'))>
    @if($hasBlogBanner)
        @php(the_content())
    @else
        <x-ui.container>
            <div class="mx-auto max-w-[1000px] py-10 sm:py-14 lg:py-16">
                @if(!empty($postCategories))
                    <div class="mb-4 flex flex-wrap gap-2">
                        @foreach($postCategories as $term)
                            <span class="rounded-[6px] border border-brand/25 bg-brand/10 px-3 py-1 text-[12px] font-semibold text-brand-active">{{ $term->name }}</span>
                        @endforeach
                    </div>
                @endif

                <h1>{{ get_the_title($postId) }}</h1>

                <div class="mt-5 flex flex-wrap items-center gap-5 text-[13px] text-muted sm:text-[14px]">
                    <time datetime="{{ esc_attr(get_the_date('c', $postId)) }}">{{ get_the_date('j F Y', $postId) }}</time>
                    <span>{{ $readingTime }} {{ __('min czytania', 'appandsite') }}</span>
                </div>

                @if(has_post_thumbnail($postId))
                    <figure class="mt-8 overflow-hidden rounded-[14px] bg-surface">
                        {!! get_the_post_thumbnail($postId, 'full', [
                            'class' => 'h-auto w-full object-cover',
                            'loading' => 'eager',
                            'decoding' => 'async',
                        ]) !!}
                    </figure>
                @endif

                <div class="wysiwyg mt-8 sm:mt-12">
                    @php(the_content())
                </div>
            </div>
        </x-ui.container>
    @endif
</article>
