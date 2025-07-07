// vehiculos.js - Validaciones y scripts para formularios de vehículos

var patente = document.getElementById('patente');
if(patente) {
    patente.addEventListener('input', function (e) {
        this.value = this.value.toUpperCase().replace(/[^A-Z0-9]/g, '');
    });
}

['marca', 'modelo'].forEach(function(id) {
    var el = document.getElementById(id);
    if (el) {
        el.addEventListener('input', function (e) {
            this.value = soloTexto(this.value);
        });
    }
}); 