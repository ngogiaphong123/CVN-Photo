<?php

namespace Services;

use App\Common\Enums\DefaultCategory;
use App\Common\Error\CategoryError;
use App\Common\Error\PhotoError;
use App\Exceptions\HttpException;
use App\Repositories\CategoryRepository;
use App\Repositories\PhotoCategoryRepository;
use App\Repositories\PhotoRepository;
use App\Services\PhotoCategoryService;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class PhotoCategoryServiceTest extends TestCase
{
    private PhotoCategoryService $photoCategoryService;
    private MockObject $photoCategoryRepositoryMock;
    private MockObject $photoRepositoryMock;
    private MockObject $categoryRepositoryMock;

    /**
     * @throws Exception
     */
    public function setUp(): void
    {
        $this->photoCategoryRepositoryMock = $this->createMock(PhotoCategoryRepository::class);
        $this->photoRepositoryMock = $this->createMock(PhotoRepository::class);
        $this->categoryRepositoryMock = $this->createMock(CategoryRepository::class);
        $this->photoCategoryService = new PhotoCategoryService(
            $this->photoCategoryRepositoryMock,
            $this->photoRepositoryMock,
            $this->categoryRepositoryMock
        );
    }

    public function testCheckPhotoCategoryOwnerWhenPhotoNotFound()
    {
        $this->photoRepositoryMock->method('findOne')->willReturn(null);
        $this->expectException(HttpException::class);
        $this->expectExceptionMessage(PhotoError::PHOTO_NOT_FOUND->value);
        $this->photoCategoryService->checkPhotoCategoryOwner([
            'photoId' => '1',
            'categoryId' => '1',
        ], '1');
    }

    public function testCheckPhotoCategoryOwnerWhenCategoryNotFound()
    {
        $this->photoRepositoryMock->method('findOne')->willReturn([
            'id' => '1',
            'userId' => '1',
        ]);
        $this->categoryRepositoryMock->method('findOne')->willReturn(null);
        $this->expectException(HttpException::class);
        $this->expectExceptionMessage(CategoryError::CATEGORY_NOT_FOUND->value);
        $this->photoCategoryService->checkPhotoCategoryOwner([
            'photoId' => '1',
            'categoryId' => '1',
        ], '1');
    }

    /**
     * @throws HttpException
     */
    public function testCheckPhotoCategoryOwnerSuccess()
    {
        $this->photoRepositoryMock->method('findOne')->willReturn([
            'id' => '1',
            'userId' => '1',
        ]);
        $this->categoryRepositoryMock->method('findOne')->willReturn([
            'id' => '1',
            'userId' => '1',
        ]);
        $result = $this->photoCategoryService->checkPhotoCategoryOwner([
            'photoId' => '1',
            'categoryId' => '1',
        ], '1');
        $this->assertEquals([
            'photo' => [
                'id' => '1',
                'userId' => '1',
            ],
            'category' => [
                'id' => '1',
                'userId' => '1',
            ],
        ], $result);
    }

    public function testRemovePhotoFromCategoryWhenCategoryIsUncategorized()
    {
        $this->photoRepositoryMock->method('findOne')->willReturn([
            'id' => '1',
            'userId' => '1',

        ]);
        $this->categoryRepositoryMock->method('findOne')->willReturn([
            'id' => '1',
            'userId' => '1',
            'name' => DefaultCategory::UNCATEGORIZED->value,
        ]);
        $this->expectException(HttpException::class);
        $this->expectExceptionMessage(CategoryError::CANNOT_REMOVE_FROM_UNCATEGORIZED->value);
        $this->photoCategoryService->removePhotoFromCategory([
            'photoId' => '1',
            'categoryId' => '1',
        ], '1');
    }

    public function testRemovePhotoFromCategoryWhenPhotoNotInCategory()
    {
        $this->photoRepositoryMock->method('findOne')->willReturn([
            'id' => '1',
            'userId' => '1',

        ]);
        $this->categoryRepositoryMock->method('findOne')->willReturn([
            'id' => '1',
            'userId' => '1',
            'name' => "test",
        ]);
        $this->photoCategoryRepositoryMock->method('findOne')->willReturn([]);
        $this->expectException(HttpException::class);
        $this->expectExceptionMessage(CategoryError::PHOTO_NOT_IN_CATEGORY->value);
        $this->photoCategoryService->removePhotoFromCategory([
            'photoId' => '1',
            'categoryId' => '1',
        ], '1');
    }

    /**
     * @throws HttpException
     */
    public function testRemovePhotoFromCategoryWhenCategoryIsFavorite()
    {
        $this->photoRepositoryMock->method('findOne')->willReturn([
            'id' => '1',
            'userId' => '1',

        ]);
        $this->categoryRepositoryMock->method('findOne')->willReturn([
            'id' => '1',
            'userId' => '1',
            'name' => DefaultCategory::FAVORITE->value,
        ]);
        $this->photoCategoryRepositoryMock->method('findOne')->willReturn([
            'id' => '1',
            'userId' => '1',
        ]);
        $this->photoRepositoryMock->method('update')->willReturn([
            'id' => '1',
            'userId' => '1',
            'isFavorite' => false,
        ]);
        $this->photoCategoryRepositoryMock->method('delete')->willReturn([
            'id' => '1',
            'userId' => '1',
        ]);
        $result = $this->photoCategoryService->removePhotoFromCategory([
            'photoId' => '1',
            'categoryId' => '1',
        ], '1');
        $this->assertEquals([
            'id' => '1',
            'userId' => '1',
        ], $result);
    }

    /**
     * @throws HttpException
     */
    public function testRemovePhotoFromCategorySuccess()
    {
        $this->photoRepositoryMock->method('findOne')->willReturn([
            'id' => '1',
            'userId' => '1',

        ]);
        $this->categoryRepositoryMock->method('findOne')->willReturn([
            'id' => '1',
            'userId' => '1',
            'name' => "test",
        ]);
        $this->photoCategoryRepositoryMock->method('findOne')->willReturn([
            'id' => '1',
            'userId' => '1',
        ]);
        $this->photoCategoryRepositoryMock->method('delete')->willReturn([
            'id' => '1',
            'userId' => '1',
        ]);
        $result = $this->photoCategoryService->removePhotoFromCategory([
            'photoId' => '1',
            'categoryId' => '1',
        ], '1');
        $this->assertEquals([
            'id' => '1',
            'userId' => '1',
        ], $result);
    }

    /**
     * @throws HttpException
     */
    public function testAddPhotoToCategoryWhenCategoryIsFavorite()
    {
        $this->photoRepositoryMock->method('findOne')->willReturn([
            'id' => '1',
            'userId' => '1',

        ]);
        $this->categoryRepositoryMock->method('findOne')->willReturn([
            'id' => '1',
            'userId' => '1',
            'name' => DefaultCategory::FAVORITE->value,
        ]);
        $this->photoCategoryRepositoryMock->method('findOne')->willReturn([]);
        $this->photoRepositoryMock->method('update')->willReturn([
            'id' => '1',
            'userId' => '1',
            'isFavorite' => true,
        ]);
        $this->photoCategoryRepositoryMock->method('create')->willReturn([
            'id' => '1',
            'userId' => '1',
        ]);
        $result = $this->photoCategoryService->addPhotoToCategory([
            'photoId' => '1',
            'categoryId' => '1',
        ], '1');
        $this->assertEquals([
            'id' => '1',
            'userId' => '1',
        ], $result);
    }

    public function testAddPhotoToCategoryWhenPhotoAlreadyInCategory()
    {
        $this->photoRepositoryMock->method('findOne')->willReturn([
            'id' => '1',
            'userId' => '1',

        ]);
        $this->categoryRepositoryMock->method('findOne')->willReturn([
            'id' => '1',
            'userId' => '1',
            'name' => "test",
        ]);
        $this->photoCategoryRepositoryMock->method('findOne')->willReturn([
            'id' => '1',
            'userId' => '1',
        ]);
        $this->expectException(HttpException::class);
        $this->expectExceptionMessage(CategoryError::PHOTO_ALREADY_IN_CATEGORY->value);
        $this->photoCategoryService->addPhotoToCategory([
            'photoId' => '1',
            'categoryId' => '1',
        ], '1');
    }

    /**
     * @throws HttpException
     */
    public function testAddPhotoToCategorySuccess()
    {
        $this->photoRepositoryMock->method('findOne')->willReturn([
            'id' => '1',
            'userId' => '1',

        ]);
        $this->categoryRepositoryMock->method('findOne')->willReturn([
            'id' => '1',
            'userId' => '1',
            'name' => "test",
        ]);
        $this->photoCategoryRepositoryMock->method('findOne')->willReturn([]);
        $this->photoCategoryRepositoryMock->method('create')->willReturn([
            'id' => '1',
            'userId' => '1',
        ]);
        $result = $this->photoCategoryService->addPhotoToCategory([
            'photoId' => '1',
            'categoryId' => '1',
        ], '1');
        $this->assertEquals([
            'id' => '1',
            'userId' => '1',
        ], $result);
    }
}
