<?php
declare(strict_types = 1);

namespace App\Core;

class Config {
	private static array $config = [];

	public function __construct () {
		self::$config = [
			'db' => [
				'host' => $_ENV['DB_HOST'],
				'port' => $_ENV['DB_PORT'],
				'database' => $_ENV['DB_NAME'],
				'username' => $_ENV['DB_USERNAME'],
				'password' => $_ENV['DB_PASSWORD'],
			],
			"user" => [
				"default" => [
					"avatar" => $_ENV['DEFAULT_AVATAR'],
					"avatarPublicId" => $_ENV['DEFAULT_AVATAR_PUBLIC_ID'],
				]
			],
			"jwt" => [
				"privateKey" => $_ENV['JWT_PRIVATE_KEY'],
				"publicKey" => $_ENV['JWT_PUBLIC_KEY'],
				"accessTokenTTL" => $_ENV['JWT_ACCESS_TOKEN_TTL'],
				"refreshTokenTTL" => $_ENV['JWT_REFRESH_TOKEN_TTL'],
			],
		];
	}

	public static function get (string $key) {

		return self::$config[$key] ?? NULL;
	}
}
