<?php

namespace App\Support;

class Collaborations
{
    public static function all(): array
    {
        if (!function_exists('get_field')) {
            return [];
        }

        return self::normalize(get_field('collaborations', 'option'));
    }

    public static function normalize(mixed $rows): array
    {
        if (!is_array($rows)) {
            return [];
        }

        $collaborations = [];

        foreach ($rows as $row) {
            if (!is_array($row)) {
                continue;
            }

            $title = self::text($row['title'] ?? null);

            if ($title === '') {
                continue;
            }

            $features = [];

            foreach (is_array($row['features'] ?? null) ? $row['features'] : [] as $feature) {
                if (!is_array($feature)) {
                    continue;
                }

                $normalizedFeature = [
                    'icon_id' => self::imageId($feature['icon'] ?? null),
                    'title' => self::text($feature['title'] ?? null),
                    'description' => self::text($feature['description'] ?? null),
                ];

                if ($normalizedFeature['icon_id'] === 0 && $normalizedFeature['title'] === '' && $normalizedFeature['description'] === '') {
                    continue;
                }

                $features[] = $normalizedFeature;
            }

            $discountCode = self::text($row['discount_code'] ?? null);
            $actionTitle = self::text($row['action_title'] ?? null);

            if ($actionTitle === '') {
                $actionTitle = $discountCode !== ''
                    ? __('Kod rabatowy', 'appandsite')
                    : __('Link afiliacyjny', 'appandsite');
            }

            $collaborations[] = [
                'logo_id' => self::imageId($row['logo'] ?? null),
                'prefix' => self::text($row['prefix'] ?? null),
                'title' => $title,
                'description' => wp_kses_post(self::string($row['description'] ?? null)),
                'sidebar_logo_id' => self::imageId($row['sidebar_logo'] ?? null),
                'sidebar_title' => self::text($row['sidebar_title'] ?? null),
                'sidebar_description' => wp_kses_post(self::string($row['sidebar_description'] ?? null)),
                'features' => $features,
                'action_title' => $actionTitle,
                'action_description' => self::text($row['action_description'] ?? null),
                'affiliate_url' => esc_url_raw(self::string($row['affiliate_url'] ?? null)),
                'affiliate_label' => self::text($row['affiliate_label'] ?? null) ?: __('Przejdź do oferty', 'appandsite'),
                'discount_code' => $discountCode,
                'copy_label' => self::text($row['copy_label'] ?? null) ?: __('Kopiuj kod', 'appandsite'),
            ];
        }

        return $collaborations;
    }

    private static function imageId(mixed $value): int
    {
        if (is_array($value)) {
            $value = $value['ID'] ?? $value['id'] ?? null;
        }

        return is_int($value) || (is_string($value) && ctype_digit($value))
            ? max(0, (int) $value)
            : 0;
    }

    private static function text(mixed $value): string
    {
        return trim(wp_strip_all_tags(self::string($value)));
    }

    private static function string(mixed $value): string
    {
        return is_string($value) || is_int($value) || is_float($value)
            ? trim((string) $value)
            : '';
    }
}
