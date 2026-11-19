<?php

namespace Rivetphp\Exceptions\Http;

class MethodNotAllowedException extends \Exception
{
    public function __construct(
        string $message = "",
        int $code = 405,
        \Throwable|null $previous = null,
    ) {
        return parent::__construct($message, $code, $previous);
    }
}
