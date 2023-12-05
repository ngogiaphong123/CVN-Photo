<?php

namespace App\Controllers;

use App\Common\Enums\StatusCode;
use App\Common\Message\AuthMessage;
use App\Core\Request;
use App\Core\Response;
use App\Core\Session;
use App\Exceptions\HttpException;
use App\Services\AuthService;

class AuthController {
	public function __construct (private readonly AuthService $authService, private readonly Request $request, private readonly Response $response) {}

	/**
	 * @throws HttpException
	 */
	public function register (): void {
		$this->response->response(StatusCode::CREATED->value, AuthMessage::REGISTER_SUCCESSFULLY->value, $this->authService->register($this->request->getBody()));
	}

	/**
	 * @throws HttpException
	 */
	public function login (): void {
		$this->response->response(StatusCode::OK->value, AuthMessage::LOGIN_SUCCESSFULLY->value, $this->authService->login($this->request->getBody()));
	}

	/**
	 * @throws HttpException
	 */
	public function getMe (): void {
		$this->response->response(StatusCode::OK->value, AuthMessage::GET_ME_SUCCESSFULLY->value, $this->authService->getMe(Session::get('userId')));
	}

	/**
	 * @throws HttpException
	 */
	public function logout (): void {
		$this->response->response(StatusCode::OK->value, AuthMessage::LOGOUT_SUCCESSFULLY->value, $this->authService->logout(Session::get('userId')));
	}

	/**
	 * @throws HttpException
	 */
	public function refreshToken (): void {
		$this->response->response(StatusCode::OK->value, AuthMessage::REFRESH_TOKEN_SUCCESSFULLY->value, $this->authService->refreshTokens($this->request->getBody()));
	}
}
