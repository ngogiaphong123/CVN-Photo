<?php

namespace Services;

use App\Common\Enums\DefaultCategory;
use App\Common\Error\CategoryError;
use App\Exceptions\HttpException;
use App\Repositories\CategoryRepository;
use App\Services\CategoryService;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class CategoryServiceTest extends TestCase {
	private CategoryService $categoryService;
	private MockObject $categoryRepositoryMock;

	/**
	 * @throws Exception
	 */
	public function setUp (): void {
		$this->categoryRepositoryMock = $this->createMock(CategoryRepository::class);
		$this->categoryService = new CategoryService($this->categoryRepositoryMock);
	}

	/**
	 * @throws HttpException
	 */
	public function testUpdate () {
		$this->categoryRepositoryMock->expects($this->once())->method('findOne')->willReturn([
			'id' => '1',
			'userId' => '1',
			'name' => 'Category 1',
			'memo' => 'Uncategorized photos',
			'url' => 'https://res.cloudinary.com/dt9pwfpi5/image/upload/v1629968239/avatars/1.jpg',
			'publicId' => 'avatars/1.jpg',
			'createdAt' => '2021-08-26 00:00:00',
			'updatedAt' => '2021-08-26 00:00:00',
		]);
		$this->categoryRepositoryMock->expects($this->once())->method('update')->with('1', [
			'name' => 'Category 1',
			'memo' => 'Uncategorized photos',
			'url' => 'https://res.cloudinary.com/dt9pwfpi5/image/upload/v1629968239/avatars/1.jpg',
			'publicId' => 'avatars/1.jpg',
		], [
			'id' => '1',
			'userId' => '1',
			'name' => 'Category 1',
			'memo' => 'Uncategorized photos',
			'url' => 'https://res.cloudinary.com/dt9pwfpi5/image/upload/v1629968239/avatars/1.jpg',
			'publicId' => 'avatars/1.jpg',
			'createdAt' => '2021-08-26 00:00:00',
			'updatedAt' => '2021-08-26 00:00:00',
		]);
		$this->categoryService->update('1', [
			'name' => 'Category 1',
			'memo' => 'Uncategorized photos',
			'url' => 'https://res.cloudinary.com/dt9pwfpi5/image/upload/v1629968239/avatars/1.jpg',
			'publicId' => 'avatars/1.jpg',
			'test' => 'test',
		], '1');
	}

	public function testCheckOwnerAndNameWhenCategoryNotFound () {
		$this->categoryRepositoryMock->method('findOne')->willReturn(NULL);
		$this->expectException(HttpException::class);
		$this->expectExceptionMessage(CategoryError::CATEGORY_NOT_FOUND->value);
		$this->categoryService->checkOwnerAndName('1', '1');
	}

	public function testCheckOwnerAndNameWhenInvalidUserId () {
		$this->categoryRepositoryMock->method('findOne')->willReturn([
			'id' => '1',
			'userId' => '1',
		]);
		$this->expectException(HttpException::class);
		$this->expectExceptionMessage(CategoryError::CATEGORY_NOT_FOUND->value);
		$this->categoryService->checkOwnerAndName('1', '2');
	}

	public function testCheckOwnerAndNameWhenNameIsUncategorized () {
		$this->categoryRepositoryMock->method('findOne')->willReturn([
			'id' => '1',
			'userId' => '1',
			'name' => DefaultCategory::UNCATEGORIZED->value,
		]);
		$this->expectException(HttpException::class);
		$this->expectExceptionMessage(CategoryError::CATEGORY_NOT_ALLOWED->value);
		$this->categoryService->checkOwnerAndName('1', '1');
	}

	public function testCheckOwnerAndNameWhenNameIsFavorite () {
		$this->categoryRepositoryMock->method('findOne')->willReturn([
			'id' => '1',
			'userId' => '1',
			'name' => DefaultCategory::FAVORITE->value,
		]);
		$this->expectException(HttpException::class);
		$this->expectExceptionMessage(CategoryError::CATEGORY_NOT_ALLOWED->value);
		$this->categoryService->checkOwnerAndName('1', '1');
	}

	/**
	 * @throws HttpException
	 */
	public function testCheckOwnerAndNameSuccess () {
		$this->categoryRepositoryMock->method('findOne')->willReturn([
			'id' => '1',
			'userId' => '1',
			'name' => 'Category 1',
		]);
		$result = $this->categoryService->checkOwnerAndName('1', '1');
		$this->assertEquals([
			'id' => '1',
			'userId' => '1',
			'name' => 'Category 1',
		], $result);
	}

	public function testFindUserCategories () {
		$this->categoryRepositoryMock->method('findUserCategories')->willReturn([
			[
				'id' => '1',
				'userId' => '1',
				'name' => DefaultCategory::UNCATEGORIZED->value,
				'memo' => 'Uncategorized photos',
				'url' => 'https://res.cloudinary.com/dt9pwfpi5/image/upload/v1629968239/avatars/1.jpg',
				'publicId' => 'avatars/1.jpg',
				'createdAt' => '2021-08-26 00:00:00',
				'updatedAt' => '2021-08-26 00:00:00',
				'photoCount' => '1',
			],
		]);
		$result = $this->categoryService->findUserCategories('1');
		$this->assertEquals([
			[
				'id' => '1',
				'userId' => '1',
				'name' => DefaultCategory::UNCATEGORIZED->value,
				'memo' => 'Uncategorized photos',
				'url' => 'https://res.cloudinary.com/dt9pwfpi5/image/upload/v1629968239/avatars/1.jpg',
				'publicId' => 'avatars/1.jpg',
				'createdAt' => '2021-08-26 00:00:00',
				'updatedAt' => '2021-08-26 00:00:00',
				'photoCount' => '1',
			],
		], $result);
	}

	/**
	 * @throws HttpException
	 */
	public function testFindOneWhenCategoryNotFound () {
		$this->categoryRepositoryMock->method('findOne')->willReturn(NULL);
		$this->expectException(HttpException::class);
		$this->expectExceptionMessage(CategoryError::CATEGORY_NOT_FOUND->value);
		$this->categoryService->findOne('1', '1');
	}

	public function testFindOneWhenInvalidUserId () {
		$this->categoryRepositoryMock->method('findOne')->willReturn([
			'id' => '1',
			'userId' => '1',
		]);
		$this->expectException(HttpException::class);
		$this->expectExceptionMessage(CategoryError::CATEGORY_NOT_FOUND->value);
		$this->categoryService->findOne('1', '2');
	}

	public function testFindOneSuccess () {
		$this->categoryRepositoryMock->method('findOne')->willReturn([
			'id' => '1',
			'userId' => '1',
			'name' => 'Category 1',
			'memo' => 'Uncategorized photos',
			'url' => 'https://res.cloudinary.com/dt9pwfpi5/image/upload/v1629968239/avatars/1.jpg',
			'publicId' => 'avatars/1.jpg',
			'createdAt' => '2021-08-26 00:00:00',
			'updatedAt' => '2021-08-26 00:00:00',
		]);
		$result = $this->categoryService->findOne('1', '1');
		$this->assertEquals([
			'id' => '1',
			'userId' => '1',
			'name' => 'Category 1',
			'memo' => 'Uncategorized photos',
			'url' => 'https://res.cloudinary.com/dt9pwfpi5/image/upload/v1629968239/avatars/1.jpg',
			'publicId' => 'avatars/1.jpg',
			'createdAt' => '2021-08-26 00:00:00',
			'updatedAt' => '2021-08-26 00:00:00',
		], $result);
	}

	public function testFindCategoryPhotosByPageWithInvalidPage () {
		$this->expectException(HttpException::class);
		$this->expectExceptionMessage(CategoryError::INVALID_PAGE_OR_LIMIT->value);
		$this->categoryService->findCategoryPhotosByPage('1', '1', 'a', '10');
	}

	public function testFindCategoryPhotosByPageWithInvalidLimit () {
		$this->expectException(HttpException::class);
		$this->expectExceptionMessage(CategoryError::INVALID_PAGE_OR_LIMIT->value);
		$this->categoryService->findCategoryPhotosByPage('1', '1', '1', 'a');
	}

	public function testFindCategoryPhotosByPageWhenCategoryNotFound () {
		$this->categoryRepositoryMock->method('findOne')->willReturn(NULL);
		$this->expectException(HttpException::class);
		$this->expectExceptionMessage(CategoryError::CATEGORY_NOT_FOUND->value);
		$this->categoryService->findCategoryPhotosByPage('1', '1', '1', '10');
	}

	public function testFindCategoryPhotosByPageWithInvalidUserId () {
		$this->categoryRepositoryMock->method('findOne')->willReturn([
			'id' => '1',
			'userId' => '1',
		]);
		$this->expectException(HttpException::class);
		$this->expectExceptionMessage(CategoryError::CATEGORY_NOT_FOUND->value);
		$this->categoryService->findCategoryPhotosByPage('1', '2', '1', '10');
	}

	/**
	 * @throws HttpException
	 */
	public function testFindCategoryPhotosByPageSuccess () {
		$this->categoryRepositoryMock->method('findOne')->willReturn([
			'id' => '1',
			'userId' => '1',
		]);
		$this->categoryRepositoryMock->method('findCategoryPhotosByPage')->willReturn([
			[
				'id' => '1',
				'name' => 'name',
				'userId' => '1',
				'publicId' => 'avatars/1.jpg',
				'url' => 'https://res.cloudinary.com/dt9pwfpi5/image/upload/v1629968239/avatars/1.jpg',
				'size' => 123456,
				'description' => '',
				'takenAt' => "2021-08-26 00:00:00",
				'createdAt' => '2021-08-26 00:00:00',
				'updatedAt' => '2021-08-26 00:00:00',
			],
		]);
		$result = $this->categoryService->findCategoryPhotosByPage('1', '1', '1', '1');
		$this->assertEquals([
			[
				'id' => '1',
				'name' => 'name',
				'userId' => '1',
				'publicId' => 'avatars/1.jpg',
				'url' => 'https://res.cloudinary.com/dt9pwfpi5/image/upload/v1629968239/avatars/1.jpg',
				'size' => 123456,
				'description' => '',
				'takenAt' => "2021-08-26 00:00:00",
				'createdAt' => '2021-08-26 00:00:00',
				'updatedAt' => '2021-08-26 00:00:00',
			],
		], $result);
	}

	/**
	 * @throws HttpException
	 */
	public function testCreate () {
		$this->categoryRepositoryMock->method('findOneByName')->willReturn(NULL);
		$this->categoryRepositoryMock->method('create')->willReturn([
			'id' => '1',
			'userId' => '1',
			'name' => 'Category 1',
			'memo' => 'Uncategorized photos',
			'url' => 'https://res.cloudinary.com/dt9pwfpi5/image/upload/v1629968239/avatars/1.jpg',
			'publicId' => 'avatars/1.jpg',
			'createdAt' => '2021-08-26 00:00:00',
			'updatedAt' => '2021-08-26 00:00:00',
		]);
		$result = $this->categoryService->create([
			'name' => 'Category 1',
			'memo' => 'Uncategorized photos',
			'url' => 'https://res.cloudinary.com/dt9pwfpi5/image/upload/v1629968239/avatars/1.jpg',
			'publicId' => 'avatars/1.jpg',
		], '1');
		$this->assertEquals([
			'id' => '1',
			'userId' => '1',
			'name' => 'Category 1',
			'memo' => 'Uncategorized photos',
			'url' => 'https://res.cloudinary.com/dt9pwfpi5/image/upload/v1629968239/avatars/1.jpg',
			'publicId' => 'avatars/1.jpg',
			'createdAt' => '2021-08-26 00:00:00',
			'updatedAt' => '2021-08-26 00:00:00',
		], $result);
	}

	public function testCreateWithExistingName () {
		$this->categoryRepositoryMock->method('findOneByName')->willReturn([
			'id' => '1',
			'userId' => '1',
			'name' => 'Category 1',
			'memo' => 'Uncategorized photos',
			'url' => 'https://res.cloudinary.com/dt9pwfpi5/image/upload/v1629968239/avatars/1.jpg',
			'publicId' => 'avatars/1.jpg',
			'createdAt' => '2021-08-26 00:00:00',
			'updatedAt' => '2021-08-26 00:00:00',
		]);
		$this->expectException(HttpException::class);
		$this->expectExceptionMessage(CategoryError::CATEGORY_ALREADY_EXISTS->value);
		$this->categoryService->create([
			'name' => 'Category 1',
			'memo' => 'Uncategorized photos',
			'url' => 'https://res.cloudinary.com/dt9pwfpi5/image/upload/v1629968239/avatars/1.jpg',
			'publicId' => 'avatars/1.jpg',
		], '1');
	}

	public function testFindPhotosNotInCategoryWhenCategoryNotFound () {
		$this->categoryRepositoryMock->method('findOne')->willReturn(NULL);
		$this->expectException(HttpException::class);
		$this->expectExceptionMessage(CategoryError::CATEGORY_NOT_FOUND->value);
		$this->categoryService->findPhotosNotInCategory('1', '1');
	}

	public function testFindPhotosNotInCategoryWithInvalidUserId () {
		$this->categoryRepositoryMock->method('findOne')->willReturn([
			'id' => '1',
			'userId' => '1',
		]);
		$this->expectException(HttpException::class);
		$this->expectExceptionMessage(CategoryError::CATEGORY_NOT_FOUND->value);
		$this->categoryService->findPhotosNotInCategory('1', '2');
	}

	/**
	 * @throws HttpException
	 */
	public function testFindPhotosNotInCategorySuccess () {
		$this->categoryRepositoryMock->method('findOne')->willReturn([
			'id' => '1',
			'userId' => '1',
		]);
		$this->categoryRepositoryMock->method('findPhotosNotInCategory')->willReturn([
			[
				'id' => '1',
				'name' => 'name',
				'userId' => '1',
				'publicId' => 'avatars/1.jpg',
				'url' => 'https://res.cloudinary.com/dt9pwfpi5/image/upload/v1629968239/avatars/1.jpg',
				'size' => 123456,
				'description' => '',
				'takenAt' => "2021-08-26 00:00:00",
				'createdAt' => '2021-08-26 00:00:00',
				'updatedAt' => '2021-08-26 00:00:00',
			],
		]);
		$result = $this->categoryService->findPhotosNotInCategory('1', '1');
		$this->assertEquals([
			[
				'id' => '1',
				'name' => 'name',
				'userId' => '1',
				'publicId' => 'avatars/1.jpg',
				'url' => 'https://res.cloudinary.com/dt9pwfpi5/image/upload/v1629968239/avatars/1.jpg',
				'size' => 123456,
				'description' => '',
				'takenAt' => "2021-08-26 00:00:00",
				'createdAt' => '2021-08-26 00:00:00',
				'updatedAt' => '2021-08-26 00:00:00',
			],
		], $result);
	}

	/**
	 * @throws HttpException
	 */
	public function testFindCategoryPhotosWhenCategoryNotFound () {
		$this->categoryRepositoryMock->method('findOne')->willReturn(NULL);
		$this->expectException(HttpException::class);
		$this->expectExceptionMessage(CategoryError::CATEGORY_NOT_FOUND->value);
		$this->categoryService->findCategoryPhotos('1', '1');
	}

	public function testFindCategoryPhotosWithInvalidUserId () {
		$this->categoryRepositoryMock->method('findOne')->willReturn([
			'id' => '1',
			'userId' => '1',
		]);
		$this->expectException(HttpException::class);
		$this->expectExceptionMessage(CategoryError::CATEGORY_NOT_FOUND->value);
		$this->categoryService->findCategoryPhotos('1', '2');
	}

	/**
	 * @throws HttpException
	 */
	public function testFindCategoryPhotosSuccess () {
		$this->categoryRepositoryMock->method('findOne')->willReturn([
			'id' => '1',
			'userId' => '1',
		]);
		$this->categoryRepositoryMock->method('findCategoryPhotos')->willReturn([
			[
				'id' => '1',
				'name' => 'name',
				'userId' => '1',
				'publicId' => 'avatars/1.jpg',
				'url' => 'https://res.cloudinary.com/dt9pwfpi5/image/upload/v1629968239/avatars/1.jpg',
				'size' => 123456,
				'description' => '',
				'takenAt' => "2021-08-26 00:00:00",
				'createdAt' => '2021-08-26 00:00:00',
				'updatedAt' => '2021-08-26 00:00:00',
			],
		]);
		$result = $this->categoryService->findCategoryPhotos('1', '1');
		$this->assertEquals([
			[
				'id' => '1',
				'name' => 'name',
				'userId' => '1',
				'publicId' => 'avatars/1.jpg',
				'url' => 'https://res.cloudinary.com/dt9pwfpi5/image/upload/v1629968239/avatars/1.jpg',
				'size' => 123456,
				'description' => '',
				'takenAt' => "2021-08-26 00:00:00",
				'createdAt' => '2021-08-26 00:00:00',
				'updatedAt' => '2021-08-26 00:00:00',
			],
		], $result);
	}

	/**
	 * @throws HttpException
	 */
	public function testDelete () {
		$this->categoryRepositoryMock->method('findOne')->willReturn([
			'id' => '1',
			'userId' => '1',
			'name' => 'Category 1',
		]);
		$this->categoryRepositoryMock->expects($this->once())->method('delete')->with('1');
		$this->categoryService->delete('1', '1');
	}

	public function testFindPhotosNotInCategoryByPageWithInvalidPage () {
		$this->expectException(HttpException::class);
		$this->expectExceptionMessage(CategoryError::INVALID_PAGE_OR_LIMIT->value);
		$this->categoryService->findPhotosNotInCategoryByPage('1', '1', 'a', '10');
	}

	public function testFindPhotosNotInCategoryByPageWithInvalidLimit () {
		$this->expectException(HttpException::class);
		$this->expectExceptionMessage(CategoryError::INVALID_PAGE_OR_LIMIT->value);
		$this->categoryService->findPhotosNotInCategoryByPage('1', '1', '1', 'a');
	}

	public function testFindPhotosNotInCategoryByPageWhenCategoryNotFound () {
		$this->categoryRepositoryMock->method('findOne')->willReturn(NULL);
		$this->expectException(HttpException::class);
		$this->expectExceptionMessage(CategoryError::CATEGORY_NOT_FOUND->value);
		$this->categoryService->findPhotosNotInCategoryByPage('1', '1', '1', '10');
	}

	public function testFindPhotosNotInCategoryByPageWithInvalidUserId () {
		$this->categoryRepositoryMock->method('findOne')->willReturn([
			'id' => '1',
			'userId' => '1',
		]);
		$this->expectException(HttpException::class);
		$this->expectExceptionMessage(CategoryError::CATEGORY_NOT_FOUND->value);
		$this->categoryService->findPhotosNotInCategoryByPage('1', '2', '1', '10');
	}

	/**
	 * @throws HttpException
	 */
	public function testFindPhotosNotInCategoryByPageSuccess () {
		$this->categoryRepositoryMock->method('findOne')->willReturn([
			'id' => '1',
			'userId' => '1',
		]);
		$this->categoryRepositoryMock->method('findPhotosNotInCategoryByPage')->willReturn([
			[
				'id' => '1',
				'name' => 'name',
				'userId' => '1',
				'publicId' => 'avatars/1.jpg',
				'url' => 'https://res.cloudinary.com/dt9pwfpi5/image/upload/v1629968239/avatars/1.jpg',
				'size' => 123456,
				'description' => '',
				'takenAt' => "2021-08-26 00:00:00",
				'createdAt' => '2021-08-26 00:00:00',
				'updatedAt' => '2021-08-26 00:00:00',
			],
		]);
		$result = $this->categoryService->findPhotosNotInCategoryByPage('1', '1', '1', '1');
		$this->assertEquals([
			[
				'id' => '1',
				'name' => 'name',
				'userId' => '1',
				'publicId' => 'avatars/1.jpg',
				'url' => 'https://res.cloudinary.com/dt9pwfpi5/image/upload/v1629968239/avatars/1.jpg',
				'size' => 123456,
				'description' => '',
				'takenAt' => "2021-08-26 00:00:00",
				'createdAt' => '2021-08-26 00:00:00',
				'updatedAt' => '2021-08-26 00:00:00',
			],
		], $result);
	}
}
