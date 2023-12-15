<?php

namespace Services;

use App\Common\Error\UploadError;
use App\Exceptions\HttpException;
use App\Services\UploadService;
use Cloudinary\Api\Exception\ApiError;
use Cloudinary\Api\Upload\UploadApi;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class UploadServiceTest extends TestCase {
	private UploadService $uploadService;
	private MockObject $uploadApiMock;

	/**
	 * @throws Exception
	 */
	public function setUp (): void {
		$this->uploadApiMock = $this->createMock(UploadApi::class);
		$this->uploadService = new UploadService($this->uploadApiMock);
	}
	public function testDelete () {
		$this->uploadApiMock->expects($this->once())->method('destroy')->with('publicId');
		$this->uploadService->delete('publicId');
	}

	/**
	 * @throws HttpException
	 */
	public function testUploadWhenFileIsTooLarge () {
		$this->expectException(HttpException::class);
		$this->expectExceptionMessage(UploadError::FILE_IS_TOO_LARGE->value);
		$this->uploadService->upload([
			'size' => UploadService::$MAX_FILE_SIZE + 1,
		], 'userId');
	}

	/**
	 * @throws HttpException
	 */
	public function testUploadWhenFileTypeIsNotAllowed () {
		$this->expectException(HttpException::class);
		$this->expectExceptionMessage(UploadError::FILE_TYPE_IS_NOT_ALLOWED->value);
		$this->uploadService->upload([
			'size' => UploadService::$MAX_FILE_SIZE - 1,
			'type' => 'image/gif',
		], 'userId');
	}

	/**
	 * @throws HttpException
	 */
	public function testUploadWhenUploadApiThrowException () {
		$this->expectException(HttpException::class);
		$this->expectExceptionMessage('Upload error');
		$this->uploadApiMock->method('upload')->willThrowException(new ApiError('Upload error'));
		$this->uploadService->upload([
			'size' => UploadService::$MAX_FILE_SIZE - 1,
			'type' => 'image/jpeg',
			'tmp_name' => 'tmp_name',
		], 'userId');
	}

	/**
	 * @throws HttpException
	 */
	public function testUploadSuccess () {
		$this->uploadApiMock->method('upload')->willReturn([
			'secure_url' => 'https://res.cloudinary.com/dt9pwfpi5/image/upload/v1629968239/avatars/1.jpg',
			'public_id' => 'avatars/1.jpg',
		]);
		$result = $this->uploadService->upload([
			'size' => UploadService::$MAX_FILE_SIZE - 1,
			'type' => 'image/jpeg',
			'tmp_name' => 'tmp_name',
		], 'userId');
		$this->assertEquals([
			'secureUrl' => 'https://res.cloudinary.com/dt9pwfpi5/image/upload/q_auto,f_auto/v1629968239/avatars/1.jpg',
			'publicId' => 'avatars/1.jpg',
		], $result);
	}
}
