<?php

namespace App\Http\Controllers;

use App\Exceptions\ApiException;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;
use Laravel\Lumen\Routing\Controller as BaseController;
use Illuminate\Support\Str;

class Controller extends BaseController
{
    public function validate(\Illuminate\Http\Request $request, array $rules, array $messages = [], array $customAttributes = [])
    {
        try {
            $validator = Validator::make($request->all(), $rules, $messages, $customAttributes);
            if ($validator->fails()) {
                throw new Exception($validator->errors()->first());
            }
            return $request->only(array_keys($rules));
            // return $this->extractInputFromRules($request, $rules);
            //return parent::validate($request, $rules, $messages, $customAttributes);
        } catch (\Exception $e) {
            throw new ApiException($e->getMessage());
        }
    }

    /**
     * @param mixed $ability
     * @param mixed|array $arguments
     * @return \Illuminate\Auth\Access\Response
     * @throws ApiException
     */
    public function authorize($ability, $arguments = [])
    {
        try {
            return parent::authorize($ability, $arguments);
        } catch (\Exception $e) {
            throw new ApiException($e->getMessage());
        }
    }

    public function error($message, $code = null)
    {
        throw new ApiException($message, $code);
    }

    public function responseSuccess($response = null)
    {
        return $this->response(true, Response::HTTP_OK, $response);
    }

    public function response($status, $code, $response = null, $message = '')
    {
        if ($code == Response::HTTP_OK) {
            $message = 'Success';
        } else {
            $status = false;
        }        
        return array(
            'status' => $status,
            'statusCode' => intval($code),
            'message' => trans($message),
            'response' => $response, 
        );
    }
}
