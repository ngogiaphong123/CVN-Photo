<?php

namespace App\Repositories;

use App\Common\Enums\DefaultCategory;
use App\Core\Database;
use App\Entities\PhotoCategoryEntity;
use PDO;

class PhotoCategoryRepository {
	public function __construct (private Database $database) {}

	public function create (array $data): array {
		$photoCategoryEntity = new PhotoCategoryEntity();
		$photoCategoryEntity->setPhotoId($data['photoId'])->setCategoryId($data['categoryId']);
		$query = "INSERT INTO photoCategory (photoId, categoryId) VALUES (:photoId, :categoryId)";
		$statement = $this->database->getConnection()->prepare($query);
		$statement->execute([
			':photoId' => $photoCategoryEntity->getPhotoId(),
			':categoryId' => $photoCategoryEntity->getCategoryId(),
		]);
		return [
			'photoId' => $photoCategoryEntity->getPhotoId(),
			'categoryId' => $photoCategoryEntity->getCategoryId(),
		];
	}

	public function delete (array $data): array {
		$photoCategoryEntity = new PhotoCategoryEntity();
		$photoCategoryEntity->setPhotoId($data['photoId'])->setCategoryId($data['categoryId']);
		$query = "DELETE FROM photoCategory WHERE photoId = :photoId AND categoryId = :categoryId";
		$statement = $this->database->getConnection()->prepare($query);
		$statement->execute([
			':photoId' => $photoCategoryEntity->getPhotoId(),
			':categoryId' => $photoCategoryEntity->getCategoryId(),
		]);
		return [
			'photoId' => $photoCategoryEntity->getPhotoId(),
			'categoryId' => $photoCategoryEntity->getCategoryId(),
		];
	}

	public function addToUncategorized (string $photoId, $userId): array {
		$defaultCategory = DefaultCategory::UNCATEGORIZED->value;
		$query = "SELECT * FROM categories WHERE userId = :userId AND name = :name";
		$statement = $this->database->getConnection()->prepare($query);
		$statement->execute([
			':userId' => $userId,
			':name' => $defaultCategory,
		]);
		$result = $statement->fetch(PDO::FETCH_ASSOC);
		if (!$result) {
			return [];
		}
		return $this->create([
			'photoId' => $photoId,
			'categoryId' => $result['id'],
		]);
	}

	public function findOne (string $photoId, string $categoryId): array {
		$query = "SELECT * FROM photoCategory WHERE photoId = :photoId AND categoryId = :categoryId";
		$statement = $this->database->getConnection()->prepare($query);
		$statement->execute([
			':photoId' => $photoId,
			':categoryId' => $categoryId,
		]);
		$result = $statement->fetch(PDO::FETCH_ASSOC);
		if (!$result) {
			return [];
		}
		return $result;
	}
}
