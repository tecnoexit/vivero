<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Proveedor extends Model
{
    use HasFactory;

    protected $table = 'proveedores';

    protected $fillable = ['nombre', 'contacto', 'notas'];

    public function bandejas(): HasMany
    {
        return $this->hasMany(Bandeja::class, 'proveedor_id');
    }

    public function plantas(): HasMany
    {
        return $this->hasMany(Planta::class, 'proveedor_id');
    }
}
