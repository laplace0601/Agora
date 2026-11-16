<?php

namespace App\Traits;

use CodeIgniter\HTTP\Files\UploadedFile;

/**
 * Trait FileUploadTrait
 *
 * Provee métodos centralizados, seguros y reusables para la carga,
 * validación y eliminación física de archivos e imágenes.
 */
trait FileUploadTrait
{
    /**
     * Valida, procesa y almacena una imagen subida de forma segura.
     * Retorna:
     * - string: ruta relativa guardada (ej. 'uploads/branding/logo.png')
     * - array: ['error' => string] si falló la validación
     * - null: si no se subió ningún archivo nuevo
     *
     * @param UploadedFile|null $archivo               Archivo obtenido vía $this->request->getFile()
     * @param string            $carpetaDestino        Subcarpeta en public/uploads/ (ej: 'branding', 'bitacora')
     * @param string|null       $archivoAnterior       Ruta previa para eliminar el archivo viejo reemplazado
     * @param int               $maxMb                 Tamaño máximo en megabytes (default 2)
     * @param array             $extensionesPermitidas Extensiones válidas
     * @param string            $prefijo               Prefijo opcional en el nombre (ej: 'login_bg_')
     */
    protected function subirImagenSegura(
        ?UploadedFile $archivo,
        string $carpetaDestino,
        ?string $archivoAnterior = null,
        int $maxMb = 2,
        array $extensionesPermitidas = ['jpg', 'jpeg', 'png', 'webp', 'svg', 'jfif'],
        string $prefijo = ''
    ): string|array|null {
        if (! $archivo || $archivo->getError() === UPLOAD_ERR_NO_FILE) {
            return null;
        }

        if (! $archivo->isValid() || $archivo->hasMoved()) {
            return ['error' => 'Error al procesar el archivo: ' . $archivo->getErrorString()];
        }

        $ext  = strtolower($archivo->getClientExtension());
        $mime = strtolower($archivo->getMimeType());

        $mimesPermitidos = [
            'image/jpeg', 'image/pjpeg', 'image/png', 'image/webp',
            'image/svg+xml', 'image/svg', 'image/jpg', 'image/jfif',
            'image/x-png', 'image/x-citrix-jpeg', 'image/pipeg'
        ];

        if (! in_array($ext, $extensionesPermitidas, true) || ! in_array($mime, $mimesPermitidos, true)) {
            $formatosTexto = implode(', ', $extensionesPermitidas);
            return ['error' => "Formato de imagen no permitido ({$ext}). Formatos aceptados: {$formatosTexto}."];
        }

        if ($archivo->getSizeByUnit('mb') > $maxMb) {
            return ['error' => "El archivo supera el tamaño máximo permitido de {$maxMb} MB."];
        }

        // Eliminar archivo previo si se especificó y existe
        $this->eliminarArchivoSeguro($archivoAnterior);

        // Asegurar que el directorio de destino exista
        $carpetaLimpia = trim(str_replace(['..', '\\'], ['', '/'], $carpetaDestino), '/');
        $rutaCompleta  = FCPATH . 'uploads/' . $carpetaLimpia . '/';

        if (! is_dir($rutaCompleta)) {
            mkdir($rutaCompleta, 0775, true);
        }

        $nuevoNombre = $prefijo . $archivo->getRandomName();
        $archivo->move($rutaCompleta, $nuevoNombre);

        return 'uploads/' . $carpetaLimpia . '/' . $nuevoNombre;
    }

    /**
     * Elimina físicamente un archivo en public/ si pertenece al directorio uploads/
     * Protegido contra Path Traversal.
     */
    protected function eliminarArchivoSeguro(?string $rutaRelativa): bool
    {
        if (empty($rutaRelativa)) {
            return false;
        }

        // Restringir estrictamente al directorio uploads/
        if (str_contains($rutaRelativa, 'uploads/')) {
            $archivoFisico = FCPATH . ltrim($rutaRelativa, '/\\');
            if (is_file($archivoFisico)) {
                return @unlink($archivoFisico);
            }
        }

        return false;
    }
}
