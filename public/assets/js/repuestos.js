// repuestos.js - Validaciones y scripts para formularios de repuestos

['nombre', 'categoria'].forEach(function(id) {
    var el = document.getElementById(id);
    if (el) {
        el.addEventListener('input', function (e) {
            this.value = soloTexto(this.value);
        });
    }
});

var precio = document.getElementById('precio');
if(precio) {
    precio.addEventListener('input', function (e) {
        this.value = this.value.replace(/[^0-9.]/g, '');
    });
}

var stock = document.getElementById('stock');
if(stock) {
    stock.addEventListener('input', function (e) {
        this.value = this.value.replace(/[^0-9]/g, '');
    });
} 