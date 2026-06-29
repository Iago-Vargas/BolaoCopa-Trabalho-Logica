<?php

namespace App\Http\Controllers;

use App\Models\Prediction;
use App\Models\WorldCupMatch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PredictionsController extends Controller
{
    public function index(Request $request)
    {
        $matches = WorldCupMatch::query()
            ->where('stage', 'group')
            ->with(['homeTeam', 'awayTeam'])
            ->with(['predictions' => fn ($query) => $query->where('user_id', $request->user()->id)])
            ->orderBy('group_name')
            ->orderBy('starts_at')
            ->get()
            ->groupBy('group_name');

        $teamsByGroup = $matches->map(function ($groupMatches) {
            return $groupMatches
                ->flatMap(fn ($match) => [$match->homeTeam, $match->awayTeam])
                ->unique('id')
                ->values();
        });
        $roundLocks = $this->roundLocks($matches);

        return view('predictions.index', [
            'matchesByGroup' => $matches,
            'teamsByGroup' => $teamsByGroup,
            'roundLocks' => $roundLocks,
            'flagCodes' => $this->flagCodes(),
            'teamNames' => $this->teamNames(),
        ]);
    }

    public function scores()
    {
        return response()->json([
            'matches' => WorldCupMatch::query()
                ->where('stage', 'group')
                ->select([
                    'id',
                    'home_score',
                    'away_score',
                    'is_finished',
                    'match_status',
                    'last_score_synced_at',
                ])
                ->get()
                ->map(fn (WorldCupMatch $match) => [
                    'id' => $match->id,
                    'home_score' => $match->home_score,
                    'away_score' => $match->away_score,
                    'is_finished' => $match->is_finished,
                    'match_status' => $match->match_status,
                    'last_score_synced_at' => $match->last_score_synced_at?->toIso8601String(),
                ])
                ->values(),
        ]);
    }

    public function store(Request $request)
    {
        $payload = $request->input('predictions', []);

        if (! is_array($payload)) {
            return back()->with('prediction_error', 'Existem valores invalidos nos palpites.');
        }

        $now = now('America/Sao_Paulo');
        $openMatchIds = [];
        $validatedPredictions = [];

        $matchesByGroup = WorldCupMatch::query()
            ->where('stage', 'group')
            ->orderBy('group_name')
            ->orderBy('starts_at')
            ->get()
            ->groupBy('group_name');

        $roundLocks = $this->roundLocks($matchesByGroup);
        $matchesById = $matchesByGroup->flatten()->keyBy('id');

        foreach ($matchesByGroup as $matches) {
            foreach ($matches->chunk(2) as $roundIndex => $roundMatches) {
                $isClosed = $now->greaterThanOrEqualTo($roundLocks[$roundIndex]['closes_at']);

                foreach ($roundMatches as $match) {
                    if ($isClosed) {
                        if (array_key_exists($match->id, $payload)) {
                            return back()->with('prediction_error', 'Esta rodada ja foi fechada. Nao e mais possivel alterar esses palpites.');
                        }

                        continue;
                    }

                    $openMatchIds[$match->id] = true;
                }
            }
        }

        foreach ($payload as $matchId => $scores) {
            if (! is_array($scores)) {
                return back()->with('prediction_error', 'Existem valores invalidos nos palpites.');
            }

            if (! $matchesById->has((int) $matchId) || ! isset($openMatchIds[(int) $matchId])) {
                return back()->with('prediction_error', 'Esta rodada ja foi fechada. Nao e mais possivel alterar esses palpites.');
            }

            $homeScore = $scores['home_score'] ?? null;
            $awayScore = $scores['away_score'] ?? null;

            if (($homeScore === null || $homeScore === '') && ($awayScore === null || $awayScore === '')) {
                continue;
            }

            if ($homeScore === null || $awayScore === null || $homeScore === '' || $awayScore === '') {
                return back()->with('prediction_error', 'Existem valores nao definidos. Preencha todos os placares antes de salvar.');
            }

            if (! preg_match('/^[0-9]$/', (string) $homeScore) || ! preg_match('/^[0-9]$/', (string) $awayScore)) {
                return back()->with('prediction_error', 'Isso e Copa do Mundo, nao Kings League. Informe placares de 0 a 9.');
            }

            $validatedPredictions[] = [
                'match_id' => (int) $matchId,
                'home_score' => (int) $homeScore,
                'away_score' => (int) $awayScore,
            ];
        }

        if ($validatedPredictions === []) {
            return back()->with('prediction_error', 'Preencha os placares da rodada antes de salvar.');
        }

        DB::transaction(function () use ($request, $validatedPredictions) {
            foreach ($validatedPredictions as $prediction) {
                Prediction::updateOrCreate(
                    [
                        'user_id' => $request->user()->id,
                        'match_id' => $prediction['match_id'],
                    ],
                    [
                        'home_score' => $prediction['home_score'],
                        'away_score' => $prediction['away_score'],
                        'points' => 0,
                    ]
                );
            }
        });

        return back()->with('prediction_success', 'Palpites salvos com sucesso.');
    }

    private function roundLocks($matchesByGroup): array
    {
        $roundKickoffs = [];

        foreach ($matchesByGroup as $matches) {
            foreach ($matches->chunk(2) as $roundIndex => $roundMatches) {
                $firstKickoff = $roundMatches
                    ->sortBy('starts_at')
                    ->first()
                    ->starts_at
                    ->copy()
                    ->timezone('America/Sao_Paulo');

                if (! isset($roundKickoffs[$roundIndex]) || $firstKickoff->lessThan($roundKickoffs[$roundIndex])) {
                    $roundKickoffs[$roundIndex] = $firstKickoff;
                }
            }
        }

        return collect($roundKickoffs)
            ->map(fn ($kickoff) => [
                'first_kickoff' => $kickoff,
                'closes_at' => $kickoff->copy()->subHour(),
            ])
            ->all();
    }

    private function flagCodes(): array
    {
        return [
            'ALG' => 'dz',
            'ARG' => 'ar',
            'AUS' => 'au',
            'AUT' => 'at',
            'BEL' => 'be',
            'BIH' => 'ba',
            'BRA' => 'br',
            'CAN' => 'ca',
            'CIV' => 'ci',
            'COD' => 'cd',
            'COL' => 'co',
            'CPV' => 'cv',
            'CRO' => 'hr',
            'CUW' => 'cw',
            'CZE' => 'cz',
            'ECU' => 'ec',
            'EGY' => 'eg',
            'ENG' => 'gb-eng',
            'ESP' => 'es',
            'FRA' => 'fr',
            'GER' => 'de',
            'GHA' => 'gh',
            'HAI' => 'ht',
            'IRN' => 'ir',
            'IRQ' => 'iq',
            'JOR' => 'jo',
            'JPN' => 'jp',
            'KOR' => 'kr',
            'KSA' => 'sa',
            'MAR' => 'ma',
            'MEX' => 'mx',
            'NED' => 'nl',
            'NOR' => 'no',
            'NZL' => 'nz',
            'PAN' => 'pa',
            'PAR' => 'py',
            'POR' => 'pt',
            'QAT' => 'qa',
            'RSA' => 'za',
            'SCO' => 'gb-sct',
            'SEN' => 'sn',
            'SUI' => 'ch',
            'SWE' => 'se',
            'TUN' => 'tn',
            'TUR' => 'tr',
            'URU' => 'uy',
            'USA' => 'us',
            'UZB' => 'uz',
        ];
    }

    private function teamNames(): array
    {
        return [
            'ALG' => 'Argélia',
            'ARG' => 'Argentina',
            'AUS' => 'Austrália',
            'AUT' => 'Áustria',
            'BEL' => 'Bélgica',
            'BIH' => 'Bósnia e Herzegovina',
            'BRA' => 'Brasil',
            'CAN' => 'Canadá',
            'CIV' => 'Costa do Marfim',
            'COD' => 'RD Congo',
            'COL' => 'Colômbia',
            'CPV' => 'Cabo Verde',
            'CRO' => 'Croácia',
            'CUW' => 'Curaçao',
            'CZE' => 'República Tcheca',
            'ECU' => 'Equador',
            'EGY' => 'Egito',
            'ENG' => 'Inglaterra',
            'ESP' => 'Espanha',
            'FRA' => 'França',
            'GER' => 'Alemanha',
            'GHA' => 'Gana',
            'HAI' => 'Haiti',
            'IRN' => 'Irã',
            'IRQ' => 'Iraque',
            'JOR' => 'Jordânia',
            'JPN' => 'Japão',
            'KOR' => 'Coreia do Sul',
            'KSA' => 'Arábia Saudita',
            'MAR' => 'Marrocos',
            'MEX' => 'México',
            'NED' => 'Holanda',
            'NOR' => 'Noruega',
            'NZL' => 'Nova Zelândia',
            'PAN' => 'Panamá',
            'PAR' => 'Paraguai',
            'POR' => 'Portugal',
            'QAT' => 'Catar',
            'RSA' => 'África do Sul',
            'SCO' => 'Escócia',
            'SEN' => 'Senegal',
            'SUI' => 'Suíça',
            'SWE' => 'Suécia',
            'TUN' => 'Tunísia',
            'TUR' => 'Turquia',
            'URU' => 'Uruguai',
            'USA' => 'Estados Unidos',
            'UZB' => 'Uzbequistão',
        ];
    }
}
