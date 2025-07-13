## Necessary:
    - .htaccess(in root project folder)
## Install library:
```bash
    composer require nigr/router:@dev
```
## Start router:
    - $router = new Router();
    - $router->add("prefer-method", "/your-url", [\YourController::class, "your-action"]);
    - $response = $router->run();
