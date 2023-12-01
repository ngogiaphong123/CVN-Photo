<?php
declare(strict_types = 1);

namespace App\Middlewares;

use App\Core\Request;
use App\Exceptions\HttpException;
use App\Services\JwtService;
use Exception;

class JwtGuard extends BaseMiddleware {
	public function __construct (private Request $request, private JwtService $jwtService) {
	}

	/**
	 * @throws HttpException
	 */
	public function process (): void {
		$authorization = $this->request->getHeader('HTTP_AUTHORIZATION');
		if (empty($authorization)) {
			throw new HttpException(401, "Invalid access token");
		}
		$token = explode(' ', $authorization)[1];
		try {
			$decoded = $this->jwtService->verifyToken($token);
			$_SESSION['userId'] = $decoded['userId'];
		} catch (Exception $e) {
			throw new HttpException(401, "Access token expired");
		}
	}
}
