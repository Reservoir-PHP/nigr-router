<?php

namespace Nigr\Router;

class Routes
{
	private array $routes;

	public function __construct()
	{
		$this->routes = [];
	}

	/**
	 * @param Route $route
	 * @return void
	 */
	public function add(Route $route): void
	{
		$this->routes[] = $route;
	}

	/**
	 * @return array
	 */
	public function get(): array
	{
		return $this->routes;
	}
}
