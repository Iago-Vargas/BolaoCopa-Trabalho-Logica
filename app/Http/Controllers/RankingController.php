<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Facades\DB;

class RankingController extends Controller
{
    public function index()
    {
        $ranking = User::query()
            ->leftJoin('predictions', 'predictions.user_id', '=', 'users.id')
            ->leftJoin('matches', 'matches.id', '=', 'predictions.match_id')
            ->select([
                'users.id',
                'users.name',
                'users.nickname',
                DB::raw('COALESCE(SUM(predictions.points), 0) as points'),
                DB::raw('COALESCE(SUM(CASE WHEN matches.is_finished = 1 AND matches.home_score IS NOT NULL AND matches.away_score IS NOT NULL AND predictions.home_score = matches.home_score AND predictions.away_score = matches.away_score THEN 1 ELSE 0 END), 0) as exact_scores'),
                DB::raw('COUNT(predictions.id) as predictions_count'),
                DB::raw('COALESCE(SUM(CASE WHEN matches.is_finished = 1 AND matches.home_score IS NOT NULL AND matches.away_score IS NOT NULL THEN 1 ELSE 0 END), 0) as scored_predictions_count'),
            ])
            ->groupBy('users.id', 'users.name', 'users.nickname')
            ->orderByDesc('points')
            ->orderByDesc('exact_scores')
            ->orderByDesc('predictions_count')
            ->orderBy('users.name')
            ->get()
            ->values()
            ->map(function ($user, $index) {
                $user->position = $index + 1;

                return $user;
            });

        return view('ranking.index', [
            'ranking' => $ranking,
            'leader' => $ranking->first(),
            'totalUsers' => $ranking->count(),
        ]);
    }
}
