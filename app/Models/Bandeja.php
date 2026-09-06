<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Bandeja extends Model
{
    use HasFactory;

    protected $table = 'bandejas';

    protected $fillable = ['variedad_id', 'origen', 'proveedor_id', 'fecha_siembra', 'n_semillas', 'notas'];

    protected $casts = [
        'fecha_siembra' => 'date',
    ];

    public function variedad(): BelongsTo
    {
        return $this->belongsTo(Variedad::class, 'variedad_id');
    }

    public function proveedor(): BelongsTo
    {
        return $this->belongsTo(Proveedor::class, 'proveedor_id');
    }

    public function plantas(): HasMany
    {
        return $this->hasMany(Planta::class, 'bandeja_id');
    }
}
