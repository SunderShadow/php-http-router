<?php

use PHPUnit\Framework\TestCase;
use Sunder\Http\Request\Request;
use Sunder\Http\Request\Response;
use Sunder\Http\Router;

class RouterTest extends TestCase
{
    public function test_add_route()
    {
        $router = new Router();
        $router->addRoute('GET', '/asd', function (Request $request) {
            return new Response(
                status: 200,
                body:   $request instanceof (Request::class)
            );
        });

        $response = $router->run(new Request('GET', '/asd'));
        $this->assertTrue((bool) $response->body);
    }

    public function test_add_dynamic_routes()
    {
        $router = new Router();
        $router->addRoute('GET', '/some/[dynamic]/route/[otherDynamic]', function (Request $request) {
            return new Response(
                status: 200,
                body:   json_encode($request->routeData),
            );
        });

        $response = $router->run(new Request('GET', '/some/really_dynamic/route/123'));
        $data = json_decode($response->body, true);

        $this->assertEquals('really_dynamic', $data['dynamic']);
        $this->assertEquals('123', $data['otherDynamic']);
    }

    public function test_add_dynamic_routes_request_uri_longer_than_defined()
    {
        $router = new Router();
        $router->addRoute('GET', '/some/[dynamic]/route/[otherDynamic]', function (Request $request) {
            return new Response(
                status: 200,
                body:   json_encode($request->routeData),
            );
        });

        $response = $router->run(new Request('GET', '/some/really_dynamic/route/123/zxc'));

        $this->assertEquals(404, $response->status);
    }

    public function test_add_dynamic_routes_request_uri_shorten_than_defined()
    {
        $router = new Router();
        $router->addRoute('GET', '/some/[dynamic]/route/[otherDynamic]/zxc', function (Request $request) {
            return new Response(
                status: 200,
                body:   json_encode($request->routeData),
            );
        });

        $response = $router->run(new Request('GET', '/some/really_dynamic/route/123'));

        $this->assertEquals(404, $response->status);
    }

    public function test_404_route()
    {
        $router = new Router();

        $response = $router->run(new Request('GET', '/ReAl_undefined_Route'));
        $this->assertEquals(404, $response->status);
    }

    public function test_user_404_route()
    {
        $router = new Router();
        $router->set404Handler(function (Request $request) {
            return new Response(
                status: 404,
                body:   'banana'
            );
        });

        $response = $router->run(new Request('GET', '/Another_ReAl_undefined_Route'));
        $this->assertEquals('banana', $response->body);
    }
}