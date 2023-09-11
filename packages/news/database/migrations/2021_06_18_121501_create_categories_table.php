<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Kelnik\News\Models\Category;

return new class extends Migration
{
    private string $tableName = 'news_categories';

    public function up(): void
    {
        Schema::create($this->tableName, function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable()->default(Category::DEFAULT_INT_VALUE)->index();
            $table->boolean('active')->default(false)->index();
            $table->integer('priority')->default(Category::PRIORITY_DEFAULT)->index();
            $table->char('locale', 2)->default('ru')->index();
            $table->string('slug', 150)->nullable()->default(null)->index();
            $table->string('title')->nullable();
            $table->timestamps();

            $table->unique(['locale', 'slug']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists($this->tableName);
    }
};
