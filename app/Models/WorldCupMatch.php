<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Model;

class WorldCupMatch extends Model
{
    protected $table = 'matches';

    protected $fillable = [
        'stage',
        'match_number',
        'group_name',
        'round_name',
        'home_team_id',
        'away_team_id',
        'home_slot',
        'away_slot',
        'starts_at',
        'venue',
        'home_score',
        'away_score',
        'is_finished',
        'match_status',
        'last_score_synced_at',
    ];

    protected function casts(): array
    {
        return [
            'starts_at' => 'datetime',
            'is_finished' => 'boolean',
            'last_score_synced_at' => 'datetime',
        ];
    }

    public function homeTeam(): BelongsTo
    {
        return $this->belongsTo(Team::class, 'home_team_id');
    }

    public function awayTeam(): BelongsTo
    {
        return $this->belongsTo(Team::class, 'away_team_id');
    }

    public function predictions(): HasMany
    {
        return $this->hasMany(Prediction::class, 'match_id');
    }
}
