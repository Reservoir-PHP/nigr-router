<?php

namespace Nigr\Router;

class Router
{
	private array $routes = [];
	private string $path = '';
	private string $method = '';
	private array $params = [];

	public function __construct($routes = [])
	{
		$this->routes = $routes;
		$this->path = $this->parseUrl($_SERVER['REQUEST_URI'])['path'];
		$this->method = $_SERVER['REQUEST_METHOD'];
		$this->params = $this->parseUrl($_SERVER['REQUEST_URI'])['params'];
	}

	public function run()
	{

		foreach ($this->routes as $route) {
			if ($route['method'] !== $this->method || $route['path'] !== $this->path) continue;

			$controller = new $route['handler'][0]();
			$action = $route['handler'][1];

			return $controller->$action($this->params);
		}

		return [
			'status' => false,
			'data' => [
				'message' => 'Page not found, error 404'
			]
		];
	}

	public function add(string $method, string $path, array $handler): void
	{
		$this->routes[] = [
			'method' => strtoupper($method),
			'path' => $this->normalizePath($path),
			'handler' => $handler
		];
	}

	function normalizePath(string $path): string
	{
		return trim($path, '/');
	}

	function parseUrl($url): array
	{
//	$method = $_SERVER["REQUEST_METHOD"];
		$pathString = trim(explode('?', $url)[0], '/');
		$queryParams = $this->parseQueryString(explode('?', $url)[1] ?? '');

		return [
//		'method' => strtolower($method),
			'path' => $pathString,
			'params' => $queryParams
		];
	}

	function parseQueryString($queryString): array
	{
		$queryParams = [];

		foreach (explode('&', $queryString) as $query) {

			if (!str_contains($query, '=')) return [];

			$key = explode('=', $query)[0];
			$value = explode('=', $query)[1];

			$queryParams[$key] = $value;
		}

		return $queryParams;
	}
}
