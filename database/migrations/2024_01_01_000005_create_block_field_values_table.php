<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('block_field_values', function (Blueprint $table) {
            $table->id();
            $table->foreignId('page_block_id')->constrained('page_block')->cascadeOnDelete();
            $table->foreignId('block_field_id')->constrained('block_fields')->cascadeOnDelete();
            $table->unsignedInteger('row')->default(0);
            $table->unsignedInteger('index')->default(0);
            $table->json('value')->nullable();
            $table->timestamps();
            $table->unique(['page_block_id', 'block_field_id', 'row', 'index'], 'bfv_unique');
            $table->index(['page_block_id', 'block_field_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('block_field_values');
    }
};
