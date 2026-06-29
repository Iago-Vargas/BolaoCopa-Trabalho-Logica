<?php

namespace App\Services\Predictions;

use App\Models\Prediction;

class PredictionScorer
{
    public function score(int $predictionHome, int $predictionAway, int $resultHome, int $resultAway): int
    {
        if ($predictionHome === $resultHome && $predictionAway === $resultAway) {
            return 10;
        }

        if ($this->outcome($predictionHome, $predictionAway) === $this->outcome($resultHome, $resultAway)) {
            return 3;
        }

        return 0;
    }

    public function recalculateFinishedMatches(): int
    {
        $updated = 0;

        Prediction::query()
            ->whereHas('match', fn ($query) => $query
                ->where('is_finished', true)
                ->whereNotNull('home_score')
                ->whereNotNull('away_score'))
            ->with('match')
            ->chunkById(100, function ($predictions) use (&$updated) {
                foreach ($predictions as $prediction) {
                    $points = $this->score(
                        $prediction->home_score,
                        $prediction->away_score,
                        $prediction->match->home_score,
                        $prediction->match->away_score,
                    );

                    if ($prediction->points === $points) {
                        continue;
                    }

                    $prediction->forceFill(['points' => $points])->save();
                    $updated++;
                }
            });

        return $updated;
    }

    private function outcome(int $homeScore, int $awayScore): int
    {
        return $homeScore <=> $awayScore;
    }
}
