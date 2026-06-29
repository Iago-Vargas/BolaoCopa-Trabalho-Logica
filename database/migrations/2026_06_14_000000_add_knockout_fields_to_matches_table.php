<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('matches', function (Blueprint $table) {
            $table->string('stage', 20)->default('group')->after('id');
            $table->unsignedSmallInteger('match_number')->nullable()->unique()->after('stage');
            $table->string('round_name', 40)->nullable()->after('group_name');
            $table->string('home_slot')->nullable()->after('away_team_id');
            $table->string('away_slot')->nullable()->after('home_slot');
        });

        Schema::table('matches', function (Blueprint $table) {
            $table->foreignId('home_team_id')->nullable()->change();
            $table->foreignId('away_team_id')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('matches', function (Blueprint $table) {
            $table->foreignId('home_team_id')->nullable(false)->change();
            $table->foreignId('away_team_id')->nullable(false)->change();
        });

        Schema::table('matches', function (Blueprint $table) {
            $table->dropUnique(['match_number']);
            $table->dropColumn(['stage', 'match_number', 'round_name', 'home_slot', 'away_slot']);
        });
    }
};
