<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Kelnik\Progress\Models\Album;

return new class extends Migration
{
    private string $tableName = 'progress_albums';

    public function up(): void
    {
        Schema::create($this->tableName, function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('user_id')->nullable()->default(Album::DEFAULT_INT_VALUE)->index();
            $table->boolean('active')->default(false)->index();

            // Dates
            $table->timestamp('publish_date')->nullable();

            // Strings, text
            $table->char('locale', 2)->default('ru')->index();
            $table->string('title')->nullable();
            $table->string('comment')->nullable();
            $table->text('description')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists($this->tableName);
    }
};
