<?php
global $router;

use App\Controllers\CategoryController;
use App\Middlewares\JwtGuard;

$router->get("categories", [JwtGuard::class], [CategoryController::class, 'findUserCategories'])
	->get("categories/:categoryId",[JwtGuard::class], [CategoryController::class, 'findOneCategory'])
	->get("categories/:categoryId/photos", [JwtGuard::class], [CategoryController::class, 'findCategoryPhotos'])
	->post("categories", [JwtGuard::class], [CategoryController::class, 'createCategory'])
	->post("categories/:categoryId", [JwtGuard::class], [CategoryController::class, 'updateCategory'])
	->delete("categories/:categoryId", [JwtGuard::class], [CategoryController::class, 'deleteCategory']);
