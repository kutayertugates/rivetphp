<?php

namespace Rivetphp\Exceptions\Http;

/**
 * Raised when the client is not allowed to access the resource
 *
 * @package Rivetphp\Exceptions\Http
 * @since v0.1.0
 */
class ForbiddenExceiption extends \Exception
{
    /**
     * Creates a 403 Forbidden exception
     *
     * @param string $message Error message
     * @param int $code HTTP status code
     * @param \Throwable|null $previous Previous exception, if any
     * @since v0.1.0
     */
    public function __construct(
        string $message = "",
        int $code = 403,
        \Throwable|null $previous = null,
    ) {
        return parent::__construct($message, $code, $previous);
    }
}
