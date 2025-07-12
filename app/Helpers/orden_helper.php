<?php

/**
 * Helper para funciones relacionadas con órdenes de trabajo
 */

if (!function_exists('getEstadoBadgeClass')) {
    /**
     * Obtiene la clase CSS del badge según el estado de la orden
     */
    function getEstadoBadgeClass($estado)
    {
        $clases = [
            'PENDIENTE' => 'warning',
            'EN PROCESO' => 'info',
            'COMPLETADA' => 'success',
            'CANCELADA' => 'danger',
            'FACTURADA' => 'primary'
        ];
        
        return $clases[strtoupper($estado)] ?? 'secondary';
    }
}

if (!function_exists('formatearMoneda')) {
    /**
     * Formatea un valor monetario
     */
    function formatearMoneda($valor)
    {
        return '$' . number_format($valor, 0, ',', '.');
    }
}

if (!function_exists('formatearFecha')) {
    /**
     * Formatea una fecha
     */
    function formatearFecha($fecha, $formato = 'd/m/Y H:i')
    {
        return date($formato, strtotime($fecha));
    }
}

if (!function_exists('getEstadoColor')) {
    /**
     * Obtiene el color del estado para CSS
     */
    function getEstadoColor($estado)
    {
        $colores = [
            'PENDIENTE' => '#ffc107',
            'EN PROCESO' => '#17a2b8',
            'COMPLETADA' => '#28a745',
            'CANCELADA' => '#dc3545',
            'FACTURADA' => '#007bff'
        ];
        
        return $colores[strtoupper($estado)] ?? '#6c757d';
    }
} 