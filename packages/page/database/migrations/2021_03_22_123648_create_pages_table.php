<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Kelnik\Page\Models\Enums\RedirectType;
use Kelnik\Page\Models\Page;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pages', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('parent_id')->default(Page::DEFAULT_INT_VALUE)->index();
            $table->boolean('active')->default(false)->index();
            $table->integer('priority')->default(Page::PRIORITY_DEFAULT)->index();
            $table->char('locale', 2)->default('ru')->index();
            $table->char('path', 32)->index()->comment('Hashed full url');
            $table->string('slug')->nullable()->default(null)->index();
            $table->unsignedTinyInteger('redirect_type')->default(RedirectType::Disabled->value)->index();
            $table->string('title')->nullable();
            $table->string('redirect_url')->nullable();
            $table->timestamps();

            $table->unique(['locale', 'path']);
            $table->unique(['parent_id', 'locale', 'slug']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pages');
    }
};
