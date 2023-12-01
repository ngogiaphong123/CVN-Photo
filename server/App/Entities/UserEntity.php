<?php

namespace App\Entities;

use App\Common\Enums\StatusCode;
use App\Common\Validator\Validator;
use App\Exceptions\HttpException;

class UserEntity extends BaseEntity {
	private string $email;
	private string $displayName;
	private string $password;
	private string $avatar;
	private string $avatarPublicId;
	private string $accessToken;
	private string $refreshToken;
	private string $createdAt;
	private string $updatedAt;

	public function __construct () {
		parent::__construct();
	}
	public function build() : UserEntity {
		$this->setCreatedAt(date('Y-m-d H:i:s'));
		$this->setUpdatedAt(date('Y-m-d H:i:s'));
		return $this;
	}

	public function getEmail (): string {
		return $this->email;
	}

	/**
	 * @throws HttpException
	 */
	public function setEmail (string $email): UserEntity {
		$error = Validator::validate([
			'email' => $email,
		], [
			'email' => 'required|email',
		]);
		if(!empty($error)) {
			throw new HttpException(StatusCode::BAD_REQUEST->value, "Validation failed", $error);
		}
		$this->email = $email;
		return $this;
	}

	public function getDisplayName (): string {
		return $this->displayName;
	}

	/**
	 * @throws HttpException
	 */
	public function setDisplayName (string $displayName): UserEntity {
		$error = Validator::validate([
			'displayName' => $displayName,
		], [
			'displayName' => 'required|min:3|max:32',
		]);
		if(!empty($error)) {
			throw new HttpException(StatusCode::BAD_REQUEST->value, "Validation failed", $error);
		}
		$this->displayName = $displayName;
		return $this;
	}

	public function getPassword (): string {
		return $this->password;
	}

	public function setPassword (string $password): UserEntity {
		$error = Validator::validate([
			'password' => $password,
		], [
			'password' => 'required|min:8|max:32',
		]);
		$this->password = password_hash($password, PASSWORD_DEFAULT);
		return $this;
	}

	public function getAvatar (): string {
		return $this->avatar;
	}

	public function setAvatar (string $avatar): UserEntity {
		$this->avatar = $avatar;
		return $this;
	}

	public function getAvatarPublicId (): string {
		return $this->avatarPublicId;
	}

	public function setAvatarPublicId (string $avatarPublicId): UserEntity {
		$this->avatarPublicId = $avatarPublicId;
		return $this;
	}

	public function getAccessToken (): string {
		return $this->accessToken;
	}

	public function setAccessToken (string $accessToken): UserEntity {
		$this->accessToken = $accessToken;
		return $this;
	}

	public function getRefreshToken (): string {
		return $this->refreshToken;
	}

	public function setRefreshToken (string $refreshToken): UserEntity {
		$this->refreshToken = $refreshToken;
		return $this;
	}

	public function getCreatedAt (): string {
		return $this->createdAt;
	}

	public function setCreatedAt (string $createdAt): UserEntity {
		$this->createdAt = $createdAt;
		return $this;
	}

	public function getUpdatedAt (): string {
		return $this->updatedAt;
	}

	public function setUpdatedAt (string $updatedAt): UserEntity {
		$this->updatedAt = $updatedAt;
		return $this;
	}
	public function toArray (): array {
		return [
			'id' => $this->getId(),
			'email' => $this->getEmail(),
			'displayName' => $this->getDisplayName(),
			'avatar' => $this->getAvatar(),
			'avatarPublicId' => $this->getAvatarPublicId(),
			'createdAt' => $this->getCreatedAt(),
			'updatedAt' => $this->getUpdatedAt(),
		];
	}

}
