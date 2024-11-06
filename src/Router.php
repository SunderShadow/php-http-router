<?php

namespace Sunder\Http;

use Closure;
use Sunder\Http\Request\Request;
use Sunder\Http\Request\Response;

class Router
{
    /**
     * All registered routes
     * stores here
     * @var array
     */
    private array $routes = [];

    /**
     * Handler that will be
     * triggered on undefined route
     * @var Closure
     */
    private mixed $_404Handler;

    public function __construct()
    {
        $this->_404Handler = function (Request $request) {
            return new Response(
                status: 404,
                body:   "Undefined route $request->method $request->uri"
            );
        };
    }

    /**
     * Add new route or rewrite old
     * @param string $method
     * @param string $route
     * @param callable $cb
     * @return void
     */
    public function addRoute(string $method, string $route, callable $cb): void
    {
        if (!$this->routes[$method]) {
            $this->routes[$method] = [];
        }

        $this->routes[$method][$route] = $cb;
    }

    /**
     * Set handler that will be
     * triggered on undefined route
     * @see static::$_404Handler
     * @param callable $cb
     * @return void
     */
    public function set404Handler(callable $cb): void
    {
        $this->_404Handler = $cb;
    }

    /**
     * Run all processes
     * @param Request $request
     * @return Response
     */
    public function run(Request $request): Response
    {
        $handler = $this->_404Handler;

        if ($this->routeExists($request->method, $request->uri)) {
            $handler = $this->routes[$request->method][$request->uri];
        }

        return $handler($request);
    }

    /**
     * Check if route exists
     * @param string $method
     * @param string $uri
     * @return bool
     */
    public function routeExists(string $method, string $uri): bool
    {
        return isset($this->routes[$method][$uri]);
    }
}