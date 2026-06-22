/**
 * @file super_gestion_usuarios.js
 * @description Filtro global instantáneo y mapeo dinámico para el Modal Universal de Edición.
 */
document.addEventListener("DOMContentLoaded", function () {
    // Buscador interactivo único para todas las tablas
    const searchInput = document.getElementById("searchGlobal");
    if (searchInput) {
        searchInput.addEventListener("keyup", function () {
            const filtro = searchInput.value.toLowerCase();
            const tablas = document.querySelectorAll(".tabla-busqueda");
            
            tablas.forEach(tabla => {
                const filas = tabla.getElementsByTagName("tr");
                for (let i = 1; i < filas.length; i++) {
                    const textoFila = filas[i].textContent.toLowerCase();
                    filas[i].style.display = textoFila.includes(filtro) ? "" : "none";
                }
            });
        });
    }
});

function abrirModalEdicion(rol, id, nombre, cedula, telefono, correo, apto='', condominio='', plan='', area='') {
    // Asignación de valores básicos
    document.getElementById("editId").value = id;
    document.getElementById("editTipoRol").value = rol;
    document.getElementById("editNombre").value = nombre;
    document.getElementById("editCedula").value = cedula;
    document.getElementById("editTelefono").value = telefono;
    document.getElementById("editCorreo").value = correo;
    
    // Cambiar título según el rol
    document.getElementById("modalEdicionTitulo").innerHTML = `<i class="bi bi-pencil-square me-2"></i> Modificar Perfil de ${rol.toUpperCase()}`;

    // Resetear visibilidad de campos extras
    document.getElementById("divInputApto").style.display = "none";
    document.getElementById("divInputCondominio").style.display = "none";
    document.getElementById("divInputPlan").style.display = "none";
    document.getElementById("divInputArea").style.display = "none";

    // Mostrar campos específicos según el rol seleccionado
    if (rol === 'residente') {
        document.getElementById("divInputApto").style.display = "block";
        document.getElementById("editApto").value = apto;
    } else if (rol === 'admin') {
        document.getElementById("divInputCondominio").style.display = "block";
        document.getElementById("divInputPlan").style.display = "block";
        document.getElementById("editCondominio").value = condominio;
        document.getElementById("editPlan").value = plan;
    } else if (rol === 'super') {
        document.getElementById("divInputArea").style.display = "block";
        document.getElementById("editArea").value = area;
    }

    // Desplegar modal
    const miModal = new bootstrap.Modal(document.getElementById("modalEditarUsuarioUniversal"));
    miModal.show();
}