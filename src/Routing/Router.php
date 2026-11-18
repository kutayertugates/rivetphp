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

    private function add_route(
        string $_method,
        string $_path,
        callable $_handler,
    ) {
        $this->routes[] = new Route($_method, $_path, $_handler);
        return;
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

    // PUBLIC

    public function get(string $_path, callable $_handler)
    {
        $this->add_route("GET", $_path, $_handler);
    }

    public function post(string $_path, callable $_handler)
    {
        $this->add_route("POST", $_path, $_handler);
    }

    public function put(string $_path, callable $_handler)
    {
        $this->add_route("PUT", $_path, $_handler);
    }

    public function patch(string $_path, callable $_handler)
    {
        $this->add_route("PATCH", $_path, $_handler);
    }

    public function delete(string $_path, callable $_handler)
    {
        $this->add_route("DELETE", $_path, $_handler);
    }

    public function options(string $_path, callable $_handler)
    {
        $this->add_route("OPTIONS", $_path, $_handler);
    }

    public function dispatch(): BaseResponse
    {
        $route = null;
        $params = null;
        foreach ($this->routes as $r) {
            $params = $this->matchPattern($r->pattern, $this->request->path);
            if ($params === null) {
                continue;
            }

            if ($this->request->method !== $r->method) {
                continue;
            }

            $route = $r;
            break;
        }

        if ($route === null) {
            throw new NotFoundException("Page not found", 404);
        }

        $this->request->params = $params;

        $response = ($route->handler)($this->request);

        return $response;
    }
}
