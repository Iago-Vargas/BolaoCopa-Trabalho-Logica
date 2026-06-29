<?php

namespace Tests\Feature;

use App\Models\Prediction;
use App\Models\Team;
use App\Models\User;
use App\Models\WorldCupMatch;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class KnockoutPredictionsTest extends TestCase
{
    use RefreshDatabase;

    public function test_knockout_page_is_locked_until_group_predictions_are_complete(): void
    {
        $user = User::factory()->create();

        $this->createGroupMatches(72);
        WorldCupMatch::query()->create([
            'stage' => 'r32',
            'match_number' => 73,
            'group_name' => 'R32',
            'round_name' => 'Fase de 32',
            'home_slot' => 'Runner-up Group A',
            'away_slot' => 'Runner-up Group B',
            'starts_at' => now()->addDays(10),
        ]);

        $this->actingAs($user)
            ->get(route('knockout.index'))
            ->assertOk()
            ->assertSee('Complete os palpites das 3 rodadas')
            ->assertSee('0 / 72 palpites');
    }

    public function test_completed_group_predictions_unlock_knockout_predictions(): void
    {
        $user = User::factory()->create();
        $groupMatches = $this->createGroupMatches(72);
        $knockoutMatch = WorldCupMatch::query()->create([
            'stage' => 'r32',
            'match_number' => 73,
            'group_name' => 'R32',
            'round_name' => 'Fase de 32',
            'home_slot' => 'Runner-up Group A',
            'away_slot' => 'Runner-up Group B',
            'starts_at' => now()->addDays(10),
        ]);

        foreach ($groupMatches as $match) {
            Prediction::query()->create([
                'user_id' => $user->id,
                'match_id' => $match->id,
                'home_score' => 1,
                'away_score' => 0,
            ]);
        }

        $this->actingAs($user)
            ->get(route('knockout.index'))
            ->assertOk()
            ->assertSee('FASE 32')
            ->assertSee('Alemanha')
            ->assertSee('Simulado');

        $this->actingAs($user)
            ->post(route('knockout.store'), [
                'predictions' => [
                    $knockoutMatch->id => [
                        'home_score' => 2,
                        'away_score' => 1,
                    ],
                ],
            ])
            ->assertRedirect(route('knockout.index'))
            ->assertSessionHas('prediction_success');

        $this->assertDatabaseHas('predictions', [
            'user_id' => $user->id,
            'match_id' => $knockoutMatch->id,
            'home_score' => 2,
            'away_score' => 1,
        ]);
    }

    private function createGroupMatches(int $count)
    {
        $home = Team::query()->create(['name' => 'Brasil', 'code' => 'BRA']);
        $away = Team::query()->create(['name' => 'Alemanha', 'code' => 'ALE']);

        return collect(range(1, $count))->map(fn (int $index) => WorldCupMatch::query()->create([
            'stage' => 'group',
            'group_name' => 'A',
            'home_team_id' => $home->id,
            'away_team_id' => $away->id,
            'starts_at' => now()->addDays($index),
        ]));
    }
}
