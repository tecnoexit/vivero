<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Variedad extends Model
{
    use HasFactory;

    protected $table = 'variedades';

    protected $fillable = ['nombre', 'especie', 'notas'];

    public function bandejas(): HasMany
    {
        return $this->hasMany(Bandeja::class, 'variedad_id');
    }

    public function plantas(): HasMany
    {
        return $this->hasMany(Planta::class, 'variedad_id');
    }

    public function prefijoCodigo(): string
    {
        $slug = Str::upper(Str::slug($this->nombre, ''));
        $prefijo = Str::substr($slug, 0, 3);

        return str_pad($prefijo ?: 'PLT', 3, 'X');
    }

    public function __toString(): string
    {
        return $this->nombre;
    }
}
