<?php
declare(strict_types = 1);

namespace App\Core;

use PDO;

class Database {
	private static ?PDO $connection = NULL;

	public function __construct (private readonly Config $config) {
		$config = $this->config::get('db');
		$dsn = "mysql:host={$config['host']};port={$config['port']};dbname={$config['database']}";
		self::$connection = new PDO($dsn, $config['username'], $config['password']);
		self::$connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	}

	public static function getConnection (): PDO {
		return self::$connection;
	}
}
