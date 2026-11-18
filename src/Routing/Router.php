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
        array $_methods,
        string $_path,
        callable $_handler,
    ) {
        $this->routes[] = new Route($_methods, $_path, $_handler);
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
            if ($params === null) {
                continue;
            }

            if (!in_array($this->request->method, $r->methods)) {
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
