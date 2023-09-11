<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private string $tableName = 'estate_visual_step_element_angle_masks';

    public function up(): void
    {
        Schema::create($this->tableName, function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('angle_id')->default(0)->index();
            $table->unsignedBigInteger('element_id')->default(0)->index();
            $table->unsignedBigInteger('estate_premises_id')->default(0)->index('estate_premises_id');
            $table->string('type', 50)->nullable()->index();
            $table->string('value')->nullable();
            $table->string('pointer', 50)->nullable();
            $table->text('coords')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists($this->tableName);
    }
};
