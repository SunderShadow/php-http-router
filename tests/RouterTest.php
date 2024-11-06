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