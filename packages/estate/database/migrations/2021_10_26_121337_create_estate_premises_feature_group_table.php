<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Kelnik\Estate\Models\PremisesFeatureGroup;

return new class extends Migration
{
    private string $tableName = 'estate_premises_feature_groups';

    public function up(): void
    {
        Schema::create($this->tableName, function (Blueprint $table) {
            $table->id();
            $table->integer('priority')->default(PremisesFeatureGroup::PRIORITY_DEFAULT)->index();
            $table->boolean('active')->default(false)->index();
            $table->boolean('general')->default(false);
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
