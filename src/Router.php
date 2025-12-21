<?php

namespace Nigr\Router;

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
	 * @return array|string
	 */
	public function run(): array|string
	{
		foreach ($this->routes as $route) {
			$params = [];
			$param = $route->getParamName();

			if ($queryParams = $this->parseUrl()['queryParams']) {
				$params = array_merge($params, $queryParams);
			}

			if (strtolower($route->getMethod()) !== strtolower($this->method)) {
				continue;
			}

			if ($route->getPath()[0] !== $this->parseUrl()['path'][0]) {
				continue;
			}

			if (count($this->parseUrl()["path"]) > 1 && !$param) {
				continue;
			}

			if ($param) {
				$params[$param] = $this->parseUrl()['path'][1];
			}

			$controller = $route->getHandler()[0];
			$action = $route->getHandler()[1];

			return [$controller, $action, $params];
		}

		return "Route does not exist!";
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
