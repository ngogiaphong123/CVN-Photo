<?php

namespace App\Controllers;

use App\Common\Enums\StatusCode;
use App\Common\Message\UserMessage;
use App\Core\Request;
use App\Core\Response;
use App\Core\Session;
use App\Exceptions\HttpException;
use App\Services\UserService;

class UserController {
	public function __construct (private readonly UserService $userService, private readonly Request $request, private readonly Response $response) {}

	/**
	 * @throws HttpException
	 */
	public function uploadAvatar (): void {
		$this->response->response(StatusCode::OK->value, UserMessage::UPLOAD_AVATAR_SUCCESSFULLY->value,
			$this->userService->uploadAvatar($this->request->getFile('avatar'), Session::get("userId"))
		);
	}

	/**
	 * @throws HttpException
	 */
	public function updateProfile (): void {
		$this->response->response(StatusCode::OK->value, UserMessage::UPDATE_PROFILE_SUCCESSFULLY->value,
			$this->userService->updateProfile($this->request->getBody(), Session::get("userId"))
		);
	}
}
