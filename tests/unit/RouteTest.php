<?php

use Nigr\Router\Route;
use PHPUnit\Framework\TestCase;

class RouteTest extends TestCase
{
	/**
	 * @param string $method
	 * @param string $expected
	 * @return void
	 * @dataProvider getMethods
	 */
	public function testGetMethod(string $method, string $expected): void
	{
		$route = new Route($method, '', [TestController::class, 'action']);

		self::assertEquals(strtolower($expected), $route->getMethod());
	}

	public static function getMethods(): array
	{
		return [
			['GET', 'get'],
			['POST', 'post'],
			['put', 'put'],
			['Delete', 'delete'],
		];
	}

	/**
	 * @param $expected
	 * @param $path
	 * @return void
	 * @dataProvider getPaths
	 */
	public function testGetPath($path, $expected): void
	{
		$route = new Route('GET', $path, [TestController::class, 'action']);

		self::assertEquals($expected[0], $route->getPath()[0]);
	}

	public static function getPaths(): array
	{
		return [
			['', ['']],
			['/', ['']],
			['name', ['name']],
			['/name/', ['name']],
			['name/{id}', ['name']],
			['/name/{id}/', ['name']],
			['//name/{id}/', ['name']],
		];
	}

	/**
	 * @param array $handler
	 * @param array $expected
	 * @return void
	 * @dataProvider getHandlers
	 */
	public function testGetHandler(array $handler, array $expected): void
	{
		$route = new Route('GET', '', $handler);

		self::assertEquals($expected, $route->getHandler());
	}

	public static function getHandlers(): array
	{
		return [
			[[TestController::class, 'read'], [TestController::class, 'read']],
			[[TestController::class, 'test'], [TestController::class, 'test']],
		];
	}

	/**
	 * @param string $path
	 * @param string|null $expected
	 * @return void
	 * @dataProvider getPathsForParam
	 */
	public function testGetParamName(string $path, string|null $expected): void
	{
		$route = new Route('GET', $path, [TestController::class, 'action']);

		self::assertEquals($expected, $route->getParamName());
	}

	public static function getPathsForParam(): array
	{
		return [
			['', null],
			['name', null],
			['name/{id}', 'id'],
			['/name/{id}/', 'id'],
			['name/test', null],
			['/name/test/', null],
		];
	}
}
