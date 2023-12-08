<?php

namespace App\Entities;

use App\Common\Enums\StatusCode;
use App\Common\Error\PhotoError;
use App\Common\Validator\Validator;
use App\Exceptions\HttpException;

class PhotoEntity extends BaseEntity {
	private string $name;
	private string $description;
	private string $url;
	private string $publicId;
	private int $size;
	private string $userId;

	private string $takenAt;

	private string $createdAt;
	private string $updatedAt;

	public function __construct () {
		parent::__construct();
	}

	public function build (): PhotoEntity {
		$this->setCreatedAt(date('Y-m-d H:i:s'))->setUpdatedAt(date('Y-m-d H:i:s'));
		return $this;
	}

	public function getName (): string {
		return $this->name;
	}

	public function setName (string $name): PhotoEntity {
		$this->name = $name;
		return $this;
	}

	public function getUrl (): string {
		return $this->url;
	}

	public function setUrl (string $url): PhotoEntity {
		$this->url = $url;
		return $this;
	}

	public function getPublicId (): string {
		return $this->publicId;
	}

	public function setPublicId (string $publicId): PhotoEntity {
		$this->publicId = $publicId;
		return $this;
	}

	public function getSize (): int {
		return $this->size;
	}

	public function setSize (int $size): PhotoEntity {
		$this->size = $size;
		return $this;
	}

	public function getUserId (): string {
		return $this->userId;
	}

	public function setUserId (string $userId): PhotoEntity {
		$this->userId = $userId;
		return $this;
	}

	public function getCreatedAt (): string {
		return $this->createdAt;
	}

	public function setCreatedAt (string $createdAt): PhotoEntity {
		$this->createdAt = $createdAt;
		return $this;
	}

	public function getUpdatedAt (): string {
		return $this->updatedAt;
	}

	public function setUpdatedAt (string $updatedAt): PhotoEntity {
		$this->updatedAt = $updatedAt;
		return $this;
	}

	public function getDescription (): string {
		return $this->description;
	}

	public function setDescription (string $description): PhotoEntity {
		$this->description = $description;
		return $this;
	}

	public function getTakenAt (): string {
		return $this->takenAt;
	}

	/**
	 * @throws HttpException
	 */
	public function setTakenAt (string $takenAt): PhotoEntity {
		$ok = Validator::validateDate($takenAt);
		if (!$ok) {
			throw new HttpException(StatusCode::BAD_REQUEST->value, PhotoError::INVALID_DATE->value);
		}
		$this->takenAt = $takenAt;
		return $this;
	}
}
