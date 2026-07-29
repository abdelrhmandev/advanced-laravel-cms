<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('block_fields', function (Blueprint $table) {
            $table->id();
            $table->foreignId('block_id')->constrained()->cascadeOnDelete();
            $table->foreignId('parent_id')->nullable()->constrained('block_fields')->cascadeOnDelete(); // للـ repeater sub-fields
            $table->string('key');
            $table->string('label');
            $table->enum('type', ['icon','text', 'textarea', 'richtext', 'image', 'file', 'select', 'color', 'number', 'repeater', 'relation']);
            $table->boolean('translatable')->default(false);
            $table->boolean('required')->default(false);
            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('order')->default(0);
            $table->json('settings')->nullable(); // options للـ select، accepted_types للـ file، إلخ
            $table->json('validation')->nullable();
            $table->string('hint')->nullable();
            $table->timestamps();
            $table->unique(['block_id', 'key', 'parent_id'], 'bf_unique');
            $table->index(['block_id', 'order']);
            $table->index(['parent_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('block_fields');
    }
};
