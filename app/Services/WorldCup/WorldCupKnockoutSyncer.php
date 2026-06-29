<?php

namespace App\Services\WorldCup;

use App\Models\WorldCupMatch;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;

class WorldCupKnockoutSyncer
{
    public function sync(): int
    {
        $games = $this->fetchGames();
        $synced = 0;

        foreach ($games as $game) {
            if (($game['type'] ?? 'group') === 'group') {
                continue;
            }

            WorldCupMatch::updateOrCreate(
                ['match_number' => (int) $game['id']],
                [
                    'stage' => $game['type'],
                    'group_name' => $game['group'] ?? null,
                    'round_name' => $this->roundName((string) $game['type']),
                    'home_team_id' => null,
                    'away_team_id' => null,
                    'home_slot' => $game['home_team_label'] ?? null,
                    'away_slot' => $game['away_team_label'] ?? null,
                    'starts_at' => Carbon::createFromFormat('m/d/Y H:i', $game['local_date'], 'America/New_York')->utc(),
                    'venue' => $this->stadiumName((string) ($game['stadium_id'] ?? '')),
                ]
            );

            $synced++;
        }

        return $synced;
    }

    private function fetchGames(): array
    {
        $request = Http::timeout(20)
            ->retry(2, 500)
            ->acceptJson();

        if (! config('services.worldcup2026.verify_ssl')) {
            $request = $request->withoutVerifying();
        }

        $response = $request->get(config('services.worldcup2026.games_url'));

        $response->throw();

        return $response->json('games', []);
    }

    private function roundName(string $stage): string
    {
        return [
            'r32' => 'Fase de 32',
            'r16' => 'Oitavas de final',
            'qf' => 'Quartas de final',
            'sf' => 'Semifinais',
            'third' => 'Disputa de 3º lugar',
            'final' => 'Final',
        ][$stage] ?? strtoupper($stage);
    }

    private function stadiumName(string $stadiumId): ?string
    {
        return [
            '1' => 'Mexico City',
            '2' => 'Guadalajara',
            '3' => 'Atlanta',
            '4' => 'Los Angeles',
            '5' => 'Dallas',
            '6' => 'Kansas City',
            '7' => 'Miami',
            '8' => 'New York/New Jersey',
            '9' => 'Boston',
            '10' => 'Philadelphia',
            '11' => 'Toronto',
            '12' => 'Seattle',
            '13' => 'San Francisco Bay Area',
            '14' => 'Houston',
            '15' => 'Vancouver',
            '16' => 'Monterrey',
        ][$stadiumId] ?? null;
    }
}
