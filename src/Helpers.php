<?php

namespace Nigr\Router;

class Helpers
{
	/**
	 * @param string $url
	 * @return string
	 */
	public function getPathFromUrl(string $url): string
	{
		return $this->normalizePath(explode('?', $url)[0]);
	}

	/**
	 * @param string $path
	 * @return string
	 */
	public function normalizePath(string $path): string
	{
		return trim(strtolower($path), '/');
	}

	/**
	 * @param $path
	 * @return array
	 */
	public function getPathAndParamKeyFromPath($path): array
	{
		if (preg_match('/\{([^}]*)}/', $path, $matches)) {
			$paramKey = $matches[1];
			$path = str_replace($matches[0], '', $path);
		}

		return [trim($path, '/'), $paramKey ?? null];
	}
}
