<?php

use App\Enums\NewsProviderEnum;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('articles', function (Blueprint $table) {
            $table->id();
            $table->text('title');
            $table->string('slug', 500)->unique();
            $table->foreignId('author_id')->nullable()->constrained('authors')->onDelete('set null');
            $table->foreignId('category_id')->nullable()->constrained('categories')->onDelete('restrict');
            $table->foreignId('source_id')->nullable()->constrained('sources')->onDelete('restrict');
            $table->string('source_url')->nullable();
            $table->string('image_url')->nullable();
            $table->longText('content')->nullable();
            $table->enum('news_provider', NewsProviderEnum::getAllValues());
            $table->timestamp('published_at')->nullable();
            $table->timestamps();

            $table->fullText(['title', 'content']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('articles');
    }
};
