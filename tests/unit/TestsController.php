<?php

namespace Nigr\Tests\unit;

class TestsController
{
	public function get($params): array
	{
		return ['action' => 'get', 'params' => $params];
	}

	public function post(): array
	{
		$params = json_decode(file_get_contents("php://input"), true);

		return ['action' => 'post', 'params' => $params];
	}
}