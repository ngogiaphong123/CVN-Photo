<?php

namespace App\Services;

use App\Common\Enums\DefaultCategory;
use App\Common\Enums\StatusCode;
use App\Common\Error\CategoryError;
use App\Common\Error\PhotoError;
use App\Exceptions\HttpException;
use App\Repositories\CategoryRepository;
use App\Repositories\PhotoCategoryRepository;
use App\Repositories\PhotoRepository;

class PhotoCategoryService
{
    public function __construct(
        private PhotoCategoryRepository $photoCategoryRepository,
        private PhotoRepository $photoRepository,
        private CategoryRepository $categoryRepository
    ) {}

    /**
     * @throws HttpException
     */
    public function checkPhotoCategoryOwner(array $data, string $userId): array
    {
        $photo = $this->photoRepository->findOne($data['photoId'] ?? '');
        if (!$photo || $photo['userId'] !== $userId) {
            throw new HttpException(StatusCode::BAD_REQUEST->value, PhotoError::PHOTO_NOT_FOUND->value);
        }
        $category = $this->categoryRepository->findOne($data['categoryId'] ?? '');
        if (!$category || $category['userId'] !== $userId) {
            throw new HttpException(StatusCode::BAD_REQUEST->value, CategoryError::CATEGORY_NOT_FOUND->value);
        }
        return [
            'photo' => $photo,
            'category' => $category,
        ];
    }

    /**
     * @throws HttpException
     */
    public function addPhotoToCategory(array $data, string $userId): array
    {
        $result = $this->checkPhotoCategoryOwner($data, $userId);
        $found = $this->photoCategoryRepository->findOne($result['photo']['id'] ?? '', $result['category']['id'] ?? '');
        if ($result['category']['name'] === DefaultCategory::FAVORITE->value) {
            $this->photoRepository->update($result['photo']['id'], [
                'isFavorite' => true,
            ], $result['photo']);
        }
        if ($found) {
            throw new HttpException(StatusCode::BAD_REQUEST->value, CategoryError::PHOTO_ALREADY_IN_CATEGORY->value);
        }
        return $this->photoCategoryRepository->create([
            'photoId' => $result['photo']['id'],
            'categoryId' => $result['category']['id'],
        ]);
    }

    /**
     * @throws HttpException
     */
    public function removePhotoFromCategory(array $data, string $userId): array
    {
        $result = $this->checkPhotoCategoryOwner($data, $userId);
        if ($result['category']['name'] === DefaultCategory::UNCATEGORIZED->value) {
            throw new HttpException(
                StatusCode::BAD_REQUEST->value,
                CategoryError::CANNOT_REMOVE_FROM_UNCATEGORIZED->value
            );
        }
        $found = $this->photoCategoryRepository->findOne($result['photo']['id'] ?? '', $result['category']['id'] ?? '');
        if ($result['category']['name'] === DefaultCategory::FAVORITE->value) {
            $this->photoRepository->update($result['photo']['id'], [
                'isFavorite' => false,
            ], $result['photo']);
        }
        if (!$found) {
            throw new HttpException(StatusCode::BAD_REQUEST->value, CategoryError::PHOTO_NOT_IN_CATEGORY->value);
        }
        return $this->photoCategoryRepository->delete([
            'photoId' => $result['photo']['id'],
            'categoryId' => $result['category']['id'],
        ]);
    }
}
