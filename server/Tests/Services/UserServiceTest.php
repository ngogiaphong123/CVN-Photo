<?php

namespace Services;

use App\Common\Error\AuthError;
use App\Common\Error\UploadError;
use App\Core\Config;
use App\Exceptions\HttpException;
use App\Repositories\UserRepository;
use App\Services\UploadService;
use App\Services\UserService;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class UserServiceTest extends TestCase
{

    private UserService $userService;
    private MockObject $userRepositoryMock;
    private MockObject $configMock;
    private MockObject $uploadServiceMock;

    /**
     * @throws Exception
     */
    public function setUp(): void
    {
        $this->userRepositoryMock = $this->createMock(UserRepository::class);
        $this->configMock = $this->createMock(Config::class);
        $this->uploadServiceMock = $this->createMock(UploadService::class);
        $this->userService = new UserService($this->userRepositoryMock, $this->uploadServiceMock, $this->configMock);
    }

    /**
     * @throws HttpException
     */
    public function testUpdateProfileWhenUserNotFound()
    {
        $this->userRepositoryMock->method('findOne')->willReturn(null);
        $this->expectException(HttpException::class);
        $this->expectExceptionMessage(AuthError::USER_DOES_NOT_EXIST->value);
        $this->userService->updateProfile([
            'displayName' => 'Phong Ngo',
        ], '1');
    }

    /**
     * @throws HttpException
     */
    public function testUpdateProfileSuccess()
    {
        $data = [
            'id' => '1',
            'displayName' => 'Phong Ngo updated',
            'email' => 'giaphong@gmail.com',
            'avatar' => 'https://res.cloudinary.com/dt9pwfpi5/image/upload/v1629968239/avatars/1.jpg',
            'avatarPublicId' => 'avatars/1.jpg',
            'createdAt' => '2021-08-26 00:00:00',
            'updatedAt' => '2021-08-26 00:00:00',
        ];
        $this->userRepositoryMock->expects($this->once())->method('findOne')->willReturn([
            'id' => '1',
            'email' => 'giaphong@gmail.com',
            'displayName' => 'Phong Ngo',
            'password' => '123456',
            'avatar' => 'https://res.cloudinary.com/dt9pwfpi5/image/upload/v1629968239/avatars/1.jpg',
            'avatarPublicId' => 'avatars/1.jpg',
            'accessToken' => '',
            'refreshToken' => '',
            'createdAt' => '2021-08-26 00:00:00',
            'updatedAt' => '2021-08-26 00:00:00',
        ]);
        $this->userRepositoryMock->expects($this->once())->method('update')->willReturn([
            'id' => '1',
            'email' => 'giaphong@gmail.com',
            'displayName' => 'Phong Ngo updated',
            'password' => '123456',
            'avatar' => 'https://res.cloudinary.com/dt9pwfpi5/image/upload/v1629968239/avatars/1.jpg',
            'avatarPublicId' => 'avatars/1.jpg',
            'accessToken' => '',
            'refreshToken' => '',
            'createdAt' => '2021-08-26 00:00:00',
            'updatedAt' => '2021-08-26 00:00:00',
        ]);
        $this->assertEquals($data, $this->userService->updateProfile([
            'displayName' => 'Phong Ngo updated',
            'id' => '1',
        ], '1'));
    }

    public function testUploadAvatarWhenNoFile()
    {
        $this->expectException(HttpException::class);
        $this->expectExceptionMessage(UploadError::FILE_IS_REQUIRED->value);
        $this->userService->uploadAvatar(null, '1');
    }

    /**
     * @throws HttpException
     */
    public function testUploadAvatarWhenUserNotFound()
    {
        $this->userRepositoryMock->method('findOne')->willReturn(null);
        $this->expectException(HttpException::class);
        $this->expectExceptionMessage(AuthError::USER_DOES_NOT_EXIST->value);
        $this->userService->uploadAvatar([
            'name' => 'avatar.jpg',
            'type' => 'image/jpeg',
            'tmp_name' => 'C:\xampp\tmp\phpB8A4.tmp',
            'error' => 0,
            'size' => 123456,
        ], '1');
    }

    /**
     * @throws HttpException
     */
    public function testUploadAvatarWhenAvatarPublicIdIsNotDefault()
    {
        $this->userRepositoryMock->expects($this->once())->method('findOne')->willReturn([
            'id' => '1',
            'email' => 'giaphong@gmail.com',
            'displayName' => 'Phong Ngo',
            'password' => '123456',
            'avatar' => 'https://res.cloudinary.com/dt9pwfpi5/image/upload/v1629968239/avatars/1.jpg',
            'avatarPublicId' => 'avatars/1.jpg',
            'accessToken' => '',
            'refreshToken' => '',
            'createdAt' => '2021-08-26 00:00:00',
            'updatedAt' => '2021-08-26 00:00:00',
        ]);
        $this->configMock->method('get')->willReturn([
            'default' => [
                'avatar' => 'https://res.cloudinary.com/dt9pwfpi5/image/upload/v1629968239/avatars/default.jpg',
                'avatarPublicId' => 'avatars/default.jpg',
            ],
        ]);
        $this->uploadServiceMock->expects($this->once())->method('delete');
        $this->uploadServiceMock->expects($this->once())->method('upload')->willReturn([
            'secureUrl' => 'https://res.cloudinary.com/dt9pwfpi5/image/upload/v1629968239/avatars/2.jpg',
            'publicId' => 'avatars/2.jpg',
        ]);
        $this->userRepositoryMock->expects($this->once())->method('update')->willReturn([
            'id' => '1',
            'email' => 'giaphong@gmail.com',
            'displayName' => 'Phong Ngo updated',
            'password' => '123456',
            'avatar' => 'https://res.cloudinary.com/dt9pwfpi5/image/upload/v1629968239/avatars/2.jpg',
            'avatarPublicId' => 'avatars/2.jpg',
            'accessToken' => '',
            'refreshToken' => '',
            'createdAt' => '2021-08-26 00:00:00',
            'updatedAt' => '2021-08-26 00:00:00',
        ]);
        $this->assertEquals([
            'id' => '1',
            'email' => 'giaphong@gmail.com',
            'displayName' => 'Phong Ngo updated',
            'avatar' => 'https://res.cloudinary.com/dt9pwfpi5/image/upload/v1629968239/avatars/2.jpg',
            'avatarPublicId' => 'avatars/2.jpg',
            'createdAt' => '2021-08-26 00:00:00',
            'updatedAt' => '2021-08-26 00:00:00',
        ],
            $this->userService->uploadAvatar([
                'name' => 'avatar.jpg',
                'type' => 'image/jpeg',
                'tmp_name' => 'C:\xampp\tmp\phpB8A4.tmp',
                'error' => 0,
                'size' => 123456,
            ], '1'));
    }

    /**
     * @throws HttpException
     */
    public function testUploadAvatarWhenAvatarPublicIdIsDefault()
    {
        $this->userRepositoryMock->expects($this->once())->method('findOne')->willReturn([
            'id' => '1',
            'email' => 'giaphong@gmail.com',
            'displayName' => 'Phong Ngo',
            'password' => '123456',
            'avatar' => 'https://res.cloudinary.com/dt9pwfpi5/image/upload/v1629968239/avatars/1.jpg',
            'avatarPublicId' => 'avatars/1.jpg',
            'accessToken' => '',
            'refreshToken' => '',
            'createdAt' => '2021-08-26 00:00:00',
            'updatedAt' => '2021-08-26 00:00:00',
        ]);
        $this->configMock->method('get')->willReturn([
            'default' => [
                'avatar' => 'https://res.cloudinary.com/dt9pwfpi5/image/upload/v1629968239/avatars/default.jpg',
                'avatarPublicId' => 'avatars/default.jpg',
            ],
        ]);
        $this->uploadServiceMock->expects($this->once())->method('delete');
        $this->uploadServiceMock->expects($this->never())->method('upload');
        $this->userRepositoryMock->expects($this->once())->method('update')->willReturn([
            'id' => '1',
            'email' => 'giaphong@gmail.com',
            'displayName' => 'Phong Ngo',
            'password' => '123456',
            'avatar' => 'https://res.cloudinary.com/dt9pwfpi5/image/upload/v1629968239/avatars/default.jpg',
            'avatarPublicId' => 'avatars/default.jpg',
            'accessToken' => '',
            'refreshToken' => '',
            'createdAt' => '2021-08-26 00:00:00',
            'updatedAt' => '2021-08-26 00:00:00',
        ]);
        $this->assertEquals([
            'id' => '1',
            'email' => 'giaphong@gmail.com',
            'displayName' => 'Phong Ngo',
            'avatar' => 'https://res.cloudinary.com/dt9pwfpi5/image/upload/v1629968239/avatars/default.jpg',
            'avatarPublicId' => 'avatars/default.jpg',
            'createdAt' => '2021-08-26 00:00:00',
            'updatedAt' => '2021-08-26 00:00:00',
        ],
            $this->userService->uploadAvatar([
                'name' => '',
                'type' => '',
                'tmp_name' => '',
                'error' => 0,
                'size' => 0,
            ], '1'));
    }
}
