<?php

namespace Services;

use App\Common\Enums\DefaultCategory;
use App\Common\Enums\StatusCode;
use App\Common\Error\AuthError;
use App\Exceptions\HttpException;
use App\Repositories\CategoryRepository;
use App\Repositories\UserRepository;
use App\Services\AuthService;
use App\Services\JwtService;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class AuthServiceTest extends TestCase {
	private AuthService $authService;
	private MockObject $userRepositoryMock;
	private MockObject $jwtServiceMock;
	private MockObject $categoryRepositoryMock;

	/**
	 * @throws Exception
	 */
	public function setUp (): void {
		$this->userRepositoryMock = $this->createMock(UserRepository::class);
		$this->jwtServiceMock = $this->createMock(JwtService::class);
		$this->categoryRepositoryMock = $this->createMock(CategoryRepository::class);
		$this->authService = new AuthService(
			$this->userRepositoryMock,
			$this->jwtServiceMock,
			$this->categoryRepositoryMock,
		);
	}

	/**
	 * @throws HttpException
	 */
	public function testLogoutWithValidUserId () {
		$userId = 'userId';
		$this->userRepositoryMock->expects($this->once())->method('findOne')->willReturn(NULL);
		$this->expectException(HttpException::class);
		$this->expectExceptionMessage(AuthError::USER_DOES_NOT_EXIST->value);
		$this->authService->logout($userId);
	}

	/**
	 * @throws HttpException
	 */
	public function testLogoutWithUserId () {
		$userId = 'userId';
		$this->userRepositoryMock->expects($this->once())->method('findOne')->willReturn(
			[
				'id' => $userId,
				'email' => 'email',
				'password' => 'password',
				'displayName' => 'displayName',
				'avatar' => 'avatar',
				'avatarPublicId' => 'avatarPublicId',
				'accessToken' => 'accessToken',
				'refreshToken' => 'refreshToken',
				'createdAt' => 'createdAt',
				'updatedAt' => 'updatedAt',
			]
		);
		$this->userRepositoryMock->expects($this->once())->method('update')->willReturn([
				'id' => $userId,
				'email' => 'email',
				'password' => 'password',
				'displayName' => 'displayName',
				'avatar' => 'avatar',
				'avatarPublicId' => 'avatarPublicId',
				'accessToken' => '',
				'refreshToken' => '',
				'createdAt' => 'createdAt',
				'updatedAt' => 'updatedAt',
			]
		);
		$this->assertEquals([
			'accessToken' => '',
			'refreshToken' => '',
		], $this->authService->logout($userId));
	}

	/**
	 * @throws HttpException
	 */
	public function testRefreshTokensWhenValidToken () {
		$refreshToken = 'validRefreshToken';
		$userId = 'userId';
		$user = [
			'id' => $userId,
			'refreshToken' => $refreshToken,
		];
		$this->jwtServiceMock
			->expects($this->once())
			->method('verifyToken')
			->with($refreshToken)
			->willReturn(['userId' => $userId]);
		$this->userRepositoryMock
			->expects($this->once())
			->method('findOne')
			->with($userId)
			->willReturn($user);
		$this->jwtServiceMock
			->expects($this->exactly(2)) // Expect two calls to generateToken
			->method('generateToken')
			->willReturn('mockedAccessToken');
		$this->userRepositoryMock
			->expects($this->once())
			->method('update')
			->with(
				$userId,
				[
					'accessToken' => 'mockedAccessToken',
					'refreshToken' => 'mockedAccessToken', // Adjust this if needed
				],
				$user
			)
			->willReturn([
				'id' => $userId,
				'email' => 'email',
				'password' => 'password',
				'displayName' => 'displayName',
				'avatar' => 'avatar',
				'avatarPublicId' => 'avatarPublicId',
				'accessToken' => 'mockedAccessToken',
				'refreshToken' => 'mockedAccessToken',
				'createdAt' => 'createdAt',
				'updatedAt' => 'updatedAt',
			]);
		$result = $this->authService->refreshTokens(['refreshToken' => $refreshToken]);
		$this->assertEquals([
			'accessToken' => 'mockedAccessToken',
			'refreshToken' => 'mockedAccessToken', // Adjust this if needed
		], $result);
	}

	public function testRefreshTokensWhenInvalidRefreshToken () {
		$this->expectException(HttpException::class);
		$this->expectExceptionMessage(AuthError::REFRESH_TOKEN_IS_INVALID->value);

		$refreshToken = 'invalidRefreshToken';

		$this->jwtServiceMock
			->expects($this->once())
			->method('verifyToken')
			->with($refreshToken)
			->willThrowException(new HttpException(StatusCode::UNAUTHORIZED->value, AuthError::REFRESH_TOKEN_IS_INVALID->value));

		$this->authService->refreshTokens(['refreshToken' => $refreshToken]);
	}

	public function testRefreshTokensWhenUserDoesNotExist () {
		$this->expectException(HttpException::class);
		$this->expectExceptionMessage(AuthError::USER_DOES_NOT_EXIST->value);

		$refreshToken = 'validRefreshToken';
		$userId = 'nonExistentUserId';

		$this->jwtServiceMock
			->expects($this->once())
			->method('verifyToken')
			->with($refreshToken)
			->willReturn(['userId' => $userId]);

		$this->userRepositoryMock
			->expects($this->once())
			->method('findOne')
			->with($userId)
			->willReturn(NULL);

		$this->authService->refreshTokens(['refreshToken' => $refreshToken]);
	}

	public function testRefreshTokensWhenRefreshTokenMismatch () {
		$this->expectException(HttpException::class);
		$this->expectExceptionMessage(AuthError::REFRESH_TOKEN_IS_INVALID->value);

		$refreshToken = 'providedRefreshToken';
		$storedRefreshToken = 'storedRefreshToken';
		$userId = 'userId';

		$user = [
			'id' => $userId,
			'refreshToken' => $storedRefreshToken,
		];

		$this->jwtServiceMock
			->expects($this->once())
			->method('verifyToken')
			->with($refreshToken)
			->willReturn(['userId' => $userId]);

		$this->userRepositoryMock
			->expects($this->once())
			->method('findOne')
			->with($userId)
			->willReturn($user);

		$this->authService->refreshTokens(['refreshToken' => $refreshToken]);
	}


	public function testGetMeWithInvalidUserId () {
		$userId = 'userId';
		$this->userRepositoryMock->expects($this->once())->method('findOne')->willReturn(NULL);
		$this->expectException(HttpException::class);
		$this->expectExceptionMessage(AuthError::USER_DOES_NOT_EXIST->value);
		$this->authService->getMe($userId);
	}

	/**
	 * @throws HttpException
	 */
	public function testGetMeWithValidUserId () {
		$userId = 'userId';
		$this->userRepositoryMock->expects($this->once())->method('findOne')->willReturn(
			[
				'id' => $userId,
				'email' => 'email',
				'password' => 'password',
				'displayName' => 'displayName',
				'avatar' => 'avatar',
				'avatarPublicId' => 'avatarPublicId',
				'accessToken' => 'accessToken',
				'refreshToken' => 'refreshToken',
				'createdAt' => 'createdAt',
				'updatedAt' => 'updatedAt',
			]
		);
		$this->assertEquals([
			'id' => $userId,
			'email' => 'email',
			'displayName' => 'displayName',
			'avatar' => 'avatar',
			'avatarPublicId' => 'avatarPublicId',
			'createdAt' => 'createdAt',
			'updatedAt' => 'updatedAt',
		], $this->authService->getMe($userId));

	}


	public function testHideUserCredentials () {
		$user = [
			'id' => 'id',
			'email' => 'email',
			'displayName' => 'displayName',
			'avatar' => 'avatar',
			'avatarPublicId' => 'avatarPublicId',
			'createdAt' => 'createdAt',
			'updatedAt' => 'updatedAt',
		];
		$this->assertEquals([
			'id' => 'id',
			'email' => 'email',
			'displayName' => 'displayName',
			'avatar' => 'avatar',
			'avatarPublicId' => 'avatarPublicId',
			'createdAt' => 'createdAt',
			'updatedAt' => 'updatedAt',
		], $this->authService->hideUserCredentials($user));
	}

	public function testLoginWithInvalidEmail () {
		$data = [
			'email' => 'email',
			'password' => 'password',
		];
		$this->userRepositoryMock->expects($this->once())->method('findOneByEmail')->willReturn(NULL);
		$this->expectException(HttpException::class);
		$this->expectExceptionMessage(AuthError::EMAIL_DOES_NOT_EXIST->value);
		$this->authService->login($data);
	}

	public function testLoginWithInvalidPassword () {
		$data = [
			'email' => 'email',
			'password' => 'password',
		];
		$this->userRepositoryMock->expects($this->once())->method('findOneByEmail')->willReturn(
			[
				'id' => 'id',
				'email' => 'email',
				'password' => 'password2',
				'displayName' => 'displayName',
				'avatar' => 'avatar',
				'avatarPublicId' => 'avatarPublicId',
				'accessToken' => 'accessToken',
				'refreshToken' => 'refreshToken',
				'createdAt' => 'createdAt',
				'updatedAt' => 'updatedAt',
			]
		);
		$this->expectException(HttpException::class);
		$this->expectExceptionMessage(AuthError::PASSWORD_IS_INCORRECT->value);
		$this->authService->login($data);
	}

	/**
	 * @throws HttpException
	 */
	public function testLoginWithValidData () {
		$data = [
			'email' => 'email',
			'password' => 'password',
		];

		$this->userRepositoryMock
			->expects($this->once())
			->method('findOneByEmail')
			->willReturn([
				'id' => 'id',
				'email' => 'email',
				'password' => password_hash('password', PASSWORD_DEFAULT),
				'displayName' => 'displayName',
				'avatar' => 'avatar',
				'avatarPublicId' => 'avatarPublicId',
				'accessToken' => 'accessToken',
				'refreshToken' => 'refreshToken',
				'createdAt' => 'createdAt',
				'updatedAt' => 'updatedAt',
			]);

		$this->jwtServiceMock
			->expects($this->exactly(2)) // Expect two calls to generateToken
			->method('generateToken')
			->willReturn('mockedAccessToken');
		$result = $this->authService->login($data);
		$this->assertEquals([
			'accessToken' => 'mockedAccessToken',
			'refreshToken' => 'mockedAccessToken',
			'user' => [
				'id' => 'id',
				'email' => 'email',
				'displayName' => 'displayName',
				'avatar' => 'avatar',
				'avatarPublicId' => 'avatarPublicId',
				'createdAt' => 'createdAt',
				'updatedAt' => 'updatedAt',
			],
		], $result);
	}

	/**
	 * @throws HttpException
	 */
	public function testGenerateTokens () {
		$user = [
			'id' => 'id',
			'email' => 'email',
			'displayName' => 'displayName',
			'avatar' => 'avatar',
			'avatarPublicId' => 'avatarPublicId',
			'createdAt' => 'createdAt',
			'updatedAt' => 'updatedAt',
		];
		$this->jwtServiceMock
			->expects($this->exactly(2)) // Expect two calls to generateToken
			->method('generateToken')
			->willReturn('mockedAccessToken');
		$result = $this->authService->generateTokens($user);
		$this->assertEquals([
			'accessToken' => 'mockedAccessToken',
			'refreshToken' => 'mockedAccessToken',
			'user' => [
				'id' => 'id',
				'email' => 'email',
				'displayName' => 'displayName',
				'avatar' => 'avatar',
				'avatarPublicId' => 'avatarPublicId',
				'createdAt' => 'createdAt',
				'updatedAt' => 'updatedAt',
			],
		], $result);
	}

	/**
	 * @throws HttpException
	 */
	public function testRegisterWithValidData () {
		// Test data
		$userData = [
			'email' => 'newuser@example.com',
			'password' => 'password',
			'displayName' => 'New User',
		];
		$this->userRepositoryMock
			->expects($this->once())
			->method('findOneByEmail')
			->with($userData['email'])
			->willReturn(NULL);
		$createdUser = [
			'id' => 'newUserId',
			'email' => $userData['email'],
			'password' => password_hash($userData['password'], PASSWORD_DEFAULT),
			'displayName' => $userData['displayName'],
			'avatar' => $_ENV['DEFAULT_USER_AVATAR'],
			'avatarPublicId' => $_ENV['DEFAULT_USER_AVATAR_PUBLIC_ID'],
			'accessToken' => '',
			'refreshToken' => '',
			'createdAt' => '',
			'updatedAt' => '',
		];

		$this->userRepositoryMock
			->expects($this->once())
			->method('create')
			->with($userData)
			->willReturn($createdUser);
		$this->jwtServiceMock
			->expects($this->exactly(2))
			->method('generateToken')
			->willReturn('mockedAccessToken');
		$this->categoryRepositoryMock
			->expects($this->exactly(2))
			->method('create')
			->willReturn([
				'id' => 'newCategoryId',
				'name' => DefaultCategory::UNCATEGORIZED->value,
				'memo' => '',
				'url' => $_ENV['DEFAULT_CATEGORY_THUMBNAIL'],
				'publicId' => '',
				'userId' => $createdUser['id'],
			]);

		// Call the register method
		$result = $this->authService->register($userData);

		// Assert the result
		$this->assertEquals([
			'accessToken' => 'mockedAccessToken',
			'refreshToken' => 'mockedAccessToken',
			'user' => [
				'id' => $createdUser['id'],
				'email' => $createdUser['email'],
				'displayName' => $createdUser['displayName'],
				'avatar' => $createdUser['avatar'],
				'avatarPublicId' => $createdUser['avatarPublicId'],
				'createdAt' => $createdUser['createdAt'],
				'updatedAt' => $createdUser['updatedAt'],
			],
		], $result);
	}

	public function testRegisterWithEmailAlreadyExists () {
		$this->expectException(HttpException::class);
		$this->expectExceptionMessage(AuthError::EMAIL_ALREADY_EXISTS->value);

		// Test data
		$userData = [
			'email' => 'existinguser@example.com',
		];

		$existingUser = [
			'id' => 'existingUserId',
			'email' => $userData['email'],
		];

		// Set up expectations for the userRepositoryMock
		$this->userRepositoryMock
			->expects($this->once())
			->method('findOneByEmail')
			->with($userData['email'])
			->willReturn($existingUser);

		$this->authService->register($userData);
	}

}
