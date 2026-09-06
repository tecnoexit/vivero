<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Medicion extends Model
{
    use HasFactory;

    protected $table = 'mediciones';

    protected $fillable = [
        'planta_id',
        'fecha',
        'altura_cm',
        'diametro_tallo_mm',
        'longitud_hoja_cm',
        'diametro_copa_cm',
        'n_ramas',
        'notas',
        'autor_id',
    ];

    protected $casts = [
        'fecha' => 'date',
    ];

    protected static function booted(): void
    {
        static::saved(fn (self $medicion) => $medicion->planta?->recalcularMetricas());
        static::deleted(fn (self $medicion) => $medicion->planta?->recalcularMetricas());
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
