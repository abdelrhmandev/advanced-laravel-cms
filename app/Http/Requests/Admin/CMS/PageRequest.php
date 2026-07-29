<?php
namespace App\Http\Requests\Admin\CMS;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;

class PageRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(?int $Id = null, $transTable = null, $transFkId = null): array
    {
        $rules = [
            'image' => ['nullable', 'image', 'mimes:png,jpg,jpeg,webp,gif', 'max:2048'],
            'is_active' => ['boolean'],
            'template' => ['nullable'],
            // SEO
            'ogImage' => ['nullable', 'image', 'mimes:png,jpg,jpeg,webp,gif', 'max:2048'],
            'twitterImage' => ['nullable', 'image', 'mimes:png,jpg,jpeg,webp,gif', 'max:2048'],
            'canonicalUrl' => ['nullable', 'url', 'max:255'],
            'noIndex' => ['nullable', 'boolean'],
            'noFollow' => ['nullable', 'boolean'],
            'selectedBlocks' => ['nullable', 'array'],
        ];

        foreach (LaravelLocalization::getSupportedLocales() as $localeCode => $properties) {
            $rules["translations.$localeCode.title"] = ['required', 'string', 'max:255'];
            $rules["translations.$localeCode.slug"] = ['required', 'string', 'max:255', Rule::unique($transTable, 'slug')->ignore($Id, $transFkId)];
            $rules["translations.$localeCode.description"] = ['nullable', 'string', 'max:5000'];

            //Search Engine Metadata Normal
            $rules["seoTranslations.$localeCode.meta_title"] = ['nullable', 'string', 'max:255'];
            $rules["seoTranslations.$localeCode.meta_description"] = ['nullable', 'string', 'max:160'];
            $rules["seoTranslations.$localeCode.meta_keywords"] = ['nullable', 'string'];



            //Search Engine Metadata OG
            $rules["seoTranslations.$localeCode.og_title"] = ['nullable', 'string', 'max:255'];
            $rules["seoTranslations.$localeCode.og_description"] = ['nullable', 'string', 'max:160'];



                        //Search Engine Metadata Twitter
            $rules["seoTranslations.$localeCode.twitter_title"] = ['nullable', 'string', 'max:255'];
            $rules["seoTranslations.$localeCode.twitter_description"] = ['nullable', 'string', 'max:160'];


        }

        return $rules;
    }

    public function messages(): array
    {
        $messages = [];

        foreach (LaravelLocalization::getSupportedLocales() as $localeCode => $properties) {
            $messages["translations.$localeCode.title.required"] = "Title ({$properties['name']}) is required";
            $messages["translations.$localeCode.slug.required"] = "Slug ({$properties['name']}) is required";
        }

        return $messages;
    }
}
