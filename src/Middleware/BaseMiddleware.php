<?php

namespace Rivetphp\Middleware;

use Rivetphp\Http\Request\Request;

class BaseMiddleware
{
    protected $request;

    public function __construct(Request $_request)
    {
        $this->request = $_request;
    }

    public function apply() {}
}
