<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Planta extends Model
{
    use HasFactory;

    protected $table = 'plantas';

    protected $fillable = [
        'codigo',
        'variedad_id',
        'origen',
        'proveedor_id',
        'bandeja_id',
        'fecha_alta',
        'etapa_fenologica_id',
        'contenedor',
        'lote_id',
        'estado',
        'fecha_baja',
        'motivo_baja',
        'notas',
        'token_publico',
        'publico_activo',
    ];

    protected $casts = [
        'fecha_alta' => 'date',
        'fecha_baja' => 'date',
        'ultima_fecha_medicion' => 'date',
        'publico_activo' => 'boolean',
    ];

    protected static function booted(): void
    {
        static::creating(function (self $planta): void {
            if (! $planta->token_publico) {
                $planta->token_publico = (string) Str::uuid();
            }

            if (! $planta->codigo) {
                $planta->codigo = static::generarCodigoAutomatico($planta->variedad_id);
            }
        });
    }

    public function variedad(): BelongsTo
    {
        return $this->belongsTo(Variedad::class, 'variedad_id');
    }

    public function proveedor(): BelongsTo
    {
        return $this->belongsTo(Proveedor::class, 'proveedor_id');
    }

    public function bandeja(): BelongsTo
    {
        return $this->belongsTo(Bandeja::class, 'bandeja_id');
    }

    public function etapaFenologica(): BelongsTo
    {
        return $this->belongsTo(EtapaFenologica::class, 'etapa_fenologica_id');
    }

    public function lote(): BelongsTo
    {
        return $this->belongsTo(Lote::class, 'lote_id');
    }

    public function mediciones(): HasMany
    {
        return $this->hasMany(Medicion::class, 'planta_id');
    }

    public function evaluaciones(): HasMany
    {
        return $this->hasMany(Evaluacion::class, 'planta_id');
    }

    public function fotos(): HasMany
    {
        return $this->hasMany(Foto::class, 'planta_id');
    }

    public function cambiosEstado(): HasMany
    {
        return $this->hasMany(CambioEstado::class, 'planta_id');
    }

    public function eventos(): BelongsToMany
    {
        return $this->belongsToMany(Evento::class, 'evento_planta', 'planta_id', 'evento_id')->withTimestamps();
    }

    public static function generarCodigoAutomatico(?int $variedadId): string
    {
        $prefijo = 'PLT';

        if ($variedadId) {
            $variedad = Variedad::find($variedadId);
            if ($variedad) {
                $prefijo = $variedad->prefijoCodigo();
            }
        }

        $secuencia = 1;

        do {
            $codigo = sprintf('%s-%04d', $prefijo, $secuencia);
            $exists = static::where('codigo', $codigo)->exists();
            $secuencia++;
        } while ($exists);

        return $codigo;
    }

    public function recalcularMetricas(): void
    {
        $mediciones = $this->mediciones()->orderByDesc('fecha')->orderByDesc('id')->get();
        $ultima = $mediciones->first();

        $this->ultima_altura = $ultima?->altura_cm;
        $this->ultimo_diametro = $ultima?->diametro_tallo_mm;
        $this->ultima_fecha_medicion = $ultima?->fecha;

        if ($ultima && $ultima->altura_cm !== null && $ultima->diametro_tallo_mm) {
            $this->indice_esbeltez = round($ultima->altura_cm / max($ultima->diametro_tallo_mm, 0.01), 2);
        } else {
            $this->indice_esbeltez = null;
        }

        if ($mediciones->count() >= 2) {
            $anterior = $mediciones->skip(1)->first();
            $dias = Carbon::parse($ultima->fecha)->diffInDays(Carbon::parse($anterior->fecha));
            if ($dias > 0 && $ultima->altura_cm !== null && $anterior->altura_cm !== null) {
                $this->tasa_crecimiento = round(($ultima->altura_cm - $anterior->altura_cm) / max($dias / 7, 0.01), 2);
            } else {
                $this->tasa_crecimiento = null;
            }
        } else {
            $this->tasa_crecimiento = null;
        }

        $this->n_eventos_fitosanitarios = $this->eventos()
            ->whereHas('tipoEvento', function ($query): void {
                $query->where('nombre', 'like', '%fitosanit%');
            })
            ->count();

        $ultimaEvaluacion = $this->evaluaciones()->orderByDesc('fecha')->orderByDesc('id')->first();
        $this->score_vigor_actual = $ultimaEvaluacion?->score_vigor;
        $this->score_sanidad_actual = $ultimaEvaluacion?->score_sanidad;

        $this->saveQuietly();
    }

    public function timeline()
    {
        return collect()
            ->concat($this->mediciones()->get()->map(fn ($item) => [
                'tipo' => 'medicion',
                'fecha' => $item->fecha,
                'titulo' => 'Medicion',
                'detalle' => trim(sprintf('Altura %s cm, diametro %s mm', $item->altura_cm, $item->diametro_tallo_mm)),
                'modelo' => $item,
            ]))
            ->concat($this->evaluaciones()->get()->map(fn ($item) => [
                'tipo' => 'evaluacion',
                'fecha' => $item->fecha,
                'titulo' => 'Evaluacion',
                'detalle' => 'Vigor '.$item->score_vigor.' / Sanidad '.$item->score_sanidad,
                'modelo' => $item,
            ]))
            ->concat($this->eventos()->get()->map(fn ($item) => [
                'tipo' => 'evento',
                'fecha' => $item->fecha,
                'titulo' => 'Evento',
                'detalle' => $item->tipoEvento?->nombre,
                'modelo' => $item,
            ]))
            ->concat($this->fotos()->get()->map(fn ($item) => [
                'tipo' => 'foto',
                'fecha' => $item->fecha,
                'titulo' => 'Foto',
                'detalle' => $item->tipoFoto?->nombre,
                'modelo' => $item,
            ]))
            ->concat($this->cambiosEstado()->get()->map(fn ($item) => [
                'tipo' => 'estado',
                'fecha' => $item->fecha,
                'titulo' => 'Estado',
                'detalle' => $item->estado_nuevo,
                'modelo' => $item,
            ]))
            ->sortByDesc('fecha')
            ->values();
    }
}
