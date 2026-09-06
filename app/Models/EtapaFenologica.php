<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class EtapaFenologica extends Model
{
    use HasFactory;

    protected $table = 'etapa_fenologicas';

    protected $fillable = ['nombre', 'orden'];

    public function plantas(): HasMany
    {
        return $this->hasMany(Planta::class, 'etapa_fenologica_id');
    }
}
