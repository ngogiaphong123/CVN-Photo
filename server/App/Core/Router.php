<?php
declare(strict_types = 1);

namespace App\Core;

use App\Common\Enums\HttpMethod;
use App\Common\Enums\StatusCode;
use App\Exceptions\HttpException;
use DI\DependencyException;
use DI\NotFoundException;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;

class Router {
	private static string $PARAM_PATTERN = '~^\:(\w+)$~';
	private static string $REPLACE_PATTERN = '([\w-]+)';
	private array $routes = [];

	public function __construct (private readonly Request $request, private readonly ContainerInterface $container) {}

	public function get (string $path, array $middlewares = [], array $callbacks = []): Router {
		$this->addRoute(HttpMethod::GET, $path, $middlewares, $callbacks);
		return $this;
	}

	/**
	 * @param string $path
	 * @param array $middlewares
	 * @param array $callbacks
	 * @return Router
	 * @throws ContainerExceptionInterface
	 * @throws DependencyException
	 * @throws HttpException
	 * @throws NotFoundException
	 * @throws NotFoundExceptionInterface
	 */
	public function post (string $path, array $middlewares = [], array $callbacks = []): Router {
		$this->addRoute(HttpMethod::POST, $path, $middlewares, $callbacks);
		return $this;
	}

	/**
	 * @param string $path
	 * @param array $middlewares
	 * @param array $callbacks
	 * @return Router
	 * @throws ContainerExceptionInterface
	 * @throws DependencyException
	 * @throws HttpException
	 * @throws NotFoundException
	 * @throws NotFoundExceptionInterface
	 */
	public function put (string $path, array $middlewares = [], array $callbacks = []): Router {
		$this->addRoute(HttpMethod::PUT, $path, $middlewares, $callbacks);
		return $this;
	}

	/**
	 * @param string $path
	 * @param array $middlewares
	 * @param array $callbacks
	 * @return Router
	 * @throws ContainerExceptionInterface
	 * @throws DependencyException
	 * @throws HttpException
	 * @throws NotFoundException
	 * @throws NotFoundExceptionInterface
	 */
	public function delete (string $path, array $middlewares = [], array $callbacks = []): Router {
		$this->addRoute(HttpMethod::DELETE, $path, $middlewares, $callbacks);
		return $this;
	}

	/**
	 * @throws NotFoundExceptionInterface
	 * @throws NotFoundException
	 * @throws ContainerExceptionInterface
	 * @throws DependencyException
	 * @throws HttpException
	 */
	public function addRoute (HttpMethod $method, string $path, array $middlewares = [], array $callbacks = []): void {
		$method = strtolower($method->value);
		$regex = $this->convertPathToRegex($method, $path);
		$this->routes[$method][$regex['path']]["params"] = $regex['params'];
		$this->routes[$method][$regex['path']]["callbacks"] = $callbacks;
		foreach ($middlewares as $middleware) {
			if (!class_exists($middleware)) {
				throw new HttpException(StatusCode::INTERNAL_SERVER_ERROR->value, "Middleware $middleware not found");
			}
			$this->routes[$method][$regex['path']]["middlewares"][] = $this->container->get($middleware);
		}
	}

	/**
	 * @throws HttpException
	 */
	public function resolve (): mixed {
		$method = strtolower($this->request->getMethod());
		$path = $this->request->getPath();
		$routes = $this->routes[$method] ?? [];
		foreach ($routes as $route => $routeInfo) {
			if (preg_match($route, $path, $matches)) {
				$params = [];
				if (array_key_exists("params", $routeInfo)) {
					$params = array_combine($routeInfo["params"], array_slice($matches, 1));
				}
				foreach ($params as $key => $value) {
					$this->request->setParam($key, $value);
				}
				if (array_key_exists("middlewares", $routeInfo)) {
					foreach ($routeInfo["middlewares"] as $middleware) {
						$middleware->process();
					}
				}
				if (array_key_exists("callbacks", $routeInfo)) {
					return $this->container->call($routeInfo["callbacks"]);
				}
			}
		}
		throw new HttpException(StatusCode::NOT_FOUND->value, "Route Not found", "Route $method $path not found");
	}

	public function convertPathToRegex ($method, $path): array {
		$pathParts = explode('/', $path);
		$params = [];
		foreach ($pathParts as $key => $part) {
			if (preg_match(self::$PARAM_PATTERN, $part, $matches)) {
				$pathParts[$key] = self::$REPLACE_PATTERN;
				$params[] = $matches[1];
			}
		}
		return [
			'path' => '~^/' . implode('/', $pathParts) . '$~',
			'params' => $params,
		];
	}
}
