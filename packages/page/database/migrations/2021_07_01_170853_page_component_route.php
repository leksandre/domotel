<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Kelnik\Page\Models\PageComponentRoute;

return new class extends Migration
{
    private string $tableName = 'page_component_routes';

    public function up(): void
    {
        Schema::create($this->tableName, function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('page_component_id')->nullable()->default(
                PageComponentRoute::DEFAULT_INT_VALUE
            )->index();
            $table->boolean('ignore_page_slug')->default(false);
            $table->string('path')->nullable()->default(null);
            $table->text('params')->nullable()->default(null);
            $table->timestamps();
            $table->unique(['page_component_id', 'path']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists($this->tableName);
    }
};
