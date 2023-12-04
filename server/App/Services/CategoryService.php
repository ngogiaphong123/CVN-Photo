<?php

namespace App\Services;

use App\Common\Enums\StatusCode;
use App\Common\Error\CategoryError;
use App\Exceptions\HttpException;
use App\Repositories\CategoryRepository;

class CategoryService {
	public function __construct (private CategoryRepository $categoryRepository) {}

	/**
	 * @throws HttpException
	 */
	public function create (array $data, string $userId) {
		$data['userId'] = $userId;
		return $this->categoryRepository->create($data);
	}

	public function findOne (string $id) {
		return $this->categoryRepository->findOne($id);
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
		$category = $this->categoryRepository->findOne($id);
		if (!$category || $category['userId'] !== $userId) {
			throw new HttpException(StatusCode::BAD_REQUEST->value, CategoryError::CATEGORY_NOT_FOUND->value);
		}
		return $this->categoryRepository->update($id, $data, $category);
	}

	/**
	 * @throws HttpException
	 */
	public function delete (string $id, string $userId): int {
		$category = $this->categoryRepository->findOne($id);
		if (!$category || $category['userId'] !== $userId) {
			throw new HttpException(StatusCode::BAD_REQUEST->value, CategoryError::CATEGORY_NOT_FOUND->value);
		}
		return $this->categoryRepository->delete($id);
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

}
