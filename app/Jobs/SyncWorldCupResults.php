<?php

namespace App\Jobs;

use App\Services\WorldCup\WorldCupResultsSyncer;
use App\Services\WorldCup\WorldCupKnockoutSyncer;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;

class SyncWorldCupResults implements ShouldBeUnique, ShouldQueue
{
    use Queueable;

    public int $tries = 3;

    public int $timeout = 60;

    public int $uniqueFor = 120;

    public function handle(WorldCupResultsSyncer $syncer, WorldCupKnockoutSyncer $knockoutSyncer): void
    {
        $knockoutSummary = $knockoutSyncer->sync();
        $summary = $syncer->sync();

        Log::info('World Cup knockout matches synced.', ['synced' => $knockoutSummary]);
        Log::info('World Cup results synced.', $summary);
    }

    public function uniqueId(): string
    {
        return 'worldcup-results-sync';
    }
}
