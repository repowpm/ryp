// ordenes.js - Validaciones y scripts para formularios de órdenes de trabajo

var diagnostico = document.getElementById('diagnostico');
if(diagnostico) {
    diagnostico.addEventListener('input', function (e) {
        this.value = this.value.replace(/[^A-Za-zÁÉÍÓÚáéíóúÑñ0-9,. ]/g, '');
    });
}

var total = document.getElementById('total');
if(total) {
    total.addEventListener('input', function (e) {
        this.value = this.value.replace(/[^0-9.]/g, '');
    });
} 