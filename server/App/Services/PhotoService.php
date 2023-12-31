<?php

namespace App\Services;

use App\Common\Enums\StatusCode;
use App\Common\Error\AuthError;
use App\Common\Error\PhotoError;
use App\Common\Error\UploadError;
use App\Common\Validator\Validator;
use App\Exceptions\HttpException;
use App\Repositories\PhotoCategoryRepository;
use App\Repositories\PhotoRepository;
use App\Repositories\UserRepository;

class PhotoService
{
    public function __construct(
        private PhotoRepository $photoRepository,
        private UploadService $uploadService,
        private UserRepository $userRepository,
        private PhotoCategoryRepository $photoCategoryRepository
    ) {}

    /**
     * @throws HttpException
     */
    public function upload(array $data, string $userId): array
    {
        $user = $this->userRepository->findOne($userId);
        if (!$user) {
            throw new HttpException(StatusCode::BAD_REQUEST->value, AuthError::USER_DOES_NOT_EXIST->value);
        }
        foreach ($data['type'] as $key => $value) {
            if (!in_array($value, ['image/jpeg', 'image/png', 'image/jpg'])) {
                throw new HttpException(StatusCode::BAD_REQUEST->value, UploadError::FILE_TYPE_IS_NOT_ALLOWED->value);
            }
            if ($data['size'][$key] > UploadService::$MAX_FILE_SIZE) {
                throw new HttpException(StatusCode::BAD_REQUEST->value, UploadError::FILE_IS_TOO_LARGE->value);
            }
        }
        $photos = [];
        foreach ($data['name'] as $key => $value) {
            $file = [
                'name' => $data['name'][$key],
                'type' => $data['type'][$key],
                'tmp_name' => $data['tmp_name'][$key],
                'size' => $data['size'][$key],
            ];
            $checkImage = $this->uploadService->checkImage($file);
            if (!$checkImage) {
                throw new HttpException(StatusCode::BAD_REQUEST->value, UploadError::FILE_TYPE_IS_NOT_ALLOWED->value);
            }
            $result = $this->uploadService->upload($file, $userId);
            $photo = $this->photoRepository->create([
                'name' => $file['name'],
                'userId' => $userId,
                'publicId' => $result['publicId'],
                'url' => $result['secureUrl'],
                'size' => $file['size'],
                'description' => '',
            ]);
            $this->photoCategoryRepository->addToUncategorized($photo['id'], $userId);
            $photos[] = $photo;
        }
        return $photos;
    }

    /**
     * @throws HttpException
     */
    public function delete(string $id, string $userId): int
    {
        $photo = $this->photoRepository->findOne($id);
        if (!$photo || $photo['userId'] !== $userId) {
            throw new HttpException(StatusCode::BAD_REQUEST->value, PhotoError::PHOTO_NOT_FOUND->value);
        }
        $this->uploadService->delete($photo['publicId']);
        return $this->photoRepository->delete($id);
    }

    /**
     * @throws HttpException
     */
    public function update(string $id, array $data, string $userId): array
    {
        $allowedFields = ['name', 'description', 'takenAt'];
        foreach ($data as $key => $value) {
            if (!in_array($key, $allowedFields)) {
                unset($data[$key]);
            }
        }
        $photo = $this->photoRepository->findOne($id);
        if (!$photo || $photo['userId'] !== $userId) {
            throw new HttpException(StatusCode::BAD_REQUEST->value, PhotoError::PHOTO_NOT_FOUND->value);
        }
        return $this->photoRepository->update($id, $data, $photo);
    }

    public function findUserPhotos(string $userId): array
    {
        return $this->photoRepository->findUserPhotos($userId);
    }

    /**
     * @throws HttpException
     */
    public function findUserPhoto(string $id, string $userId): array
    {
        $photo = $this->photoRepository->findUserPhoto($id, $userId);
        if (!$photo || $photo['userId'] !== $userId) {
            throw new HttpException(StatusCode::BAD_REQUEST->value, PhotoError::PHOTO_NOT_FOUND->value);
        }
        return $photo;
    }

    /**
     * @throws HttpException
     */
    public function findUsersPhotoByPage(string $userId, mixed $page, mixed $limit): array
    {
        $isValid = Validator::validateInteger([
            'page' => $page,
            'limit' => $limit,
        ]);
        if (!$isValid) {
            throw new HttpException(StatusCode::BAD_REQUEST->value, PhotoError::INVALID_PAGE_OR_LIMIT->value);
        }
        return $this->photoRepository->findUsersPhotos($userId, $page, $limit);
    }
}
