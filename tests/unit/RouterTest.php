<?php

namespace Nigr\Tests\unit;

use Nigr\Router\Router;
use PHPUnit\Framework\TestCase;
use ReflectionClass;
use ReflectionException;

class RouterTest extends TestCase
{
	private Router $router;

	protected function setUp(): void
	{
		parent::setUp();

		$_SERVER['REQUEST_METHOD'] = 'GET';
		$_SERVER['REQUEST_URI'] = '/test';

		$this->router = new Router();
	}

	/**
	 * @param string $expected
	 * @param string $path
	 * @return void
	 * @throws ReflectionException
	 * @dataProvider getPathData
	 */
	public function testNormalizePath(string $expected, string $path): void
	{
		$reflection = new ReflectionClass($this->router);
		$normalizePathMethod = $reflection->getMethod('normalizePath');

		$result = $normalizePathMethod->invoke($this->router, $path);

		self::assertEquals($expected, $result);
	}

	public static function getPathData(): array
	{
		return [
			['test', "/test"],
			['test', "test/"],
			['test', "/test/"],
			['test', "test"]
		];
	}

	/**
	 * @param $expected
	 * @param $queryParams
	 * @return void
	 * @throws ReflectionException
	 * @dataProvider getQueryParamsData
	 */
	public function testGetQueryParams($expected, $queryParams): void
	{
		$reflection = new ReflectionClass($this->router);
		$getQueryParamsMethod = $reflection->getMethod('getQueryParams');

		$result = $getQueryParamsMethod->invoke($this->router, $queryParams);

		self::assertEquals($expected, $result);
	}

	public static function getQueryParamsData(): array
	{
		return [
			[[], ""],
			[['name' => "bob"], "name=bob"],
			[['name' => "bob", 'age' => 25], "name=bob&age=25"],
		];
	}

	public function testAdd()
	{
		$this->router->add('GET', '/test', [TestsController::class, 'get']);

		$reflection = new ReflectionClass($this->router);
		$routesProperty = $reflection->getProperty('routes');
		$routes = $routesProperty->getValue($this->router);

		self::assertCount(1, $routes);
		self::assertEquals('GET', $routes[0]['method']);
		self::assertEquals('test', $routes[0]['path']);
		self::assertEquals([TestsController::class, 'get'], $routes[0]['handler']);
	}

	public function testRunReturns404()
	{
		$response = $this->router->run();

		self::assertIsArray($response);
		self::assertFalse($response['status']);
		self::assertEquals(404, $response['code']);
		self::assertArrayHasKey('message', $response['data']);
	}

	public function testRouteMatchesAndReturnsControllerResult(): void
	{
		$_GET = ['test' => 'test'];

		$this->router->add('GET', '/test', [\Nigr\Tests\unit\TestsController::class, 'get']);

		$result = $this->router->run();

		$this->assertIsArray($result);
		$this->assertArrayHasKey('action', $result);
		$this->assertEquals($_GET, $result['params']);
	}
}