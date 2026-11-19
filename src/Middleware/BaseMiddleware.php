<?php

namespace Rivetphp\Middleware;

use Rivetphp\Http\Request\Request;

/**
 * Base middleware contract
 *
 * Middlewares receive the request and run before the route handler
 *
 * @package Rivetphp\Middleware
 * @since v0.1.0
 */
class BaseMiddleware
{
    protected $request;

    /**
     * Stores the request for use in the middleware
     *
     * @param Request $_request Current request context
     * @since v0.1.0
     */
    public function __construct(Request $_request)
    {
        $this->request = $_request;
    }

    /**
     * Runs the middleware logic
     *
     * Override this method in concrete middlewares
     *
     * @return void
     * @since v0.1.0
     */
    public function apply() {}
}
