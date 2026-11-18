<?php

namespace Rivetphp\Http\Response;

class BaseResponse
{
    private string $raw_response;
    private int $status_code;

    public function __construct(string $_raw_response, int $_status_code)
    {
        $this->raw_response = $_raw_response;
        $this->status_code = $_status_code;
    }

    public function apply()
    {
        http_response_code($this->status_code);
        echo $this->raw_response;
    }
}
