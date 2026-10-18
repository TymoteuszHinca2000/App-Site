<?php

namespace App\Blocks;

use Illuminate\Support\Facades\Vite;
use Log1x\AcfComposer\Block;
use Log1x\AcfComposer\Builder;

class Hero extends Block
{
    /**
     * The block name.
     *
     * @var string
     */
    public $name = 'Hero';

    /**
     * The block slug.
     *
     * @var string
     */
    public $slug = 'hero';

    /**
     * The block description.
     *
     * @var string
     */
    public $description = 'Example Hero Block';

    /**
     * The block category.
     *
     * @var string
     */
    public $category = 'appandsite';

    /**
     * The block icon.
     *
     * @var string|array
     */
    public $icon = 'editor-ul';

    /**
     * The block keywords.
     *
     * @var array
     */
    public $keywords = [];

    /**
     * The block post type allow list.
     *
     * @var array
     */
    public $post_types = [];

    /**
     * The internal ACF block version.
     *
     * @var int
     */
    public $blockVersion = 3;

    /**
     * The parent block type allow list.
     *
     * @var array
     */
    public $parent = [];

    /**
     * The ancestor block type allow list.
     *
     * @var array
     */
    public $ancestor = [];

    /**
     * The default block mode.
     *
     * @var string
     */
    public $mode = 'edit';

    /**
     * The default block alignment.
     *
     * @var string
     */
    public $align = '';

    /**
     * The default block text alignment.
     *
     * @var string
     */
    public $align_text = '';

    /**
     * The default block content alignment.
     *
     * @var string
     */
    public $align_content = '';

    /**
     * The default block spacing.
     *
     * @var array
     */
    public $spacing = [
        'padding' => null,
        'margin' => null,
    ];

    /**
     * The supported block features.
     *
     * @var array
     */
    public $supports = [
        'align' => false,
        'align_text' => false,
        'align_content' => false,
        'full_height' => false,
        'anchor' => false,
        'mode' => true,
        'multiple' => true,
        'jsx' => true,
        'color' => [
            'background' => false,
            'text' => false,
            'gradients' => false,
        ],
        'spacing' => [
            'padding' => false,
            'margin' => false,
        ],
    ];

    /**
     * The block styles.
     *
     * @var array
     */
    public $styles = [];

    /**
     * The block preview example data.
     *
     * @var array
     */
    public $example = [];

    /**
     * The block template.
     *
     * @var array
     */
    public $template = [];

    /**
     * Data to be passed to the block before rendering.
     */
    public function with(): array
    {
        return get_fields() ?: [];
    }

    /**
     * The block field group.
     */
    public function fields(): array
    {
        $fields = Builder::make('example');

        $fields
            ->addText('title', [
                'label' => 'Tytuł',
            ])
            ->addImage('image', [
                'label' => 'Zdjęcie',
            ])
        ;

        return $fields->build();
    }

    /**
     * Assets enqueued with 'enqueue_block_assets' when rendering the block.
     *
     * @link https://developer.wordpress.org/block-editor/how-to-guides/enqueueing-assets-in-the-editor/#editor-content-scripts-and-styles
     */
    public function assets(array $block): void
    {

    }
}
