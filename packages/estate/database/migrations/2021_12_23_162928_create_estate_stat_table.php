<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private string $tableName = 'estate_stat';

    public function up(): void
    {
        Schema::create($this->tableName, function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('model_row_id')->default(0)->index();
            $table->unsignedBigInteger('premises_type_id')->default(0)->index();
            $table->decimal('value', 20, 6)->default(0);
            $table->string('model_name', 150)->nullable()->index();
            $table->string('name', 100)->nullable()->index();
            $table->timestamp('created_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists($this->tableName);
    }
};
