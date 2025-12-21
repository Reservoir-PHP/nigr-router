<?php

use Nigr\Router\Route;
use Nigr\Router\Routes;
use PHPUnit\Framework\TestCase;

class RoutesTest extends TestCase
{
	public static array $result;

	public static function setUpBeforeClass(): void
	{
		parent::setUpBeforeClass();

		$handler = [TestController::class, 'get'];
		$routes = new Routes();
		$routes->add(new Route("GET", "/name/{id}", $handler));
		self::$result = $routes->get();
	}

	/**
	 * @return void
	 */
	public function testAdd(): void
	{
		self::assertIsArray(self::$result);
		foreach (self::$result as $item) {
			self::assertInstanceOf(Route::class, $item);
		}
	}

	/**
	 * @return void
	 */
	public function testGet(): void
	{
		self::assertIsArray(self::$result);
		foreach (self::$result as $item) {
			self::assertInstanceOf(Route::class, $item);
		}
	}
}
