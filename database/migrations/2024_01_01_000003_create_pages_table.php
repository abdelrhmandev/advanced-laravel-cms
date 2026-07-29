<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
class CreatePagesTable extends Migration
{
    public function up()
    {
        Schema::create('pages', function (Blueprint $table) {
            $table->id();
            $table->string('image', 150)->nullable();
            $table->boolean('is_active')->default(1);
            $table->string('template')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
        Schema::create('page_translations', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug');
            $table->longText('description')->nullable();
            $table->string('locale')->index();
            $table->foreignId('page_id')->constrained('pages')->onDelete('cascade');
            $table->unique(['page_id', 'locale']);
            $table->unique(['slug','locale']);
            $table->index(['title', 'slug']);
            $table->timestamps();
        });
    }
    public function down()
    {
        Schema::dropIfExists('page_translations');
        Schema::dropIfExists('pages');
    }
}
