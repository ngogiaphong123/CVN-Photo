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

$app->run();
