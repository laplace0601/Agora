document.addEventListener("DOMContentLoaded", function () {
    const selectArea = document.getElementById("areaComun");
    const inputFecha = document.getElementById("fechaReserva");
    const switchTodoElDia = document.getElementById("switchTodoElDia");
    const bloqueHoras = document.getElementById("bloqueHoras");
    const alertaOcupado = document.getElementById("alertaOcupado");
    const btnEnviar = document.getElementById("btnEnviarReserva");
    const tarjetas = document.querySelectorAll(".card-area-seleccionable");

    // Simulación de fechas ocupadas ("ID_AREA-AAAA-MM-DD")
    const fechasOcupadas = ["1-2026-06-27", "2-2026-06-28"];

    tarjetas.forEach(tarjeta => {
        tarjeta.addEventListener("click", function () {
            const idArea = this.getAttribute("data-area-id");
            if (selectArea) { selectArea.value = idArea; verificarDisponibilidad(); }
        });
    });

    if (switchTodoElDia) {
        switchTodoElDia.addEventListener("change", function () {
            if (this.checked) { bloqueHoras.classList.add("d-none"); } 
            else { bloqueHoras.classList.remove("d-none"); }
        });
    }

    if (inputFecha) inputFecha.addEventListener("change", verificarDisponibilidad);
    if (selectArea) selectArea.addEventListener("change", verificarDisponibilidad);

    function verificarDisponibilidad() {
        const area = selectArea.value; const fecha = inputFecha.value;
        if (!area || !fecha) return;

        if (fechasOcupadas.includes(`${area}-${fecha}`)) {
            alertaOcupado.classList.remove("d-none");
            inputFecha.classList.add("is-invalid");
            btnEnviar.disabled = true;
            btnEnviar.classList.replace("btn-primary", "btn-danger");
            btnEnviar.textContent = "Espacio No Disponible ❌";
        } else {
            alertaOcupado.classList.add("d-none");
            inputFecha.classList.remove("is-invalid"); inputFecha.classList.add("is-valid");
            btnEnviar.disabled = false;
            btnEnviar.classList.replace("btn-danger", "btn-primary");
            btnEnviar.textContent = "Enviar Solicitud de Reserva 📅";
        }
    }
});