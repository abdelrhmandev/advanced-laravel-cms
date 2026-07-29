<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('seos', function (Blueprint $table) {
            $table->id();
            $table->morphs('seoable'); // seoable_id, seoable_type
            $table->string('og_image')->nullable();
            $table->string('twitter_image')->nullable();
            $table->string('canonical_url')->nullable();
            $table->boolean('no_index')->default(false);
            $table->boolean('no_follow')->default(false);
            $table->timestamps();
            $table->unique(['seoable_id', 'seoable_type'], 'seo_morph_unique');
        });

        Schema::create('seo_translations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('seo_id')->constrained()->cascadeOnDelete();
            $table->string('locale', 5);

            $table->string('meta_title')->nullable();
            $table->string('meta_description', 500)->nullable();
            $table->string('meta_keywords')->nullable();

            $table->string('og_title')->nullable();
            $table->string('og_description', 500)->nullable();

            $table->string('twitter_title')->nullable();
            $table->string('twitter_description', 500)->nullable();

            $table->timestamps();

            $table->unique(['seo_id', 'locale'], 'seo_translation_locale_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('seo_translations');
        Schema::dropIfExists('seos');
    }
};
