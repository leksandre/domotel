<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private string $tableName = 'page_component_route_module_elements';

    public function up(): void
    {
        Schema::create($this->tableName, function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('page_component_route_id')->nullable()->index('page_component_route');
            $table->unsignedBigInteger('element_id')->nullable()->index('element');
            $table->string('module_name', 50)->index('module');
            $table->string('model_name')->index('model');
            $table->unique(['page_component_route_id', 'element_id', 'module_name', 'model_name'], 'unique_link');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists($this->tableName);
    }
};
