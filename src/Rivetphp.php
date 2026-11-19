<?php

namespace Rivetphp;

// Exception
use Rivetphp\Exceptions\NotFoundException;
// Http
use Rivetphp\Http\Request\Request;
use Rivetphp\Http\Response\BaseResponse;
// Routing
use Rivetphp\Routing\Router;

class Rivetphp
{
    private Request $request;
    private Router $router;

    public function __construct()
    {
        $this->request = new Request();
        $this->router = new Router($this->request);
    }

    public function route(
        string $_path,
        callable $_handler,
        array $_methods = ["GET"],
        array $_middlewares = [],
    ) {
        $this->router->add_route($_path, $_handler, $_methods, $_middlewares);
    }

    public function run()
    {
        try {
            $response = $this->router->dispatch();
        } catch (NotFoundException $e) {
            $response = new BaseResponse("", 404);
        } catch (\Throwable $e) {
            $response = new BaseResponse("", 500);
        }

        $response->apply();
    }
}
