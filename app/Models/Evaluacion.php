<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Evaluacion extends Model
{
    use HasFactory;

    protected $table = 'evaluaciones';

    protected $fillable = [
        'planta_id',
        'fecha',
        'score_vigor',
        'score_sanidad',
        'notas',
        'autor_id',
    ];

    protected $casts = [
        'fecha' => 'date',
    ];

    protected static function booted(): void
    {
        static::saved(fn (self $evaluacion) => $evaluacion->planta?->recalcularMetricas());
        static::deleted(fn (self $evaluacion) => $evaluacion->planta?->recalcularMetricas());
    }

    public function planta(): BelongsTo
    {
        return $this->belongsTo(Planta::class, 'planta_id');
    }

    public function autor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'autor_id');
    }
}
