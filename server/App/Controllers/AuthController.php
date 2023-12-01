<?php

namespace App\Controllers;

use App\Common\Enums\StatusCode;
use App\Core\Request;
use App\Core\Response;
use App\Exceptions\HttpException;
use App\Services\AuthService;

class AuthController {
	public function __construct (private readonly AuthService $authService, private readonly Request $request, private readonly Response $response) {}

	/**
	 * @throws HttpException
	 */
	public function register (): void {
		$this->response->response(StatusCode::CREATED->value, "Register successfully", $this->authService->register($this->request->getBody()));
	}

	/**
	 * @throws HttpException
	 */
	public function login (): void {
		$this->response->response(StatusCode::OK->value, "Login successfully", $this->authService->login($this->request->getBody()));
	}

	/**
	 * @throws HttpException
	 */
	public function getMe (): void {
		$this->response->response(StatusCode::OK->value, "Get me successfully", $this->authService->getMe($_SESSION['userId']));
	}

	/**
	 * @throws HttpException
	 */
	public function logout (): void {
		$this->response->response(StatusCode::OK->value, "Logout successfully", $this->authService->logout($_SESSION['userId']));
	}

	/**
	 * @throws HttpException
	 */
	public function refreshToken (): void {
		$this->response->response(StatusCode::OK->value, "Refresh token successfully", $this->authService->refreshTokens($this->request->getBody()));
	}
}
