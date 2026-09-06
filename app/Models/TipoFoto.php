<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TipoFoto extends Model
{
    use HasFactory;

    protected $table = 'tipo_fotos';

    protected $fillable = ['nombre'];

    public function fotos(): HasMany
    {
        return $this->hasMany(Foto::class, 'tipo_foto_id');
    }
}
