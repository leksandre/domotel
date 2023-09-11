<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Kelnik\Menu\Models\MenuItem;

return new class extends Migration
{
    private string $tableName = 'menu_items';

    public function up(): void
    {
        Schema::create($this->tableName, function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('menu_id')->default(MenuItem::DEFAULT_INT_VALUE)->index();
            $table->unsignedBigInteger('parent_id')->default(MenuItem::DEFAULT_INT_VALUE)->index();
            $table->boolean('active')->default(false)->index();
            $table->unsignedInteger('priority')->default(MenuItem::PRIORITY_DEFAULT)->index();
            $table->unsignedBigInteger('page_id')->default(MenuItem::DEFAULT_INT_VALUE)->index();
            $table->unsignedBigInteger('page_component_id')->default(MenuItem::DEFAULT_INT_VALUE)->index();
            $table->unsignedBigInteger('icon_image')->nullable()->default(0);

            $table->string('title')->nullable();
            $table->string('link')->nullable();
            $table->text('params')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists($this->tableName);
    }
};
