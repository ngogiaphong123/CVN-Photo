<?php
require_once __DIR__ . '/../vendor/autoload.php';

use App\Controllers\AuthController;
use App\Core\Application;
use App\Core\Router;
use App\Middlewares\JwtGuard;

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();

$container = require_once __DIR__ . '/../App/Common/Container/Container.php';

$app = $container->get(Application::class);

$router = $container->get(Router::class);

$router->post("auth/register", [], [AuthController::class, 'register'])
	->post("auth/login", [], [AuthController::class, 'login'])
	->get("auth/logout", [JwtGuard::class], [AuthController::class, 'logout'])
	->get("auth/me", [JwtGuard::class], [AuthController::class, 'getMe'])
	->post("auth/refresh", [], [AuthController::class, 'refreshToken']);

$app->run();
