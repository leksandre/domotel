<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private string $tableName = 'estate_hide_prices';

    public function up(): void
    {
        Schema::create($this->tableName, function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('premises_type_id')->nullable()->default(0);
            $table->unsignedBigInteger('model_row_id')->nullable()->default(0);
            $table->string('model_type');
            $table->timestamps();

            $table->unique(['premises_type_id', 'model_row_id', 'model_type'], 'model_to_type');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists($this->tableName);
    }
};
