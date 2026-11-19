<?php

namespace Rivetphp\Http\Response;

/**
 * Sends JSON responses
 *
 * Encodes the payload and sets the JSON content type before sending
 *
 * @package Rivetphp\Http\Response
 * @since v0.1.0
 */
class JsonResponse extends BaseResponse
{
    /**
     * Encodes the payload as a JSON response
     *
     * @param array $_payload Data to encode
     * @param int $_status_code HTTP status code
     * @since v0.1.0
     */
    public function __construct(array $_payload, int $_status_code = 200)
    {
        $raw_response = json_encode($_payload);
        $this->addHeader("Content-Type", "application/json");

        parent::__construct($raw_response, $_status_code);
    }
}
