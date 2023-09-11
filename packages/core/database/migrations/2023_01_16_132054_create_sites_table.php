<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Kelnik\Core\Models\Enums\Lang;
use Kelnik\Core\Models\Enums\Type;

return new class extends Migration
{
    private string $tableName = 'sites';

    public function up(): void
    {
        Schema::create($this->tableName, function (Blueprint $table) {
            $table->id();
            $table->boolean('active')->default(false);
            $table->boolean('primary')->default(false);
            $table->char('type', 10)->default(Type::Site->value);
            $table->char('locale', 2)->default(Lang::Russian->value);
            $table->json('settings');
            $table->string('title')->nullable()->default(null);
            $table->timestamps();
            $table->index(['active', 'primary'], 'active_primary');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists($this->tableName);
    }
};
