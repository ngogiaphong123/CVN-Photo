<?php
global $router;

use App\Controllers\UserController;
use App\Middlewares\JwtGuard;

$router->post("users/upload-avatar", [JwtGuard::class], [UserController::class, 'uploadAvatar'])
	->post("users/update-profile", [JwtGuard::class], [UserController::class, 'updateProfile']);
