<?php

namespace App\Services;

use App\Common\Enums\StatusCode;
use App\Common\Enums\Token;
use App\Exceptions\HttpException;
use App\Repositories\UserRepository;
use Exception;

class AuthService {
	public function __construct (
		private UserRepository $userRepository,
		private JwtService     $jwtService,
	) {}

	/**
	 * @throws HttpException
	 */
	public function register (array $data): array {
		$user = $this->userRepository->findOneByEmail($data['email']);
		if ($user) {
			throw new HttpException(StatusCode::BAD_REQUEST->value, "Email already exists");
		}
		$user = $this->userRepository->create($data);
		return $this->generateTokens($user);
	}

	/**
	 * @throws HttpException
	 */
	public function login (array $data): array {
		$user = $this->userRepository->findOneByEmail($data['email']);
		if (!$user) {
			throw new HttpException(StatusCode::BAD_REQUEST->value, "Email does not exist");
		}
		if (!password_verify($data['password'], $user['password'])) {
			throw new HttpException(StatusCode::BAD_REQUEST->value, "Password is incorrect");
		}
		return $this->generateTokens($user);
	}

	/**
	 * @throws HttpException
	 */
	public function getMe (string $userId) {
		$user = $this->userRepository->findOne($userId);
		if (!$user) {
			throw new HttpException(StatusCode::BAD_REQUEST->value, "User does not exist");
		}
		unset($user['password']);
		unset($user['accessToken']);
		unset($user['refreshToken']);
		return $user;
	}

	/**
	 * @throws HttpException
	 */
	public function logout (string $userId): array {
		$user = $this->userRepository->findOne($userId);
		if (!$user) {
			throw new HttpException(StatusCode::BAD_REQUEST->value, "User does not exist");
		}
		$this->userRepository->updateTokens($user['id'], [
			'accessToken' => '',
			'refreshToken' => '',
		]);
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
		if(!$data['refreshToken']) {
			throw new HttpException(StatusCode::BAD_REQUEST->value, "Refresh token is required");
		}
		$payload = $this->jwtService->verifyToken($data['refreshToken']);
		$user = $this->userRepository->findOne($payload['userId']);
		if (!$user) {
			throw new HttpException(StatusCode::BAD_REQUEST->value, "User does not exist");
		}
		if($user['refreshToken'] !== $data['refreshToken']) {
			throw new HttpException(StatusCode::BAD_REQUEST->value, "Invalid refresh token");
		}
		$accessToken = $this->jwtService->generateToken($user['id'], Token::ACCESS_TOKEN);
		$refreshToken = $this->jwtService->generateToken($user['id'], Token::REFRESH_TOKEN);
		$this->userRepository->updateTokens($user['id'], [
			'accessToken' => $accessToken,
			'refreshToken' => $refreshToken,
		]);
		return [
			'accessToken' => $accessToken,
			'refreshToken' => $refreshToken,
		];
	}

	/**
	 * @param mixed $user
	 * @return array
	 */
	public function generateTokens (mixed $user): array {
		$accessToken = $this->jwtService->generateToken($user['id'], Token::ACCESS_TOKEN);
		$refreshToken = $this->jwtService->generateToken($user['id'], Token::REFRESH_TOKEN);
		$this->userRepository->updateTokens($user['id'], [
			'accessToken' => $accessToken,
			'refreshToken' => $refreshToken,
		]);
		unset($user['password']);
		unset($user['accessToken']);
		unset($user['refreshToken']);
		return [
			'accessToken' => $accessToken,
			'refreshToken' => $refreshToken,
			'user' => $user,
		];
	}
}
