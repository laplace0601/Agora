<?php
session_start();

if (!isset($_SESSION['anuncios'])) {
    $_SESSION['anuncios'] = [
        ['id' => 1, 'titulo' => 'Mantenimiento del Sistema Ágora', 'descripcion' => 'Bienvenido al panel. Este es un anuncio simulado cargado en la sesión activa.', 'estado' => 'publicado'],
    ];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['crear_anuncio'])) {
    $titulo = trim($_POST['titulo']);
    $descripcion = trim($_POST['descripcion']);

    if (!empty($titulo) && !empty($descripcion)) {
        $nuevo_id = time();
        $_SESSION['anuncios'][] = [
            'id' => $nuevo_id,
            'titulo' => $titulo,
            'descripcion' => $descripcion,
            'estado' => 'publicado'
        ];
    }
    header("Location: " . $_SERVER['PHP_SELF']);
    exit;
}

if (isset($_GET['eliminar_id'])) {
    $id_eliminar = (int)$_GET['eliminar_id'];
    foreach ($_SESSION['anuncios'] as &$anuncio) {
        if ($anuncio['id'] === $id_eliminar) {
            $anuncio['estado'] = 'borrado';
            break;
        }
    }
    header("Location: " . $_SERVER['PHP_SELF']);
    exit;
}
?>