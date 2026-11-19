<?php

namespace Rivetphp\Exceptions\Http;

/**
 * Raised when the HTTP method is not allowed for the route
 *
 * @package Rivetphp\Exceptions\Http
 * @since v0.1.0
 */
class MethodNotAllowedException extends \Exception
{
    /**
     * Creates a 405 Method Not Allowed exception
     *
     * @param string $message Error message
     * @param int $code HTTP status code
     * @param \Throwable|null $previous Previous exception, if any
     * @since v0.1.0
     */
    public function __construct(
        string $message = "",
        int $code = 405,
        \Throwable|null $previous = null,
    ) {
        return parent::__construct($message, $code, $previous);
    }
}
