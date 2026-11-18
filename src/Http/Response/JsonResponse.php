<?php

namespace Rivetphp\Http\Response;

class JsonResponse extends BaseResponse
{
    public function __construct(array $_payload, int $_status_code = 200)
    {
        $raw_response = json_encode($_payload);
        parent::__construct($raw_response, $_status_code);
    }

    public function apply()
    {
        header("Content-Type: application/json");
        return parent::apply();
    }
}
