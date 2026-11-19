<?php

namespace Rivetphp;

use Rivetphp\Http\Request\Request;
use Rivetphp\Http\Response\BaseResponse;
use Rivetphp\Http\Response\HtmlResponse;
use Rivetphp\Routing\Router;

/**
 * Application entry point
 *
 * Wires the request, router and error handlers together
 *
 * @package Rivetphp
 * @since v0.1.0
 */
class Rivetphp
{
    private string $app_path;

    private Request $request;
    private Router $router;

    private array $error_handlers = [];
    private array $after_request = [];

    public string $template_dir;

    /**
     * Creates the request and router instances
     *
     * @since v0.1.0
     */
    public function __construct(string $_app_path)
    {
        $this->app_path = rtrim($_app_path, "/") . "/";
        $this->request = new Request();
        $this->router = new Router($this->request);

        $this->template_dir = $this->app_path . "templates/";
    }

    public function setTemplateDir(string $_template_dir)
    {
        $this->template_dir =
            $this->app_path . rtrim($_template_dir, "/") . "/";
    }

    /**
     * Registers a handler for a specific throwable type
     *
     * @param string $_type Throwable class name
     * @param callable $_handler Callback that receives the exception
     * @return void
     * @since v0.1.0
     */
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

    public function register_after_request(callable $_func)
    {
        $this->after_request[] = $_func;
    }

    /**
     * Registers a route on the router
     *
     * @param string $_path Route path pattern
     * @param callable $_handler Route handler
     * @param array $_methods Allowed HTTP methods
     * @param array $_middlewares Middleware class names
     * @return void
     * @since v0.1.0
     */
    public function route(
        string $_path,
        callable $_handler,
        array $_methods = ["GET"],
        array $_middlewares = [],
    ) {
        $_methods[] = "OPTIONS";
        $this->router->add_route($_path, $_handler, $_methods, $_middlewares);
    }

    /**
     * Dispatches the request and sends the response
     *
     * Falls back to registered error handlers when something throws
     *
     * @return void
     * @since v0.1.0
     */
    public function run()
    {
        try {
            $response = $this->router->dispatch();
        } catch (\Throwable $e) {
            $error_handler_matched = false;
            foreach ($this->error_handlers as $h) {
                if ($e instanceof $h["type"]) {
                    $error_handler_matched = true;
                    $response = $h["handler"]($e);
                    break;
                }
            }
            if (!$error_handler_matched) {
                $response = new BaseResponse($e->getMessage(), 500);
            }
        }

        foreach ($this->after_request as $f) {
            $response = $f($response);
        }

        if ($response instanceof HtmlResponse) {
            $response->update_relpath_to_abspath($this->template_dir);
        }

        $response->apply();
    }
}
