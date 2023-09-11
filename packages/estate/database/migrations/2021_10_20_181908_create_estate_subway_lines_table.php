<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Kelnik\Estate\Models\SubwayLine;

return new class extends Migration
{
    private string $tableName = 'estate_subway_lines';

    public function up(): void
    {
        Schema::create($this->tableName, function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('city_id')->nullable()->default(0)->index();
            $table->integer('priority')->default(SubwayLine::PRIORITY_DEFAULT)->index();
            $table->string('title')->nullable()->index();
            $table->char('color', 10)->nullable();
            $table->string('external_id')->nullable()->index();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists($this->tableName);
    }
};
