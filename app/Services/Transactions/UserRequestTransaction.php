<?php

namespace App\Services\Transactions;

use App\Models\User;
use App\Models\UserRequest;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Response;


class UserRequestTransaction extends Transaction
{
    /**
     * @param array $params
     * @param string $request.email
     * @param boolean $allow_commit
     * @throws \Exception
     * @return array
     */
    public function create($params, $allow_commit = false)
    {
        $response = null;
        $this->beginTransaction($allow_commit);
        try {
            $user = User::where('email',$params)->first();
            if (!isset($user->email)) {
                $this->error('Email không tồn tại');
            } elseif (!$user->isActive()) {
                $this->error('Tài khoản đang bị khóa');
            } else {
                $user_request = new UserRequest();
                $user_request->user_id = $user->id;
                $chars = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
                $user_request->otp = md5(mt_rand(10000,99999));
                $user_request->checksum = md5(str_shuffle($chars));
                $user_request->number_fail = 0;
                $user_request->expired_at = $user_request->getExpiredAt(time());
                $user_request->save();
            }
        } catch (\Exception $e) {
            // rollback transaction
            $this->rollback($allow_commit, $e);
        }
        //commit and response
        return $this->response($allow_commit, $response);
    }
}
