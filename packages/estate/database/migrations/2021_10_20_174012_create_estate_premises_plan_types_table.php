<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Kelnik\Estate\Models\PremisesPlanType;

return new class extends Migration
{
    private string $tableName = 'estate_premises_plan_types';

    public function up(): void
    {
        Schema::create($this->tableName, function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('complex_id')->nullable()->default(0)->index();
            $table->integer('priority')->default(PremisesPlanType::PRIORITY_DEFAULT)->index();
            $table->unsignedBigInteger('list_image_id')->nullable();
            $table->unsignedBigInteger('card_image_id')->nullable();
            $table->string('title')->nullable()->index();
            $table->string('slug')->nullable()->index();
            $table->string('external_id')->nullable()->index();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists($this->tableName);
    }
};
