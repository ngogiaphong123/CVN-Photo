<?php

declare(strict_types = 1);

namespace App\Core;

use PDO;

class Database
{
    private PDO $connection;

    public function __construct(private readonly Config $config)
    {
        $config = $this->config->get('db');
        $dsn = "mysql:host={$config['host']};port={$config['port']};dbname={$config['database']}";
        $this->connection = new PDO($dsn, $config['username'], $config['password']);
        $this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }

    public function getConnection(): PDO
    {
        return $this->connection;
    }
}
