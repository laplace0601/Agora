/**
 * @file residente_reportar_pago.js
 * @description Capta la subida del comprobante y da feedback visual.
 */
document.addEventListener("DOMContentLoaded", function () {
    const dropzone = document.getElementById("dropzoneContainer");
    const fileInput = document.getElementById("fileComprobante");
    const dropzoneText = document.getElementById("dropzoneText");

    if (dropzone && fileInput) {
        // Hace que al hacer clic en el cuadro punteado, se abra el selector de archivos
        dropzone.addEventListener("click", () => fileInput.click());

        // Cuando el usuario selecciona la foto/PDF
        fileInput.addEventListener("change", function () {
            if (this.files.length > 0) {
                const nombreArchivo = this.files[0].name;
                // Cambia el texto dinámicamente y pone el cuadro en verde
                dropzoneText.innerHTML = `<i class="bi bi-file-earmark-check-fill text-success me-1"></i> Archivo listo: <strong>${nombreArchivo}</strong>`;
                dropzone.style.borderColor = "#198754"; 
                dropzone.style.backgroundColor = "rgba(25, 135, 84, 0.02)";
            }
        });
    }
});