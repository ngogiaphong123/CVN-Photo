<?php
declare(strict_types = 1);

namespace App\Core;

use PDO;

class Database {
	private static ?PDO $connection = NULL;
	private Config $config;

	public function __construct (Config $config) {
		$this->config = $config;
		$config = $this->config::get('db');
		$dsn = "mysql:host={$config['host']};port={$config['port']};dbname={$config['database']}";
		self::$connection = new PDO($dsn, $config['username'], $config['password']);
		self::$connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	}

	public static function getConnection (): PDO {
		return self::$connection;
	}
}
