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

	public function testUser($params = []): array
	{
		return ['action' => 'test', 'params' => $params];
	}

	public function createUser(): array
	{
		$params = json_decode(file_get_contents("php://input"), true) ?? $_POST;

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
	 * @throws Exception
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
		// TODO To decide
//		if (isset(explode("?", $data[0])[1])) {
//			parse_str(parse_url($data[0], PHP_URL_QUERY), $_GET);
//		}

		$routes = new Routes();
		$routes->add(new Route("GET", "", [TestController::class, 'home']));
		$routes->add(new Route("GET", "users", [TestController::class, 'readUser']));
		$routes->add(new Route("GET", "users/test", [TestController::class, 'testUser']));
		$routes->add(new Route("GET", "/users/{id}/", [TestController::class, 'readUser']));
		$routes->add(new Route("POST", "users", [TestController::class, 'createUser']));
		$router = new Router($routes->get());

		if (is_string($expected)) {
			$this->expectException(Exception::class);
			$this->expectExceptionMessage($expected);
			$router->run();

			return;
		}

		if (isset($data[2]) && $_SERVER['REQUEST_METHOD'] === 'POST' && $data[2] === ["post" => "form_data"]) {
			$_POST = $data[2];
			$_SERVER['CONTENT_TYPE'] = 'application/x-www-form-urlencoded';
			$_SERVER['CONTENT_LENGTH'] = strlen(http_build_query($_POST));
		}

		list($controller, $action, $param) = $router->run();

		if (isset($data[2])) {
			$result = (new $controller)->$action($param);

			self::assertEquals($data[2], $result['params']);
		}

		self::assertEquals($expected['controller'], $controller);
		self::assertEquals($expected['action'], $action);
		self::assertEquals($expected['params'], $param);
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
				"Route 'name' does not exist!"
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
				['users/test', "GET"],
				['controller' => TestController::class, 'action' => 'testUser', 'params' => []]
			],
			[
				['/users/3?name=ivan&age=30', "GET"],
				['controller' => TestController::class, 'action' => 'readUser', 'params' => ['age' => 30, 'name' => 'ivan', 'id' => '3']]
			],
			[
				['users', "POST"],
				['controller' => TestController::class, 'action' => 'createUser', 'params' => []]
			],
			[
				['users', "POST", ["post" => "form_data"]],
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
