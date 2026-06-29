<?php

namespace Database\Seeders;

use App\Models\Team;
use App\Models\WorldCupMatch;
use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class GroupStageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $fixtures = [
            ['A', 'Mexico', 'South Africa', '2026-06-11 19:00', 'Mexico City'],
            ['A', 'Korea Republic', 'Czechia', '2026-06-12 02:00', 'Guadalajara'],
            ['A', 'Czechia', 'South Africa', '2026-06-18 16:00', 'Atlanta'],
            ['A', 'Mexico', 'Korea Republic', '2026-06-19 01:00', 'Guadalajara'],
            ['A', 'Czechia', 'Mexico', '2026-06-25 01:00', 'Mexico City'],
            ['A', 'South Africa', 'Korea Republic', '2026-06-25 01:00', 'Monterrey'],

            ['B', 'Canada', 'Bosnia and Herzegovina', '2026-06-12 19:00', 'Toronto'],
            ['B', 'Qatar', 'Switzerland', '2026-06-13 19:00', 'San Francisco Bay Area'],
            ['B', 'Switzerland', 'Bosnia and Herzegovina', '2026-06-18 19:00', 'Los Angeles'],
            ['B', 'Canada', 'Qatar', '2026-06-18 22:00', 'Vancouver'],
            ['B', 'Switzerland', 'Canada', '2026-06-24 19:00', 'Vancouver'],
            ['B', 'Bosnia and Herzegovina', 'Qatar', '2026-06-24 19:00', 'Seattle'],

            ['C', 'Brazil', 'Morocco', '2026-06-13 22:00', 'New York/New Jersey'],
            ['C', 'Haiti', 'Scotland', '2026-06-14 01:00', 'Boston'],
            ['C', 'Scotland', 'Morocco', '2026-06-19 22:00', 'Boston'],
            ['C', 'Brazil', 'Haiti', '2026-06-20 00:30', 'Philadelphia'],
            ['C', 'Scotland', 'Brazil', '2026-06-24 22:00', 'Miami'],
            ['C', 'Morocco', 'Haiti', '2026-06-24 22:00', 'Atlanta'],

            ['D', 'United States', 'Paraguay', '2026-06-13 01:00', 'Los Angeles'],
            ['D', 'Australia', 'Turkey', '2026-06-14 04:00', 'Vancouver'],
            ['D', 'United States', 'Australia', '2026-06-19 19:00', 'Seattle'],
            ['D', 'Turkey', 'Paraguay', '2026-06-20 03:00', 'San Francisco Bay Area'],
            ['D', 'Turkey', 'United States', '2026-06-26 02:00', 'Los Angeles'],
            ['D', 'Paraguay', 'Australia', '2026-06-26 02:00', 'San Francisco Bay Area'],

            ['E', 'Germany', 'Curacao', '2026-06-14 17:00', 'Houston'],
            ['E', "Cote d'Ivoire", 'Ecuador', '2026-06-14 23:00', 'Philadelphia'],
            ['E', 'Germany', "Cote d'Ivoire", '2026-06-20 20:00', 'Toronto'],
            ['E', 'Ecuador', 'Curacao', '2026-06-21 00:00', 'Kansas City'],
            ['E', 'Curacao', "Cote d'Ivoire", '2026-06-25 20:00', 'Philadelphia'],
            ['E', 'Ecuador', 'Germany', '2026-06-25 20:00', 'New York/New Jersey'],

            ['F', 'Netherlands', 'Japan', '2026-06-14 20:00', 'Dallas'],
            ['F', 'Sweden', 'Tunisia', '2026-06-15 02:00', 'Monterrey'],
            ['F', 'Netherlands', 'Sweden', '2026-06-20 17:00', 'Houston'],
            ['F', 'Tunisia', 'Japan', '2026-06-21 04:00', 'Monterrey'],
            ['F', 'Japan', 'Sweden', '2026-06-25 23:00', 'Dallas'],
            ['F', 'Tunisia', 'Netherlands', '2026-06-25 23:00', 'Kansas City'],

            ['G', 'Belgium', 'Egypt', '2026-06-15 19:00', 'Seattle'],
            ['G', 'Iran', 'New Zealand', '2026-06-16 01:00', 'Los Angeles'],
            ['G', 'Belgium', 'Iran', '2026-06-21 19:00', 'Los Angeles'],
            ['G', 'New Zealand', 'Egypt', '2026-06-22 01:00', 'Vancouver'],
            ['G', 'Egypt', 'Iran', '2026-06-27 03:00', 'Seattle'],
            ['G', 'New Zealand', 'Belgium', '2026-06-27 03:00', 'Vancouver'],

            ['H', 'Spain', 'Cabo Verde', '2026-06-15 16:00', 'Atlanta'],
            ['H', 'Saudi Arabia', 'Uruguay', '2026-06-15 22:00', 'Miami'],
            ['H', 'Spain', 'Saudi Arabia', '2026-06-21 16:00', 'Atlanta'],
            ['H', 'Uruguay', 'Cabo Verde', '2026-06-21 22:00', 'Miami'],
            ['H', 'Cabo Verde', 'Saudi Arabia', '2026-06-27 00:00', 'Houston'],
            ['H', 'Uruguay', 'Spain', '2026-06-27 00:00', 'Guadalajara'],

            ['I', 'France', 'Senegal', '2026-06-16 19:00', 'New York/New Jersey'],
            ['I', 'Iraq', 'Norway', '2026-06-16 22:00', 'Boston'],
            ['I', 'France', 'Iraq', '2026-06-22 21:00', 'Philadelphia'],
            ['I', 'Norway', 'Senegal', '2026-06-23 00:00', 'New York/New Jersey'],
            ['I', 'Norway', 'France', '2026-06-26 19:00', 'Boston'],
            ['I', 'Senegal', 'Iraq', '2026-06-26 19:00', 'Toronto'],

            ['J', 'Argentina', 'Algeria', '2026-06-17 01:00', 'Kansas City'],
            ['J', 'Austria', 'Jordan', '2026-06-17 04:00', 'San Francisco Bay Area'],
            ['J', 'Argentina', 'Austria', '2026-06-22 17:00', 'Dallas'],
            ['J', 'Jordan', 'Algeria', '2026-06-23 03:00', 'San Francisco Bay Area'],
            ['J', 'Algeria', 'Austria', '2026-06-28 02:00', 'Kansas City'],
            ['J', 'Jordan', 'Argentina', '2026-06-28 02:00', 'Dallas'],

            ['K', 'Portugal', 'Congo DR', '2026-06-17 17:00', 'Houston'],
            ['K', 'Uzbekistan', 'Colombia', '2026-06-18 02:00', 'Mexico City'],
            ['K', 'Portugal', 'Uzbekistan', '2026-06-23 17:00', 'Houston'],
            ['K', 'Colombia', 'Congo DR', '2026-06-24 02:00', 'Guadalajara'],
            ['K', 'Colombia', 'Portugal', '2026-06-27 23:30', 'Miami'],
            ['K', 'Congo DR', 'Uzbekistan', '2026-06-27 23:30', 'Atlanta'],

            ['L', 'England', 'Croatia', '2026-06-17 20:00', 'Dallas'],
            ['L', 'Ghana', 'Panama', '2026-06-17 23:00', 'Toronto'],
            ['L', 'England', 'Ghana', '2026-06-23 20:00', 'Boston'],
            ['L', 'Panama', 'Croatia', '2026-06-23 23:00', 'Toronto'],
            ['L', 'Panama', 'England', '2026-06-27 21:00', 'New York/New Jersey'],
            ['L', 'Croatia', 'Ghana', '2026-06-27 21:00', 'Philadelphia'],
        ];

        foreach ($fixtures as [$group, $home, $away, $startsAt, $venue]) {
            $homeTeam = Team::firstOrCreate(
                ['name' => $home],
                ['code' => $this->codeFor($home)]
            );

            $awayTeam = Team::firstOrCreate(
                ['name' => $away],
                ['code' => $this->codeFor($away)]
            );

            WorldCupMatch::updateOrCreate(
                [
                    'group_name' => $group,
                    'home_team_id' => $homeTeam->id,
                    'away_team_id' => $awayTeam->id,
                ],
                [
                    'starts_at' => Carbon::parse($startsAt, 'UTC'),
                    'venue' => $venue,
                ]
            );
        }
    }

    private function codeFor(string $team): string
    {
        return [
            'Algeria' => 'ALG',
            'Argentina' => 'ARG',
            'Australia' => 'AUS',
            'Austria' => 'AUT',
            'Belgium' => 'BEL',
            'Bosnia and Herzegovina' => 'BIH',
            'Brazil' => 'BRA',
            'Cabo Verde' => 'CPV',
            'Canada' => 'CAN',
            'Colombia' => 'COL',
            'Congo DR' => 'COD',
            "Cote d'Ivoire" => 'CIV',
            'Croatia' => 'CRO',
            'Curacao' => 'CUW',
            'Czechia' => 'CZE',
            'Ecuador' => 'ECU',
            'Egypt' => 'EGY',
            'England' => 'ENG',
            'France' => 'FRA',
            'Germany' => 'GER',
            'Ghana' => 'GHA',
            'Haiti' => 'HAI',
            'Iran' => 'IRN',
            'Iraq' => 'IRQ',
            'Japan' => 'JPN',
            'Jordan' => 'JOR',
            'Korea Republic' => 'KOR',
            'Mexico' => 'MEX',
            'Morocco' => 'MAR',
            'Netherlands' => 'NED',
            'New Zealand' => 'NZL',
            'Norway' => 'NOR',
            'Panama' => 'PAN',
            'Paraguay' => 'PAR',
            'Portugal' => 'POR',
            'Qatar' => 'QAT',
            'Saudi Arabia' => 'KSA',
            'Scotland' => 'SCO',
            'Senegal' => 'SEN',
            'South Africa' => 'RSA',
            'Spain' => 'ESP',
            'Sweden' => 'SWE',
            'Switzerland' => 'SUI',
            'Tunisia' => 'TUN',
            'Turkey' => 'TUR',
            'United States' => 'USA',
            'Uruguay' => 'URU',
            'Uzbekistan' => 'UZB',
        ][$team];
    }
}
