<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Kelnik\Contact\Models\SocialLink;

return new class extends Migration
{
    private string $tableName = 'contacts_social_links';

    public function up(): void
    {
        Schema::create($this->tableName, function (Blueprint $table) {
            $table->id();
            $table->boolean('active')->default(false)->index();
            $table->integer('priority')->default(SocialLink::PRIORITY_DEFAULT)->index();
            $table->char('locale', 2)->default('ru')->index();
            $table->unsignedBigInteger('icon_id')->nullable()->default(null);
            $table->string('title')->nullable();
            $table->string('link')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists($this->tableName);
    }
};
