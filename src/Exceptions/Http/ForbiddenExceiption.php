<?php

namespace Rivetphp\Exceptions\Http;

class ForbiddenExceiption extends \Exception
{
    public function __construct(
        string $message = "",
        int $code = 403,
        \Throwable|null $previous = null,
    ) {
        return parent::__construct($message, $code, $previous);
    }
}
