<?php

namespace Rivetphp;

use Rivetphp\Http\Request\Request;
use Rivetphp\Routing\Router;

class Rivetphp
{
    private Request $request;
    private Router $router;
    private array $error_handlers = [];

    public function __construct()
    {
        $this->request = new Request();
        $this->router = new Router($this->request);
    }

    public function register_error_handler(string $_type, callable $_handler)
    {
        if (!is_a($_type, \Throwable::class, true)) {
            throw new \InvalidArgumentException(
                "Handler type must implement Throwable.",
            );
        }

        $this->error_handlers[] = [
            "type" => $_type,
            "handler" => $_handler,
        ];
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
        } catch (\Throwable $e) {
            foreach ($this->error_handlers as $h) {
                if ($e instanceof $h["type"]) {
                    $response = $h["handler"]($e);
                    break;
                }
            }
        }

        $response->apply();
    }
}
