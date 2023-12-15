<?php

namespace App\Entities;

use App\Common\Enums\StatusCode;
use App\Common\Validator\Validator;
use App\Exceptions\HttpException;

class CategoryEntity extends BaseEntity
{
    private string $name;
    private string $memo;
    private string $url;
    private string $publicId;
    private string $userId;
    private string $createdAt;
    private string $updatedAt;

    public function __construct()
    {
        parent::__construct();
    }

    public function build(): CategoryEntity
    {
        $this->setCreatedAt(date('Y-m-d H:i:s'))->setUpdatedAt(date('Y-m-d H:i:s'));
        return $this;
    }

    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @throws HttpException
     */
    public function setName(string $name): CategoryEntity
    {
        $error = Validator::validate(
            ['name' => $name],
            ['name' => 'required|min:3|max:100']
        );
        if (!empty($error)) {
            throw new HttpException(StatusCode::BAD_REQUEST->value, "Validation failed", $error);
        }
        $this->name = $name;
        return $this;
    }

    public function getMemo(): string
    {
        return $this->memo;
    }

    public function setMemo(string $memo): CategoryEntity
    {
        $this->memo = $memo;
        return $this;
    }

    public function getUrl(): string
    {
        return $this->url;
    }

    public function setUrl(string $url): CategoryEntity
    {
        $this->url = $url;
        return $this;
    }

    public function getPublicId(): string
    {
        return $this->publicId;
    }

    public function setPublicId(string $publicId): CategoryEntity
    {
        $this->publicId = $publicId;
        return $this;
    }

    public function getUserId(): string
    {
        return $this->userId;
    }

    public function setUserId(string $userId): CategoryEntity
    {
        $this->userId = $userId;
        return $this;
    }

    public function getCreatedAt(): string
    {
        return $this->createdAt;
    }

    public function setCreatedAt(string $createdAt): CategoryEntity
    {
        $this->createdAt = $createdAt;
        return $this;
    }

    public function getUpdatedAt(): string
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(string $updatedAt): CategoryEntity
    {
        $this->updatedAt = $updatedAt;
        return $this;
    }
}
