<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Kelnik\Progress\Models\AlbumVideo;

return new class extends Migration
{
    private string $tableName = 'progress_album_videos';

    public function up(): void
    {
        Schema::create($this->tableName, function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('album_id')->nullable()->default(AlbumVideo::DEFAULT_INT_VALUE)->index();
            $table->integer('priority')->default(AlbumVideo::PRIORITY_DEFAULT)->index();

            $table->string('url')->nullable()->default(null);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists($this->tableName);
    }
};
