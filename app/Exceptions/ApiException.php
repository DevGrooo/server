<?php

namespace App\Exceptions;

use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Response;

class ApiException extends HttpResponseException
{
    /**
     * @param string $message
     */
    function __construct($message, $code = null)
    {
        if ($code == null) {
            $code = Response::HTTP_OK;
        }
        $json = response()->json([
            'status'   => false,
            'statusCode'     => intval($code),
            'message' => trans($message),
            'response' => null
        ], $code);
        parent::__construct($json);
    }
}