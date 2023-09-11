<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Kelnik\Estate\Models\Planoplan;

return new class extends Migration
{
    private string $tableName = 'estate_planoplan';

    public function up(): void
    {
        Schema::create($this->tableName, function (Blueprint $table) {
            $table->char('id', Planoplan::CODE_MAX_LENGTH)->unique();
            $table->boolean('active')->index();
            $table->unsignedTinyInteger('version')->default(Planoplan::DEFAULT_VERSION);
            $table->json('data')->nullable();
            $table->timestamps();
            $table->index('updated_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists($this->tableName);
    }
};
