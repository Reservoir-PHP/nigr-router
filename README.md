## Necessary:
    - .htaccess
## Start:
    - $router = new Router();
    - $router->add("GET", "/test", [\TestController::class, "get"]);
    - $response = $router->run();
