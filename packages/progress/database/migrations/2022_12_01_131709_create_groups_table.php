<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private string $tableName = 'progress_groups';

    public function up(): void
    {
        Schema::create($this->tableName, function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('user_id')->nullable()->index();
            $table->boolean('active')->default(false)->index();

            // Strings, text
            $table->char('locale', 2)->default('ru')->index();
            $table->string('title')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists($this->tableName);
    }
};
