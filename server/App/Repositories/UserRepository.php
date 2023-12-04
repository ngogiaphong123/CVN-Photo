<?php

namespace App\Repositories;

use App\Core\Config;
use App\Core\Database;
use App\Entities\UserEntity;
use App\Exceptions\HttpException;
use PDO;

class UserRepository implements IRepository {
	static string $tableName = 'users';

	public function __construct (
		private readonly Database $database,
		private readonly Config   $config,
	) {}

	/**
	 * @throws HttpException
	 */
	public function create (array $data) {
		$userEntity = new UserEntity();
		$userEntity->setEmail($data['email'])->setDisplayName($data['displayName'])->setPassword($data["password"])
			->setAvatar($this->config::get('user')['default']['avatar'])->setAvatarPublicId($this->config::get('user')['default']['avatarPublicId'])
			->setAccessToken("")->setRefreshToken("")
			->build();
		$query = "INSERT INTO users (id, email, displayName, password, avatar, avatarPublicId, accessToken, refreshToken, createdAt, updatedAt) 
		VALUES (:id, :email, :displayName, :password, :avatar, :avatarPublicId, :accessToken, :refreshToken, :createdAt, :updatedAt)";
		$statement = $this->database->getConnection()->prepare($query);
		$statement->execute([
			':id' => $userEntity->getId(),
			':email' => $userEntity->getEmail(),
			':displayName' => $userEntity->getDisplayName(),
			':password' => $userEntity->getPassword(),
			':avatar' => $userEntity->getAvatar(),
			':avatarPublicId' => $userEntity->getAvatarPublicId(),
			':accessToken' => $userEntity->getAccessToken(),
			':refreshToken' => $userEntity->getRefreshToken(),
			':createdAt' => $userEntity->getCreatedAt(),
			':updatedAt' => $userEntity->getUpdatedAt(),
		]);
		return $this->findOne($userEntity->getId());
	}

	public function findOne (string $id) {
		$query = "SELECT * FROM users WHERE id = :id";
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
	public function findOneByEmail (string $email) {
		$userEntity = new UserEntity();
		$userEntity->setEmail($email)->build();
		$query = "SELECT * FROM users WHERE email = :email";
		$statement = $this->database->getConnection()->prepare($query);
		$statement->execute([
			':email' => $userEntity->getEmail(),
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
	public function update (string $id, array $data, array $user) {
		$userEntity = new UserEntity();
		$userEntity->setEmail($data['email'] ?? $user['email'])->setDisplayName($data['displayName'] ?? $user['displayName'])
			->setAvatar($data['avatar'] ?? $user['avatar'])->setAvatarPublicId($data['avatarPublicId'] ?? $user['avatarPublicId'])
			->setAccessToken($data['accessToken'] ?? $user['accessToken'])->setRefreshToken($data['refreshToken'] ?? $user['refreshToken'])
			->build();
		$query = "UPDATE users SET email = :email, displayName = :displayName, avatar = :avatar, avatarPublicId = :avatarPublicId, accessToken = :accessToken, refreshToken = :refreshToken, updatedAt = :updatedAt WHERE id = :id";
		$statement = $this->database->getConnection()->prepare($query);
		$statement->execute([
			':id' => $id,
			':email' => $userEntity->getEmail(),
			':displayName' => $userEntity->getDisplayName(),
			':avatar' => $userEntity->getAvatar(),
			':avatarPublicId' => $userEntity->getAvatarPublicId(),
			':accessToken' => $userEntity->getAccessToken(),
			':refreshToken' => $userEntity->getRefreshToken(),
			':updatedAt' => $userEntity->getUpdatedAt(),
		]);
		return $this->findOne($id);
	}
}
