<?php

namespace App\Services;

use App\Common\Enums\DefaultCategory;
use App\Common\Enums\StatusCode;
use App\Common\Error\CategoryError;
use App\Common\Validator\Validator;
use App\Exceptions\HttpException;
use App\Repositories\CategoryRepository;

class CategoryService {
	public function __construct (private CategoryRepository $categoryRepository) {}

	/**
	 * @throws HttpException
	 */
	public function create (array $data, string $userId): array {
		$data['userId'] = $userId;
		$category = $this->categoryRepository->findOneByName($data);
		if ($category) {
			throw new HttpException(StatusCode::BAD_REQUEST->value, CategoryError::CATEGORY_ALREADY_EXISTS->value);
		}
		return $this->categoryRepository->create($data);
	}

	/**
	 * @throws HttpException
	 */
	public function update (string $id, array $data, string $userId) {
		$allowedFields = ['name', 'memo', 'url', 'publicId'];
		foreach ($data as $key => $value) {
			if (!in_array($key, $allowedFields)) {
				unset($data[$key]);
			}
		}
		$category = $this->checkOwnerAndName($id, $userId);
		return $this->categoryRepository->update($id, $data, $category);
	}

	/**
	 * @throws HttpException
	 */
	public function delete (string $id, string $userId): int {
		$category = $this->checkOwnerAndName($id, $userId);
		return $this->categoryRepository->delete($category['id']);
	}

	/**
	 * @throws HttpException
	 */
	public function findCategoryPhotos (string $id, string $userId): array {
		$category = $this->categoryRepository->findOne($id);
		if (!$category || $category['userId'] !== $userId) {
			throw new HttpException(StatusCode::BAD_REQUEST->value, CategoryError::CATEGORY_NOT_FOUND->value);
		}
		return $this->categoryRepository->findCategoryPhotos($id);
	}

	public function findUserCategories (string $userId): array {
		return $this->categoryRepository->findUserCategories($userId);
	}

	/**
	 * @param string $id
	 * @param string $userId
	 * @return mixed
	 * @throws HttpException
	 */
	public function checkOwnerAndName (string $id, string $userId): mixed {
		$category = $this->categoryRepository->findOne($id);
		if (!$category || $category['userId'] !== $userId) {
			throw new HttpException(StatusCode::BAD_REQUEST->value, CategoryError::CATEGORY_NOT_FOUND->value);
		}
		if ($category['name'] === DefaultCategory::FAVORITE->value || $category['name'] === DefaultCategory::UNCATEGORIZED->value) {
			throw new HttpException(StatusCode::BAD_REQUEST->value, CategoryError::CATEGORY_NOT_ALLOWED->value);
		}
		return $category;
	}

	/**
	 * @throws HttpException
	 */
	public function findOne (string $categoryId, string $userId) {
		$category = $this->categoryRepository->findOne($categoryId);
		if (!$category || $category['userId'] !== $userId) {
			throw new HttpException(StatusCode::BAD_REQUEST->value, CategoryError::CATEGORY_NOT_FOUND->value);
		}
		return $category;
	}

	/**
	 * @throws HttpException
	 */
	public function findCategoryPhotosByPage (string $categoryId, string $userId, string $page, string $limit) : array {
		$isValid = Validator::validateInteger([
			'page' => $page,
			'limit' => $limit,
		]);
		if (!$isValid) {
			throw new HttpException(StatusCode::BAD_REQUEST->value, CategoryError::INVALID_PAGE_OR_LIMIT->value);
		}
		$category = $this->categoryRepository->findOne($categoryId);
		if (!$category || $category['userId'] !== $userId) {
			throw new HttpException(StatusCode::BAD_REQUEST->value, CategoryError::CATEGORY_NOT_FOUND->value);
		}
		return $this->categoryRepository->findCategoryPhotosByPage($categoryId, $page, $limit);
	}

	/**
	 * @throws HttpException
	 */
	public function findPhotosNotInCategoryByPage (string $categoryId, string $userId, string $page, string $limit): array {
		$isValid = Validator::validateInteger([
			'page' => $page,
			'limit' => $limit,
		]);
		if (!$isValid) {
			throw new HttpException(StatusCode::BAD_REQUEST->value, CategoryError::INVALID_PAGE_OR_LIMIT->value);
		}
		$category = $this->categoryRepository->findOne($categoryId);
		if (!$category || $category['userId'] !== $userId) {
			throw new HttpException(StatusCode::BAD_REQUEST->value, CategoryError::CATEGORY_NOT_FOUND->value);
		}
		return $this->categoryRepository->findPhotosNotInCategoryByPage($categoryId, $page, $limit, $userId);
	}

	/**
	 * @throws HttpException
	 */
	public function findPhotosNotInCategory (string $categoryId, string $userId): array {
		$category = $this->categoryRepository->findOne($categoryId);
		if (!$category || $category['userId'] !== $userId) {
			throw new HttpException(StatusCode::BAD_REQUEST->value, CategoryError::CATEGORY_NOT_FOUND->value);
		}
		return $this->categoryRepository->findPhotosNotInCategory($categoryId, $userId);
	}
}
