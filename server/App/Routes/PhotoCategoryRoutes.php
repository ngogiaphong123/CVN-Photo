<?php

global $router;

use App\Controllers\PhotoCategoryController;
use App\Middlewares\JwtGuard;

$router->post("photo-category", [JwtGuard::class], [PhotoCategoryController::class, 'addPhotoToCategory'])
    ->delete("photo-category/delete/category/:categoryId/photo/:photoId", [JwtGuard::class], [PhotoCategoryController::class, 'removePhotoFromCategory']);
