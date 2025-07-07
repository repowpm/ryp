// clientes.js - Validaciones y scripts para formularios de clientes

['nombres', 'apellido_paterno', 'apellido_materno'].forEach(function(id) {
    var el = document.getElementById(id);
    if (el) {
        el.addEventListener('input', function (e) {
            this.value = soloTexto(this.value);
        });
    }
});

var run = document.getElementById('run');
if(run) {
    run.addEventListener('input', function (e) {
        this.value = autoformatoRun(this.value);
    });
}

var tel = document.getElementById('telefono');
if(tel) {
    tel.addEventListener('input', function (e) {
        this.value = soloNumeros(this.value);
    });
} 