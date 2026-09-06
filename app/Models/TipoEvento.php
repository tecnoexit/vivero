<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TipoEvento extends Model
{
    use HasFactory;

    protected $table = 'tipo_eventos';

    protected $fillable = ['nombre'];

    public function eventos(): HasMany
    {
        return $this->hasMany(Evento::class, 'tipo_evento_id');
    }
}
