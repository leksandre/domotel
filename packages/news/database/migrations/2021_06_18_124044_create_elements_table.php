<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Kelnik\News\Models\Element;

return new class extends Migration
{
    private string $tableName = 'news_elements';

    public function up(): void
    {
        Schema::create($this->tableName, function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('category_id')->nullable()->default(Element::DEFAULT_INT_VALUE)->index();
            $table->unsignedBigInteger('user_id')->nullable()->default(Element::DEFAULT_INT_VALUE)->index();

            $table->boolean('active')->default(false)->index();
            $table->boolean('show_timer')->default(false);
            $table->integer('priority')->default(Element::PRIORITY_DEFAULT)->index();
            $table->unsignedBigInteger('preview_image')->nullable()->default(Element::DEFAULT_INT_VALUE);
            $table->unsignedBigInteger('body_image')->nullable()->default(Element::DEFAULT_INT_VALUE);

            // Dates
            $table->timestamp('active_date_start')->nullable()->index();
            $table->timestamp('active_date_finish')->nullable()->index();
            $table->timestamp('publish_date')->nullable();
            $table->timestamp('publish_date_start')->nullable();
            $table->timestamp('publish_date_finish')->nullable();

            $table->char('locale', 2)->default('ru')->index();
            $table->string('slug', 150)->nullable()->default(null)->index();

            // Text
            $table->string('title')->nullable();
            $table->text('preview')->nullable();
            $table->text('body')->nullable();

            $table->string('button', 500)->nullable();

            $table->timestamps();

            // Keys
            $table->unique(['category_id', 'locale', 'slug']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists($this->tableName);
    }
};
