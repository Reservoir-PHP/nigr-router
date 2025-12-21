<?php

use Nigr\Router\Helpers;
use PHPUnit\Framework\TestCase;

class HelpersTest extends TestCase
{
	private static Helpers $helpers;

	public static function setUpBeforeClass(): void
	{
		parent::setUpBeforeClass();

		self::$helpers = new Helpers();
	}

	/**
	 * @param string $expected
	 * @param string $data
	 * @return void
	 * @dataProvider getUrls
	 */
	public function testGetPathFromUrl(string $data, string $expected): void
	{
		$result = self::$helpers->getPathFromUrl($data);

		self::assertEquals($expected, $result);
	}

	public static function getUrls(): array
	{
		return [
			['', ''],
			['/', ''],
			['path', 'path'],
			['/paTh/', 'path'],
			['/path?id=1', 'path'],
			['/path/test?id=1', 'path/test'],
			['/path/test?id=1&name=test', 'path/test'],
		];
	}

	/**
	 * @param string $expected
	 * @param string $data
	 * @return void
	 * @dataProvider getPaths
	 */
	public function testNormalizePath(string $data, string $expected): void
	{
		$result = self::$helpers->normalizePath($data);

		self::assertEquals($expected, $result);
	}

	public static function getPaths(): array
	{
		return [
			['', ''],
			['/', ''],
			['path', 'path'],
			['/path/', 'path'],
			['/pAth/', 'path'],
		];
	}

	/**
	 * @param string $path
	 * @param array $expected
	 * @return void
	 * @dataProvider getPathsOrPathsWithParam
	 */
	public function testGetParamKeyFromPath(string $path, array $expected): void
	{
		$result = self::$helpers->getPathAndParamKeyFromPath($path);

		self::assertEquals($expected[0], $result[0]);
		self::assertEquals($expected[1], $result[1]);
	}

	public static function getPathsOrPathsWithParam(): array
	{
		return [
			['', ['', null, null]],
			['/', ['', null, null]],
			['name', ['name', null, null]],
			['name/{id}', ['name', 'id', null]],
			['/name/{id}/', ['name', 'id', null]],
		];
	}
}
