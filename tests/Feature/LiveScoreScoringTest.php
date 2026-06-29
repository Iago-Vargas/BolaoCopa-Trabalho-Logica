<?php

namespace Tests\Feature;

use App\Models\Prediction;
use App\Models\Team;
use App\Models\User;
use App\Models\WorldCupMatch;
use App\Services\Predictions\PredictionScorer;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LiveScoreScoringTest extends TestCase
{
    use RefreshDatabase;

    public function test_live_scores_do_not_update_prediction_points_until_match_is_finished(): void
    {
        $user = User::factory()->create();
        $home = Team::query()->create(['name' => 'Germany', 'code' => 'GER']);
        $away = Team::query()->create(['name' => 'Curacao', 'code' => 'CUW']);
        $match = WorldCupMatch::query()->create([
            'stage' => 'group',
            'group_name' => 'E',
            'home_team_id' => $home->id,
            'away_team_id' => $away->id,
            'starts_at' => now()->subHour(),
            'home_score' => 2,
            'away_score' => 1,
            'is_finished' => false,
            'match_status' => 'live',
        ]);
        $prediction = Prediction::query()->create([
            'user_id' => $user->id,
            'match_id' => $match->id,
            'home_score' => 2,
            'away_score' => 1,
            'points' => 0,
        ]);

        app(PredictionScorer::class)->recalculateFinishedMatches();

        $this->assertSame(0, $prediction->fresh()->points);

        $match->forceFill([
            'is_finished' => true,
            'match_status' => 'finished',
        ])->save();

        app(PredictionScorer::class)->recalculateFinishedMatches();

        $this->assertSame(10, $prediction->fresh()->points);
    }
}
