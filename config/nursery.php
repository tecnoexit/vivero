<?php

use App\Models\Bandeja;
use App\Models\CambioEstado;
use App\Models\EtapaFenologica;
use App\Models\Evento;
use App\Models\Evaluacion;
use App\Models\Foto;
use App\Models\Lote;
use App\Models\Medicion;
use App\Models\Planta;
use App\Models\Proveedor;
use App\Models\TipoEvento;
use App\Models\TipoFoto;
use App\Models\Variedad;

return [
    'roles' => [
        'admin',
        'operario',
    ],

    'resources' => [
        'variedades' => [
            'model' => Variedad::class,
            'title' => 'Variedades',
            'fields' => [
                ['name' => 'nombre', 'label' => 'Nombre', 'type' => 'text', 'rules' => 'required|string|max:255'],
                ['name' => 'especie', 'label' => 'Especie', 'type' => 'text', 'rules' => 'nullable|string|max:255'],
                ['name' => 'notas', 'label' => 'Notas', 'type' => 'textarea', 'rules' => 'nullable|string'],
            ],
            'columns' => ['nombre', 'especie', 'notas'],
        ],
        'proveedores' => [
            'model' => Proveedor::class,
            'title' => 'Proveedores',
            'fields' => [
                ['name' => 'nombre', 'label' => 'Nombre', 'type' => 'text', 'rules' => 'required|string|max:255'],
                ['name' => 'contacto', 'label' => 'Contacto', 'type' => 'text', 'rules' => 'nullable|string|max:255'],
                ['name' => 'notas', 'label' => 'Notas', 'type' => 'textarea', 'rules' => 'nullable|string'],
            ],
            'columns' => ['nombre', 'contacto', 'notas'],
        ],
        'etapas' => [
            'model' => EtapaFenologica::class,
            'title' => 'Etapas fenologicas',
            'fields' => [
                ['name' => 'nombre', 'label' => 'Nombre', 'type' => 'text', 'rules' => 'required|string|max:255'],
                ['name' => 'orden', 'label' => 'Orden', 'type' => 'number', 'rules' => 'required|integer|min:0'],
            ],
            'columns' => ['nombre', 'orden'],
        ],
        'tipos-evento' => [
            'model' => TipoEvento::class,
            'title' => 'Tipos de evento',
            'fields' => [
                ['name' => 'nombre', 'label' => 'Nombre', 'type' => 'text', 'rules' => 'required|string|max:255'],
            ],
            'columns' => ['nombre'],
        ],
        'tipos-foto' => [
            'model' => TipoFoto::class,
            'title' => 'Tipos de foto',
            'fields' => [
                ['name' => 'nombre', 'label' => 'Nombre', 'type' => 'text', 'rules' => 'required|string|max:255'],
            ],
            'columns' => ['nombre'],
        ],
        'lotes' => [
            'model' => Lote::class,
            'title' => 'Lotes',
            'fields' => [
                ['name' => 'nombre', 'label' => 'Nombre', 'type' => 'text', 'rules' => 'required|string|max:255'],
                ['name' => 'ubicacion', 'label' => 'Ubicacion', 'type' => 'text', 'rules' => 'nullable|string|max:255'],
                ['name' => 'tipo', 'label' => 'Tipo', 'type' => 'select', 'options' => [
                    'invernadero' => 'Invernadero',
                    'lote' => 'Lote',
                    'hilera' => 'Hilera',
                ], 'rules' => 'required|string|max:255'],
                ['name' => 'notas', 'label' => 'Notas', 'type' => 'textarea', 'rules' => 'nullable|string'],
            ],
            'columns' => ['nombre', 'ubicacion', 'tipo', 'notas'],
        ],
        'bandejas' => [
            'model' => Bandeja::class,
            'title' => 'Bandejas',
            'fields' => [
                ['name' => 'variedad_id', 'label' => 'Variedad', 'type' => 'select', 'relation' => Variedad::class, 'rules' => 'required|exists:variedades,id'],
                ['name' => 'origen', 'label' => 'Origen', 'type' => 'select', 'options' => ['proveedor' => 'Proveedor', 'propia' => 'Propia'], 'rules' => 'required|string|max:255'],
                ['name' => 'proveedor_id', 'label' => 'Proveedor', 'type' => 'select', 'relation' => Proveedor::class, 'rules' => 'nullable|exists:proveedores,id'],
                ['name' => 'fecha_siembra', 'label' => 'Fecha de siembra', 'type' => 'date', 'rules' => 'nullable|date'],
                ['name' => 'n_semillas', 'label' => 'N. semillas', 'type' => 'number', 'rules' => 'required|integer|min:0'],
                ['name' => 'notas', 'label' => 'Notas', 'type' => 'textarea', 'rules' => 'nullable|string'],
            ],
            'columns' => ['variedad_id', 'origen', 'proveedor_id', 'fecha_siembra', 'n_semillas'],
        ],
        'plantas' => [
            'model' => Planta::class,
            'title' => 'Plantas',
            'fields' => [
                ['name' => 'codigo', 'label' => 'Codigo', 'type' => 'text', 'rules' => 'nullable|string|max:255|unique:plantas,codigo'],
                ['name' => 'variedad_id', 'label' => 'Variedad', 'type' => 'select', 'relation' => Variedad::class, 'rules' => 'required|exists:variedades,id'],
                ['name' => 'origen', 'label' => 'Origen', 'type' => 'select', 'options' => ['proveedor' => 'Proveedor', 'propia' => 'Propia'], 'rules' => 'required|string|max:255'],
                ['name' => 'proveedor_id', 'label' => 'Proveedor', 'type' => 'select', 'relation' => Proveedor::class, 'rules' => 'nullable|exists:proveedores,id'],
                ['name' => 'bandeja_id', 'label' => 'Bandeja', 'type' => 'select', 'relation' => Bandeja::class, 'rules' => 'nullable|exists:bandejas,id'],
                ['name' => 'fecha_alta', 'label' => 'Fecha de alta', 'type' => 'date', 'rules' => 'nullable|date'],
                ['name' => 'etapa_fenologica_id', 'label' => 'Etapa', 'type' => 'select', 'relation' => EtapaFenologica::class, 'rules' => 'nullable|exists:etapa_fenologicas,id'],
                ['name' => 'contenedor', 'label' => 'Contenedor', 'type' => 'select', 'options' => ['suelo' => 'Suelo', 'maceta' => 'Maceta'], 'rules' => 'required|string|max:255'],
                ['name' => 'lote_id', 'label' => 'Lote', 'type' => 'select', 'relation' => Lote::class, 'rules' => 'nullable|exists:lotes,id'],
                ['name' => 'estado', 'label' => 'Estado', 'type' => 'select', 'options' => [
                    'activa' => 'Activa',
                    'muerta' => 'Muerta',
                    'vendida' => 'Vendida',
                    'regalada' => 'Regalada',
                    'descartada' => 'Descartada',
                    'seleccionada' => 'Seleccionada',
                ], 'rules' => 'required|string|max:255'],
                ['name' => 'fecha_baja', 'label' => 'Fecha de baja', 'type' => 'date', 'rules' => 'nullable|date'],
                ['name' => 'motivo_baja', 'label' => 'Motivo de baja', 'type' => 'text', 'rules' => 'nullable|string|max:255'],
                ['name' => 'notas', 'label' => 'Notas', 'type' => 'textarea', 'rules' => 'nullable|string'],
                ['name' => 'publico_activo', 'label' => 'Publico activo', 'type' => 'checkbox', 'rules' => 'nullable|boolean'],
            ],
            'columns' => ['codigo', 'variedad_id', 'estado', 'lote_id', 'publico_activo'],
        ],
        'mediciones' => [
            'model' => Medicion::class,
            'title' => 'Mediciones',
            'fields' => [
                ['name' => 'planta_id', 'label' => 'Planta', 'type' => 'select', 'relation' => Planta::class, 'rules' => 'required|exists:plantas,id'],
                ['name' => 'fecha', 'label' => 'Fecha', 'type' => 'date', 'rules' => 'required|date'],
                ['name' => 'altura_cm', 'label' => 'Altura cm', 'type' => 'number', 'step' => '0.01', 'rules' => 'nullable|numeric'],
                ['name' => 'diametro_tallo_mm', 'label' => 'Diametro tallo mm', 'type' => 'number', 'step' => '0.01', 'rules' => 'nullable|numeric'],
                ['name' => 'longitud_hoja_cm', 'label' => 'Longitud hoja cm', 'type' => 'number', 'step' => '0.01', 'rules' => 'nullable|numeric'],
                ['name' => 'diametro_copa_cm', 'label' => 'Diametro copa cm', 'type' => 'number', 'step' => '0.01', 'rules' => 'nullable|numeric'],
                ['name' => 'n_ramas', 'label' => 'N. ramas', 'type' => 'number', 'rules' => 'nullable|integer|min:0'],
                ['name' => 'notas', 'label' => 'Notas', 'type' => 'textarea', 'rules' => 'nullable|string'],
            ],
            'columns' => ['planta_id', 'fecha', 'altura_cm', 'diametro_tallo_mm', 'n_ramas'],
        ],
        'evaluaciones' => [
            'model' => Evaluacion::class,
            'title' => 'Evaluaciones',
            'fields' => [
                ['name' => 'planta_id', 'label' => 'Planta', 'type' => 'select', 'relation' => Planta::class, 'rules' => 'required|exists:plantas,id'],
                ['name' => 'fecha', 'label' => 'Fecha', 'type' => 'date', 'rules' => 'required|date'],
                ['name' => 'score_vigor', 'label' => 'Vigor', 'type' => 'number', 'rules' => 'required|integer|min:1|max:5'],
                ['name' => 'score_sanidad', 'label' => 'Sanidad', 'type' => 'number', 'rules' => 'required|integer|min:1|max:5'],
                ['name' => 'notas', 'label' => 'Notas', 'type' => 'textarea', 'rules' => 'nullable|string'],
            ],
            'columns' => ['planta_id', 'fecha', 'score_vigor', 'score_sanidad'],
        ],
        'eventos' => [
            'model' => Evento::class,
            'title' => 'Eventos',
            'fields' => [
                ['name' => 'tipo_evento_id', 'label' => 'Tipo de evento', 'type' => 'select', 'relation' => TipoEvento::class, 'rules' => 'required|exists:tipo_eventos,id'],
                ['name' => 'fecha', 'label' => 'Fecha', 'type' => 'date', 'rules' => 'required|date'],
                ['name' => 'producto', 'label' => 'Producto', 'type' => 'text', 'rules' => 'nullable|string|max:255'],
                ['name' => 'dosis', 'label' => 'Dosis', 'type' => 'text', 'rules' => 'nullable|string|max:255'],
                ['name' => 'plantas_ids', 'label' => 'Plantas', 'type' => 'multiselect', 'relation' => Planta::class, 'rules' => 'nullable|array'],
                ['name' => 'notas', 'label' => 'Notas', 'type' => 'textarea', 'rules' => 'nullable|string'],
            ],
            'columns' => ['tipo_evento_id', 'fecha', 'producto', 'dosis'],
        ],
        'fotos' => [
            'model' => Foto::class,
            'title' => 'Fotos',
            'fields' => [
                ['name' => 'planta_id', 'label' => 'Planta', 'type' => 'select', 'relation' => Planta::class, 'rules' => 'required|exists:plantas,id'],
                ['name' => 'evento_id', 'label' => 'Evento', 'type' => 'select', 'relation' => Evento::class, 'rules' => 'nullable|exists:eventos,id'],
                ['name' => 'tipo_foto_id', 'label' => 'Tipo de foto', 'type' => 'select', 'relation' => TipoFoto::class, 'rules' => 'required|exists:tipo_fotos,id'],
                ['name' => 'fecha', 'label' => 'Fecha', 'type' => 'date', 'rules' => 'required|date'],
                ['name' => 'imagen', 'label' => 'Imagen', 'type' => 'file', 'rules' => 'required|image|max:4096'],
                ['name' => 'activa', 'label' => 'Activa', 'type' => 'checkbox', 'rules' => 'nullable|boolean'],
            ],
            'columns' => ['planta_id', 'tipo_foto_id', 'fecha', 'activa'],
        ],
        'cambios-estado' => [
            'model' => CambioEstado::class,
            'title' => 'Cambios de estado',
            'fields' => [
                ['name' => 'planta_id', 'label' => 'Planta', 'type' => 'select', 'relation' => Planta::class, 'rules' => 'required|exists:plantas,id'],
                ['name' => 'estado_anterior', 'label' => 'Estado anterior', 'type' => 'text', 'rules' => 'nullable|string|max:255'],
                ['name' => 'estado_nuevo', 'label' => 'Estado nuevo', 'type' => 'text', 'rules' => 'required|string|max:255'],
                ['name' => 'motivo', 'label' => 'Motivo', 'type' => 'text', 'rules' => 'nullable|string|max:255'],
                ['name' => 'fecha', 'label' => 'Fecha', 'type' => 'date', 'rules' => 'required|date'],
            ],
            'columns' => ['planta_id', 'estado_anterior', 'estado_nuevo', 'fecha'],
        ],
    ],
];
