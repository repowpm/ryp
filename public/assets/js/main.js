// main.js - Funciones globales reutilizables

// Solo texto (letras y espacios)
function soloTexto(valor) {
    return valor.replace(/[^A-Za-zÁÉÍÓÚáéíóúÑñ ]/g, '');
}

// Solo números y + (para teléfonos)
function soloNumeros(valor) {
    return valor.replace(/[^0-9+]/g, '');
}

// Solo alfanumérico y guion bajo (para username)
function soloAlfanumerico(valor) {
    return valor.replace(/[^A-Za-z0-9_]/g, '');
}

// Autoformato y validación básica de RUN chileno
function autoformatoRun(valor) {
    let val = valor.replace(/[^0-9kK]/g, '').toUpperCase();
    if (val.length > 1) {
        return val.slice(0, -1) + '-' + val.slice(-1);
    }
    return val;
}

// Validación básica de email
function esEmail(valor) {
    return /^[^@\s]+@[^@\s]+\.[^@\s]+$/.test(valor);
}

// Validación de longitud mínima y máxima
function longitudValida(valor, min, max) {
    return valor.length >= min && valor.length <= max;
}

// Puedes agregar más funciones globales aquí para otros módulos 