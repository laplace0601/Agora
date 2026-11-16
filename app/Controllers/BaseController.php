<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;

/**
 * BaseController provides a convenient place for loading components
 * and performing functions that are needed by all your controllers.
 *
 * Extend this class in any new controllers:
 * ```
 *     class Home extends BaseController
 * ```
 *
 * For security, be sure to declare any new methods as protected or private.
 */
abstract class BaseController extends Controller
{
    /**
     * Be sure to declare properties for any property fetch you initialized.
     * The creation of dynamic property is deprecated in PHP 8.2.
     */

    // protected $session;

    /**
     * @return void
     */
    public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger)
    {
        // Load here all helpers you want to be available in your controllers that extend BaseController.
        // Caution: Do not put the this below the parent::initController() call below.
        // $this->helpers = ['form', 'url'];

        // Caution: Do not edit this line.
        parent::initController($request, $response, $logger);

        // Preload any models, libraries, etc, here.
        // $this->session = service('session');
    }

    /**
     * Respuesta unificada: detecta automáticamente si la solicitud es AJAX/JSON o navegación estándar.
     * Si es AJAX/JSON, retorna una respuesta JSON estructurada con código HTTP y token CSRF.
     * Si es navegación web estándar, redirige con mensaje flash a la URL indicada (o back).
     *
     * @param string      $status      'success', 'error', 'warning', etc.
     * @param string      $mensaje     Texto descriptivo para el usuario
     * @param int         $statusCode  Código de respuesta HTTP (200, 400, 403, 404, 422, etc.)
     * @param array       $extraData   Datos adicionales para el payload JSON
     * @param string|null $redirectUrl Ruta relativa a site_url() para redirección estándar (null = back)
     */
    protected function responder(
        string $status,
        string $mensaje,
        int $statusCode = 200,
        array $extraData = [],
        ?string $redirectUrl = null
    ): ResponseInterface {
        $accept = (string) $this->request->getHeaderLine('Accept');
        $isJsonExpected = $this->request->isAJAX() || str_contains($accept, 'application/json');

        if ($isJsonExpected) {
            $payload = array_merge([
                'status'  => $status,
                'message' => $mensaje,
                'csrf'    => csrf_hash(),
            ], $extraData);

            return $this->response->setJSON($payload)->setStatusCode($statusCode);
        }

        $flashKey = in_array($status, ['success', 'ok'], true) ? 'success' : 'error';
        $redirect = $redirectUrl ? redirect()->to(site_url($redirectUrl)) : redirect()->back();

        return $redirect->with($flashKey, $mensaje);
    }
}
