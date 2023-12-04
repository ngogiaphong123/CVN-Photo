<?php

namespace App\Services;

use App\Common\Enums\AuthError;
use App\Common\Enums\StatusCode;
use App\Core\Config;
use App\Exceptions\HttpException;
use App\Repositories\UserRepository;

class UserService {
	private function hideUserCredentials (array $user): array {
		unset($user['password']);
		unset($user['accessToken']);
		unset($user['refreshToken']);
		return $user;
	}

	public function __construct (private readonly UserRepository $userRepository, private readonly UploadService $uploadService, private readonly Config $config) {}

	/**
	 * @throws HttpException
	 */
	public function uploadAvatar (array $file, string $userId): array {
		$user = $this->userRepository->findOne($userId);
		if (!$user) {
			throw new HttpException(StatusCode::BAD_REQUEST->value, AuthError::USER_DOES_NOT_EXIST->value);
		}
		if ($user['avatarPublicId'] !== $this->config::get('user')['default']['avatarPublicId']) {
			$this->uploadService->delete($user['avatarPublicId']);
		}
		if ($file["name"] === "" && $file["size"] === 0) {
			$updatedUser = $this->userRepository->update($userId, [
				"avatar" => $this->config::get('user')['default']['avatar'],
				"avatarPublicId" => $this->config::get('user')['default']['avatarPublicId'],
			], $user);
			return $this->hideUserCredentials($updatedUser);
		}
		$updatedUser = $this->userRepository->update($userId, $this->uploadService->upload($file, $userId), $user);
		return $this->hideUserCredentials($updatedUser);
	}

	/**
	 * @throws HttpException
	 */
	public function updateProfile (array $data, string $userId): array {
		$allowedFields = ['displayName'];
		foreach ($data as $key => $value) {
			if (!in_array($key, $allowedFields)) {
				unset($data[$key]);
			}
		}
		$user = $this->userRepository->findOne($userId);
		if (!$user) {
			throw new HttpException(StatusCode::BAD_REQUEST->value, AuthError::USER_DOES_NOT_EXIST->value);
		}
		$updatedUser = $this->userRepository->update($userId, $data, $user);
		return $this->hideUserCredentials($updatedUser);
	}
}
