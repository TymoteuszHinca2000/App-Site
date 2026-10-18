<?php

namespace App\Blocks;

use Log1x\AcfComposer\Block;
use Log1x\AcfComposer\Builder;

class TextSection extends Block
{
    /**
     * The block name.
     *
     * @var string
     */
    public $name = 'Sekcja tekstowa';

    /**
     * The block slug.
     *
     * @var string
     */
    public $slug = 'text-section';

    /**
     * The block description.
     *
     * @var string
     */
    public $description = 'Wysiwyg text section block.';

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
     * The default block mode.
     *
     * @var string
     */
    public $mode = 'preview';

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
        'mode' => false,
        'multiple' => true,
        'jsx' => false,
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
     * The block preview example data.
     *
     * @var array
     */
    public $example = [
        'content' => '<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed non risus. Suspendisse lectus tortor, dignissim sit amet, adipiscing nec, ultricies sed, dolor. Cras elementum ultrices diam. Maecenas ligula massa, varius a, semper congue, euismod non, mi.</p>',
    ];

    /**
     * Data to be passed to the block before rendering.
     */
    public function with(): array
    {
        return [
            'content' => get_field('content'),
        ];
    }

    /**
     * The block field group.
     */
    public function fields(): array
    {
        $fields = Builder::make('text_section');

        $fields
            ->addWysiwyg('content', [
                'label' => 'Treść',
            ]);

        return $fields->build();
    }

    /**
     * Assets enqueued with 'enqueue_block_assets' when rendering the block.
     *
     * @link https://developer.wordpress.org/block-editor/how-to-guides/enqueueing-assets-in-the-editor/#editor-content-scripts-and-styles
     */
    public function assets(array $block): void
    {
        //
    }
}
