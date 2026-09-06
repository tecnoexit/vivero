<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Lote extends Model
{
    use HasFactory;

    protected $table = 'lotes';

    protected $fillable = ['nombre', 'ubicacion', 'tipo', 'notas'];

    public function plantas(): HasMany
    {
        return $this->hasMany(Planta::class, 'lote_id');
    }
}
