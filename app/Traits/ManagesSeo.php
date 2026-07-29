<?php
namespace App\Traits;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;

trait ManagesSeo
{
    public ?int $seoId = null;
    public ?string $existingOgImage = null;
    public ?string $existingTwitterImage = null;
    public ?string $canonicalUrl = null;
    public bool $noIndex = false;
    public bool $noFollow = false;
    public array $seoTranslations = [];
    public string|\Livewire\Features\SupportFileUploads\TemporaryUploadedFile|null $ogImage = null;
    public string|\Livewire\Features\SupportFileUploads\TemporaryUploadedFile|null $twitterImage = null;

    protected function seoValidationRules(string $prefix = ''): array
    {
        return [
            "{$prefix}canonicalUrl" => ['nullable', 'string', 'max:255'],
            "{$prefix}ogImage" => ['nullable', 'image', 'max:2048'],
            "{$prefix}twitterImage" => ['nullable', 'image', 'max:2048'],
            "{$prefix}noIndex" => ['boolean'],
            "{$prefix}noFollow" => ['boolean'],
        ];
    }

    /**
     * Resolve seo-related uploads (og_image / twitter_image) and return
     * a clean array ready to be persisted via saveSeoFor().
     */
    protected function prepareSeoData(string $uploadDir = 'seo'): array
    {
        $ogImage = $this->existingOgImage ?? null;
        $twitterImage = $this->existingTwitterImage ?? null;

        if ($this->ogImage instanceof UploadedFile) {
            $ogImage = $this->upload($this->ogImage, $uploadDir, $this->existingOgImage);
        }

        if ($this->twitterImage instanceof UploadedFile) {
            $twitterImage = $this->upload($this->twitterImage, $uploadDir, $this->existingTwitterImage);
        }

        return [
            'canonical_url' => $this->canonicalUrl ?? null,
            'og_image' => $ogImage,
            'twitter_image' => $twitterImage,
            'no_index' => $this->noIndex ?? false,
            'no_follow' => $this->noFollow ?? false,
        ];
    }

    /**
     * Persist seo data for any model using the HasSeo trait.
     * Call this from inside your existing DB::transaction() closure.
     */
    protected function saveSeoFor($model, string $uploadDir = 'seo'): void
    {
        $model->seo()->updateOrCreate([], $this->prepareSeoData($uploadDir));
    }

    public function mountSeo($model): void
    {
        $locales = array_keys(LaravelLocalization::getSupportedLocales());

        $seo = $model?->seo;

        $this->seoId = $seo?->id;
        $this->existingOgImage = $seo?->og_image;
        $this->existingTwitterImage = $seo?->twitter_image;
        $this->canonicalUrl = $seo?->canonical_url;
        $this->noIndex = (bool) $seo?->no_index;
        $this->noFollow = (bool) $seo?->no_follow;

        foreach ($locales as $locale) {
            $seoTrans = $seo?->translations->firstWhere('locale', $locale);

            $this->seoTranslations[$locale] = [
                'meta_title' => $seoTrans?->meta_title,
                'meta_description' => $seoTrans?->meta_description,
                'meta_keywords' => $seoTrans?->meta_keywords,
                'og_title' => $seoTrans?->og_title,
                'og_description' => $seoTrans?->og_description,
                'twitter_title' => $seoTrans?->twitter_title,
                'twitter_description' => $seoTrans?->twitter_description,
            ];
        }
    }
}
