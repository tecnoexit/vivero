<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Evento extends Model
{
    use HasFactory;

    protected $table = 'eventos';

    protected $fillable = [
        'tipo_evento_id',
        'fecha',
        'producto',
        'dosis',
        'notas',
        'autor_id',
    ];

    protected $casts = [
        'fecha' => 'date',
    ];

    public function tipoEvento(): BelongsTo
    {
        return $this->belongsTo(TipoEvento::class, 'tipo_evento_id');
    }

    public function autor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'autor_id');
    }

    public function plantas(): BelongsToMany
    {
        return $this->belongsToMany(Planta::class, 'evento_planta', 'evento_id', 'planta_id')->withTimestamps();
    }

    protected static function booted(): void
    {
        static::saved(function (self $evento): void {
            $evento->plantas->each(fn (Planta $planta) => $planta->recalcularMetricas());
        });

        static::deleted(function (self $evento): void {
            $evento->plantas()->get()->each(fn (Planta $planta) => $planta->recalcularMetricas());
        });
    }
}
