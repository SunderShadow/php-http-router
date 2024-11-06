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
```

## 404 Route definition

By default, router has its own [404 handler (16 line)](./src/Router.php)
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
