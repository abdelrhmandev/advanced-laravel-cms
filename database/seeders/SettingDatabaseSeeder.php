<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class SettingDatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $baseSettings = [
            ['key' => 'site_title', 'label' => 'Site Title', 'type' => 'textbox', 'value' => config('project.app.title')], ['key' => 'site_description', 'label' => 'Site Description', 'type' => 'textarea', 'value' => config('project.app.description')], ['key' => 'site_key_words', 'label' => 'Site SEO Key Words', 'type' => 'textbox', 'value' => config('project.app.key_words')], ['key' => 'site_footer_copy_right', 'label' => 'Site Footer Copy Right', 'type' => 'textarea', 'value' => '© copyright 2026 by <a href="#" style="color:#fff">Invent Solution</a>'], ['key' => 'site_footer_text', 'label' => 'Site Footer Text', 'type' => 'textarea', 'value' => 'Footer Text']];


        // Inside SettingDatabaseSeeder.php

        $imageSettings = [
            [
                'key' => 'site_logo_light',
                'label' => 'Site Logo Light',
                'value' => null,
                'type' => 'image',
            ],
            [
                'key' => 'site_logo_dark',
                'label' => 'Site Logo Dark',
                'value' => null,
                'type' => 'image',
            ],
            [
                'key' => 'site_favicon',
                'label' => 'Site Favicon',
                'value' => null,
                'type' => 'image',
            ],
            [
                'key' => 'site_admin_logo',
                'label' => 'Site Admin Logo',
                'value' => null,
                'type' => 'image',
            ],

            [
                'key' => 'site_image_backgroud_admin',
                'label' => 'Site Image Backgroud Admin',
                'value' => null,
                'type' => 'image',
            ],

            [
                'key' => 'site_image_login_backgroud_admin',
                'label' => 'Site Image Login Backgroud Admin',
                'value' => null,
                'type' => 'image',
            ],
        ];

        $simpleSettings = [
            ['key' => 'site_admin_email', 'label' => 'Site Admin Email', 'value' => 'info@domain.com', 'type' => 'email'],
            ['key' => 'site_contact_received_email', 'label' => 'Site Contact Email', 'value' => 'contact@domain', 'type' => 'email'],
            ['key' => 'site_phone', 'label' => 'Phone', 'value' => '22222222', 'type' => 'phone'],
            ['key' => 'site_mobile', 'label' => 'Mobile', 'value' => '0111111111', 'type' => 'mobile'],
            ['key' => 'site_google_map_location_latitude', 'label' => 'Latitude', 'value' => '27.12072911846673', 'type' => 'google_map'],
            ['key' => 'site_google_map_location_longitude', 'label' => 'Longitude', 'value' => '28.548187701894005', 'type' => 'google_map'],
            // Added Social Networks
            ['key' => 'social_facebook', 'label' => 'Facebook URL', 'value' => 'https://facebook.com', 'type' => 'textbox'],
            ['key' => 'social_twitter', 'label' => 'Twitter URL', 'value' => 'https://twitter.com', 'type' => 'textbox'],
            ['key' => 'social_instagram', 'label' => 'Instagram URL', 'value' => 'https://instagram.com', 'type' => 'textbox'],
            ['key' => 'social_youtube', 'label' => 'YouTube URL', 'value' => 'https://youtube.com', 'type' => 'textbox'],
        ];

        $languages = ['en' => 'English', 'ar' => 'Arabic'];
        $allSettings = [];

        // Build Multilingual
        foreach ($baseSettings as $setting) {
            foreach ($languages as $lang => $labelLang) {
                $allSettings[] = [
                    'key' => $setting['key'] . '_' . $lang,
                    'label' => "{$setting['label']} ($labelLang)",
                    'value' => $setting['value'],
                    'type' => $setting['type'],
                ];
            }
        }
         $allSettings[] = [
                'key' => 'activity_log_retention_days',
                'label' => 'Activity Log Retention Days',
                'value' => '30',
                'type' => 'number',
            ];

        // Build Images
        foreach ($imageSettings as $setting) {
            $allSettings[] = [
                'key' => $setting['key'],
                'label' => $setting['label'],
                'value' => $setting['value'],
                'type' => 'image',
            ];
        }

        // Build Simple
        foreach ($simpleSettings as $setting) {
            $allSettings[] = $setting;
        }

        // Use updateOrInsert to prevent duplicate errors or data loss
        foreach ($allSettings as $item) {
            DB::table('settings')->updateOrInsert(['key' => $item['key']], $item);
        }

        Cache::forget('settings');
    }
}
