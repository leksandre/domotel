<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Kelnik\Document\Models\Element;

return new class extends Migration
{
    private string $tableName = 'documents_elements';

    public function up(): void
    {
        Schema::create($this->tableName, function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('category_id')->nullable()->default(Element::DEFAULT_INT_VALUE)->index();
            $table->unsignedBigInteger('user_id')->nullable()->default(Element::DEFAULT_INT_VALUE)->index();

            $table->boolean('active')->false(0)->index();
            $table->integer('priority')->default(Element::PRIORITY_DEFAULT)->index();
            $table->unsignedBigInteger('attachment_id')->nullable()->default(Element::DEFAULT_INT_VALUE);
            $table->timestamp('publish_date')->nullable();
            $table->char('locale', 2)->default('ru')->index();
            $table->string('title')->nullable();
            $table->string('author')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists($this->tableName);
    }
};
