<?php

namespace App\Repositories;

use App\Core\Database;
use App\Entities\PhotoEntity;
use App\Exceptions\HttpException;
use PDO;

class PhotoRepository
{

    public function __construct(private readonly Database $database) {}

    /**
     * @param array $data ['name', 'description', 'url', 'publicId', 'userId']
     * @return mixed|null
     * @throws HttpException
     */
    public function create(array $data): mixed
    {
        $photoEntity = new PhotoEntity();
        $photoEntity->setName($data['name'] ?? '')->setUrl($data['url'] ?? '')->setPublicId($data['publicId'] ?? '')
            ->setSize($data['size'] ?? 0)->setUserId($data['userId'] ?? '')->setDescription(
                $data['description'] ?? ''
            )->setTakenAt(date('Y-m-d H:i:s'))->build();
        $query = "INSERT INTO photos (id, name, description, url, publicId, size, userId,takenAt ,createdAt, updatedAt) VALUES (:id, :name, :description, :url, :publicId, :size, :userId,:takenAt, :createdAt, :updatedAt)";
        $statement = $this->database->getConnection()->prepare($query);
        $statement->execute([
            ':id' => $photoEntity->getId(),
            ':name' => $photoEntity->getName(),
            ':description' => $photoEntity->getDescription(),
            ':url' => $photoEntity->getUrl(),
            ':publicId' => $photoEntity->getPublicId(),
            ':size' => $photoEntity->getSize(),
            ':userId' => $photoEntity->getUserId(),
            ':createdAt' => $photoEntity->getCreatedAt(),
            ':updatedAt' => $photoEntity->getUpdatedAt(),
            ':takenAt' => $photoEntity->getTakenAt(),
        ]);
        return $this->findOne($photoEntity->getId());
    }

    public function findOne(string $id)
    {
        $query = "SELECT * FROM photos WHERE id = :id";
        $statement = $this->database->getConnection()->prepare($query);
        $statement->execute([
            ':id' => $id,
        ]);
        $result = $statement->fetch(PDO::FETCH_ASSOC);
        if (!$result) {
            return null;
        }
        return $result;
    }

    /**
     * @throws HttpException
     */
    public function update(string $id, array $data, array $originalData)
    {
        $photoEntity = new PhotoEntity();
        $photoEntity->setName($data['name'] ?? $originalData['name'])->setUrl(
            $data['url'] ?? $originalData['url']
        )->setPublicId($data['publicId'] ?? $originalData['publicId'])
            ->setSize($data['size'] ?? $originalData['size'])->setUserId(
                $data['userId'] ?? $originalData['userId']
            )->setDescription($data['description'] ?? $originalData['description'])
            ->setTakenAt($data['takenAt'] ?? $originalData['takenAt'])->setIsFavorite(
                $data['isFavorite'] ?? $originalData['isFavorite']
            )->build();
        $query = "UPDATE photos SET name = :name, description = :description, url = :url, publicId = :publicId, size = :size, updatedAt = :updatedAt, takenAt = :takenAt, isFavorite = :isFavorite WHERE id = :id";
        $statement = $this->database->getConnection()->prepare($query);
        $statement->execute([
            ':id' => $id,
            ':name' => $photoEntity->getName(),
            ':description' => $photoEntity->getDescription(),
            ':url' => $photoEntity->getUrl(),
            ':publicId' => $photoEntity->getPublicId(),
            ':size' => $photoEntity->getSize(),
            ':updatedAt' => $photoEntity->getUpdatedAt(),
            ':takenAt' => $photoEntity->getTakenAt(),
            ':isFavorite' => $photoEntity->getIsFavorite() ? 1 : 0,
        ]);
        return $this->findOne($id);
    }

    public function delete(string $id): int
    {
        $query = "DELETE FROM photos WHERE id = :id";
        $statement = $this->database->getConnection()->prepare($query);
        $statement->execute([
            ':id' => $id,
        ]);
        return $statement->rowCount();
    }

    public function findUserPhotos(string $userId): array
    {
        $query = "SELECT * FROM photos WHERE userId = :userId 
        ORDER BY takenAt DESC";
        $statement = $this->database->getConnection()->prepare($query);
        $statement->execute([
            ':userId' => $userId,
        ]);
        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }

    public function findUsersPhotos(string $userId, int $page, int $limit): false|array
    {
        $offset = ($page - 1) * $limit;
        $query = "SELECT * FROM photos WHERE userId = :userId ORDER BY takenAt DESC LIMIT :limit OFFSET :offset";
        $statement = $this->database->getConnection()->prepare($query);
        $statement->bindValue(':userId', $userId, PDO::PARAM_STR);
        $statement->bindValue(':limit', $limit, PDO::PARAM_INT);
        $statement->bindValue(':offset', $offset, PDO::PARAM_INT);
        $statement->execute();
        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }

    public function findUserPhoto(string $id, string $userId): array
    {
        $query = "SELECT * FROM photos WHERE id = :id AND userId = :userId";
        $statement = $this->database->getConnection()->prepare($query);
        $statement->execute([
            ':id' => $id,
            ':userId' => $userId,
        ]);
        $result = $statement->fetch(PDO::FETCH_ASSOC);
        if (!$result) {
            return [];
        }
        $photos = $this->findUserPhotos($userId);
        $index = array_search($result, $photos);
        $result['previous'] = $photos[$index - 1]['id'] ?? null;
        $result['next'] = $photos[$index + 1]['id'] ?? null;
        return $result;
    }
}
