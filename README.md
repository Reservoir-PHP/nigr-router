## Necessary:

    - .htaccess(in root project folder)?

## Install library:

```bash
    composer require nigr/router:@dev
```

## Scheme URI

```text
	[method] [protocol]://[host]/[path]?[queryParams]
	---
	[GET] [https]://[www.example.ru]/
	[GET] [https]://[www.example.ru]/[test]?[age=20]
	[GET] [https]://[www.example.ru]/[test/1]?[age=20]
```

## Start router:

```php
	$router = new Router();
	list($controller, $action, $params) = $router->run();
	...
```

## Route structure:

```php
$route = ("method" => "", "path" => "", "handler" => ["controller", "action"]);

return [
	'controller' => $controller, 
	'action' => $action, 
	'params' => ['get' => $queryParams, 'post' => $post, 'input' => $input]
];

```
