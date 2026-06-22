/**
 * @file super_soporte_recibido.js
 * @description Filtro reactivo por planes y control dinámico del modal de resolución de tickets.
 */
document.addEventListener("DOMContentLoaded", function () {
    const filtroPrioridad = document.getElementById("filtroPrioridad");
    const filasTickets = document.querySelectorAll(".fila-ticket");

    // 1. FILTRO DINÁMICO POR PLANES (Oro, Plata, Bronce)
    if (filtroPrioridad) {
        filtroPrioridad.addEventListener("change", function () {
            const planSeleccionado = this.value; // Puede ser 'todos', 'oro', 'plata', etc.

            filasTickets.forEach(fila => {
                const planFila = fila.getAttribute("data-plan");

                if (planSeleccionado === "todos" || planFila === planSeleccionado) {
                    fila.style.display = ""; // Muestra la fila
                } else {
                    fila.style.display = "none"; // Oculta la fila
                }
            });
        });
    }
});

/**
 * 2. CONTROL DEL MODAL: Rellena los datos del ticket seleccionado dinámicamente
 * @param {number} id - ID del Ticket
 * @param {string} cliente - Nombre del condominio
 * @param {string} asunto - Requerimiento del administrador
 */
function abrirModalAtencion(id, cliente, asunto) {
    // Capturamos los elementos dentro del modal
    const inputId = document.getElementById("ticketIdInput");
    const textCliente = document.getElementById("modalClienteName");
    const textAsunto = document.getElementById("modalAsuntoText");
    const textareaRespuesta = document.getElementById("respuestaSoporte");

    if (inputId && textCliente && textAsunto) {
        // Inyectamos los valores dinámicos que vinieron del botón de la tabla
        inputId.value = id;
        textCliente.textContent = cliente;
        textAsunto.textContent = asunto;

        // Limpiamos el campo de texto por si había algo escrito antes
        if (textareaRespuesta) textareaRespuesta.value = "";

        // Levantamos el modal usando la librería nativa de Bootstrap 5
        const miModal = new bootstrap.Modal(document.getElementById('modalResponderTicket'));
        miModal.show();
    }
}