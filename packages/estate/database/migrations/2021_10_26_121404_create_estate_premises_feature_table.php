<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Kelnik\Estate\Models\PremisesFeature;

return new class extends Migration
{
    private string $tableName = 'estate_premises_features';

    public function up(): void
    {
        Schema::create($this->tableName, function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('group_id')->nullable()->default(0)->index();
            $table->integer('priority')->default(PremisesFeature::PRIORITY_DEFAULT)->index();
            $table->boolean('active')->default(false)->index();
            $table->string('title')->nullable()->index();
            $table->string('external_id')->nullable()->index();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists($this->tableName);
    }
};
