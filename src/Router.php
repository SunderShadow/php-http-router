<?php

namespace Sunder\Http;

use Closure;
use Sunder\Http\Request\Request;
use Sunder\Http\Request\Response;

class Router
{
    const string DYNAMIC_ROUTE_PART_START = '[';
    const string DYNAMIC_ROUTE_PART_END   = ']';

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
        if (!isset($this->routes[$method])) {
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
        $handler = $this->findRoute($request) ?? $this->_404Handler;

        return $handler($request);
    }

    private function findRoute(Request &$request): ?callable
    {
        // TODO: Improve route search
        if (!isset($this->routes[$request->method])) {
            return null;
        }

        $methodRoutes = $this->routes[$request->method];

        $requestURI = explode('/', $request->uri);
        foreach ($methodRoutes as $route => $handler) {
            $routeURI = explode('/', $route);

            if (count($requestURI) !== count($routeURI)) {
                continue;
            }

            $isCurrentRoute = true;
            $routeData = [];

            foreach ($routeURI as $k => $routePart) {
                $requestURIPart = $requestURI[$k];

                if ($this->routePartIsDynamic($routePart)) {
                    $routeData[substr($routePart, 1, -1)] = $requestURIPart;
                } else if ($routePart !== $requestURIPart) {
                    $isCurrentRoute = false;
                    break;
                }
            }

            if ($isCurrentRoute) {
                $request = new Request($request->method, $request->uri, $routeData, $request->data);
                return $handler;
            }
        }

        return null;
    }

    private function routePartIsDynamic(string $part): bool
    {
        return
               str_starts_with($part, self::DYNAMIC_ROUTE_PART_START)
            && str_ends_with($part, self::DYNAMIC_ROUTE_PART_END);
    }
}