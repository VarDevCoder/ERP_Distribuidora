<?php

/**
 * Configuración del sistema ANKOR
 *
 * Centraliza todos los valores configurables del flujo ANKOR
 * para evitar hardcoding en controladores y vistas.
 */

return [
    /*
    |--------------------------------------------------------------------------
    | Paginación
    |--------------------------------------------------------------------------
    */
    'pagination' => [
        'per_page' => 15,
        'per_page_large' => 30,
    ],

    /*
    |--------------------------------------------------------------------------
    | Límites de registros
    |--------------------------------------------------------------------------
    */
    'limits' => [
        'recent_solicitudes' => 10,
        'dashboard_recent' => 5,
    ],

    /*
    |--------------------------------------------------------------------------
    | Entregas
    |--------------------------------------------------------------------------
    */
    'delivery' => [
        'default_days' => 7,
        'min_days' => 1,
        'max_days' => 90,
    ],

    /*
    |--------------------------------------------------------------------------
    | Valores por defecto
    |--------------------------------------------------------------------------
    */
    'defaults' => [
        'descuento' => 0,
        'cantidad_inicial' => 1,
    ],

    /*
    |--------------------------------------------------------------------------
    | Métodos de envío disponibles
    |--------------------------------------------------------------------------
    */
    'metodos_envio' => [
        'RETIRO_LOCAL' => 'Retiro en Local',
        'DELIVERY_PROPIO' => 'Delivery Propio',
        'TRANSPORTE_TERCERO' => 'Transporte Tercero',
        'ENCOMIENDA' => 'Encomienda',
    ],

    /*
    |--------------------------------------------------------------------------
    | Unidades de medida
    |--------------------------------------------------------------------------
    */
    'unidades_medida' => [
        'unid' => 'Unidad',
        'kg' => 'Kilogramo',
        'm' => 'Metro',
        'm2' => 'Metro Cuadrado',
        'm3' => 'Metro Cúbico',
        'lt' => 'Litro',
        'set' => 'Set/Juego',
        'bolsa' => 'Bolsa',
        'barra' => 'Barra',
        'rollo' => 'Rollo',
        'galon' => 'Galón',
        'balde' => 'Balde',
        'caja' => 'Caja',
    ],
];
