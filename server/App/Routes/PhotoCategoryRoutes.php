<?php
global $router;

use App\Controllers\PhotoCategoryController;
use App\Middlewares\JwtGuard;

$router->post("photo-category", [JwtGuard::class], [PhotoCategoryController::class, 'addPhotoToCategory'])
	->post("photo-category/delete", [JwtGuard::class], [PhotoCategoryController::class, 'removePhotoFromCategory']);
