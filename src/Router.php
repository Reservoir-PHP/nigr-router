<?php

namespace Nigr\Router;

class Router
{
	private array $routes = [];
	private string $method;
	private string $path;

	public function __construct()
	{
		$this->method = strtolower($_SERVER['REQUEST_METHOD']);
		$this->path = $this->parseUrl($_SERVER['REQUEST_URI'])['path'];
	}

	public function run()
	{
		foreach ($this->routes as $route) {
			if (strtolower($route['method']) !== $this->method || $route['path'] !== $this->path) continue;

			$controller = new $route['handler'][0]();
			$action = $route['handler'][1];

			return $controller->$action($_GET);
		}

		return [
			'status' => false,
            "code" => 404,
			'data' => ['message' => 'Page not found, error 404']
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

	public function parseUrl(string $url): array
	{
		$path = $this->getPath($url);

        if ($this->method === "get") {
            $queryString = $this->normalizePath(explode('?', $url)[1] ?? "");
            $params = $this->getQueryParams($queryString);
        }

		return ['path' => $path, 'params' => $params ?? null];
	}

    private function getPath(string $url): string
    {
        return $this->normalizePath(explode('?', $url)[0]);
    }

    private function normalizePath(string $path): string
    {
        return trim(strtolower($path), '/');
    }

	private function getQueryParams(string $queryString): array
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
