/**
 * @file admin_normas_gestion.js
 * @description Pasa los datos de una norma al formulario para poder editarla.
 */
function cargarNorma(id, titulo, contenido) {
    document.getElementById('normaId').value = id;
    document.getElementById('tituloNorma').value = titulo;
    document.getElementById('contenidoNorma').value = contenido;
    
    // Al hacer clic en editar, el cursor viaja automáticamente al input del título
    document.getElementById('tituloNorma').focus();
}

function limpiarFormulario() {
    document.getElementById('normaId').value = '';
}