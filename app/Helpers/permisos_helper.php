<?php

if (!function_exists('puede_crear_repuestos')) {
    function puede_crear_repuestos() {
        $userRole = session()->get('id_rol');
        return $userRole == 1; // Solo administradores
    }
}

if (!function_exists('puede_editar_repuestos')) {
    function puede_editar_repuestos() {
        $userRole = session()->get('id_rol');
        return $userRole == 1; // Solo administradores
    }
}

if (!function_exists('puede_eliminar_repuestos')) {
    function puede_eliminar_repuestos() {
        $userRole = session()->get('id_rol');
        return $userRole == 1; // Solo administradores
    }
}

if (!function_exists('puede_ver_repuestos')) {
    function puede_ver_repuestos() {
        $userRole = session()->get('id_rol');
        return in_array($userRole, [1, 2]); // Administradores y mecánicos
    }
}

if (!function_exists('es_administrador')) {
    function es_administrador() {
        return session()->get('id_rol') == 1;
    }
}

if (!function_exists('es_mecanico')) {
    function es_mecanico() {
        return session()->get('id_rol') == 2;
    }
} 