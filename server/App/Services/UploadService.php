<?php

namespace App\Services;
use App\Common\Enums\StatusCode;
use App\Common\Error\UploadError;
use App\Exceptions\HttpException;
use Cloudinary\Api\Exception\ApiError;
use Cloudinary\Api\Upload\UploadApi;

class UploadService
{
    public function __construct(private UploadApi $uploadApi) {}

    public static int $MAX_FILE_SIZE = 1024 * 1024 * 2;

    /**
     * @throws HttpException
     */
    public function upload(array $file, string $userId): array
    {
        if ($file['size'] > self::$MAX_FILE_SIZE) {
            throw new HttpException(StatusCode::BAD_REQUEST->value, UploadError::FILE_IS_TOO_LARGE->value);
        }
        if (!in_array($file['type'], ['image/jpeg', 'image/png', 'image/jpg'])) {
            throw new HttpException(StatusCode::BAD_REQUEST->value, UploadError::FILE_TYPE_IS_NOT_ALLOWED->value);
        }
        try {
            $result = $this->uploadApi->upload($file['tmp_name'], [
                'folder' => "web-photo/$userId",
                'resource_type' => 'image',
            ]);
        } catch (ApiError $e) {
            throw new HttpException(StatusCode::BAD_REQUEST->value, $e->getMessage());
        }
        $result['secure_url'] = str_replace('upload/', 'upload/q_auto,f_auto/', $result['secure_url']);
        return [
            'secureUrl' => $result['secure_url'],
            'publicId' => $result['public_id'],
        ];
    }

    public function delete(string $publicId): void
    {
        $this->uploadApi->destroy($publicId);
    }

    public function checkImage (array $file): bool
    {
        $checkImage = getimagesize($file['tmp_name']);
        if (!$checkImage) {
            return false;
        }
        return true;
    }
}
