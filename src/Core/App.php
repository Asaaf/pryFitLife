<?php

declare(strict_types=1);

namespace App\Core;

use App\Config\Config;
use App\Database\Connection;
use App\Http\Controller\ResourceController;
use App\Http\OpenApiSpec;

// Repositories
use App\Domain\Repository\AfiliadoRepository;
use App\Domain\Repository\CiudadRepository;
use App\Domain\Repository\ClaseGrupalRepository;
use App\Domain\Repository\EjercicioRepository;
use App\Domain\Repository\EjercicioRutinaRepository;
use App\Domain\Repository\EmpleadoRepository;
use App\Domain\Repository\EspecialidadRepository;
use App\Domain\Repository\EstadoRepository;
use App\Domain\Repository\HorarioRepository;
use App\Domain\Repository\PagoRepository;
use App\Domain\Repository\PaisRepository;
use App\Domain\Repository\PlanRepository;
use App\Domain\Repository\PlanNutricionalRepository;
use App\Domain\Repository\RutinaRepository;
use App\Domain\Repository\SedeRepository;
use App\Domain\Repository\SeguimientoRepository;
use App\Domain\Repository\TipoDocumentoRepository;

// Services
use App\Domain\Service\AfiliadoService;
use App\Domain\Service\CiudadService;
use App\Domain\Service\ClaseGrupalService;
use App\Domain\Service\EjercicioService;
use App\Domain\Service\EjercicioRutinaService;
use App\Domain\Service\EmpleadoService;
use App\Domain\Service\EspecialidadService;
use App\Domain\Service\EstadoService;
use App\Domain\Service\HorarioService;
use App\Domain\Service\PagoService;
use App\Domain\Service\PaisService;
use App\Domain\Service\PlanService;
use App\Domain\Service\PlanNutricionalService;
use App\Domain\Service\RutinaService;
use App\Domain\Service\SedeService;
use App\Domain\Service\SeguimientoService;
use App\Domain\Service\TipoDocumentoService;

use Throwable;

/**
 * Nucleo de aplicacion: enruta solicitudes REST y estandariza respuestas JSON.
 *
 * Tabla de recursos disponibles:
 *   GET    /health
 *   GET    /{resource}         Lista todos los registros
 *   GET    /{resource}/{id}    Obtiene un registro por id
 *   POST   /{resource}         Crea un nuevo registro
 *   PUT    /{resource}/{id}    Actualiza un registro existente
 *   DELETE /{resource}/{id}    Elimina un registro
 *
 * Recursos: afiliados, ciudades, clases-grupales, ejercicios, ejercicios-rutina,
 *           empleados, especialidades, estados, horarios, pagos, paises, planes,
 *           planes-nutricionales, rutinas, sedes, seguimientos, tipos-documento
 */
final class App
{
    /**
     * Mapeo de segmento de ruta → [RepositoryClass, ServiceClass].
     * El router instancia cada par bajo demanda (lazy).
     */
    private const RESOURCES = [
        'afiliados'            => [AfiliadoRepository::class,       AfiliadoService::class],
        'ciudades'             => [CiudadRepository::class,         CiudadService::class],
        'clases-grupales'      => [ClaseGrupalRepository::class,    ClaseGrupalService::class],
        'ejercicios'           => [EjercicioRepository::class,      EjercicioService::class],
        'ejercicios-rutina'    => [EjercicioRutinaRepository::class, EjercicioRutinaService::class],
        'empleados'            => [EmpleadoRepository::class,       EmpleadoService::class],
        'especialidades'       => [EspecialidadRepository::class,   EspecialidadService::class],
        'estados'              => [EstadoRepository::class,         EstadoService::class],
        'horarios'             => [HorarioRepository::class,        HorarioService::class],
        'pagos'                => [PagoRepository::class,           PagoService::class],
        'paises'               => [PaisRepository::class,           PaisService::class],
        'planes'               => [PlanRepository::class,           PlanService::class],
        'planes-nutricionales' => [PlanNutricionalRepository::class, PlanNutricionalService::class],
        'rutinas'              => [RutinaRepository::class,         RutinaService::class],
        'sedes'                => [SedeRepository::class,           SedeService::class],
        'seguimientos'         => [SeguimientoRepository::class,    SeguimientoService::class],
        'tipos-documento'      => [TipoDocumentoRepository::class,  TipoDocumentoService::class],
    ];

    /** @return array{status:int, body:array<string, mixed>} */
    public function handleRequest(string $method, string $uri): array
    {
        $path = parse_url($uri, PHP_URL_PATH) ?? '/';
        $query = parse_url($uri, PHP_URL_QUERY) ?? '';

        // Normaliza: elimina barra final salvo en raiz.
        if ($path !== '/' && str_ends_with($path, '/')) {
            $path = rtrim($path, '/');
        }

        try {
            if ($method === 'GET' && $path === '/health') {
                return $this->healthCheck();
            }

            if ($method === 'GET' && $path === '/docs/openapi.json') {
                return ['status' => 200, 'body' => OpenApiSpec::generate()];
            }

            if ($method === 'GET' && ($path === '/docs' || $path === '/docs/')) {
                return ['status' => 200, 'content_type' => 'text/html; charset=utf-8', 'body' => $this->swaggerUiHtml()];
            }

            return $this->dispatch($method, $path, $this->parseQueryParams($query));
        } catch (Throwable $exception) {
            return ErrorHandler::toApiResponse($exception, Config::isDebug());
        }
    }

    /** @return array{status:int, body:array<string, mixed>} */
    private function dispatch(string $method, string $path, array $queryParams = []): array
    {
        // Descompone la ruta en segmentos significativos (ignora el primer '/').
        $segments = array_values(array_filter(explode('/', $path)));

        $resource = $segments[0] ?? null;
        $rawId    = $segments[1] ?? null;

        if ($resource === null || !isset(self::RESOURCES[$resource])) {
            return ['status' => 404, 'body' => ['message' => 'Ruta no encontrada.']];
        }

        // Valida que el id, cuando esta presente, sea un entero positivo.
        $id = null;
        if ($rawId !== null) {
            if (!ctype_digit($rawId) || (int) $rawId < 1) {
                return ['status' => 400, 'body' => ['message' => 'El id debe ser un entero positivo.']];
            }
            $id = (int) $rawId;
        }

        $controller = $this->makeController($resource);

        return match (true) {
            $method === 'GET'    && $id === null => $controller->index($queryParams),
            $method === 'GET'    && $id !== null => $controller->show($id),
            $method === 'POST'   && $id === null => $controller->store($this->parseBody()),
            $method === 'PUT'    && $id !== null => $controller->replace($id, $this->parseBody()),
            $method === 'DELETE' && $id !== null => $controller->destroy($id),
            default => ['status' => 405, 'body' => ['message' => 'Metodo no permitido.']],
        };
    }

    private function makeController(string $resource): ResourceController
    {
        [$repoClass, $serviceClass] = self::RESOURCES[$resource];
        return new ResourceController(new $serviceClass(new $repoClass()));
    }

    /**
     * Lee y decodifica el cuerpo JSON de la solicitud.
     * Devuelve array vacio si no hay cuerpo o no es JSON valido.
     *
     * @return array<string, mixed>
     */
    private function parseBody(): array
    {
        $raw = file_get_contents('php://input');

        if ($raw === false || $raw === '') {
            return [];
        }

        $decoded = json_decode($raw, true);

        return is_array($decoded) ? $decoded : [];
    }

    /**
     * @return array<string, mixed>
     */
    private function parseQueryParams(string $query): array
    {
        if ($query === '') {
            return [];
        }

        parse_str($query, $params);

        return is_array($params) ? $params : [];
    }

    /** @return array{status:int, body:array<string, mixed>} */
    private function healthCheck(): array
    {
        $pdo       = Connection::getInstance();
        $dbVersion = (string) $pdo->query('SELECT VERSION()')->fetchColumn();

        return [
            'status' => 200,
            'body' => [
                'service'       => 'ok',
                'database'      => 'ok',
                'mysql_version' => $dbVersion,
            ],
        ];
    }

    /** Devuelve el HTML de Swagger UI apuntando al spec generado por esta misma API. */
    private function swaggerUiHtml(): string
    {
        return <<<'HTML'
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>FitLife API — Documentación</title>
  <link rel="stylesheet" href="https://unpkg.com/swagger-ui-dist@5/swagger-ui.css" />
  <style>
    body { margin: 0; }
    #swagger-ui .topbar { background-color: #1a1a2e; }
    #swagger-ui .topbar .topbar-wrapper .link { display: none; }
  </style>
</head>
<body>
  <div id="swagger-ui"></div>
  <script src="https://unpkg.com/swagger-ui-dist@5/swagger-ui-bundle.js"></script>
  <script>
    SwaggerUIBundle({
      url: '/docs/openapi.json',
      dom_id: '#swagger-ui',
      presets: [SwaggerUIBundle.presets.apis, SwaggerUIBundle.SwaggerUIStandalonePreset],
      layout: 'BaseLayout',
      deepLinking: true,
      displayRequestDuration: true,
      filter: true,
      tryItOutEnabled: true,
    });
  </script>
</body>
</html>
HTML;
    }
}
