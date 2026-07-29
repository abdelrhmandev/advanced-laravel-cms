<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('blocks', function (Blueprint $table) {
            $table->id();
            $table->json('title');
            $table->boolean('show_title')->default(true);
            $table->boolean('is_active')->default(true);
            $table->json('settings')->nullable();
            $table->boolean('is_repeatable')->default(false);
             $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('blocks');
    }
};
