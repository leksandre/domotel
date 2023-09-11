<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Kelnik\Progress\Models\Camera;

return new class extends Migration
{
    private string $tableName = 'progress_cameras';

    public function up(): void
    {
        Schema::create($this->tableName, function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('user_id')->nullable()->default(Camera::DEFAULT_INT_VALUE)->index();
            $table->boolean('active')->default(false)->index();
            $table->integer('priority')->default(Camera::PRIORITY_DEFAULT)->index();
            $table->unsignedBigInteger('cover_image')->nullable()->default(Camera::DEFAULT_INT_VALUE);

            // Strings, text
            $table->char('locale', 2)->default('ru')->index();
            $table->string('title')->nullable();
            $table->string('url')->nullable()->default(null);
            $table->text('description')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists($this->tableName);
    }
};
