<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Kelnik\Estate\Models\Planoplan;
use Kelnik\Estate\Models\Premises;

return new class extends Migration
{
    private string $tableName = 'estate_premises';

    public function up(): void
    {
        Schema::create($this->tableName, function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('type_id')->nullable()->default(0)->index();
            $table->unsignedBigInteger('original_type_id')->nullable()->default(0)->index();

            $table->unsignedBigInteger('status_id')->nullable()->default(0)->index();
            $table->unsignedBigInteger('original_status_id')->nullable()->default(0)->index();

            $table->unsignedBigInteger('floor_id')->nullable()->default(0)->index();
            $table->unsignedBigInteger('section_id')->nullable()->default(0)->index();
            $table->unsignedBigInteger('plan_type_id')->nullable()->default(0)->index();

            $table->boolean('active')->default(false)->index();

            $table->smallInteger('rooms')->default(Premises::ROOMS_DEFAULT);

            $table->unsignedDecimal('price', 10, 2)->default(Premises::PRICE_DEFAULT)->index();
            $table->unsignedDecimal('price_total', 10, 2)->default(Premises::PRICE_DEFAULT)->index();
            $table->unsignedDecimal('price_sale', 10, 2)->default(Premises::PRICE_DEFAULT);
            $table->unsignedDecimal('price_meter', 10, 2)->default(Premises::PRICE_DEFAULT);
            $table->unsignedDecimal('price_rent', 10, 2)->default(Premises::PRICE_DEFAULT);

            $table->unsignedDecimal('area_total', 10, 2)->default(Premises::AREA_DEFAULT)->index();
            $table->unsignedDecimal('area_living', 10, 2)->default(Premises::AREA_DEFAULT)->index();
            $table->unsignedDecimal('area_kitchen', 10, 2)->default(Premises::AREA_DEFAULT);

            $table->unsignedBigInteger('image_list_id')->nullable();
            $table->unsignedBigInteger('image_plan_id')->nullable();
            $table->unsignedBigInteger('image_3d_id')->nullable();
            $table->unsignedBigInteger('image_on_floor_id')->nullable();

            $table->string('external_id')->nullable()->default(null)->index();
            $table->char('hash', 32)->nullable()->default(null);
            $table->string('plan_type_string')->nullable()->index();
            $table->string('number', 100)->nullable()->index();
            $table->string('title')->nullable()->index();
            $table->char('planoplan_code', Planoplan::CODE_MAX_LENGTH)->nullable()->index();
            $table->json('additional_properties')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists($this->tableName);
    }
};
