<?php

namespace App\Entities;

class PhotoCategoryEntity extends BaseEntity
{
    private string $photoId;
    private string $categoryId;

    public function getPhotoId(): string
    {
        return $this->photoId;
    }

    public function setPhotoId(string $photoId): PhotoCategoryEntity
    {
        $this->photoId = $photoId;
        return $this;
    }

    public function getCategoryId(): string
    {
        return $this->categoryId;
    }

    public function setCategoryId(string $categoryId): PhotoCategoryEntity
    {
        $this->categoryId = $categoryId;
        return $this;
    }
}
