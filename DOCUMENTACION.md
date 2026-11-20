# 📋 Documentación Técnica — Ágora CRM

> Sistema de gestión integral para condominios residenciales.  
> Versión: 1.0 · Stack: CodeIgniter 4.7.2 · PHP 8.2 · MySQL · Despliegue: InfinityFree

---

## Tabla de Contenidos

1. [Descripción General](#1-descripción-general)
2. [Stack Tecnológico](#2-stack-tecnológico)
3. [Estructura de Directorios](#3-estructura-de-directorios)
4. [Arquitectura MVC](#4-arquitectura-mvc)
5. [Sistema de Autenticación](#5-sistema-de-autenticación)
6. [Roles y Permisos (RBAC)](#6-roles-y-permisos-rbac)
7. [Módulos del Sistema](#7-módulos-del-sistema)
8. [Base de Datos](#8-base-de-datos)
9. [Cálculo de Alícuotas](#9-cálculo-de-alícuotas)
10. [Sistema de Planes y Licencias](#10-sistema-de-planes-y-licencias)
11. [Marca Blanca](#11-marca-blanca)
12. [Rutas Principales](#12-rutas-principales)
13. [Variables de Entorno (.env)](#13-variables-de-entorno-env)
14. [Despliegue en InfinityFree](#14-despliegue-en-infinityfree)
15. [Desarrollo Local con XAMPP](#15-desarrollo-local-con-xampp)
16. [CSS y Assets](#16-css-y-assets)

---

## 1. Descripción General

**Ágora CRM** es una plataforma web multi-tenant para la administración de condominios residenciales. Permite gestionar apartamentos, residentes, finanzas (recibos y pagos de alícuotas), áreas comunes, reservas, comunicados y tickets de soporte; todo bajo un modelo de licenciamiento por plan.

### Características clave

| Característica | Detalle |
|---|---|
| Multi-condominio | Un root puede gestionar N condominios bajo distintos planes |
| Multi-rol | root → admin → residente con permisos diferenciados |
| Marca blanca | Cada condominio puede personalizar nombre, logo y colores |
| Facturación automática | Generación de recibos por alícuota proporcional a m² |
| Solvencia | Certificado digital de pagos al día |
| Reservas | Gestión de áreas comunes con calendario de disponibilidad |

---

## 2. Stack Tecnológico

| Capa | Tecnología | Versión |
|---|---|---|
| Framework PHP | CodeIgniter | 4.7.2 |
| Lenguaje servidor | PHP | 8.2.12 |
| Base de datos | MySQL (MySQLi driver) | 5.7 / 8.x |
| Servidor web local | Apache (XAMPP) | 2.4.58 |
| Servidor web producción | LiteSpeed (InfinityFree) | — |
| Sesiones | FileHandler (archivos en `writeable/session/`) | — |
| Hashing de contraseñas | Bcrypt (`PASSWORD_BCRYPT`) | — |
| Frontend | Bootstrap 5 + CSS personalizado + JS vanilla | — |
| PDF | Vista HTML renderizada como PDF | — |
| Despliegue | FTP manual con FileZilla a InfinityFree | — |
| URL producción | `https://agora-condominio.infinityfreeapp.com/` | — |

---

## 3. Estructura de Directorios

```
Agora/
├── .env                        # Variables de entorno (NO versionar en producción)
├── .htaccess                   # Redirige todo a /public (raíz)
├── app/
│   ├── Config/
│   │   ├── Routes.php          # Definición de todas las rutas
│   │   ├── Filters.php         # Registro del filtro AuthFilter
│   │   ├── Session.php         # Config. sesiones (expiration=7200)
│   │   └── Database.php        # Config. BD (MySQLi, host, db: agora)
│   ├── Controllers/
│   │   ├── AuthController.php              # Login / Logout
│   │   ├── SuperController.php             # Panel root (condominios, aptos, planes)
│   │   ├── AdminController.php             # Panel admin (dashboard, vistas)
│   │   ├── UsuarioController.php           # Creación de usuarios
│   │   ├── GestionUsuariosRootController.php  # CRUD usuarios (API JSON)
│   │   ├── FinanzasController.php          # Recibos, pagos, solvencia
│   │   ├── LicenciaController.php          # Activación de plan / licencia
│   │   ├── HabitantesController.php        # Censo de habitantes
│   │   ├── AreasReservasController.php     # Áreas comunes y reservas
│   │   ├── ComunidadController.php         # Comunicados y tickets de soporte
│   │   ├── ResidenteController.php         # Panel residente
│   │   └── BaseController.php              # Clase base con helpers comunes
│   ├── Filters/
│   │   └── AuthFilter.php      # Filtro RBAC: verifica sesión y rol
│   ├── Models/
│   │   ├── UsuarioModel.php
│   │   ├── ResidenteModel.php
│   │   ├── CondominioModel.php
│   │   ├── ApartamentoModel.php    # + calcularMontoTotal()
│   │   ├── ReciboModel.php
│   │   ├── PagoModel.php
│   │   ├── SolvenciaModel.php
│   │   ├── HabitanteModel.php
│   │   ├── AreaComunModel.php
│   │   ├── ReservaAreaModel.php
│   │   ├── TicketModel.php
│   │   ├── ComunicadoModel.php
│   │   └── MarcaModel.php
│   ├── Services/
│   │   └── CondominioService.php   # recalcularAlicuotas()
│   ├── Helpers/
│   │   └── marca_helper.php        # Helper para datos de marca blanca
│   ├── Traits/
│   │   └── FileUploadTrait.php     # Upload de logos / imágenes
│   └── Views/
│       ├── auth/           # login.php
│       ├── root/           # Panel super-admin
│       ├── admin/          # Panel administrador
│       ├── residente/      # Panel residente
│       ├── template/       # Layouts y partials compartidos
│       ├── pdf/            # Plantillas para recibos en PDF
│       ├── landing/        # Página de inicio pública
│       └── errors/         # Páginas de error (404, 500)
├── public/
│   ├── index.php           # Entry point principal (con normalización de case)
│   ├── .htaccess           # RewriteRule para CI4
│   ├── css/                # Estilos por módulo
│   ├── js/                 # Scripts por módulo
│   ├── images/             # Imágenes estáticas
│   └── uploads/branding/   # Logos subidos de marca blanca
├── system/                 # Core de CodeIgniter (no tocar)
├── writable/
│   ├── session/            # Archivos de sesión de PHP
│   ├── logs/               # Logs de CI4
│   └── uploads/            # Archivos subidos temporales
└── spark                   # CLI de CodeIgniter
```

---

## 4. Arquitectura MVC

Ágora sigue el patrón **Model-View-Controller** de CodeIgniter 4, con una capa de **Services** para lógica de negocio compleja.

```
HTTP Request
     │
     ▼
 Routes.php ──► AuthFilter (verifica sesión + rol)
     │
     ▼
 Controller ──► Model(s) ──► MySQL (tabla agora)
     │               │
     │          CondominioService (lógica de alícuotas)
     │
     ▼
  View (.php) ──► Response HTML
```

### Responsabilidades por capa

| Capa | Responsabilidad |
|---|---|
| **Routes** | Mapear URL → Controlador::método, aplicar filtros de grupo |
| **Filter (AuthFilter)** | Verificar `isLoggedIn` en sesión + validar rol RBAC |
| **Controller** | Orquestar: recibir request, llamar modelos/servicios, retornar vista o JSON |
| **Model** | Acceso a BD con Query Builder de CI4, validaciones de campos |
| **Service** | Lógica de negocio compleja (recálculo de alícuotas en batch) |
| **View** | HTML + Bootstrap 5; recibe datos del controlador vía `$data[]` |
| **Helper** | Funciones utilitarias globales (ej: `get_marca_data()`) |

---

## 5. Sistema de Autenticación

### Flujo de Login

```
1. GET  /login          → AuthController::login()       → Vista auth/login.php
2. POST /login          → AuthController::processLogin()
   ├─ Validar campos vacíos
   ├─ UsuarioModel::verificarCredenciales($correo, $clave)
   │   ├─ Busca usuario por correo con estado='activo'
   │   └─ password_verify($clave, $hash_bcrypt)
   ├─ Si OK → session()->regenerate() → set(['isLoggedIn'=>true, 'rol'=>..., ...])
   └─ Redirect según rol: /super/panel | /admin/residentes | /residente/dashboard
```

### Datos guardados en sesión

```php
session()->set([
    'usuario_id'         => $usuario['id'],
    'nombre_usuario'     => $usuario['nombre_usuario'],
    'correo'             => $usuario['correo'],
    'rol'                => 'root' | 'admin' | 'residente',
    'isLoggedIn'         => true,
    'session_start_time' => time(),
]);
```

### Protección de rutas — AuthFilter

Archivo: `app/Filters/AuthFilter.php`

```php
// 1. ¿Está logueado?
if (!session()->get('isLoggedIn')) → redirect a /login

// 2. ¿Tiene el rol correcto?
if (!in_array($rol, $arguments))  → redirect a /login con error

// 3. ¿Tiempo de inactividad > 7200 s?
if (time() - session_start_time > 7200) → session()->destroy() → redirect
```

### Seguridad

| Mecanismo | Implementación |
|---|---|
| Hashing | `password_hash($clave, PASSWORD_BCRYPT)` al crear usuario |
| Verificación | `password_verify($input, $hash)` en `UsuarioModel` |
| CSRF | Modo `session` (CI4), token regenerado cada petición |
| Session ID | `session()->regenerate()` al hacer login |
| Inactividad | Logout automático a los 7200 s (2 horas) |
| Enumeración | Mensaje genérico "Credenciales incorrectas" (no revela si el correo existe) |

---

## 6. Roles y Permisos (RBAC)

| Rol | Acceso | Descripción |
|---|---|---|
| `root` | `/super/*` | Super-administrador del sistema. Gestiona condominios, apartamentos, usuarios y planes de licencia |
| `admin` | `/admin/*` | Administrador de un condominio específico. Gestiona residentes, finanzas, áreas comunes, comunicados |
| `residente` | `/residente/*` | Residente de un apartamento. Ve sus recibos, reporta pagos, abre tickets, hace reservas |

### Capacidades por rol

| Acción | root | admin | residente |
|---|---|---|---|
| Crear/eliminar condominios | ✅ | ❌ | ❌ |
| Crear/eliminar apartamentos | ✅ | ❌ | ❌ |
| Gestionar usuarios | ✅ | ❌ | ❌ |
| Cambiar plan de licencia | ✅ | ❌ | ❌ |
| Configurar marca blanca | ✅ | ❌ | ❌ |
| Ver/gestionar residentes | ❌ | ✅ | ❌ |
| Emitir recibos / facturar | ❌ | ✅ | ❌ |
| Validar pagos | ❌ | ✅ | ❌ |
| Gestionar áreas comunes | ❌ | ✅ | ❌ |
| Publicar comunicados | ❌ | ✅ | ❌ |
| Responder tickets | ❌ | ✅ | ❌ |
| Ver sus recibos | ❌ | ❌ | ✅ |
| Reportar pago | ❌ | ❌ | ✅ |
| Abrir ticket de soporte | ❌ | ❌ | ✅ |
| Reservar área común | ❌ | ✅ | ✅ |

---

## 7. Módulos del Sistema

### 7.1 Autenticación — `AuthController`

Gestiona el ciclo de vida de la sesión: login, logout y redirección según rol. No usa librerías externas; es 100% session-based con PHP nativo.

---

### 7.2 Gestión de Condominios y Apartamentos — `SuperController`

**Acceso:** Solo `root`

Permite al super-administrador:
- Registrar y eliminar condominios (con `total_metros_cuadrados` para alícuotas)
- Registrar y eliminar apartamentos individuales o en lote
- Ver el panel central de operaciones

Tras cada alta/baja de apartamento, invoca `CondominioService::recalcularAlicuotas()` para mantener los porcentajes actualizados.

---

### 7.3 Gestión de Usuarios — `UsuarioController` + `GestionUsuariosRootController`

**Acceso:** Solo `root`

- **`UsuarioController`**: Formulario de creación de usuarios (admin, residente, root)
- **`GestionUsuariosRootController`**: API JSON para listar, editar y eliminar usuarios con cascada:

```
DELETE usuario
  └─ pagos_recibidos
  └─ recibos_mensuales
  └─ recibos_solvencia
  └─ reservas_areas
  └─ tickets
  └─ residentes
  └─ usuarios  ← eliminación final
```

---

### 7.4 Finanzas — `FinanzasController`

**Acceso:** `admin` (facturar, validar) · `residente` (ver, reportar)

#### Emisión de recibos (`emitirRecibos`)
1. Obtiene todos los apartamentos del condominio
2. Valida que la suma de alícuotas esté entre 99.9% y 100.1%
3. Calcula el monto de cada apartamento: `monto_base × alicuota`
4. Genera un registro en `recibos_mensuales` por cada apartamento
5. Evita duplicados (un recibo por mes/año por apartamento)

#### Validación de pagos (`validarPago`)
- El admin revisa el comprobante subido por el residente
- Marca el pago como `aprobado` o `rechazado`
- Actualiza el estado del recibo a `pagado`

#### Solvencia (`procesarSolvencia`)
- Verifica que todos los recibos del residente estén pagados
- Genera un registro en `recibos_solvencia`
- Retorna un PDF descargable con el certificado

---

### 7.5 Áreas Comunes y Reservas — `AreasReservasController`

**Acceso:** `admin` (gestión de áreas) · `admin` + `residente` (reservas)

- CRUD de áreas comunes (salón, piscina, cancha, etc.)
- Cambio de estado: disponible / mantenimiento
- Creación de reservas con validación de disponibilidad (sin solapamientos)
- Cancelación de reservas

---

### 7.6 Habitantes — `HabitantesController`

**Acceso:** `admin` (aprueba/rechaza) · `residente` (solicita)

Implementa un flujo de aprobación:
1. Residente solicita añadir un habitante a su apartamento
2. Admin recibe solicitud pendiente
3. Admin aprueba o rechaza
4. El habitante queda registrado en la tabla `habitantes`

---

### 7.7 Comunidad / Cartelera — `ComunidadController`

**Acceso:** `admin` (publicar) · `residente` (ver)

- Publicación de comunicados/anuncios en la cartelera digital
- Apertura y respuesta de tickets de soporte

---

### 7.8 Licencias — `LicenciaController`

**Acceso:** Solo `root`

Controla la activación de planes mediante clave secreta. Valida que la clave coincida **exactamente** con el plan de destino seleccionado; una clave de Oro no activa un plan Bronce y viceversa.

---

### 7.9 Marca Blanca — `SuperController::guardarMarcaBlanca()` + `MarcaModel`

**Acceso:** Solo `root`

Permite personalizar por condominio:
- Nombre del condominio
- Logo (imagen subida a `public/uploads/branding/`)
- Colores primarios y secundarios

Los datos se sirven a las vistas mediante `marca_helper.php`.

---

### 7.10 Panel Residente — `ResidenteController`

Dashboard del residente que muestra:
- Sus recibos mensuales con estado (pendiente / pagado)
- Formulario para reportar un pago (con comprobante)
- Historial de tickets de soporte
- Acceso a reservas de áreas comunes
- Sus habitantes registrados

---

## 8. Base de Datos

**Driver:** MySQLi  
**Base de datos:** `agora`  
**Charset:** `utf8mb4` / `utf8mb4_general_ci`

### Tablas principales

| Tabla | Descripción |
|---|---|
| `usuarios` | Credenciales y rol de acceso (root, admin, residente) |
| `residentes` | Perfil personal del residente vinculado a un usuario |
| `condominios` | Datos del condominio, plan de licencia, total_metros_cuadrados |
| `apartamentos` | Unidades del condominio con m², alícuota calculada y residente asignado |
| `recibos_mensuales` | Recibos generados por mes con monto basado en alícuota |
| `pagos_recibidos` | Comprobantes de pago subidos por residentes |
| `recibos_solvencia` | Certificados de solvencia generados |
| `habitantes` | Personas que viven en un apartamento (distinto de residentes) |
| `areas_comunes` | Áreas disponibles para reserva (nombre, capacidad, estado) |
| `reservas_areas` | Reservas de áreas comunes con rango de fecha/hora |
| `tickets` | Tickets de soporte abiertos por residentes |
| `configuracion_marca` | Datos de marca blanca por condominio |
| `comunicados` | Anuncios publicados en la cartelera |

### Relaciones clave

```
usuarios (1) ──────────────── (1) residentes
residentes (1) ─────────────── (N) apartamentos
condominios (1) ────────────── (N) apartamentos
apartamentos (1) ───────────── (N) recibos_mensuales
recibos_mensuales (1) ──────── (N) pagos_recibidos
apartamentos (1) ───────────── (N) habitantes
condominios (1) ────────────── (1) configuracion_marca
areas_comunes (1) ──────────── (N) reservas_areas
usuarios (1) ───────────────── (N) tickets
```

---

## 9. Cálculo de Alícuotas

La alícuota representa la **proporción de los gastos comunes** que le corresponde pagar a cada apartamento, basada en su superficie en m².

### Fórmula de porcentaje

```php
// app/Services/CondominioService.php :: recalcularAlicuotas()

$nuevaAlicuota = round(($aptoMetros / $totalMetros) * 100, 4);
```

| Variable | Descripción |
|---|---|
| `$aptoMetros` | `apartamentos.metros_cuadrados` del apartamento |
| `$totalMetros` | `condominios.total_metros_cuadrados` del condominio |
| Resultado | Porcentaje con 4 decimales (ej: `12.5000`) |

**Cuándo se recalcula:** Cada vez que se registra o elimina un apartamento.  
**Mecanismo:** `updateBatch()` sobre la tabla `apartamentos` en una sola consulta.

---

### Fórmula de monto en bolívares/dólares

```php
// app/Models/ApartamentoModel.php :: calcularMontoTotal()

$monto = round($montoBase * (float)$apartamento['alicuota'], 2);
```

| Variable | Descripción |
|---|---|
| `$montoBase` | Monto total de gastos comunes del mes (definido por el admin) |
| `$apartamento['alicuota']` | Porcentaje almacenado en DB |
| Resultado | Monto a cobrar por ese apartamento ese mes |

---

### Validación de integridad (100%)

```php
// app/Controllers/FinanzasController.php :: emitirRecibos()

$sumaAlicuotas = array_sum(array_column($apartamentos, 'alicuota'));

if ($sumaAlicuotas < 99.9 || $sumaAlicuotas > 100.1) {
    // Error: las alícuotas no suman 100%. No se puede facturar.
}
```

Margen de tolerancia: **[99.9, 100.1]** para absorber errores de redondeo de flotantes.

---

### Ejemplo completo

| Apartamento | m² | Alícuota | Monto (base: $1,000) |
|---|---|---|---|
| Apto 1A | 80 m² | 40.0000% | $400.00 |
| Apto 1B | 60 m² | 30.0000% | $300.00 |
| Apto 2A | 60 m² | 30.0000% | $300.00 |
| **Total** | **200 m²** | **100.0000%** | **$1,000.00** |

---

## 10. Sistema de Planes y Licencias

### Planes disponibles

| Plan | Límite de Aptos | Color | Clave de activación |
|---|---|---|---|
| **Bronce** | 40 apartamentos | `#CD7F32` | `ClaveSecretaBronce2026` |
| **Plata** | 120 apartamentos | `#475569` | `ClaveSecretaPlata2026` |
| **Oro** | 9,999 apartamentos | `#B45309` | `ClaveSecretaOro2026` |

### Flujo de activación

```
root → Panel Planes → Selecciona condominio + plan destino
     → Modal de confirmación → Ingresa clave de activación
     → POST /crm/licencia/activar (con plan_destino en campo hidden)

LicenciaController::activarLicencia()
  ├─ Verifica que la clave sea la correcta para ese plan_destino
  ├─ Si la clave es de otro plan → Error "Clave incorrecta para este plan"
  └─ Si OK → actualiza condominios.plan = 'bronce'|'plata'|'oro'
```

### Lógica de validación (LicenciaController)

```php
$claves = [
    'bronce' => 'ClaveSecretaBronce2026',
    'plata'  => 'ClaveSecretaPlata2026',
    'oro'    => 'ClaveSecretaOro2026',
];

if ($claves[$planDestino] !== $claveIngresada) {
    // Rechazar: clave incorrecta para el plan solicitado
}
```

---

## 11. Marca Blanca

Cada condominio puede tener su propia identidad visual. Los datos se guardan en la tabla `configuracion_marca`.

### Campos personalizables

| Campo | Descripción |
|---|---|
| `nombre_condominio` | Nombre que aparece en el header y recibos |
| `logo_path` | Ruta relativa del logo subido |
| `color_primario` | Hex del color principal (navbar, botones) |
| `color_secundario` | Hex del color de acento |

### Cómo se aplica

1. El helper `marca_helper.php` expone `get_marca_data($condominioId)`
2. Los layouts de vistas llaman al helper para obtener los datos
3. Los colores se aplican con variables CSS inline en el `<head>`
4. El logo se sirve desde `public/uploads/branding/`

---

## 12. Rutas Principales

### Públicas (sin autenticación)

| Método | URL | Acción |
|---|---|---|
| GET | `/` | Vista de login |
| GET | `/login` | Vista de login |
| POST | `/login` | Procesar credenciales |
| GET | `/auth/logout` | Cerrar sesión |

### Panel Root `/super/*` (filtro: `auth:root`)

| Método | URL | Acción |
|---|---|---|
| GET | `/super/panel` | Dashboard root |
| GET/POST | `/super/apartamentos` | Gestión de condominios y apartamentos |
| GET/POST | `/super/crear-usuario` | Crear usuario |
| GET | `/super/gestion_usuarios` | Listar y gestionar usuarios |
| GET | `/super/planes` | Panel de planes de licencia |
| POST | `/super/planes/cambiar` | Cambiar plan de un condominio |
| GET/POST | `/super/marca-blanca` | Configuración de marca blanca |

### Panel Admin `/admin/*` (filtro: `auth:admin`)

| Método | URL | Acción |
|---|---|---|
| GET | `/admin/residentes` | Lista de residentes |
| GET | `/admin/finanzas` | Panel de finanzas |
| GET | `/admin/finanzas/cobro` | Emisión de recibos |
| GET | `/admin/finanzas/pagos` | Validación de pagos |
| GET | `/admin/finanzas/morosidad` | Estado de cuentas |
| GET | `/admin/cartelera` | Comunicados |
| GET | `/admin/soporte` | Tickets de soporte |
| GET | `/admin/areas-comunes` | Gestión de áreas comunes |
| GET | `/admin/reservas` | Listado de reservas |

### Panel Residente `/residente/*` (filtro: `auth:residente`)

| Método | URL | Acción |
|---|---|---|
| GET | `/residente/dashboard` | Dashboard residente |
| GET | `/residente/finanzas` | Ver recibos |
| GET/POST | `/residente/pago` | Reportar pago |
| GET/POST | `/residente/soporte` | Tickets de soporte |
| GET | `/residente/reservas` | Reservar área común |
| GET | `/residente/habitantes` | Gestión de habitantes |

### API CRM `/crm/*` (protegidas por sesión)

| Método | URL | Acción |
|---|---|---|
| POST | `/crm/licencia/activar` | Activar plan de licencia |
| POST | `/crm/finanzas/facturar` | Emitir recibos del mes |
| POST | `/crm/finanzas/pagar` | Registrar pago |
| POST | `/crm/finanzas/validar-pago` | Aprobar/rechazar pago |
| GET | `/crm/finanzas/solvencia/{id}` | Generar certificado de solvencia |
| GET | `/crm/finanzas/pagos-pendientes` | Listar pagos por validar |

---

## 13. Variables de Entorno (.env)

### Configuración local (XAMPP)

```ini
CI_ENVIRONMENT = development

app.baseURL = 'http://localhost/agora/public/'
app.forceGlobalSecureRequests = false

database.default.hostname = localhost
database.default.database = agora
database.default.username = root
database.default.password =
database.default.DBDriver = MySQLi
database.default.DBPrefix =
database.default.port     = 3306
```

### Configuración de producción (InfinityFree)

```ini
CI_ENVIRONMENT = production

app.baseURL = 'https://agora-condominio.infinityfreeapp.com/'
app.forceGlobalSecureRequests = true

database.default.hostname = <host_de_infinityfree>
database.default.database = <nombre_base_de_datos>
database.default.username = <usuario_bd>
database.default.password = <clave_bd>
database.default.DBDriver = MySQLi
database.default.port     = 3306
```

> [!CAUTION]
> **Nunca subir el archivo `.env` local a producción.** Las credenciales de BD son distintas. El archivo `.env` está en `.gitignore` por defecto en CI4.

> [!IMPORTANT]
> En producción, `CI_ENVIRONMENT = production` desactiva los mensajes de error detallados (WhyScreen) que podrían exponer información sensible.

---

## 14. Despliegue en InfinityFree

### Requisitos previos

- Cuenta en [InfinityFree](https://www.infinityfree.com)
- Base de datos MySQL creada desde el panel de InfinityFree
- Cliente FTP: [FileZilla](https://filezilla-project.org/)
- Credenciales FTP del hosting

### Pasos de despliegue

1. **Preparar archivos locales**
   - Verificar que `.env` tenga la configuración de producción (ver sección 13)
   - Asegurarse de que `CI_ENVIRONMENT = production`

2. **Subir archivos vía FileZilla**
   - Conectar con las credenciales FTP de InfinityFree
   - Destino en servidor: `/htdocs/`
   - Subir **todo el proyecto excepto**: `.git/`, `.env` local, `writable/session/*`, `writable/logs/*`

3. **Configurar .env en servidor**
   - Crear/editar el `.env` directamente en el servidor con los datos de BD de InfinityFree

4. **Importar base de datos**
   - Desde el panel de InfinityFree → phpMyAdmin
   - Importar el dump SQL de la base de datos `agora`

5. **Verificar permisos de directorios**
   - `writable/` debe tener permisos de escritura (755 o 777)
   - `public/uploads/branding/` ídem

6. **Probar la URL de producción**
   - `https://agora-condominio.infinityfreeapp.com/`

### Archivos clave de configuración de URLs

| Archivo | Propósito |
|---|---|
| `.htaccess` (raíz) | Redirige todo tráfico a `/public/` |
| `public/.htaccess` | Enruta todas las peticiones a `index.php` (CI4 estándar) |
| `public/index.php` | Entry point; normaliza el case de la ruta (Windows/Linux) |

> [!WARNING]
> InfinityFree usa **LiteSpeed** (no Apache), pero respeta `.htaccess`. Si hay problemas de rutas, verificar que los archivos `.htaccess` estén correctamente subidos y no vacíos.

---

## 15. Desarrollo Local con XAMPP

### Requisitos

- [XAMPP](https://www.apachefriends.org/) con Apache y MySQL activos
- PHP 8.2+
- El proyecto en `C:\xampp\htdocs\Agora\`

### Configuración inicial

1. **Iniciar XAMPP:** Arrancar Apache y MySQL desde el panel de XAMPP

2. **Crear la base de datos**
   - Abrir `http://localhost/phpmyadmin`
   - Crear base de datos `agora` con charset `utf8mb4_general_ci`
   - Importar el dump SQL

3. **Configurar .env** (ver sección 13 — Configuración local)

4. **Acceder a la aplicación**
   - URL: `http://localhost/agora/public/`

### Problemas comunes en Windows / XAMPP

| Error | Causa | Solución |
|---|---|---|
| `500 Internal Server Error` | Bucle de redirects en `.htaccess` | Verificar que ambos `.htaccess` estén correctos |
| `404 Not Found` | Diferencia de mayúsculas `Agora` vs `agora` en la URL | El `public/index.php` normaliza el case automáticamente |
| Pantalla en blanco | `CI_ENVIRONMENT = production` oculta errores | Cambiar a `development` para ver el detalle |
| Error de conexión BD | MySQL no iniciado | Iniciar MySQL desde el panel de XAMPP |

> [!TIP]
> Con `CI_ENVIRONMENT = development`, CodeIgniter muestra la **WhyScreen** con la traza completa del error. Usar siempre `development` en local para depurar más fácilmente.

---

## 16. CSS y Assets

### Archivos CSS por módulo

| Archivo | Módulo |
|---|---|
| `public/css/login.css` | Pantalla de login |
| `public/css/landing.css` | Landing page pública |
| `public/css/super_global.css` | Layout global del panel root |
| `public/css/admin_global.css` | Layout global del panel admin |
| `public/css/residente_global.css` | Layout global del panel residente |
| `public/css/admin_planes.css` | Panel de planes y licencias (badges de plan) |
| `public/css/admin_finanzas_cobro.css` | Emisión de recibos |
| `public/css/admin_finanzas_pago.css` | Validación de pagos |
| `public/css/admin_residentes.css` | Gestión de residentes |
| `public/css/admin_marca_blanca.css` | Configuración de marca blanca |
| `public/css/admin_gestion_reservas.css` | Áreas comunes y reservas |
| `public/css/residente_reportar_pago.css` | Formulario de reporte de pago |
| `public/css/residente_reservar_area.css` | Reservas para residentes |

### JavaScript por módulo

Cada módulo tiene su propio archivo `.js` en `public/js/` con la misma nomenclatura. Principalmente manejan:
- Peticiones `fetch()` a las rutas `/crm/*`
- Interactividad de modales Bootstrap
- Validaciones del lado del cliente

### Convenciones CSS

- Clases propias del sistema tienen prefijo `.agora-` o `.text-bg-agora`
- Se usa Bootstrap 5 como base; los archivos CSS extienden/sobrescriben componentes específicos
- Los colores de plan se definen como constantes CSS en `admin_planes.css`:
  - Bronce: `#CD7F32`
  - Plata: `#475569`
  - Oro: `#B45309`

---

*Documentación generada el 19 de noviembre de 2026 · Ágora CRM v1.0*
