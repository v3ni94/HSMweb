<?php
declare(strict_types=1);

/**
 * Front Controller. Einziger öffentlicher PHP-Einstiegspunkt.
 */

use App\Controllers\SystemController;
use App\Services\Request;
use App\Services\Response;

$config = require dirname(__DIR__) . '/app/bootstrap.php';

Response::securityHeaders();

$method = Request::method();
$path = Request::path();

try {
    $router = require HSM_ROOT . '/app/routes.php';
    $route = $router($method, $path);
    if ($route === null) {
        (new SystemController())->notFound();
        exit;
    }
    $controller = new $route['controller']();
    $controller->{$route['action']}(...array_values($route['params']));
} catch (\Throwable $e) {
    error_log('[hsm] ' . get_class($e) . ': ' . $e->getMessage() . ' @ ' . $e->getFile() . ':' . $e->getLine());
    (new SystemController())->serverError();
}
