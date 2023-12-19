<?php

global $router;

use App\Controllers\AuthController;
use App\Middlewares\JwtGuard;

$router->post("auth/register", [], [AuthController::class, 'register'])
    ->post("auth/login", [], [AuthController::class, 'login'])
    ->get("auth/logout", [JwtGuard::class], [AuthController::class, 'logout'])
    ->get("auth/me", [JwtGuard::class], [AuthController::class, 'getMe'])
    ->post("auth/refresh", [], [AuthController::class, 'refreshToken']);
