<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Foto extends Model
{
    use HasFactory;

    protected $table = 'fotos';

    protected $fillable = [
        'planta_id',
        'evento_id',
        'tipo_foto_id',
        'imagen',
        'fecha',
        'activa',
        'autor_id',
    ];

    protected $casts = [
        'fecha' => 'date',
        'activa' => 'boolean',
    ];

    protected static function booted(): void
    {
        static::saving(function (self $foto): void {
            if ($foto->activa) {
                static::where('planta_id', $foto->planta_id)
                    ->where('tipo_foto_id', $foto->tipo_foto_id)
                    ->where('id', '!=', $foto->id ?? 0)
                    ->update(['activa' => false]);
            }
        });
    }

    public function planta(): BelongsTo
    {
        return $this->belongsTo(Planta::class, 'planta_id');
    }

    public function evento(): BelongsTo
    {
        return $this->belongsTo(Evento::class, 'evento_id');
    }

    public function tipoFoto(): BelongsTo
    {
        return $this->belongsTo(TipoFoto::class, 'tipo_foto_id');
    }

    public function autor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'autor_id');
    }
}
