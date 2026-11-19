<?php

namespace Rivetphp\Routing;

use Rivetphp\Http\Request\Request;
use Rivetphp\Http\Response\BaseResponse;
use Rivetphp\Exceptions\NotFoundException;

use Rivetphp\Routing\Route;

class Router
{
    private Request $request;

    private array $routes = [];

    public function __construct(Request $_request)
    {
        $this->request = $_request;
    }

    // HELPERS

    public function add_route(
        string $_path,
        callable $_handler,
        array $_methods,
        array $_middlewares,
    ) {
        $this->routes[] = new Route(
            $_methods,
            $_path,
            $_handler,
            $_middlewares,
        );
    }

    private function matchPattern(string $_pattern, string $_path): array|null
    {
        if (!preg_match($_pattern, $_path, $matches, PREG_UNMATCHED_AS_NULL)) {
            return null;
        }

        $params = [];

        foreach ($matches as $key => $value) {
            if (is_string($key)) {
                $params[$key] = $value !== "" ? $value : null;
            }
        }

        return $params;
    }

    public function dispatch(): BaseResponse
    {
        $route = null;
        $params = null;
        foreach ($this->routes as $r) {
            $params = $this->matchPattern($r->pattern, $this->request->path);
            if (
                $params !== null &&
                in_array($this->request->method, $r->methods)
            ) {
                $route = $r;
                break;
            }
        }

        if ($route === null) {
            throw new NotFoundException("Page not found", 404);
        }

        $this->request->params = $params;

        foreach ($route->middlewares as $mw) {
            $class = new $mw($this->request);
            $class->apply();
        }

        $response = ($route->handler)($this->request);

        return $response;
    }
}
