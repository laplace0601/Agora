<?php
$mensaje = "";
$tipo_alerta = ""; 

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sincronizado 'año' del backend con el del formulario HTML
    if (!empty($_POST['mes']) && !empty($_POST['año']) && !empty($_POST['condominio']) && !empty($_POST['monto_base'])) {
        $mensaje = "¡Facturación generada con éxito para el condominio: " . htmlspecialchars($_POST['condominio']) . "!";
        $tipo_alerta = "success";
    } else {
        $mensaje = "Hubo un error de validación en los datos enviados.";
        $tipo_alerta = "danger";
    }
}
?>