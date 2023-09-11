<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Kelnik\Estate\Models\PremisesTypeGroup;

return new class extends Migration
{
    private string $tableName = 'estate_premises_type_groups';

    public function up(): void
    {
        Schema::create($this->tableName, function (Blueprint $table) {
            $table->id();
            $table->integer('priority')->default(PremisesTypeGroup::PRIORITY_DEFAULT)->index();
            $table->boolean('living')->default(false)->index();
            $table->string('title')->nullable()->index();
            $table->string('slug')->nullable()->index();
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
