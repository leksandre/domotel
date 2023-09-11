<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('progress_album_videos', function (Blueprint $table) {
            $table->dropIndex(['album_id']);
            $table->dropIndex(['priority']);

            $table->index(['album_id', 'priority']);
        });

        Schema::table('progress_albums', function (Blueprint $table) {
            $table->dropIndex(['group_id']);
            $table->dropIndex(['active']);

            $table->index(['group_id', 'active']);
        });

        Schema::table('progress_cameras', function (Blueprint $table) {
            $table->dropIndex(['group_id']);
            $table->dropIndex(['active']);
            $table->dropIndex(['priority']);

            $table->index(['group_id', 'active', 'priority']);
        });
    }

    public function down(): void
    {
        Schema::table('progress_album_videos', function (Blueprint $table) {
            $table->index(['album_id']);
            $table->index(['priority']);

            $table->dropIndex(['album_id', 'priority']);
        });

        Schema::table('progress_albums', function (Blueprint $table) {
            $table->index(['group_id']);
            $table->index(['active']);

            $table->dropIndex(['group_id', 'active']);
        });

        Schema::table('progress_cameras', function (Blueprint $table) {
            $table->index(['group_id']);
            $table->index(['active']);
            $table->index(['priority']);

            $table->dropIndex(['group_id', 'active', 'priority']);
        });
    }
};
