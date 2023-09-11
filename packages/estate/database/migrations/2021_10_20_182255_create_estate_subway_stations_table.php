<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Kelnik\Estate\Models\SubwayStation;

return new class extends Migration
{
    private string $tableName = 'estate_subway_stations';

    public function up(): void
    {
        Schema::create($this->tableName, function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('line_id')->nullable()->default(0)->index();
            $table->integer('priority')->default(SubwayStation::PRIORITY_DEFAULT)->index();
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
