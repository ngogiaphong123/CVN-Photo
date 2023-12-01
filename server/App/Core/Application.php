<?php
declare(strict_types = 1);

namespace App\Core;

use App\Exceptions\HttpException;

class Application {

	public function __construct (private Router   $router,
	                             private Request  $request,
	                             private Response $response) {}

	public function run (): void {
		try {
			$this->router->resolve();
		} catch (HttpException $e) {
			$this->response->error($e->getStatusCode(), $e->getMessage(), $e->getError());
		}
	}
}
