<?php

namespace Rivetphp\Routing;

use Rivetphp\Exceptions\Http\MethodNotAllowedException;
use Rivetphp\Http\Request\Request;
use Rivetphp\Http\Response\BaseResponse;

use Rivetphp\Routing\Route;

/**
 * Matches incoming requests against registered routes
 *
 * @package Rivetphp\Routing
 * @since v0.1.0
 */
class Router
{
    private Request $request;

    private array $routes = [];

    /**
     * Stores the request used for matching
     *
     * @param Request $_request Current request context
     * @since v0.1.0
     */
    public function __construct(Request $_request)
    {
        $this->request = $_request;
    }

    // HELPERS

    /**
     * Adds a route to the collection
     *
     * @param string $_path Route path pattern
     * @param callable $_handler Route handler
     * @param array $_methods Allowed HTTP methods
     * @param array $_middlewares Middleware class names
     * @return void
     * @since v0.1.0
     */
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

    /**
     * Matches a pattern against a path and extracts named params
     *
     * @param string $_pattern Compiled route pattern
     * @param string $_path Request path
     * @return array|null Matched params, or null when it does not match
     * @since v0.1.0
     */
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

    /**
     * Finds the matching route, runs its middlewares and calls the handler
     *
     * @return BaseResponse
     * @throws MethodNotAllowedException When no route matches the request
     * @since v0.1.0
     */
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
            throw new MethodNotAllowedException("Page not found", 404);
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
