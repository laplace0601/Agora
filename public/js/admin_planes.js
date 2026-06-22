/**
 * @file admin_planes.js
 * @description Modifica dinámicamente los campos informativos del modal de confirmación de planes.
 */

function setPlanDestino(nombrePlan) {
    // Captura de los elementos interactivos del DOM del modal
    const inputOculto = document.getElementById('input_plan_destino');
    const labelVisual = document.getElementById('label_plan_destino');
    
    if (inputOculto && labelVisual) {
        // Inyección del payload al value del input para el envío por el formulario POST
        inputOculto.value = nombrePlan;
        
        // Actualización de la interfaz del usuario en la etiqueta del modal
        labelVisual.textContent = 'Plan ' + nombrePlan;
    }
}