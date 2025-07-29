## Necessary:
    - .htaccess(in root project folder)
## Install library:
```bash
    composer require nigr/router:@dev
```
## Start router:
```php
    $router = new Router();
    $router->add("your-method", "your-url", [\YourController::class, "your-action"]);
    $response = $router->run();
```
