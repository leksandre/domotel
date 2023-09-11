<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Kelnik\Estate\Models\PremisesType;

return new class extends Migration
{
    private string $tableName = 'estate_premises_types';

    public function up(): void
    {
        Schema::create($this->tableName, function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('group_id')->nullable()->default(0)->index();
            $table->unsignedBigInteger('replace_id')->nullable()->default(0);
            $table->integer('priority')->default(PremisesType::PRIORITY_DEFAULT)->index();
            $table->unsignedTinyInteger('rooms')->default(PremisesType::ROOMS_DEFAULT);
            $table->string('title')->nullable()->index();
            $table->string('slug')->nullable()->index();
            $table->char('color', 10)->nullable();
            $table->string('external_id')->nullable()->index();
            $table->timestamps();

            $table->unique(['slug']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists($this->tableName);
    }
};
