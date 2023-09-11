<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Kelnik\Estate\Models\Floor;

return new class extends Migration
{
    private string $tableName = 'estate_floors';

    public function up(): void
    {
        Schema::create($this->tableName, function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('building_id')->nullable()->default(0)->index();

            $table->boolean('active')->default(false)->index();

            $table->integer('priority')->default(Floor::PRIORITY_DEFAULT)->index();
            $table->smallInteger('number')->default(0)->index();

            $table->string('slug')->nullable()->default(null)->index();
            $table->string('external_id')->nullable()->default(null)->index();
            $table->char('hash', 32)->nullable()->default(null);
            $table->string('title')->nullable()->index();

            $table->timestamps();

            $table->unique(['building_id', 'slug']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists($this->tableName);
    }
};
