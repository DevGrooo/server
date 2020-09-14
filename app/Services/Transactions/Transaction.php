<?php

namespace App\Services\Transactions;

use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Response;
use App\Exceptions\ApiException;
use Illuminate\Support\Facades\Validator;

class Transaction
{
    protected $e = null;

    /**
     * @param boolean allow_commit
     */
    final public function beginTransaction($allow_commit)
    {
        if ($allow_commit) {
            DB::beginTransaction();
        }
    }

    /**
     * @param boolean allow_commit
     */
    final public function commit($allow_commit)
    {
        if ($allow_commit) {
            DB::commit();
        }
    }

    /**
     * @param boolean allow_commit
     */
    final public function rollback($allow_commit, $e)
    {
        // echo get_class($this).chr(10);
        // $trace = $e->getTrace();
        // var_dump($trace[1]);die();
        $e = $this->convertToApiException($e);
        if ($allow_commit) {
            $this->e = $e;
            DB::rollBack();
        }        
        throw $e;
    }

    /**
     * @param \Exception $e
     * @return \HttpResponseException
     */
    final public function convertToApiException(\Exception $e)
    {
        if ($e instanceof ApiException) {
            return $e;
        } elseif ($e instanceof \Exception) {
            $message = $e->getMessage();
        } else {
            $message = 'Error undefined';
        }
        return new ApiException($message);
    }

    /**
     * @param string message
     * @param object response
     */
    final public function response($allow_commit, $response = null)
    {
        // $status = false;
        // if ($this->e !== null) {
        //     $this->rollback($allow_commit, $this->e);
        // } else {
        //     $status = true;
        //     $message = 'Success';
        //     $code = Response::HTTP_OK;
        //     $this->commit($allow_commit);
        // }
        if ($allow_commit) {
            $this->commit($allow_commit);
            return array(
                'status' => true,
                'statusCode' => Response::HTTP_OK,
                'message' => trans('Success'),
                'response' => $response, 
            );
        }        
        return $response;
    }

    /**
     * @param string $error_message
     * @throws ApiException
     */
    final public function error($error_message)
    {
        throw new ApiException($error_message);
    }

    /**
     * Check row exists by id
     * @param string $model_name
     * @param integer $id
     * @param string $error_message
     * @throws ApiException
     */
    final public function checkRowExists($id, $error_message, &$row = null)
    {
        $validator = Validator::make(['id' => $id], ['id' => 'row_exists']);
        if ($validator->fails()) {
            $this->error($error_message);
        }
    }
}
