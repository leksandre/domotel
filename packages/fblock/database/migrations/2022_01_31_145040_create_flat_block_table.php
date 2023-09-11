<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private string $tableName = 'flat_blocks';

    public function up(): void
    {
        Schema::create($this->tableName, function (Blueprint $table) {
            $table->id();
            $table->boolean('active')->default(false)->index();
            $table->integer('priority')->default(500)->index();
            $table->string('title')->nullable();
            $table->string('area')->nullable();
            $table->string('floor')->nullable();
            $table->string('price')->nullable();
            $table->string('planoplan_code', 100)->nullable();
            $table->string('button', 255)->nullable();
            $table->text('features')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists($this->tableName);
    }
};
