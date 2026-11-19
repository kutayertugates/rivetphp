<?php

namespace Rivetphp\Routing;

/**
 * Represents a single route
 *
 * Keeps the handler, allowed methods and compiled pattern for a path
 *
 * @package Rivetphp\Routing
 * @since v0.1.0
 */
class Route
{
    public array $methods = [];
    public string $path = "";
    public $handler;

    public string $pattern;
    public array $middlewares = [];

    /**
     * Builds the route and compiles its pattern
     *
     * @param array $_methods Allowed HTTP methods, GET when empty
     * @param string $_path Route path
     * @param callable $_handler Route handler
     * @param array $_middlewares Middleware class names
     * @since v0.1.0
     */
    public function __construct(
        array $_methods,
        string $_path,
        callable $_handler,
        array $_middlewares,
    ) {
        if ($_methods === []) {
            $this->methods[] = "GET";
        } else {
            foreach ($_methods as $m) {
                $this->methods[] = strtoupper($m);
            }
        }
        $this->path = $_path;
        $this->handler = $_handler;
        $this->pattern = $this->generatePattern();
        $this->middlewares = $_middlewares;
    }

    /**
     * Converts the path into a regex pattern
     *
     * Supports plain segments and named parameters like {id} and {id?}
     *
     * @return string Compiled regex pattern
     * @since v0.1.0
     */
    private function generatePattern(): string
    {
        $segments = array_filter(
            explode("/", trim($this->path, "/")),
            static fn(string $segment): bool => $segment !== "",
        );

        $pattern = "";

        foreach ($segments as $segment) {
            if (
                !preg_match(
                    "#^\{([a-zA-Z_][a-zA-Z0-9_]*)(\?)?\}$#",
                    $segment,
                    $m,
                )
            ) {
                $pattern .= "/" . preg_quote($segment, "#");
                continue;
            }

            $group = "(?P<" . $m[1] . ">[^/]+)";

            $pattern .=
                ($m[2] ?? "") === "?" ? "(?:/" . $group . ")?" : "/" . $group;
        }

        return "#^" . $pattern . "/?$#";
    }
}
