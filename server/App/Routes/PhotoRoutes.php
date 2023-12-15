<?php

global $router;

use App\Controllers\PhotoController;
use App\Middlewares\JwtGuard;

$router->post("photos", [JwtGuard::class], [PhotoController::class, 'uploadPhotos'])
    ->post("photos/:photoId", [JwtGuard::class], [PhotoController::class, 'updatePhoto'])
    ->delete("photos/:photoId", [JwtGuard::class], [PhotoController::class, 'deletePhoto'])
    ->get("photos/:photoId", [JwtGuard::class], [PhotoController::class, 'findUserPhoto'])
    ->get("photos", [JwtGuard::class], [PhotoController::class, 'findUserPhotos'])
    ->get("photos/:page/:limit", [JwtGuard::class], [PhotoController::class, 'findUsersPhotoByPage']);
