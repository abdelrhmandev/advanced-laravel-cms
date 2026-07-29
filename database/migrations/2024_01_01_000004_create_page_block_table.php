<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('page_block', function (Blueprint $table) {
            $table->id();
            $table->foreignId('page_id')->constrained()->cascadeOnDelete();
            $table->foreignId('block_id')->constrained()->cascadeOnDelete();
            $table->unsignedInteger('order')->default(0);
            $table->boolean('is_visible')->default(true);
            $table->string('anchor')->nullable();
            $table->timestamps();
            $table->index(['page_id', 'order']);
            $table->index(['page_id', 'is_visible']);
            $table->unique(['page_id', 'block_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('page_block');
    }
};
