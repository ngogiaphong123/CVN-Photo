<?php
require_once __DIR__ . '/../vendor/autoload.php';

use App\Controllers\AuthController;
use App\Controllers\CategoryController;
use App\Controllers\PhotoCategoryController;
use App\Controllers\PhotoController;
use App\Controllers\UserController;
use App\Core\Application;
use App\Core\Router;
use App\Middlewares\JwtGuard;


$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();
require_once __DIR__ . '/../App/Common/Cloudinary/Cloudinary.php';
require_once __DIR__ . '/../App/Common/Cors/Cors.php';
$container = require_once __DIR__ . '/../App/Common/Container/Container.php';
$app = $container->get(Application::class);

$router = $container->get(Router::class);

$router->post("auth/register", [], [AuthController::class, 'register'])
	->post("auth/login", [], [AuthController::class, 'login'])
	->get("auth/logout", [JwtGuard::class], [AuthController::class, 'logout'])
	->get("auth/me", [JwtGuard::class], [AuthController::class, 'getMe'])
	->post("auth/refresh", [], [AuthController::class, 'refreshToken'])
	->post("users/upload-avatar", [JwtGuard::class], [UserController::class, 'uploadAvatar'])
	->post("users/update-profile", [JwtGuard::class], [UserController::class, 'updateProfile'])
	->get("categories", [JwtGuard::class], [CategoryController::class, 'findUserCategories'])
	->get("categories/:categoryId/photos", [JwtGuard::class], [CategoryController::class, 'findCategoryPhotos'])
	->post("categories", [JwtGuard::class], [CategoryController::class, 'createCategory'])
	->post("categories/:categoryId", [JwtGuard::class], [CategoryController::class, 'updateCategory'])
	->delete("categories/:categoryId", [JwtGuard::class], [CategoryController::class, 'deleteCategory'])
	->post("photos", [JwtGuard::class], [PhotoController::class, 'uploadPhotos'])
	->post("photos/:photoId", [JwtGuard::class], [PhotoController::class, 'updatePhoto'])
	->delete("photos/:photoId", [JwtGuard::class], [PhotoController::class, 'deletePhoto'])
	->get("photos/:photoId", [JwtGuard::class], [PhotoController::class, 'findUserPhoto'])
	->get("photos", [JwtGuard::class], [PhotoController::class, 'findUserPhotos'])
	->post("photo-category", [JwtGuard::class], [PhotoCategoryController::class, 'addPhotoToCategory'])
	->post("photo-category/delete", [JwtGuard::class], [PhotoCategoryController::class, 'removePhotoFromCategory']);

$app->run();
