<?php

namespace Nigr\Router;

use Exception;

class Router
{
	private array $routes;
	private string $method;

	public function __construct($routes = [])
	{
		$this->routes = $routes;
		$this->method = strtolower($_SERVER['REQUEST_METHOD']);
	}

	/**
	 * @return array
	 * @throws Exception
	 */
	public function run(): array
	{
		$params = [];
		$path = $this->parseUrl()['path'];

		foreach ($this->routes as $route) {
			$param = $route->getParamName();

			if ($queryParams = $this->parseUrl()['queryParams']) {
				$params = array_merge($params, $queryParams);
			}

			if (strtolower($route->getMethod()) !== strtolower($this->method)) continue;

			if ($route->getPath()[0] !== $path[0]) continue;

			if (isset($path[1]) && is_numeric($path[1]) && !$param) continue;

			if (!$param && isset($path[1]) && (!isset($route->getPath()[1]) || $path[1] !== $route->getPath()[1])) continue;

			if ($param) {
				$params[$param] = $path[1];
			}

			$controller = $route->getHandler()[0];
			$action = $route->getHandler()[1];

			return [$controller, $action, $params];
		}

		$path = implode("/", $path);

		throw new Exception("Route '$path' does not exist!");
	}

	/**
	 * @return array
	 */
	private function parseUrl(): array
	{
		$url = explode('?', $_SERVER['REQUEST_URI'])[0];
		$path = trim($url, '/');

		$path = explode('/', $path);

		return ['path' => $path, 'queryParams' => $_GET];
	}
}
