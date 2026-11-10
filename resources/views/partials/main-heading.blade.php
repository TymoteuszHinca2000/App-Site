@php
    $postId = (int) get_the_ID();
    $content = (string) get_post_field('post_content', $postId);
    $containsHeading = static function (array $blocks) use (&$containsHeading): bool {
        foreach ($blocks as $item) {
            if (in_array($item['blockName'] ?? '', ['acf/home-hero', 'acf/banner', 'acf/blog-banner', 'acf/project-banner'], true)) {
                return true;
            }

            if (($item['blockName'] ?? '') === 'core/heading' && (int) ($item['attrs']['level'] ?? 2) === 1) {
                return true;
            }

            if (!empty($item['innerBlocks']) && $containsHeading($item['innerBlocks'])) {
                return true;
            }
        }

        return false;
    };
    $hasHeading = preg_match('/<h1(?:\s|>)/i', $content) || $containsHeading(parse_blocks($content));
@endphp

@unless($hasHeading)
    <x-ui.container class="pt-32 pb-8">
        <h1>{{ get_the_title($postId) }}</h1>
    </x-ui.container>
@endunless
