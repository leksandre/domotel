<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private string $tableName = 'forms';

    public function up(): void
    {
        Schema::create($this->tableName, function (Blueprint $table) {
            $table->id();
            $table->boolean('active')->default(false)->index();
            $table->unsignedBigInteger('policy_page_id')->nullable()->default(null);
            $table->char('locale', 2)->default('ru')->index();
            $table->string('slug', 150)->nullable()->default(null)->index();
            $table->string('title')->nullable();
            $table->string('success_title')->nullable();
            $table->string('error_title')->nullable();
            $table->string('notify_title')->nullable();
            $table->string('button_text')->nullable();
            $table->mediumText('description')->nullable();
            $table->mediumText('success_text')->nullable();
            $table->mediumText('error_text')->nullable();
            $table->timestamps();

            $table->unique(['locale', 'slug']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists($this->tableName);
    }
};
