<?php

namespace App\Support;

class TeamProfileData
{
    /**
     * Stanowisko i kontakty są zapisane w bloku ACF, a zdjęcie i zajawka w poście.
     */
    public static function forPost(int $postId): array
    {
        $data = self::findProfileData(parse_blocks((string) get_post_field('post_content', $postId)));

        return [
            'id' => $postId,
            'name' => html_entity_decode(get_the_title($postId), ENT_QUOTES | ENT_HTML5, 'UTF-8'),
            'url' => get_permalink($postId),
            'position' => sanitize_text_field((string) ($data['member_position'] ?? '')),
            'positionIconId' => self::imageId($data['member_position_icon'] ?? null),
            'excerpt' => trim(wp_strip_all_tags((string) get_post_field('post_excerpt', $postId))),
            'photoId' => (int) get_post_thumbnail_id($postId),
            'linkedin' => esc_url_raw((string) ($data['member_linkedin'] ?? '')),
            'github' => esc_url_raw((string) ($data['member_github'] ?? '')),
            'linkedinIconId' => self::imageId($data['member_linkedin_icon'] ?? null),
            'githubIconId' => self::imageId($data['member_github_icon'] ?? null),
        ];
    }

    private static function findProfileData(array $blocks): array
    {
        foreach ($blocks as $block) {
            if (($block['blockName'] ?? '') === 'acf/team-profile') {
                return is_array($block['attrs']['data'] ?? null) ? $block['attrs']['data'] : [];
            }

            if (!empty($block['innerBlocks'])) {
                $data = self::findProfileData($block['innerBlocks']);
                if ($data !== []) {
                    return $data;
                }
            }
        }

        return [];
    }

    public static function imageId(mixed $value): int
    {
        if (is_array($value)) {
            return (int) ($value['ID'] ?? $value['id'] ?? 0);
        }

        return is_numeric($value) ? (int) $value : 0;
    }
}
