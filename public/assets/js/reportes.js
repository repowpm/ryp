// reportes.js - Scripts y validaciones para el módulo de reportes

// Aquí puedes agregar funciones para filtros, fechas, validaciones, etc.
// Ejemplo: validación de fechas
var fechaInicio = document.getElementById('fecha_inicio');
var fechaFin = document.getElementById('fecha_fin');
if(fechaInicio && fechaFin) {
    fechaInicio.addEventListener('change', function() {
        if (fechaFin.value && fechaFin.value < fechaInicio.value) {
            fechaFin.value = '';
        }
    });
    fechaFin.addEventListener('change', function() {
        if (fechaInicio.value && fechaFin.value < fechaInicio.value) {
            this.value = '';
        }
    });
} 