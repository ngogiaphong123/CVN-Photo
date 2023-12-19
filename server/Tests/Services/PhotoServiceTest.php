<?php

namespace Services;

use App\Common\Error\AuthError;
use App\Common\Error\PhotoError;
use App\Common\Error\UploadError;
use App\Exceptions\HttpException;
use App\Repositories\PhotoCategoryRepository;
use App\Repositories\PhotoRepository;
use App\Repositories\UserRepository;
use App\Services\PhotoService;
use App\Services\UploadService;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class PhotoServiceTest extends TestCase
{

    private PhotoService $photoService;
    private MockObject $photoRepositoryMock;
    private MockObject $uploadServiceMock;
    private MockObject $photoCategoryRepositoryMock;
    private MockObject $userRepositoryMock;

    /**
     * @throws Exception
     * @throws Exception
     */
    public function setUp(): void
    {
        $this->photoRepositoryMock = $this->createMock(PhotoRepository::class);
        $this->uploadServiceMock = $this->createMock(UploadService::class);
        $this->photoCategoryRepositoryMock = $this->createMock(PhotoCategoryRepository::class);
        $this->userRepositoryMock = $this->createMock(UserRepository::class);
        $this->photoService = new PhotoService(
            $this->photoRepositoryMock,
            $this->uploadServiceMock,
            $this->userRepositoryMock,
            $this->photoCategoryRepositoryMock
        );
    }

    public function testUploadWhenUserNotFound()
    {
        $this->userRepositoryMock->method('findOne')->willReturn(null);
        $this->expectException(HttpException::class);
        $this->expectExceptionMessage(AuthError::USER_DOES_NOT_EXIST->value);
        $this->photoService->upload([
            'type' => ['image/jpeg'],
            'size' => [UploadService::$MAX_FILE_SIZE - 1],
            'name' => ['name'],
            'tmp_name' => ['tmp_name'],
        ], '1');
    }

    public function testUploadWhenFileTypeIsNotAllowed()
    {
        $this->userRepositoryMock->method('findOne')->willReturn([
            'id' => '1',
        ]);
        $this->expectException(HttpException::class);
        $this->expectExceptionMessage(UploadError::FILE_TYPE_IS_NOT_ALLOWED->value);
        $this->photoService->upload([
            'type' => ['image/gif'],
            'size' => [UploadService::$MAX_FILE_SIZE - 1],
            'name' => ['name'],
            'tmp_name' => ['tmp_name'],
        ], '1');
    }

    public function testUploadWhenFileIsTooLarge()
    {
        $this->userRepositoryMock->method('findOne')->willReturn([
            'id' => '1',
        ]);
        $this->expectException(HttpException::class);
        $this->expectExceptionMessage(UploadError::FILE_IS_TOO_LARGE->value);
        $this->photoService->upload([
            'type' => ['image/jpeg'],
            'size' => [UploadService::$MAX_FILE_SIZE + 1],
            'name' => ['name'],
            'tmp_name' => ['tmp_name'],
        ], '1');
    }

    public function testUploadWhenUploadServiceThrowException()
    {
        $this->userRepositoryMock->method('findOne')->willReturn([
            'id' => '1',
        ]);
        $this->uploadServiceMock->method('upload')->willThrowException(new HttpException(400, 'Upload error'));
        $this->expectException(HttpException::class);
        $this->expectExceptionMessage('Upload error');
        $this->photoService->upload([
            'type' => ['image/jpeg'],
            'size' => [UploadService::$MAX_FILE_SIZE - 1],
            'name' => ['name'],
            'tmp_name' => ['tmp_name'],
        ], '1');
    }

    /**
     * @throws HttpException
     */
    public function testUploadSuccessfully()
    {
        $this->userRepositoryMock->method('findOne')->willReturn([
            'id' => '1',
        ]);
        $this->uploadServiceMock->method('upload')->willReturn([
            'secureUrl' => 'https://res.cloudinary.com/dt9pwfpi5/image/upload/v1629968239/avatars/1.jpg',
            'publicId' => 'avatars/1.jpg',
        ]);
        $this->photoRepositoryMock->method('create')->willReturn([
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
        ]);
        $this->photoCategoryRepositoryMock->method('addToUncategorized')->willReturn([
            'photoId' => '1',
            'categoryId' => '1',
        ]);
        $result = $this->photoService->upload([
            'type' => ['image/jpeg'],
            'size' => [UploadService::$MAX_FILE_SIZE - 1],
            'name' => ['name'],
            'tmp_name' => ['tmp_name'],
        ], '1');
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

    public function testFindUserPhotos()
    {
        $this->photoRepositoryMock->method('findUserPhotos')->willReturn([
            [
                'id' => '1',
                'name' => 'name',
                'userId' => '1',
                'publicId' => 'avatars/1.jpg',
                'url' => 'https://res.cloudinary.com/dt9pwfpi5/image/upload/v1629968239/avatars/1.jpg',
                'size' => 123456,
                'description' => '',
                'takenAt' => '2021-08-26 00:00:00',
                'createdAt' => '2021-08-26 00:00:00',
                'updatedAt' => '2021-08-26 00:00:00',
            ],
        ]);
        $result = $this->photoService->findUserPhotos('1');
        $this->assertEquals([
            [
                'id' => '1',
                'name' => 'name',
                'userId' => '1',
                'publicId' => 'avatars/1.jpg',
                'url' => 'https://res.cloudinary.com/dt9pwfpi5/image/upload/v1629968239/avatars/1.jpg',
                'size' => 123456,
                'description' => '',
                'takenAt' => '2021-08-26 00:00:00',
                'createdAt' => '2021-08-26 00:00:00',
                'updatedAt' => '2021-08-26 00:00:00',
            ],
        ], $result);
    }

    public function testFindUserPhotosWhenPhotoNotFound()
    {
        $this->photoRepositoryMock->method('findUserPhotos')->willReturn([]);
        $result = $this->photoService->findUserPhotos('1');
        $this->assertEquals([], $result);
    }

    /**
     * @throws HttpException
     */
    public function testFindUsersPhotoByPage()
    {
        $this->photoRepositoryMock->method('findUsersPhotos')->willReturn([
            [
                'id' => '1',
                'name' => 'name',
                'userId' => '1',
                'publicId' => 'avatars/1.jpg',
                'url' => 'https://res.cloudinary.com/dt9pwfpi5/image/upload/v1629968239/avatars/1.jpg',
                'size' => 123456,
                'description' => '',
                'takenAt' => '2021-08-26 00:00:00',
                'createdAt' => '2021-08-26 00:00:00',
                'updatedAt' => '2021-08-26 00:00:00',
            ],
        ]);
        $result = $this->photoService->findUsersPhotoByPage('1', '1', '1');
        $this->assertEquals([
            [
                'id' => '1',
                'name' => 'name',
                'userId' => '1',
                'publicId' => 'avatars/1.jpg',
                'url' => 'https://res.cloudinary.com/dt9pwfpi5/image/upload/v1629968239/avatars/1.jpg',
                'size' => 123456,
                'description' => '',
                'takenAt' => '2021-08-26 00:00:00',
                'createdAt' => '2021-08-26 00:00:00',
                'updatedAt' => '2021-08-26 00:00:00',
            ],
        ], $result);
    }

    public function testFindUsersPhotoByPageWhenPageIsNotNumber()
    {
        $this->expectException(HttpException::class);
        $this->expectExceptionMessage(PhotoError::INVALID_PAGE_OR_LIMIT->value);
        $this->photoService->findUsersPhotoByPage('1', 'a', '1');
    }

    public function testFindUsersPhotoByPageWhenLimitIsNotNumber()
    {
        $this->expectException(HttpException::class);
        $this->expectExceptionMessage(PhotoError::INVALID_PAGE_OR_LIMIT->value);
        $this->photoService->findUsersPhotoByPage('1', '1', 'b');
    }

    /**
     * @throws HttpException
     */
    public function testFindUserPhoto()
    {
        $this->photoRepositoryMock->method('findUserPhoto')->willReturn([
            'id' => '1',
            'name' => 'name',
            'userId' => '1',
            'publicId' => 'avatars/1.jpg',
            'url' => 'https://res.cloudinary.com/dt9pwfpi5/image/upload/v1629968239/avatars/1.jpg',
            'size' => 123456,
            'description' => '',
            'takenAt' => '2021-08-26 00:00:00',
            'createdAt' => '2021-08-26 00:00:00',
            'updatedAt' => '2021-08-26 00:00:00',
        ]);
        $result = $this->photoService->findUserPhoto('1', '1');
        $this->assertEquals([
            'id' => '1',
            'name' => 'name',
            'userId' => '1',
            'publicId' => 'avatars/1.jpg',
            'url' => 'https://res.cloudinary.com/dt9pwfpi5/image/upload/v1629968239/avatars/1.jpg',
            'size' => 123456,
            'description' => '',
            'takenAt' => '2021-08-26 00:00:00',
            'createdAt' => '2021-08-26 00:00:00',
            'updatedAt' => '2021-08-26 00:00:00',
        ], $result);
    }

    public function testFindUserPhotoWhenPhotoNotFound()
    {
        $this->photoRepositoryMock->method('findUserPhoto')->willReturn([]);
        $this->expectException(HttpException::class);
        $this->expectExceptionMessage(PhotoError::PHOTO_NOT_FOUND->value);
        $this->photoService->findUserPhoto('1', '1');
    }

    public function testFindUserPhotoWithInvalidUserId()
    {
        $this->photoRepositoryMock->method('findUserPhoto')->willReturn([
            'id' => '1',
            'name' => 'name',
            'userId' => '2',
            'publicId' => 'avatars/1.jpg',
            'url' => 'https://res.cloudinary.com/dt9pwfpi5/image/upload/v1629968239/avatars/1.jpg',
            'size' => 123456,
            'description' => '',
            'takenAt' => '2021-08-26 00:00:00',
            'createdAt' => '2021-08-26 00:00:00',
            'updatedAt' => '2021-08-26 00:00:00',
        ]);
        $this->expectException(HttpException::class);
        $this->expectExceptionMessage(PhotoError::PHOTO_NOT_FOUND->value);
        $this->photoService->findUserPhoto('1', '1');
    }

    /**
     * @throws HttpException
     */
    public function testDelete()
    {
        $this->photoRepositoryMock->method('findOne')->willReturn([
            'id' => '1',
            'userId' => '1',
            'publicId' => 'avatars/1.jpg',
        ]);
        $this->uploadServiceMock->expects($this->once())->method('delete')->with('avatars/1.jpg');
        $this->photoRepositoryMock->expects($this->once())->method('delete')->with('1');
        $this->photoService->delete('1', '1');
    }

    public function testDeleteWhenPhotoNotFound()
    {
        $this->photoRepositoryMock->method('findOne')->willReturn(null);
        $this->expectException(HttpException::class);
        $this->expectExceptionMessage(PhotoError::PHOTO_NOT_FOUND->value);
        $this->photoService->delete('1', '1');
    }

    public function testDeletePhotoWithInvalidUserId()
    {
        $this->photoRepositoryMock->method('findOne')->willReturn([
            'id' => '1',
            'userId' => '2',
            'publicId' => 'avatars/1.jpg',
        ]);
        $this->expectException(HttpException::class);
        $this->expectExceptionMessage(PhotoError::PHOTO_NOT_FOUND->value);
        $this->photoService->delete('1', '1');
    }

    /**
     * @throws HttpException
     */
    public function testUpdateSuccessfully()
    {
        $this->photoRepositoryMock->method('findOne')->willReturn([
            'id' => '1',
            'userId' => '1',
            'publicId' => 'avatars/1.jpg',
        ]);
        $this->photoRepositoryMock->expects($this->once())->method('update')->willReturn([
            'id' => '1',
            'name' => 'name',
            'userId' => '1',
            'publicId' => 'avatars/1.jpg',
            'url' => 'https://res.cloudinary.com/dt9pwfpi5/image/upload/v1629968239/avatars/1.jpg',
            'size' => 123456,
            'description' => 'description',
            'takenAt' => '2021-08-26 00:00:00',
            'createdAt' => '2021-08-26 00:00:00',
            'updatedAt' => '2021-08-26 00:00:00',
        ]);
        $result = $this->photoService->update('1', [
            'name' => 'name',
            'description' => 'description',
            'takenAt' => '2021-08-26 00:00:00',
            'userId' => '1',
        ], '1');
        $this->assertEquals([
            'id' => '1',
            'name' => 'name',
            'userId' => '1',
            'publicId' => 'avatars/1.jpg',
            'url' => 'https://res.cloudinary.com/dt9pwfpi5/image/upload/v1629968239/avatars/1.jpg',
            'size' => 123456,
            'description' => 'description',
            'takenAt' => '2021-08-26 00:00:00',
            'createdAt' => '2021-08-26 00:00:00',
            'updatedAt' => '2021-08-26 00:00:00',
        ], $result);
    }

    public function testUpdateWhenPhotoNotFound()
    {
        $this->photoRepositoryMock->method('findOne')->willReturn(null);
        $this->expectException(HttpException::class);
        $this->expectExceptionMessage(PhotoError::PHOTO_NOT_FOUND->value);
        $this->photoService->update('1', [
            'name' => 'name',
            'description' => 'description',
            'takenAt' => '2021-08-26 00:00:00',
            'userId' => '1',
        ], '1');
    }

    public function testUpdatePhotoWithInvalidUserId()
    {
        $this->photoRepositoryMock->method('findOne')->willReturn([
            'id' => '1',
            'userId' => '2',
            'publicId' => 'avatars/1.jpg',
        ]);
        $this->expectException(HttpException::class);
        $this->expectExceptionMessage(PhotoError::PHOTO_NOT_FOUND->value);
        $this->photoService->update('1', [
            'name' => 'name',
            'description' => 'description',
            'takenAt' => '2021-08-26 00:00:00',
            'userId' => '1',
        ], '1');
    }
}
