<?php

/**
 * Helper: marca_helper
 *
 * Carga la configuración de marca blanca desde la tabla `configuracion_marca`.
 * Se cachea en memoria estática para evitar consultas repetidas en la misma request.
 * Diseñado para ser autocargado globalmente y usado en todas las vistas.
 */

if (! function_exists('obtenerMarca')) {
    /**
     * Retorna los datos de la marca blanca (id=1).
     * Si no existe el registro, retorna valores por defecto de Ágora.
     *
     * @return array{nombre_empresa: string, logo_url: ?string, color_primario: string, correo_contacto: string, nivel_licencia: string, limite_apartamentos: int}
     */
    function obtenerMarca(): array
    {
        static $marca = null;

        if ($marca === null) {
            try {
                $db = \Config\Database::connect();
                $marca = $db->table('configuracion_marca')
                            ->where('id', 1)
                            ->get()
                            ->getRowArray();
            } catch (\Throwable $e) {
                $marca = null;
                log_message('error', '[marca_helper] Error al leer configuracion_marca: ' . $e->getMessage());
            }

            // Valores por defecto si no existe el registro
            if (! $marca) {
                $marca = [
                    'id'                       => 1,
                    'nombre_empresa'           => 'Ágora',
                    'logo_url'                 => null,
                    'login_bg_url'             => null,
                    'color_primario'           => '#007bff',
                    'correo_contacto'          => 'contacto@agora.com',
                    'telefono_contacto'        => 'XXXX-XXXX',
                    'nivel_licencia'           => 'Bronce',
                    'limite_apartamentos'      => 40,
                    'codigo_activacion'        => null,
                    'fecha_actualizacion_plan' => null,
                    'cedula_pago'              => null,
                    'telefono_pago'            => null,
                    'banco_pago'               => null,
                ];
            }
        }

        return $marca;
    }
}

if (! function_exists('nombreEmpresa')) {
    /**
     * Atajo: retorna solo el nombre de la empresa configurado.
     */
    function nombreEmpresa(): string
    {
        return obtenerMarca()['nombre_empresa'] ?? 'Ágora';
    }
}

if (! function_exists('logoEmpresa')) {
    /**
     * Atajo: retorna la URL completa del logo, o null si no hay logo configurado.
     */
    function logoEmpresa(): ?string
    {
        $logo = obtenerMarca()['logo_url'] ?? null;
        return $logo ? base_url($logo) : null;
    }
}

if (! function_exists('colorPrimario')) {
    /**
     * Atajo: retorna el color primario configurado en la marca.
     */
    function colorPrimario(): string
    {
        $color = trim(obtenerMarca()['color_primario'] ?? '');
        return !empty($color) ? $color : '#007bff';
    }
}

if (! function_exists('loginBgEmpresa')) {
    /**
     * Atajo: retorna la URL completa del fondo de login, o null si no hay configurado.
     */
    function loginBgEmpresa(): ?string
    {
        $bg = obtenerMarca()['login_bg_url'] ?? null;
        return $bg ? base_url($bg) : null;
    }
}

if (! function_exists('telefonoContacto')) {
    /**
     * Atajo: retorna el teléfono de contacto/soporte configurado en la marca.
     */
    function telefonoContacto(): string
    {
        $tel = trim(obtenerMarca()['telefono_contacto'] ?? '');
        return !empty($tel) ? $tel : 'XXXX-XXXX';
    }
}

if (! function_exists('cedulaPago')) {
    /**
     * Atajo: retorna la cédula del titular de pago configurada en la marca.
     */
    function cedulaPago(): string
    {
        return trim(obtenerMarca()['cedula_pago'] ?? '');
    }
}

if (! function_exists('telefonoPago')) {
    /**
     * Atajo: retorna el teléfono de pago móvil configurado en la marca.
     */
    function telefonoPago(): string
    {
        return trim(obtenerMarca()['telefono_pago'] ?? '');
    }
}

if (! function_exists('bancoPago')) {
    /**
     * Atajo: retorna el banco destino de pago configurado en la marca.
     */
    function bancoPago(): string
    {
        return trim(obtenerMarca()['banco_pago'] ?? '');
    }
}

