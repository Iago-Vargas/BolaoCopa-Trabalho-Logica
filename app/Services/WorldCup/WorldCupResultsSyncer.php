<?php

namespace App\Services\WorldCup;

use App\Models\WorldCupMatch;
use App\Services\Predictions\PredictionScorer;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class WorldCupResultsSyncer
{
    public function __construct(private PredictionScorer $predictionScorer) {}

    public function sync(bool $dryRun = false): array
    {
        $games = $this->fetchGames();
        $matches = WorldCupMatch::query()
            ->with(['homeTeam', 'awayTeam'])
            ->get();

        $summary = [
            'fetched' => count($games),
            'finished' => 0,
            'live' => 0,
            'updated' => 0,
            'unchanged' => 0,
            'skipped' => 0,
            'points_updated' => 0,
            'unmatched' => [],
        ];

        foreach ($games as $game) {
            $isFinished = $this->isFinished($game);
            $status = $this->matchStatus($game);

            if (! $this->hasStarted($game) && ! $isFinished) {
                continue;
            }

            if ($isFinished) {
                $summary['finished']++;
            } else {
                $summary['live']++;
            }

            if (! $this->hasValidScore($game)) {
                $summary['skipped']++;

                continue;
            }

            $match = $this->findMatch($matches, $game);

            if (! $match) {
                $summary['unmatched'][] = $this->gameLabel($game);

                continue;
            }

            $homeScore = (int) $game['home_score'];
            $awayScore = (int) $game['away_score'];

            if (
                $match->home_score === $homeScore
                && $match->away_score === $awayScore
                && $match->is_finished === $isFinished
                && $match->match_status === $status
            ) {
                $summary['unchanged']++;

                continue;
            }

            if (! $dryRun) {
                $match->forceFill([
                    'home_score' => $homeScore,
                    'away_score' => $awayScore,
                    'is_finished' => $isFinished,
                    'match_status' => $status,
                    'last_score_synced_at' => now(),
                ])->save();
            }

            $summary['updated']++;
        }

        if (! $dryRun) {
            $summary['points_updated'] = $this->predictionScorer->recalculateFinishedMatches();
        }

        return $summary;
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

    private function findMatch($matches, array $game): ?WorldCupMatch
    {
        if (($game['type'] ?? 'group') !== 'group' && isset($game['id'])) {
            return $matches->firstWhere('match_number', (int) $game['id']);
        }

        $group = (string) ($game['group'] ?? '');
        $home = $this->canonicalTeamName((string) ($game['home_team_name_en'] ?? ''));
        $away = $this->canonicalTeamName((string) ($game['away_team_name_en'] ?? ''));

        return $matches->first(function (WorldCupMatch $match) use ($group, $home, $away) {
            return $match->group_name === $group
                && $match->homeTeam
                && $match->awayTeam
                && $this->canonicalTeamName($match->homeTeam->name) === $home
                && $this->canonicalTeamName($match->awayTeam->name) === $away;
        });
    }

    private function isFinished(array $game): bool
    {
        return strtoupper((string) ($game['finished'] ?? '')) === 'TRUE'
            || strtolower((string) ($game['time_elapsed'] ?? '')) === 'finished';
    }

    private function hasStarted(array $game): bool
    {
        $status = strtolower((string) ($game['time_elapsed'] ?? ''));

        return $status !== ''
            && ! in_array($status, ['notstarted', 'not_started', 'scheduled'], true);
    }

    private function matchStatus(array $game): string
    {
        $status = strtolower((string) ($game['time_elapsed'] ?? ''));

        return $status !== '' ? $status : 'unknown';
    }

    private function hasValidScore(array $game): bool
    {
        return isset($game['home_score'], $game['away_score'])
            && is_numeric($game['home_score'])
            && is_numeric($game['away_score']);
    }

    private function canonicalTeamName(string $name): string
    {
        $normalized = Str::of($name)
            ->ascii()
            ->lower()
            ->replaceMatches('/[^a-z0-9]+/', ' ')
            ->squish()
            ->toString();

        return [
            'south korea' => 'korea republic',
            'czech republic' => 'czechia',
            'cape verde' => 'cabo verde',
            'curacao' => 'curacao',
            'dr congo' => 'congo dr',
            'democratic republic of congo' => 'congo dr',
            'ivory coast' => 'cote d ivoire',
        ][$normalized] ?? $normalized;
    }

    private function gameLabel(array $game): string
    {
        $home = $game['home_team_name_en'] ?? '?';
        $away = $game['away_team_name_en'] ?? '?';
        $group = $game['group'] ?? '?';

        if (($game['type'] ?? 'group') !== 'group') {
            $matchNumber = $game['id'] ?? '?';

            return "Jogo {$matchNumber}: {$home} x {$away}";
        }

        return "Grupo {$group}: {$home} x {$away}";
    }
}
