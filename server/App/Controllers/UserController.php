<?php

namespace App\Controllers;

use App\Common\Enums\StatusCode;
use App\Core\Request;
use App\Core\Response;
use App\Exceptions\HttpException;
use App\Services\UserService;

class UserController {
	public function __construct (private readonly UserService $userService, private readonly Request $request, private readonly Response $response) {}

	/**
	 * @throws HttpException
	 */
	public function uploadAvatar (): void {
		$this->response->response(StatusCode::OK->value, "Upload avatar successfully",
			$this->userService->uploadAvatar($this->request->getFile('avatar'), $_SESSION['userId'])
		);
	}

	/**
	 * @throws HttpException
	 */
	public function updateProfile (): void {
		$this->response->response(StatusCode::OK->value, "Update profile successfully",
			$this->userService->updateProfile($this->request->getBody(), $_SESSION['userId'])
		);
	}
}
