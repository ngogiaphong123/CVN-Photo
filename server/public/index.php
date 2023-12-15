<?php

require_once __DIR__ . '/../vendor/autoload.php';

use App\Core\Application;
use App\Core\Router;


$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();
require_once __DIR__ . '/../App/Common/Cloudinary/Cloudinary.php';
require_once __DIR__ . '/../App/Common/Cors/Cors.php';
$container = require_once __DIR__ . '/../App/Common/Container/Container.php';
$app = $container->get(Application::class);

$router = $container->get(Router::class);

require_once __DIR__ . '/../App/Routes/AuthRoutes.php';
require_once __DIR__ . '/../App/Routes/UserRoutes.php';
require_once __DIR__ . '/../App/Routes/CategoryRoutes.php';
require_once __DIR__ . '/../App/Routes/PhotoRoutes.php';
require_once __DIR__ . '/../App/Routes/PhotoCategoryRoutes.php';

$app->run();
