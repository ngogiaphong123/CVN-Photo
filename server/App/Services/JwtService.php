<?php

namespace App\Services;

use App\Common\Enums\StatusCode;
use App\Common\Enums\Token;
use App\Common\Error\AuthError;
use App\Core\Config;
use App\Exceptions\HttpException;
use DateTime;
use Exception;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class JwtService {
	public function __construct (private Config $config) {}

	public function generateToken (string $userId, Token $type): string {
		$iat = new DateTime();
		$exp = clone $iat;
		$exp->modify("+{$this->config->get("jwt")["{$type->value}TTL"]}");
		$payload = [
			"iss" => "localhost",
			"iat" => $iat->getTimestamp(),
			"exp" => $exp->getTimestamp(),
			"userId" => $userId,
		];
		return JWT::encode($payload, $this->config->get("jwt")["privateKey"], 'RS256');
	}

	/**
	 * @throws Exception
	 */
	public function verifyToken (string $token): array {
		try {
			$decoded = JWT::decode($token, new Key($this->config->get("jwt")["publicKey"], 'RS256'));
			return (array)$decoded;
		} catch (Exception $e) {
			throw new HttpException(StatusCode::UNAUTHORIZED->value, AuthError::ACCESS_TOKEN_EXPIRED->value);
		}
	}

}
