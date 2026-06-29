<?php

use App\Jobs\SyncWorldCupResults;
use App\Services\WorldCup\WorldCupResultsSyncer;
use App\Services\WorldCup\WorldCupKnockoutSyncer;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('worldcup:sync-results {--dry-run : Simula a sincronizacao sem salvar no banco}', function () {
    $knockoutSynced = 0;

    if (! (bool) $this->option('dry-run')) {
        $knockoutSynced = app(WorldCupKnockoutSyncer::class)->sync();
    }

    $summary = app(WorldCupResultsSyncer::class)->sync((bool) $this->option('dry-run'));

    $this->info('Sincronizacao de resultados da Copa concluida.');
    $this->line("Jogos eliminatorios sincronizados: {$knockoutSynced}");
    $this->line("Jogos recebidos: {$summary['fetched']}");
    $this->line("Jogos finalizados: {$summary['finished']}");
    $this->line("Jogos ao vivo: {$summary['live']}");
    $this->line("Atualizados: {$summary['updated']}");
    $this->line("Sem alteracao: {$summary['unchanged']}");
    $this->line("Ignorados: {$summary['skipped']}");
    $this->line("Pontuacoes recalculadas: {$summary['points_updated']}");

    if ($summary['unmatched'] !== []) {
        $this->warn('Jogos finalizados nao encontrados no banco:');

        foreach ($summary['unmatched'] as $game) {
            $this->line("- {$game}");
        }
    }
})->purpose('Sincroniza placares ao vivo e finalizados da Copa 2026');

Artisan::command('worldcup:sync-knockout', function () {
    $synced = app(WorldCupKnockoutSyncer::class)->sync();

    $this->info("Jogos eliminatorios sincronizados: {$synced}");
})->purpose('Sincroniza tabela de jogos eliminatorios da Copa 2026');

Schedule::job(new SyncWorldCupResults)
    ->everyMinute()
    ->name('worldcup-results-sync')
    ->withoutOverlapping();
