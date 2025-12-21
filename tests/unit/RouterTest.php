<?php

use Nigr\Router\Route;
use Nigr\Router\Router;
use Nigr\Router\Routes;
use PHPUnit\Framework\TestCase;

class TestController
{
	public function home($params = []): array
	{
		return ['action' => 'home', 'params' => $params];
	}

	public function readUser($params = []): array
	{
		return ['action' => 'get', 'params' => $params];
	}

	public function createUser(): array
	{
		$params = json_decode(file_get_contents("php://input"), true);

		var_dump(">>>", $params);

		return ['action' => 'post', 'params' => $params];
	}
}

class RouterTest extends TestCase
{
	/**
	 * @param array $data
	 * @param array|string $expected
	 * @return void
	 * @dataProvider getRouterData
	 */
	public function testRun(array $data, array|string $expected): void
	{
		$_SERVER['REQUEST_URI'] = $data[0];
		$_SERVER['REQUEST_METHOD'] = $data[1];

		$queryParams = explode("?", $data[0])[1] ?? null;

		if ($queryParams) {
			$arr = explode("&", $queryParams);

			foreach ($arr as $param) {
				$key = explode("=", $param)[0];
				$value = explode("=", $param)[1];

				$_GET[$key] = $value;
			}
		}

		$routes = new Routes();
		$routes->add(new Route("GET", "", [TestController::class, 'home']));
		$routes->add(new Route("GET", "users", [TestController::class, 'readUser']));
		$routes->add(new Route("GET", "/users/{id}/", [TestController::class, 'readUser']));
		$routes->add(new Route("POST", "users", [TestController::class, 'createUser']));
		$router = new Router($routes->get());
		$result = $router->run();

		if (is_array($result)) {
			self::assertEquals($expected['controller'], $result[0]);
			self::assertEquals($expected['action'], $result[1]);
			self::assertEquals($expected['params'], $result[2]);
		} else {
			self::assertEquals($expected, $result);
		}
	}

	public static function getRouterData(): array
	{
		return [
			[
				['', 'GET'],
				['controller' => TestController::class, 'action' => 'home', 'params' => []]
			],
			[
				['name', 'GET'],
				"Route does not exist!"
			],
			[
				['users', "GET"],
				['controller' => TestController::class, 'action' => 'readUser', 'params' => []]
			],
			[
				['users?name=ivan', "GET"],
				['controller' => TestController::class, 'action' => 'readUser', 'params' => ['name' => "ivan"]]
			],
			[
				['users/3', "GET"],
				['controller' => TestController::class, 'action' => 'readUser', 'params' => ['id' => 3]]
			],
			[
				['/users/3?name=ivan&age=30', "GET"],
				['controller' => TestController::class, 'action' => 'readUser', 'params' => ['age' => 30, 'name' => 'ivan', 'id' => '3']]
			],
			[
				['users', "POST"],
				['controller' => TestController::class, 'action' => 'createUser', 'params' => []]
			],
		];
	}

	/**
	 * @param string $uri
	 * @param array $expected
	 * @return void
	 * @throws ReflectionException
	 * @dataProvider getUrls
	 */
	public function testParseUrl(string $uri, array $expected): void
	{
		$router = new Router();
		$_SERVER['REQUEST_URI'] = $uri;
		$reflection = new ReflectionClass($router);
		$parseUrlMethod = $reflection->getMethod('parseUrl');

		$result = $parseUrlMethod->invoke($router);

		self::assertEquals($expected[0], $result['path'][0]);
		self::assertEquals($expected[1], $result['path'][1] ?? null);
	}

	public static function getUrls(): array
	{
		return [
			['', ['', null]],
			['/', ['', null]],
			['name/3', ['name', '3']],
			['/name/23/', ['name', '23']],
			['name/test', ['name', 'test']],
			['/name/test/', ['name', 'test']],
		];
	}
}
