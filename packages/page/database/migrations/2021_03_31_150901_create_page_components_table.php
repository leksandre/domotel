<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Kelnik\Page\Models\PageComponent;

return new class extends Migration
{
    protected string $tableName = 'page_components';

    public function up(): void
    {
        Schema::create($this->tableName, function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('page_id')->default(PageComponent::DEFAULT_INT_VALUE)->index();
            $table->boolean('active')->default(false)->index();
            $table->integer('priority')->default(PageComponent::PRIORITY_DEFAULT)->index();
            $table->string('component', 255)->nullable()->default(null)->index();
            $table->mediumText('data')->nullable()->default(null);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists($this->tableName);
    }
};
