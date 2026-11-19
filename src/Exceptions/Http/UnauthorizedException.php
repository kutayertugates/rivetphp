<?php

namespace Rivetphp\Exceptions\Http;

/**
 * Raised when the request needs authentication
 *
 * @package Rivetphp\Exceptions\Http
 * @since v0.1.0
 */
class UnauthorizedException extends \Exception
{
    /**
     * Creates a 401 Unauthorized exception
     *
     * @param string $message Error message
     * @param int $code HTTP status code
     * @param \Throwable|null $previous Previous exception, if any
     * @since v0.1.0
     */
    public function __construct(
        string $message = "",
        int $code = 401,
        \Throwable|null $previous = null,
    ) {
        return parent::__construct($message, $code, $previous);
    }
}
