<?php

namespace App\Services;

use App\Common\Enums\StatusCode;
use App\Common\Error\AuthError;
use App\Common\Error\UploadError;
use App\Core\Config;
use App\Exceptions\HttpException;
use App\Repositories\UserRepository;

class UserService
{
    private function hideUserCredentials(array $user): array
    {
        unset($user['password']);
        unset($user['accessToken']);
        unset($user['refreshToken']);
        return $user;
    }

    public function __construct(
        private readonly UserRepository $userRepository,
        private readonly UploadService $uploadService,
        private readonly Config $config
    ) {}

    /**
     * @throws HttpException
     */
    public function uploadAvatar(array|null $file, string $userId): array
    {
        if (!$file) {
            throw new HttpException(StatusCode::BAD_REQUEST->value, UploadError::FILE_IS_REQUIRED->value);
        }
        $user = $this->userRepository->findOne($userId);
        if (!$user) {
            throw new HttpException(StatusCode::BAD_REQUEST->value, AuthError::USER_DOES_NOT_EXIST->value);
        }
        if ($user['avatarPublicId'] !== $this->config->get('user')['default']['avatarPublicId']) {
            $this->uploadService->delete($user['avatarPublicId']);
        }
        if ($file["name"] === "" && $file["size"] === 0) {
            $updatedUser = $this->userRepository->update($userId, [
                "secureUrl" => $this->config->get('user')['default']['avatar'],
                "publicId" => $this->config->get('user')['default']['avatarPublicId'],
            ], $user);
            return $this->hideUserCredentials($updatedUser);
        }
        $checkImage = getimagesize($file['tmp_name']);
        if (!$checkImage) {
            throw new HttpException(StatusCode::BAD_REQUEST->value, UploadError::FILE_TYPE_IS_NOT_ALLOWED->value);
        }
        $updatedUser = $this->userRepository->update($userId, $this->uploadService->upload($file, $userId), $user);
        return $this->hideUserCredentials($updatedUser);
    }

    /**
     * @throws HttpException
     */
    public function updateProfile(array $data, string $userId): array
    {
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
