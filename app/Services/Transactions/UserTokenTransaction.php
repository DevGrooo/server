<?php

namespace App\Services\Transactions;

use App\Models\User;
use App\Models\UserToken;

class UserTokenTransaction extends Transaction
{
    /**
     * Create user token
     * 
     * @param array $params
     * @param integer $params.user_id
     * @param boolean $allow_commit
     * @throws \Exception
     * @return array
     */
    public function create($params, $allow_commit = false)
    {
        $response = null;
        // start transaction
        $this->beginTransaction($allow_commit);
        try {
            $user_token = new UserToken();
            $token = $user_token->generateToken();
            $user_token->user_id = $params['user_id'];
            $user_token->hash_token = $token;
            $user_token->expired_at = $user_token->getExpiredAt(time());
            if ($user_token->save()) {
                $response['token'] = $token;
            } else {
                $this->error('Có lỗi khi sinh token');
            }
        } catch (\Exception $e) {
            // rollback transaction
            $this->rollback($allow_commit, $e);
        }
        // commit and response
        return $this->response($allow_commit, $response);
    }

    /**
     * Delete all user_token by user_id
     * 
     * @param array $params
     * @param boolean $allow_commit
     * @return array
     */
    public function deleteByUser($params, $allow_commit = false)
    {
        $response = null;
        // start transaction
        $this->beginTransaction($allow_commit);
        try {
            $rows = UserToken::where('user_id', $params['user_id']);
            if ($rows->count() && !$rows->delete()) {
                $this->error('Có lỗi khi xóa token');
            }
        } catch (\Exception $e) {
            // rollback transaction
            $this->rollback($allow_commit, $e);
        }
        // commit and response
        return $this->response($allow_commit, $response);
    }

    /**
     * Make token by user_id
     * 
     * @param array $params
     * @param boolean $allow_commit
     * @return array
     */
    public function makeToken($params, $allow_commit = false)
    {
        $response = null;
        // start transaction
        $this->beginTransaction($allow_commit);
        try {            
            $this->deleteByUser($params);
            $response = $this->create($params);
        } catch (\Exception $e) {
            // rollback transaction
            $this->rollback($allow_commit, $e);
        }
        // commit and response
        return $this->response($allow_commit, $response);
    }
}
