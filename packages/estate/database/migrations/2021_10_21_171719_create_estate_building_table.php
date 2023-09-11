<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Kelnik\Estate\Models\Building;

return new class extends Migration
{
    private string $tableName = 'estate_buildings';

    public function up(): void
    {
        Schema::create($this->tableName, function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('complex_id')->nullable()->default(0)->index();
            $table->unsignedBigInteger('completion_id')->nullable()->default(0)->index();
            $table->unsignedBigInteger('completion_stage_id')->nullable()->default(0)->index();

            $table->boolean('active')->default(false)->index();

            $table->integer('priority')->default(Building::PRIORITY_DEFAULT)->index();
            $table->unsignedTinyInteger('complete_percent')->default(0);
            $table->unsignedBigInteger('complex_plan_image_id')->nullable();

            $table->string('slug')->nullable()->default(null)->index();
            $table->string('external_id')->nullable()->default(null)->index();
            $table->char('hash', 32)->nullable()->default(null);
            $table->string('title')->nullable()->index();

            $table->text('description')->nullable();
            $table->timestamps();

            $table->unique(['complex_id', 'slug']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists($this->tableName);
    }
};
