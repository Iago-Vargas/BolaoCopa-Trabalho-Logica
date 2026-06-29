<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('matches', function (Blueprint $table) {
            $table->boolean('is_finished')->default(false)->after('away_score');
            $table->string('match_status', 30)->nullable()->after('is_finished');
            $table->timestamp('last_score_synced_at')->nullable()->after('match_status');
        });
    }

    public function down(): void
    {
        Schema::table('matches', function (Blueprint $table) {
            $table->dropColumn(['is_finished', 'match_status', 'last_score_synced_at']);
        });
    }
};
