<?php

namespace App\Repositories;

use App\Core\Database;
use App\Entities\CategoryEntity;
use App\Exceptions\HttpException;
use PDO;

class CategoryRepository {
	public function __construct (private readonly Database $database) {}

	/**
	 * @param array $data ['name', 'memo', 'url', 'publicId', 'userId']
	 * @return array
	 * @throws HttpException
	 */

	public function create (array $data): array {
		$categoryEntity = new CategoryEntity();
		$categoryEntity->setName($data['name'] ?? "")->setMemo($data['memo'] ?? "")->setUrl($data['url'] ?? "")->setPublicId($data['publicId'] ?? "")
			->setUserId($data['userId'] ?? "")->build();
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
		return $this->findUserCategories($categoryEntity->getUserId());
	}

	/**
	 * @throws HttpException
	 */
	public function findOneByName (array $data) {
		$categoryEntity = new CategoryEntity();
		$categoryEntity->setName($data['name'] ?? "")->setUserId($data['userId'] ?? "")->build();
		$query = "SELECT * FROM categories WHERE name = :name AND userId = :userId";
		$statement = $this->database->getConnection()->prepare($query);
		$statement->execute([
			':name' => $data['name'],
			':userId' => $data['userId'],
		]);
		$result = $statement->fetch(PDO::FETCH_ASSOC);
		if (!$result) {
			return NULL;
		}
		return $result;
	}

	public function getNumPhotosInCategory (string $categoryId): int {
		$query = "SELECT * FROM photoCategory WHERE categoryId = :categoryId";
		$statement = $this->database->getConnection()->prepare($query);
		$statement->execute([
			':categoryId' => $categoryId,
		]);
		return $statement->rowCount();
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
		$result['numPhotos'] = $this->getNumPhotosInCategory($result['id']);
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
		$query = "SELECT id, name, memo, url, publicId, createdAt, updatedAt FROM categories WHERE userId = :userId ORDER BY updatedAt DESC";
		$statement = $this->database->getConnection()->prepare($query);
		$statement->execute([
			':userId' => $userId,
		]);
		$result = $statement->fetchAll(PDO::FETCH_ASSOC);
		if (!$result) {
			return [];
		}
		foreach ($result as $key => $value) {
			$result[$key]['numPhotos'] = $this->getNumPhotosInCategory($value['id']);
		}
		return $result;
	}

	public function findCategoryPhotos (string $categoryId): array {
		$query = "SELECT * FROM photos WHERE id IN (SELECT photoId FROM photoCategory WHERE categoryId = :categoryId) ORDER BY takenAt DESC";
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

	public function findCategoryPhotosByPage (string $categoryId, string $page, string $limit): array {
		$query = "SELECT * FROM photos WHERE id IN (SELECT photoId FROM photoCategory WHERE categoryId = :categoryId) ORDER BY takenAt DESC LIMIT :limit OFFSET :offset";
		$statement = $this->database->getConnection()->prepare($query);
		$statement->bindValue(':categoryId', $categoryId);
		$statement->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
		$statement->bindValue(':offset', ((int)$page - 1) * (int)$limit, PDO::PARAM_INT);
		$statement->execute();
		$result = $statement->fetchAll(PDO::FETCH_ASSOC);
		if (!$result) {
			return [];
		}
		return $result;
	}

	public function findPhotosNotInCategoryByPage (string $categoryId, string $page, string $limit, string $userId): array {
		$query = "SELECT * FROM photos WHERE id NOT IN (SELECT photoId FROM photoCategory WHERE categoryId = :categoryId) AND userId = :userId ORDER BY takenAt DESC LIMIT :limit OFFSET :offset";
		$statement = $this->database->getConnection()->prepare($query);
		$statement->bindValue(':categoryId', $categoryId);
		$statement->bindValue(':userId', $userId);
		$statement->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
		$statement->bindValue(':offset', ((int)$page - 1) * (int)$limit, PDO::PARAM_INT);
		$statement->execute();
		$result = $statement->fetchAll(PDO::FETCH_ASSOC);
		if (!$result) {
			return [];
		}
		return $result;
	}
}
