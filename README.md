# Using


## Route definition

```php
$router = new \Sunder\Http\Router();
$router->addRoute(
    method: "GET",
    route:  '/some/path', 
    cb:     function (\Sunder\Http\Request\Request $request): \Sunder\Http\Request\Response {
                /** !!!Important!!!
                *   Each route MUST return
                *   \Sunder\Http\Request\Response object 
                **/ 
                return new \Sunder\Http\Request\Response(
                    // Some code here
                )
            }
);

$response = $router->run(new \Sunder\Http\Request\Request(
    method: 'GET', 
    uri: '/some/path'
))

// ---------------------------
// One of possible ways to use
// ---------------------------
foreach ($response->headers as $header) {
    header($header);
}

echo $response->body;
```

## Dynamic route definition

```php
$router = new \Sunder\Http\Router();
$router->addRoute(
    method: "GET",
    route:  '/some/[dynamic]/', 
    cb:     function (\Sunder\Http\Request\Request $request): \Sunder\Http\Request\Response {
                /**
                * @var string 'some_dynamic_data' 
                */
                $var = $request->routeData['dynamic'];
                
                return new \Sunder\Http\Request\Response(
                    // Some code here
                )
            }
);

$response = $router->run(new \Sunder\Http\Request\Request(
    method: 'GET', 
    uri: '/some/some_dynamic_data/dynamic'
))
```

## 404 Route definition

By default, router has its own [404 handler (16 line)](./src/Router.php)

But you can define your own
```php
$router = new \Sunder\Http\Router();

// ...

$router->set404Handler(function (\Sunder\Http\Request\Request $request) {
    return new \Sunder\Http\Request\Response(
        status: 404,
        body: 'Undefined route ...'
    )
});
```
