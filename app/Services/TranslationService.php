<?php
namespace App\Services;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;

class TranslationService
{
    public array $translations = [];
    public function saveTrans($model, array $translations): void
    {
        foreach ($translations as $locale => $data) {
            $model->translations()->updateOrCreate(
                ['locale' => $locale],
                [
                    'title' => $data['title'] ?? null,
                    'slug' => $data['slug'] ?? null,
                    'description' => $data['description'] ?? null,
                ],
            );
        }
        $model->touch();
    }
    public function mountTrans($model, array $fields = ['title', 'slug', 'description']): array
    {
        $locales = array_keys(LaravelLocalization::getSupportedLocales());

        $translations = [];

        foreach ($locales as $localeCode) {
            $trans = $model?->translations->firstWhere('locale', $localeCode);

            $translations[$localeCode] = collect($fields)->mapWithKeys(fn($field) => [$field => $trans?->{$field}])->toArray();
        }

        return $translations;
    }
}
