<?php

namespace App\Services;

use App\Common\Enums\DefaultCategory;
use App\Common\Enums\StatusCode;
use App\Common\Enums\Token;
use App\Common\Error\AuthError;
use App\Exceptions\HttpException;
use App\Repositories\CategoryRepository;
use App\Repositories\UserRepository;
use Exception;

class AuthService {
	public function __construct (
		private UserRepository     $userRepository,
		private JwtService         $jwtService,
		private CategoryRepository $categoryRepository,
	) {}

	public function hideUserCredentials (array $user): array {
		unset($user['password']);
		unset($user['accessToken']);
		unset($user['refreshToken']);
		return $user;
	}

	/**
	 * @throws HttpException
	 */
	public function register (array $data): array {
		$user = $this->userRepository->findOneByEmail($data['email']);
		if ($user) {
			throw new HttpException(StatusCode::BAD_REQUEST->value, AuthError::EMAIL_ALREADY_EXISTS->value);
		}
		$user = $this->userRepository->create($data);
		$this->categoryRepository->create([
			'name' => DefaultCategory::UNCATEGORIZED->value,
			'memo' => '',
			'url' => '',
			'publicId' => '',
			'userId' => $user['id'],
		]);
		$this->categoryRepository->create([
			'name' => DefaultCategory::FAVORITE->value,
			'memo' => '',
			'url' => $_ENV['DEFAULT_CATEGORY_THUMBNAIL'],
			'publicId' => '',
			'userId' => $user['id'],
		]);
		return $this->generateTokens($user);
	}

	/**
	 * @throws HttpException
	 */
	public function login (array $data): array {
		$user = $this->userRepository->findOneByEmail($data['email'] ?? "");
		if (!$user) {
			throw new HttpException(StatusCode::BAD_REQUEST->value, AuthError::EMAIL_DOES_NOT_EXIST->value);
		}
		if (!password_verify($data['password'], $user['password'])) {
			throw new HttpException(StatusCode::BAD_REQUEST->value, AuthError::PASSWORD_IS_INCORRECT->value);
		}
		return $this->generateTokens($user);
	}

	/**
	 * @throws HttpException
	 */
	public function getMe (string $userId): array {
		$user = $this->userRepository->findOne($userId);
		if (!$user) {
			throw new HttpException(StatusCode::BAD_REQUEST->value, AuthError::USER_DOES_NOT_EXIST->value);
		}
		return $this->hideUserCredentials($user);
	}

	/**
	 * @throws HttpException
	 */
	public function logout (string $userId): array {
		$user = $this->userRepository->findOne($userId);
		if (!$user) {
			throw new HttpException(StatusCode::BAD_REQUEST->value, AuthError::USER_DOES_NOT_EXIST->value);
		}
		$this->userRepository->update($user['id'], [
			'accessToken' => '',
			'refreshToken' => '',
		], $user);
		return [
			'accessToken' => '',
			'refreshToken' => '',
		];
	}

	/**
	 * @throws HttpException
	 * @throws Exception
	 */
	public function refreshTokens (array $data): array {
		if (!isset($data['refreshToken'])) {
			throw new HttpException(StatusCode::BAD_REQUEST->value, AuthError::REFRESH_TOKEN_IS_INVALID->value);
		}
		$payload = $this->jwtService->verifyToken($data['refreshToken']);
		$user = $this->userRepository->findOne($payload['userId']);
		if (!$user) {
			throw new HttpException(StatusCode::BAD_REQUEST->value, AuthError::USER_DOES_NOT_EXIST->value);
		}
		if ($user['refreshToken'] !== $data['refreshToken']) {
			throw new HttpException(StatusCode::BAD_REQUEST->value, AuthError::REFRESH_TOKEN_IS_INVALID->value);
		}
		$accessToken = $this->jwtService->generateToken($user['id'], Token::ACCESS_TOKEN);
		$refreshToken = $this->jwtService->generateToken($user['id'], Token::REFRESH_TOKEN);
		$this->userRepository->update($user['id'], [
			'accessToken' => $accessToken,
			'refreshToken' => $refreshToken,
		], $user);
		return [
			'accessToken' => $accessToken,
			'refreshToken' => $refreshToken,
		];
	}

	/**
	 * @param mixed $user
	 * @return array
	 * @throws HttpException
	 */
	public function generateTokens (mixed $user): array {
		$accessToken = $this->jwtService->generateToken($user['id'], Token::ACCESS_TOKEN);
		$refreshToken = $this->jwtService->generateToken($user['id'], Token::REFRESH_TOKEN);
		$this->userRepository->update($user['id'], [
			'accessToken' => $accessToken,
			'refreshToken' => $refreshToken,
		], $user);
		return [
			'accessToken' => $accessToken,
			'refreshToken' => $refreshToken,
			'user' => $this->hideUserCredentials($user),
		];
	}
}
