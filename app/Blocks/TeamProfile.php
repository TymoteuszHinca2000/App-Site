<?php

namespace App\Blocks;

use App\Support\TeamProfileData;
use Log1x\AcfComposer\Block;
use Log1x\AcfComposer\Builder;

class TeamProfile extends Block
{
    public $name = 'Profil pracownika';

    public $slug = 'team-profile';

    public $description = 'Stanowisko, kontakt, opis, specjalizacje i technologie pracownika.';

    public $category = 'appandsite';

    public $icon = 'id';

    public $keywords = ['pracownik', 'profil', 'zespół', 'technologie', 'kontakt'];

    public $post_types = ['team'];

    public $blockVersion = 3;

    public $mode = 'preview';

    public $align = '';

    public $align_text = '';

    public $align_content = '';

    public $supports = [
        'align' => false,
        'align_text' => false,
        'align_content' => false,
        'full_height' => false,
        'anchor' => true,
        'mode' => true,
        'multiple' => false,
        'jsx' => false,
        'color' => ['background' => false, 'text' => false, 'gradients' => false],
        'spacing' => ['padding' => false, 'margin' => false],
    ];

    public function with(): array
    {
        $postId = (int) ($this->post_id ?: get_the_ID());
        $email = sanitize_email((string) get_field('member_email'));
        $phone = trim((string) get_field('member_phone'));
        $specialties = [];
        $savedData = $this->block->data ?? [];

        foreach (get_field('specialties') ?: [] as $index => $row) {
            $title = trim((string) ($row['specialties_title'] ?? $savedData["specialties_{$index}_title"] ?? ''));
            $description = trim((string) ($row['description'] ?? ''));
            $iconId = TeamProfileData::imageId($row['icon'] ?? null);

            if ($title !== '' || $description !== '' || $iconId) {
                $specialties[] = ['title' => $title, 'description' => $description, 'iconId' => $iconId];
            }
        }

        $technologyIds = [];

        foreach (get_field('technologies') ?: [] as $technology) {
            $id = TeamProfileData::imageId($technology['icon'] ?? null);

            if ($id && wp_get_attachment_image_src($id, 'thumbnail')) {
                $technologyIds[] = $id;
            }
        }

        return [
            'personName' => html_entity_decode(get_the_title($postId), ENT_QUOTES | ENT_HTML5, 'UTF-8'),
            'photoId' => (int) get_post_thumbnail_id($postId),
            'email' => $email,
            'phone' => $phone,
            'phoneHref' => preg_replace('/[^\d+]/', '', $phone),
            'linkedin' => esc_url_raw((string) get_field('member_linkedin')),
            'github' => esc_url_raw((string) get_field('member_github')),
            'emailIconId' => TeamProfileData::imageId(get_field('member_email_icon')),
            'phoneIconId' => TeamProfileData::imageId(get_field('member_phone_icon')),
            'linkedinIconId' => TeamProfileData::imageId(get_field('member_linkedin_icon')),
            'githubIconId' => TeamProfileData::imageId(get_field('member_github_icon')),
            'aboutPrefix' => get_field('about_prefix'),
            'aboutTitle' => get_field('about_title'),
            'aboutDescription' => get_field('about_description'),
            'specialtiesPrefix' => get_field('specialties_prefix'),
            'specialtiesTitle' => get_field('specialties_title'),
            'specialties' => $specialties,
            'technologiesPrefix' => get_field('technologies_prefix'),
            'technologiesTitle' => get_field('technologies_title'),
            'technologyIds' => $technologyIds,
        ];
    }

    public function fields(): array
    {
        $fields = Builder::make('team_profile');

        $fields
            ->addTab('person_tab', ['label' => 'Dane pracownika'])
            ->addText('member_position', ['label' => 'Stanowisko w listingu'])
            ->addImage('member_position_icon', ['label' => 'Ikona stanowiska w listingu', 'return_format' => 'array', 'preview_size' => 'thumbnail'])
            ->addTab('contact_tab', ['label' => 'Dane kontaktowe'])
            ->addEmail('member_email', ['label' => 'E-mail'])
            ->addImage('member_email_icon', ['label' => 'Ikona e-mail', 'return_format' => 'array', 'preview_size' => 'thumbnail'])
            ->addText('member_phone', ['label' => 'Telefon', 'placeholder' => '+48 123 456 789'])
            ->addImage('member_phone_icon', ['label' => 'Ikona telefonu', 'return_format' => 'array', 'preview_size' => 'thumbnail'])
            ->addUrl('member_linkedin', ['label' => 'LinkedIn', 'placeholder' => 'https://linkedin.com/in/...'])
            ->addImage('member_linkedin_icon', ['label' => 'Ikona LinkedIn', 'return_format' => 'array', 'preview_size' => 'thumbnail'])
            ->addUrl('member_github', ['label' => 'GitHub', 'placeholder' => 'https://github.com/...'])
            ->addImage('member_github_icon', ['label' => 'Ikona GitHub', 'return_format' => 'array', 'preview_size' => 'thumbnail'])
            ->addTab('about_tab', ['label' => 'O mnie'])
            ->addText('about_prefix', ['label' => 'Prefix'])
            ->addWysiwyg('about_title', ['label' => 'Tytuł H2', 'toolbar' => 'basic', 'media_upload' => 0])
            ->addWysiwyg('about_description', ['label' => 'Opis', 'toolbar' => 'basic', 'media_upload' => 0])
            ->addTab('specialties_tab', ['label' => 'Specjalizacje'])
            ->addText('specialties_prefix', ['label' => 'Prefix'])
            ->addWysiwyg('specialties_title', ['label' => 'Tytuł H3', 'toolbar' => 'basic', 'media_upload' => 0])
            ->addRepeater('specialties', ['label' => 'Specjalizacje', 'layout' => 'block', 'button_label' => 'Dodaj specjalizację'])
                ->addImage('icon', ['label' => 'Ikona', 'return_format' => 'array', 'preview_size' => 'thumbnail'])
                ->addText('specialties_title', ['label' => 'Tytuł specjalizacji'])
                ->addTextarea('description', ['label' => 'Opis', 'rows' => 2, 'new_lines' => ''])
            ->endRepeater()
            ->addTab('technologies_tab', ['label' => 'Technologie'])
            ->addText('technologies_prefix', ['label' => 'Prefix'])
            ->addWysiwyg('technologies_title', ['label' => 'Tytuł H3', 'toolbar' => 'basic', 'media_upload' => 0])
            ->addRepeater('technologies', ['label' => 'Technologie', 'layout' => 'block', 'button_label' => 'Dodaj technologię'])
                ->addImage('icon', ['label' => 'Ikona technologii', 'return_format' => 'array', 'preview_size' => 'thumbnail'])
            ->endRepeater();

        return $fields->build();
    }

    public function assets(array $block): void
    {
        //
    }
}
