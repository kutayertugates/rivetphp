<?php

namespace Rivetphp\Http\Response;

/**
 * Handles the outgoing HTTP response
 *
 * Shared base for response types, holding the body and status code
 *
 * @package Rivetphp\Http\Response
 * @since v0.1.0
 */
class BaseResponse
{
    private string $raw_response;
    private int $status_code;

    /**
     * Wraps a raw response body with a status code
     *
     * @param string $_raw_response Response body
     * @param int $_status_code HTTP status code
     * @since v0.1.0
     */
    public function __construct(string $_raw_response, int $_status_code)
    {
        $this->raw_response = $_raw_response;
        $this->status_code = $_status_code;
    }

    /**
     * Sends the status code and body to the client
     *
     * @return void
     * @since v0.1.0
     */
    public function apply()
    {
        http_response_code($this->status_code);
        echo $this->raw_response;
    }
}
