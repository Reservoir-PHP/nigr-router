<?php

namespace Nigr\Router;

class Route
{
	private Helpers $helpers;
	private string $method;
	private string|array $path;
	private array $handler;
	private string | null $paramName;

	public function __construct(string $method, string $path, array $handler)
	{
		$this->helpers = new Helpers();
		$this->method = strtolower($method);
		$this->handler = $handler;

		list($path, $this->paramName) = $this->helpers->getPathAndParamKeyFromPath($path);

		$this->path = explode('/', trim($path, '/'));
	}

	/**
	 * @return string
	 */
	public function getMethod(): string
	{
		return $this->method;
	}

	/**
	 * @return array
	 */
	public function getPath(): array
	{
		return $this->path;
	}

	/**
	 * @return array
	 */
	public function getHandler(): array
	{
		return $this->handler;
	}

	/**
	 * @return string|null
	 */
	public function getParamName(): string|null
	{
		return $this->paramName;
	}
}
