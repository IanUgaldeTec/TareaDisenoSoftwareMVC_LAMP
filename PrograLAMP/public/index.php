<?php
// ============================================================
// public/index.php - Front Controller
// Punto de entrada unico de la aplicacion.
// 1. Carga config y autoloader
// 2. Registra rutas
// 3. Despacha el request al Controller correcto
// ============================================================

// ── 1. Configuracion ──────────────────────────────────────
require_once __DIR__ . '/../config/config.php';

// Headers globales: JSON por defecto, CORS abierto para desarrollo
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// Preflight CORS - el browser pregunta antes de hacer POST cross-origin
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit;
}

// ── 2. Autoloader ─────────────────────────────────────────
// Carga automaticamente las clases segun convencion de nombres
// Nombre de clase → archivo PHP en el directorio correspondiente
spl_autoload_register(function (string $class): void {
    // Mapa de directorios donde buscar clases
    $directorios = [
        __DIR__ . '/../app/core/',
        __DIR__ . '/../app/models/',
        __DIR__ . '/../app/controllers/',
        __DIR__ . '/../app/services/',
    ];

    foreach ($directorios as $dir) {
        $archivo = $dir . $class . '.php';
        if (file_exists($archivo)) {
            require_once $archivo;
            return;
        }
    }
    // Si no se encuentra la clase lanzamos error descriptivo
    throw new RuntimeException("Clase no encontrada: {$class}");
});

// ── 3. Parseo de la URI ────────────────────────────────────
// Quitamos el BASE_PATH para que el Router trabaje con rutas relativas
// Ejemplo: /url-shortener/api/urls → /api/urls
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

// Normalizamos el BASE_PATH (puede estar vacio en instalacion en root)
$basePath = rtrim(BASE_PATH, '/');
if ($basePath !== '' && str_starts_with($uri, $basePath)) {
    $uri = substr($uri, strlen($basePath));
}

// Aseguramos que siempre empiece con /
$uri = '/' . ltrim($uri, '/');

$method = $_SERVER['REQUEST_METHOD'];

// ── 4. Registro de rutas ───────────────────────────────────
$router = new Router();

// IMPORTANTE: el orden importa - las rutas especificas de /api PRIMERO
// para que no sean atrapadas por el catch-all /{shortCode}

// Servir la SPA (el unico View PHP)
$router->add('GET', '/', function () {
    // Para el HTML desactivamos el Content-Type JSON que pusimos arriba
    header('Content-Type: text/html; charset=utf-8');
    require_once __DIR__ . '/../app/views/home.php';
    exit;
});

// API de URLs
$router->add('POST', '/api/urls', function () {
    (new UrlController())->create();
});

$router->add('GET', '/api/urls', function () {
    (new UrlController())->index();
});

// Estadisticas - ANTES del catch-all /{shortCode}
$router->add('GET', '/api/urls/{shortCode}/stats', function (string $shortCode) {
    (new StatsController())->show($shortCode);
});

// Redireccion - catch-all de codigos cortos
$router->add('GET', '/{shortCode}', function (string $shortCode) {
    (new UrlController())->redirect($shortCode);
});

// ── 5. Despachar ──────────────────────────────────────────
$router->dispatch($method, $uri);
