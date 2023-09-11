<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Kelnik\Estate\Models\Complex;

return new class extends Migration
{
    private string $tableName = 'estate_complexes';

    public function up(): void
    {
        Schema::create($this->tableName, function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('type_id')->nullable()->default(0)->index();
            $table->unsignedBigInteger('status_id')->nullable()->default(0)->index();
            $table->unsignedBigInteger('district_id')->nullable()->default(0)->index();
            $table->unsignedBigInteger('completion_id')->nullable()->default(0)->index();
            $table->unsignedBigInteger('completion_stage_id')->nullable()->default(0)->index();

            $table->boolean('active')->default(false)->index();
            $table->boolean('show_custom_prices')->default(false);

            $table->integer('priority')->default(Complex::PRIORITY_DEFAULT)->index();
            $table->unsignedBigInteger('cover_image_id')->nullable();
            $table->unsignedBigInteger('logo_image_id')->nullable();
            $table->unsignedBigInteger('map_marker_image_id')->nullable();
            $table->unsignedTinyInteger('map_zoom')->default(16);

            $table->string('slug')->nullable()->default(null)->index();
            $table->string('external_id')->nullable()->default(null)->index();
            $table->char('hash', 32)->nullable()->default(null);
            $table->string('title')->nullable()->index();
            $table->string('type_description')->nullable();
            $table->string('address')->nullable();
            $table->string('site_url')->nullable();
            $table->string('map_coords', 100)->nullable();
            $table->string('map_center_coords', 100)->nullable();

            $table->json('options')->nullable();
            $table->json('custom_prices')->nullable();
            $table->text('description')->nullable();
            $table->timestamps();

            $table->unique(['slug']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists($this->tableName);
    }
};
