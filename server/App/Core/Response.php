<?php
declare(strict_types = 1);

namespace App\Core;
class Response {
	public function response (int $status, string $message, $data = NULL): void {
		$this->setStatusCode($status);
		$this->setHeader('Content-Type', 'application/json');
		echo json_encode([
			'statusCode' => $status,
			'message' => $message,
			'data' => $data,
		]);
	}

	public function error (int $status, string $message, $error = NULL): void {
		$this->setStatusCode($status);
		$this->setHeader('Content-Type', 'application/json');
		echo json_encode([
			'statusCode' => $status,
			'message' => $message,
			'error' => $error,
		]);
	}

	public function setStatusCode (int $code): void {
		http_response_code($code);
	}

	public function setHeader (string $name, string $value): void {
		header("$name: $value");
	}
}
