<?php

namespace App\Repositories;

use App\Core\Database;
use App\Entities\CategoryEntity;
use App\Exceptions\HttpException;
use PDO;

class CategoryRepository implements IRepository {
	public function __construct (private readonly Database $database) {}

	/**
	 * @param array $data ['name', 'memo', 'url', 'publicId', 'userId']
	 * @return mixed|null
	 * @throws HttpException
	 */
	public function create (array $data): mixed {
		$categoryEntity = new CategoryEntity();
		$categoryEntity->setName($data['name'])->setMemo($data['memo'])->setUrl($data['url'])->setPublicId($data['publicId'])
			->setUserId($data['userId'])->build();
		$query = "INSERT INTO categories (id, name, memo, url, publicId, userId, createdAt, updatedAt)
		VALUES (:id, :name, :memo, :url, :publicId, :userId, :createdAt, :updatedAt)";
		$statement = $this->database->getConnection()->prepare($query);
		$statement->execute([
			':id' => $categoryEntity->getId(),
			':name' => $categoryEntity->getName(),
			':memo' => $categoryEntity->getMemo(),
			':url' => $categoryEntity->getUrl(),
			':publicId' => $categoryEntity->getPublicId(),
			':userId' => $categoryEntity->getUserId(),
			':createdAt' => $categoryEntity->getCreatedAt(),
			':updatedAt' => $categoryEntity->getUpdatedAt(),
		]);
		return $this->findOne($categoryEntity->getId());
	}

	public function findOne (string $id) {
		$query = "SELECT * FROM categories WHERE id = :id";
		$statement = $this->database->getConnection()->prepare($query);
		$statement->execute([
			':id' => $id,
		]);
		$result = $statement->fetch(PDO::FETCH_ASSOC);
		if (!$result) {
			return NULL;
		}
		return $result;
	}

	/**
	 * @throws HttpException
	 */
	public function update (string $id,
	                        array  $data,
	                        array  $originalData) {
		$categoryEntity = new CategoryEntity();
		$categoryEntity->setName($data['name'] ?? $originalData['name'])->setMemo($data['memo'] ?? $originalData['memo'])
			->setUrl($data['url'] ?? $originalData['url'])->setPublicId($data['publicId'] ?? $originalData['publicId'])
			->setUserId($originalData['userId'])->build();
		$query = "UPDATE categories SET name = :name, memo = :memo, url = :url, publicId = :publicId, userId = :userId, updatedAt = :updatedAt WHERE id = :id";
		$statement = $this->database->getConnection()->prepare($query);
		$statement->execute([
			':id' => $id,
			':name' => $categoryEntity->getName(),
			':memo' => $categoryEntity->getMemo(),
			':url' => $categoryEntity->getUrl(),
			':publicId' => $categoryEntity->getPublicId(),
			':userId' => $categoryEntity->getUserId(),
			':updatedAt' => $categoryEntity->getUpdatedAt(),
		]);
		return $this->findOne($id);
	}

	public function delete (string $id): int {
		$query = "DELETE FROM categories WHERE id = :id";
		$statement = $this->database->getConnection()->prepare($query);
		$statement->execute([
			':id' => $id,
		]);
		return $statement->rowCount();
	}

	public function findUserCategories (string $userId): array {
		$query = "SELECT id, name, memo, url, publicId, createdAt, updatedAt FROM categories WHERE userId = :userId";
		$statement = $this->database->getConnection()->prepare($query);
		$statement->execute([
			':userId' => $userId,
		]);
		$result = $statement->fetchAll(PDO::FETCH_ASSOC);
		if (!$result) {
			return [];
		}
		return $result;
	}

	public function findCategoryPhotos (string $categoryId): array {
		$query = "SELECT id, name, description, url, publicId, size, createdAt, updatedAt FROM photos WHERE id IN (SELECT photoId FROM photoCategory WHERE categoryId = :categoryId)";
		$statement = $this->database->getConnection()->prepare($query);
		$statement->execute([
			':categoryId' => $categoryId,
		]);
		$result = $statement->fetchAll(PDO::FETCH_ASSOC);
		if (!$result) {
			return [];
		}
		return $result;
	}
}
