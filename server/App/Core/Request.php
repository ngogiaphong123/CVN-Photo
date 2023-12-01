<?php
declare(strict_types = 1);

namespace App\Core;
class Request {
	private array $request;
	private array $params = [];

	public function __construct () {
		$this->request = $_SERVER;
	}

	public function getMethod (): string {
		return $this->request['REQUEST_METHOD'];
	}

	public function getPath (): string {
		return $this->request['REQUEST_URI'];
	}

	public function getParsedBody (): array {
		return json_decode(file_get_contents('php://input'), true);
	}

	public function getBody (): array {
		return $_POST;
	}

	public function setParam (string $key, $value): void {
		$this->params[$key] = $value;
	}

	public function getParam (string $key) {
		return $this->params[$key] ?? NULL;
	}

	public function getParams (): array {
		return $this->params;
	}

	public function getHeader (string $string) {
		return $_SERVER[$string] ?? NULL;
	}
}
