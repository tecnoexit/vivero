<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CambioEstado extends Model
{
    use HasFactory;

    protected $table = 'cambio_estados';

    protected $fillable = [
        'planta_id',
        'estado_anterior',
        'estado_nuevo',
        'motivo',
        'fecha',
        'autor_id',
    ];

    protected $casts = [
        'fecha' => 'date',
    ];

    public function planta(): BelongsTo
    {
        return $this->belongsTo(Planta::class, 'planta_id');
    }

    public function autor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'autor_id');
    }
}
