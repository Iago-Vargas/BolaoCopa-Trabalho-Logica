<?php

namespace App\Http\Controllers;

use App\Models\Prediction;
use App\Models\WorldCupMatch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class KnockoutPredictionsController extends Controller
{
    public function index(Request $request)
    {
        $groupMatchIds = WorldCupMatch::query()
            ->where('stage', 'group')
            ->pluck('id');

        $completedGroupPredictions = Prediction::query()
            ->where('user_id', $request->user()->id)
            ->whereIn('match_id', $groupMatchIds)
            ->count();

        $requiredGroupPredictions = $groupMatchIds->count();
        $isUnlocked = $completedGroupPredictions >= $requiredGroupPredictions;

        $matchesByStage = collect();

        if ($isUnlocked) {
            $matchesByStage = WorldCupMatch::query()
                ->where('stage', '!=', 'group')
                ->with(['predictions' => fn ($query) => $query->where('user_id', $request->user()->id)])
                ->orderBy('match_number')
                ->get()
                ->groupBy('stage');
        }

        return view('knockout.index', [
            'matchesByStage' => $matchesByStage,
            'isUnlocked' => $isUnlocked,
            'completedGroupPredictions' => $completedGroupPredictions,
            'requiredGroupPredictions' => $requiredGroupPredictions,
            'flagCodes' => $this->flagCodes(),
            'simulatedTeams' => $this->simulatedTeams(),
        ]);
    }

    public function store(Request $request)
    {
        $payload = $request->input('predictions', []);

        if (! is_array($payload)) {
            return back()->with('prediction_error', 'Existem valores invalidos nos palpites.');
        }

        $groupMatchIds = WorldCupMatch::query()
            ->where('stage', 'group')
            ->pluck('id');

        $completedGroupPredictions = Prediction::query()
            ->where('user_id', $request->user()->id)
            ->whereIn('match_id', $groupMatchIds)
            ->count();

        if ($completedGroupPredictions < $groupMatchIds->count()) {
            return back()->with('prediction_error', 'Complete as 3 rodadas da fase de grupos antes de palpitar nas eliminatorias.');
        }

        $matchesById = WorldCupMatch::query()
            ->where('stage', '!=', 'group')
            ->get()
            ->keyBy('id');

        $validatedPredictions = [];
        $now = now('America/Sao_Paulo');

        foreach ($payload as $matchId => $scores) {
            if (! is_array($scores) || ! $matchesById->has((int) $matchId)) {
                return back()->with('prediction_error', 'Existem valores invalidos nos palpites.');
            }

            $match = $matchesById[(int) $matchId];

            if ($now->greaterThanOrEqualTo($match->starts_at->copy()->timezone('America/Sao_Paulo')->subHour())) {
                return back()->with('prediction_error', 'Este confronto ja fechou para palpites.');
            }

            $homeScore = $scores['home_score'] ?? null;
            $awayScore = $scores['away_score'] ?? null;

            if ($homeScore === null || $awayScore === null || $homeScore === '' || $awayScore === '') {
                return back()->with('prediction_error', 'Preencha todos os placares da fase exibida antes de salvar.');
            }

            if (! preg_match('/^[0-9]$/', (string) $homeScore) || ! preg_match('/^[0-9]$/', (string) $awayScore)) {
                return back()->with('prediction_error', 'Informe placares de 0 a 9.');
            }

            $validatedPredictions[] = [
                'match_id' => (int) $matchId,
                'home_score' => (int) $homeScore,
                'away_score' => (int) $awayScore,
            ];
        }

        if ($validatedPredictions === []) {
            return back()->with('prediction_error', 'Preencha ao menos um confronto antes de salvar.');
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

        return back()->with('prediction_success', 'Palpites das eliminatorias salvos com sucesso.');
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

    private function simulatedTeams(): array
    {
        return [
            73 => ['home' => ['code' => 'GER', 'name' => 'Alemanha'], 'away' => ['code' => 'QAT', 'name' => 'Catar']],
            74 => ['home' => ['code' => 'NOR', 'name' => 'Noruega'], 'away' => ['code' => 'AUS', 'name' => 'Australia']],
            75 => ['home' => ['code' => 'CZE', 'name' => 'Tchequia'], 'away' => ['code' => 'CAN', 'name' => 'Canada']],
            76 => ['home' => ['code' => 'JPN', 'name' => 'Japao'], 'away' => ['code' => 'SCO', 'name' => 'Escocia']],
            77 => ['home' => ['code' => 'UZB', 'name' => 'Uzbequistao'], 'away' => ['code' => 'ENG', 'name' => 'Inglaterra']],
            78 => ['home' => ['code' => 'ESP', 'name' => 'Espanha'], 'away' => ['code' => 'AUT', 'name' => 'Austria']],
            79 => ['home' => ['code' => 'PAR', 'name' => 'Paraguai'], 'away' => ['code' => 'NED', 'name' => 'Holanda']],
            80 => ['home' => ['code' => 'BEL', 'name' => 'Belgica'], 'away' => ['code' => 'ALG', 'name' => 'Argelia']],
            81 => ['home' => ['code' => 'HAI', 'name' => 'Haiti'], 'away' => ['code' => 'SWE', 'name' => 'Suecia']],
            82 => ['home' => ['code' => 'ECU', 'name' => 'Equador'], 'away' => ['code' => 'SEN', 'name' => 'Senegal']],
            83 => ['home' => ['code' => 'KOR', 'name' => 'Coreia do Sul'], 'away' => ['code' => 'CUW', 'name' => 'Curacao']],
            84 => ['home' => ['code' => 'CRO', 'name' => 'Croacia'], 'away' => ['code' => 'POR', 'name' => 'Portugal']],
            85 => ['home' => ['code' => 'ARG', 'name' => 'Argentina'], 'away' => ['code' => 'CPV', 'name' => 'Cabo Verde']],
            86 => ['home' => ['code' => 'USA', 'name' => 'Estados Unidos'], 'away' => ['code' => 'EGY', 'name' => 'Egito']],
            87 => ['home' => ['code' => 'SUI', 'name' => 'Suica'], 'away' => ['code' => 'NZL', 'name' => 'Nova Zelandia']],
            88 => ['home' => ['code' => 'COL', 'name' => 'Colombia'], 'away' => ['code' => 'GHA', 'name' => 'Gana']],
            89 => ['home' => ['code' => 'GER', 'name' => 'Alemanha'], 'away' => ['code' => 'NOR', 'name' => 'Noruega']],
            90 => ['home' => ['code' => 'CAN', 'name' => 'Canada'], 'away' => ['code' => 'JPN', 'name' => 'Japao']],
            91 => ['home' => ['code' => 'ENG', 'name' => 'Inglaterra'], 'away' => ['code' => 'ESP', 'name' => 'Espanha']],
            92 => ['home' => ['code' => 'NED', 'name' => 'Holanda'], 'away' => ['code' => 'BEL', 'name' => 'Belgica']],
            93 => ['home' => ['code' => 'SWE', 'name' => 'Suecia'], 'away' => ['code' => 'ECU', 'name' => 'Equador']],
            94 => ['home' => ['code' => 'KOR', 'name' => 'Coreia do Sul'], 'away' => ['code' => 'POR', 'name' => 'Portugal']],
            95 => ['home' => ['code' => 'ARG', 'name' => 'Argentina'], 'away' => ['code' => 'USA', 'name' => 'Estados Unidos']],
            96 => ['home' => ['code' => 'SUI', 'name' => 'Suica'], 'away' => ['code' => 'COL', 'name' => 'Colombia']],
            97 => ['home' => ['code' => 'GER', 'name' => 'Alemanha'], 'away' => ['code' => 'CAN', 'name' => 'Canada']],
            98 => ['home' => ['code' => 'ENG', 'name' => 'Inglaterra'], 'away' => ['code' => 'BEL', 'name' => 'Belgica']],
            99 => ['home' => ['code' => 'SWE', 'name' => 'Suecia'], 'away' => ['code' => 'POR', 'name' => 'Portugal']],
            100 => ['home' => ['code' => 'ARG', 'name' => 'Argentina'], 'away' => ['code' => 'COL', 'name' => 'Colombia']],
            101 => ['home' => ['code' => 'GER', 'name' => 'Alemanha'], 'away' => ['code' => 'ENG', 'name' => 'Inglaterra']],
            102 => ['home' => ['code' => 'POR', 'name' => 'Portugal'], 'away' => ['code' => 'ARG', 'name' => 'Argentina']],
            103 => ['home' => ['code' => 'ENG', 'name' => 'Inglaterra'], 'away' => ['code' => 'POR', 'name' => 'Portugal']],
            104 => ['home' => ['code' => 'GER', 'name' => 'Alemanha'], 'away' => ['code' => 'ARG', 'name' => 'Argentina']],
        ];
    }
}
