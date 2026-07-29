<?php

namespace Database\Seeders;

use App\Models\Block;
use App\Models\BlockField;
use App\Models\BlockFieldValue;
use App\Models\Page;
use App\Models\PageBlock;
use Illuminate\Database\Seeder;

class BlockDatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // -----------------------------------------------
        // 1. Create Blocks + Fields
        // -----------------------------------------------

        $hero = Block::create([
            'title'      => 'Hero Section',
            'slug'       => 'hero',
            'show_title' => false,
            'is_active'  => true,
            'order'      => 1,
        ]);

        BlockField::insert([
            [
                'block_id'      => $hero->id,
                'key'           => 'title',
                'label'         => 'العنوان',
                'type'          => 'text',
                'order'         => 1,
                'translatable'  => true,
                'repeatable'    => false,
                'required'      => true,
                'settings'      => json_encode(['max_length' => 100]),
                'default_value' => null,
                'hint'          => null,
                'created_at'    => now(),
                'updated_at'    => now(),
            ],
            [
                'block_id'      => $hero->id,
                'key'           => 'subtitle',
                'label'         => 'العنوان الفرعي',
                'type'          => 'text',
                'order'         => 2,
                'translatable'  => true,
                'repeatable'    => false,
                'required'      => false,
                'settings'      => json_encode(['max_length' => 200]),
                'default_value' => null,
                'hint'          => null,
                'created_at'    => now(),
                'updated_at'    => now(),
            ],
            [
                'block_id'      => $hero->id,
                'key'           => 'background',
                'label'         => 'صورة الخلفية',
                'type'          => 'image',
                'order'         => 3,
                'translatable'  => false,
                'repeatable'    => false,
                'required'      => false,
                'settings'      => json_encode(['disk' => 'public', 'max_size' => 4096]),
                'default_value' => null,
                'hint'          => null,
                'created_at'    => now(),
                'updated_at'    => now(),
            ],
            [
                'block_id'      => $hero->id,
                'key'           => 'alignment',
                'label'         => 'المحاذاة',
                'type'          => 'select',
                'order'         => 4,
                'translatable'  => false,
                'repeatable'    => false,
                'required'      => false,
                'settings'      => json_encode([
                    'options' => [
                        ['value' => 'left',   'label' => 'يسار'],
                        ['value' => 'center', 'label' => 'وسط'],
                        ['value' => 'right',  'label' => 'يمين'],
                    ],
                    'multiple' => false,
                ]),
                'default_value' => 'center',
                'hint'          => null,
                'created_at'    => now(),
                'updated_at'    => now(),
            ],
            [
                'block_id'      => $hero->id,
                'key'           => 'overlay_color',
                'label'         => 'لون الطبقة',
                'type'          => 'color',
                'order'         => 5,
                'translatable'  => false,
                'repeatable'    => false,
                'required'      => false,
                'settings'      => json_encode(['format' => 'hex']),
                'default_value' => '#000000',
                'hint'          => null,
                'created_at'    => now(),
                'updated_at'    => now(),
            ],
        ]);

        // -----------------------------------------------

        $pricing = Block::create([
            'title'      => 'Pricing Plans',
            'slug'       => 'pricing',
            'show_title' => true,
            'is_active'  => true,
            'order'      => 2,
        ]);

        BlockField::insert([
            [
                'block_id'      => $pricing->id,
                'key'           => 'section_title',
                'label'         => 'عنوان القسم',
                'type'          => 'text',
                'order'         => 1,
                'translatable'  => true,
                'repeatable'    => false,
                'required'      => true,
                'settings'      => json_encode(['max_length' => 200]),
                'default_value' => null,
                'hint'          => null,
                'created_at'    => now(),
                'updated_at'    => now(),
            ],
            [
                'block_id'      => $pricing->id,
                'key'           => 'plans',
                'label'         => 'الخطط',
                'type'          => 'repeater',
                'order'         => 2,
                'translatable'  => false,
                'repeatable'    => false,
                'required'      => false,
                'settings'      => json_encode([
                    'fields' => [
                        [
                            'key'          => 'name',
                            'label'        => 'اسم الخطة',
                            'type'         => 'text',
                            'translatable' => true,
                            'required'     => true,
                            'settings'     => ['max_length' => 100],
                        ],
                        [
                            'key'          => 'price',
                            'label'        => 'السعر',
                            'type'         => 'text',
                            'translatable' => false,
                            'required'     => true,
                            'settings'     => [],
                        ],
                        [
                            'key'          => 'description',
                            'label'        => 'الوصف',
                            'type'         => 'textarea',
                            'translatable' => true,
                            'required'     => false,
                            'settings'     => [],
                        ],
                        [
                            'key'          => 'badge',
                            'label'        => 'الشارة',
                            'type'         => 'select',
                            'translatable' => false,
                            'required'     => false,
                            'settings'     => [
                                'options' => [
                                    ['value' => 'none',    'label' => 'لا شيء'],
                                    ['value' => 'popular', 'label' => 'الأكثر شيوعاً'],
                                    ['value' => 'new',     'label' => 'جديد'],
                                ],
                            ],
                        ],
                        [
                            'key'          => 'card_color',
                            'label'        => 'لون الكارد',
                            'type'         => 'color',
                            'translatable' => false,
                            'required'     => false,
                            'settings'     => ['format' => 'hex'],
                        ],
                    ],
                ]),
                'default_value' => null,
                'hint'          => 'أضف خطة أو أكثر',
                'created_at'    => now(),
                'updated_at'    => now(),
            ],
        ]);

        // -----------------------------------------------
        // 2. Create Page
        // -----------------------------------------------

        /*
        $page = Page::create([
            'title'     => 'الصفحة الرئيسية',
            'slug'      => 'home',
            'locale'    => 'ar',
            'is_active' => true,
        ]);

        // -----------------------------------------------
        // 3. Attach blocks to page
        // -----------------------------------------------

        $page->blocks()->attach($hero->id,    ['order' => 1, 'is_visible' => true]);
        $page->blocks()->attach($pricing->id, ['order' => 2, 'is_visible' => true]);

        $heroPageBlock    = PageBlock::where('page_id', $page->id)->where('block_id', $hero->id)->first();
        $pricingPageBlock = PageBlock::where('page_id', $page->id)->where('block_id', $pricing->id)->first();

        // -----------------------------------------------
        // 4. Fill hero values
        // -----------------------------------------------

        $heroFields = $hero->fields->keyBy('key');

        BlockFieldValue::insert([
            [
                'page_block_id'  => $heroPageBlock->id,
                'block_field_id' => $heroFields['title']->id,
                'value'          => json_encode(['ar' => 'مرحباً بكم', 'en' => 'Welcome']),
                'created_at'     => now(),
                'updated_at'     => now(),
            ],
            [
                'page_block_id'  => $heroPageBlock->id,
                'block_field_id' => $heroFields['subtitle']->id,
                'value'          => json_encode(['ar' => 'نبني تجارب رقمية استثنائية', 'en' => 'We build exceptional digital experiences']),
                'created_at'     => now(),
                'updated_at'     => now(),
            ],
            [
                'page_block_id'  => $heroPageBlock->id,
                'block_field_id' => $heroFields['background']->id,
                'value'          => json_encode('images/hero.jpg'),
                'created_at'     => now(),
                'updated_at'     => now(),
            ],
            [
                'page_block_id'  => $heroPageBlock->id,
                'block_field_id' => $heroFields['alignment']->id,
                'value'          => json_encode('center'),
                'created_at'     => now(),
                'updated_at'     => now(),
            ],
            [
                'page_block_id'  => $heroPageBlock->id,
                'block_field_id' => $heroFields['overlay_color']->id,
                'value'          => json_encode('#1a1a2e'),
                'created_at'     => now(),
                'updated_at'     => now(),
            ],
        ]);

        // -----------------------------------------------
        // 5. Fill pricing values
        // -----------------------------------------------

        $pricingFields = $pricing->fields->keyBy('key');

        BlockFieldValue::insert([
            [
                'page_block_id'  => $pricingPageBlock->id,
                'block_field_id' => $pricingFields['section_title']->id,
                'value'          => json_encode(['ar' => 'اختر خطتك', 'en' => 'Choose Your Plan']),
                'created_at'     => now(),
                'updated_at'     => now(),
            ],
            [
                'page_block_id'  => $pricingPageBlock->id,
                'block_field_id' => $pricingFields['plans']->id,
                'value'          => json_encode([
                    [
                        'name'        => ['ar' => 'أساسي',    'en' => 'Basic'],
                        'price'       => '99',
                        'description' => ['ar' => 'مثالي للمبتدئين', 'en' => 'Perfect for starters'],
                        'badge'       => 'none',
                        'card_color'  => '#ffffff',
                    ],
                    [
                        'name'        => ['ar' => 'احترافي',  'en' => 'Pro'],
                        'price'       => '299',
                        'description' => ['ar' => 'للمحترفين والشركات', 'en' => 'For professionals & teams'],
                        'badge'       => 'popular',
                        'card_color'  => '#f0f9ff',
                    ],
                    [
                        'name'        => ['ar' => 'مؤسسي',   'en' => 'Enterprise'],
                        'price'       => '999',
                        'description' => ['ar' => 'حلول مخصصة للمؤسسات', 'en' => 'Custom solutions for enterprises'],
                        'badge'       => 'new',
                        'card_color'  => '#fefce8',
                    ],
                ]),
                'created_at'     => now(),
                'updated_at'     => now(),
            ],
        ]);

        $this->command->info('✅ Blocks seeded successfully.');
        $this->command->info("   Page: {$page->slug} → Hero + Pricing blocks");
        */
    }
}
