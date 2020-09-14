<?php
namespace App\Services\Transactions;

use App\Models\AccountCommission;
use Illuminate\Support\Facades\DB;

/**
 * @author MSI
 * @version 1.0
 * @created 13-Jul-2020 2:52:40 PM
 */
class AccountCommissionTransaction extends Transaction
{
    /**
     * 
     * @param array params[
     * account_commission_id,
     * updated_by
     * ]
     * @param allow_commit
     */
    public function active(array $params, $allow_commit = false)
    {
        $this->beginTransaction($allow_commit);
        try {
            $account = AccountCommission::find($params['account_commission_id']);
            if ($account) {
                if ($account->status == AccountCommission::STATUS_LOCK) {
                    $account->status = AccountCommission::STATUS_ACTIVE;
                    $account->updated_by = $params['updated_by'];
                    if(!$account->save()) {
                        $this->error('Có lỗi khi kích hoạt tài khoản hoa hồng');
                    }
                } else {
                    $this->error('Tài khoản hoa hồng không hợp lệ');
                }
            } else {
                $this->error('Tài khoản hoa hồng không tồn tại');
            }
        } catch (\Exception $e) {
            // rollback transaction
            $this->rollback($allow_commit, $e);
        }
        // commit and response
        return $this->response($allow_commit);
    }

    /**
     * 
     * @param array params[
     * ref_id,
     * ref_type,
     * status,
     * created_by
     * ]
     * @param boolean allow_commit
     */
    public function create(array $params, $allow_commit = false)
    {
        $response = null;
        $this->beginTransaction($allow_commit);
        try {
            $account = AccountCommission::create([
                'ref_id' => $params['ref_id'],
                'ref_type' => $params['ref_type'],
                'balance' => 0,
                'balance_available' => 0,
                'balance_freezing' => 0,
                'balance_waiting' => 0,
                'status' => $params['status'],
                'created_by' => $params['created_by'],
                'updated_by' => $params['created_by'],
            ]);
            if ($account) {
                $response['account_commission_id'] = $account->id;
            } else {
                $this->error('Có lỗi khi thêm tài khoản hoa hồng');
            }
        } catch (\Exception $e) {
            // rollback transaction
            $this->rollback($allow_commit, $e);
        }
        // commit and response
        return $this->response($allow_commit, $response);
    }

    /**
     * 
     * @param array params[
     * account_commission_id,
     * amount,
     * updated_by
     * ]
     * @param boolean allow_commit
     * @return array
     */
    public function decreaseBalance(array $params, $allow_commit = false)
    {
        $response = null;
        $this->beginTransaction($allow_commit);
        try {
            $query = "UPDATE account_commissions 
                SET balance = balance - :amount, 
                    balance_available = balance_available - :amount, 
                    updated_by = :updated_by, 
                    updated_at = :updated_at
                WHERE id = :id 
                AND status = :status 
                AND balance >= :amount 
                AND balance_available > :amount";
            $update = AccountCommission::updateRawQuery($query, [
                'id' => $params['account_commission_id'],
                'amount' => $params['amount'],
                'status' => AccountCommission::STATUS_ACTIVE,
                'updated_by' => $params['updated_by'],
                'updated_at' => date('Y-m-d H:i:s')
            ]);
            if (!$update) {
                $this->error('Có lỗi khi cập nhật số dư tài khoản hoa hồng');
            }
        } catch (\Exception $e) {
            // rollback transaction
            $this->rollback($allow_commit, $e);
        }
        // commit and response
        return $this->response($allow_commit, $response);
    }

    /**
     * 
     * @param array params[
     * account_commission_id,
     * amount,
     * updated_by
     * ]
     * @param boolean allow_commit
     * @return array
     */
    public function decreaseBalanceFreezing(array $params, $allow_commit = false)
    {
        $response = null;
        $this->beginTransaction($allow_commit);
        try {
            $query = "UPDATE account_commissions 
                SET balance_freezing = balance_freezing - :amount, 
                    balance_available = balance_available + :amount, 
                    updated_by = :updated_by, 
                    updated_at = :updated_at
                WHERE id = :id 
                AND status = :status 
                AND balance_freezing >= :amount";
            $update = AccountCommission::updateRawQuery($query, [
                'id' => $params['account_commission_id'],
                'amount' => $params['amount'],
                'status' => AccountCommission::STATUS_ACTIVE,
                'updated_by' => $params['updated_by'],
                'updated_at' => date('Y-m-d H:i:s')
            ]);
            if (!$update) {
                $this->error('Có lỗi khi cập nhật số dư tài khoản hoa hồng');
            }
        } catch (\Exception $e) {
            // rollback transaction
            $this->rollback($allow_commit, $e);
        }
        // commit and response
        return $this->response($allow_commit, $response);
    }

    /**
     * 
     * @param array params[
     * account_commission_id,
     * amount,
     * updated_by
     * ]
     * @param boolean allow_commit
     * @return array
     */
    public function decreaseBalanceWaiting(array $params, $allow_commit = false)
    {
        $response = null;
        $this->beginTransaction($allow_commit);
        try {
            $query = "UPDATE account_commissions 
                SET balance_waiting = balance_waiting - :amount, 
                    updated_by = :updated_by, 
                    updated_at = :updated_at
                WHERE id = :id 
                AND status = :status 
                AND balance_waiting >= :amount";
            $update = AccountCommission::updateRawQuery($query, [
                'id' => $params['account_commission_id'],
                'amount' => $params['amount'],
                'status' => AccountCommission::STATUS_ACTIVE,
                'updated_by' => $params['updated_by'],
                'updated_at' => date('Y-m-d H:i:s')
            ]);
            if (!$update) {
                $this->error('Có lỗi khi cập nhật số dư tài khoản hoa hồng');
            }
        } catch (\Exception $e) {
            // rollback transaction
            $this->rollback($allow_commit, $e);
        }
        // commit and response
        return $this->response($allow_commit, $response);
    }

    /**
     * 
     * @param array params[
     * account_commission_id,
     * amount,
     * updated_by
     * ]
     * @param boolean allow_commit
     * @return array
     */
    public function increaseBalance(array $params, $allow_commit = false)
    {
        $response = null;
        $this->beginTransaction($allow_commit);
        try {
            $query = "UPDATE account_commissions 
                SET balance = balance + :amount, 
                    balance_available = balance_available + :amount, 
                    updated_by = :updated_by, 
                    updated_at = :updated_at
                WHERE id = :id 
                AND status = :status";
            $update = AccountCommission::updateRawQuery($query, [
                'id' => $params['account_commission_id'],
                'amount' => $params['amount'],
                'status' => AccountCommission::STATUS_ACTIVE,
                'updated_by' => $params['updated_by'],
                'updated_at' => date('Y-m-d H:i:s'),
            ]);
            if (!$update) {
                $this->error('Có lỗi khi cập nhật số dư tài khoản hoa hồng');
            }
        } catch (\Exception $e) {
            // rollback transaction
            $this->rollback($allow_commit, $e);
        }
        // commit and response
        return $this->response($allow_commit, $response);
    }

    /**
     * 
     * @param array params[
     * account_commission_id,
     * amount,
     * updated_by
     * ]
     * @param boolean allow_commit
     * @return array
     */
    public function increaseBalanceFreezing(array $params, $allow_commit = false)
    {
        $response = null;
        $this->beginTransaction($allow_commit);
        try {
            $query = "UPDATE account_commissions 
                SET balance_freezing = balance_freezing + :amount, 
                    balance_available = balance_available - :amount, 
                    updated_by = :updated_by, 
                    updated_at = :updated_at
                WHERE id = :id 
                AND status = :status 
                AND balance_available >= :amount";
            $update = AccountCommission::updateRawQuery($query, [
                'id' => $params['account_commission_id'],
                'amount' => $params['amount'],
                'status' => AccountCommission::STATUS_ACTIVE,
                'updated_by' => $params['updated_by'],
                'updated_at' => date('Y-m-d H:i:s')
            ]);
            if (!$update) {
                $this->error('Có lỗi khi cập nhật số dư tài khoản hoa hồng');
            }
        } catch (\Exception $e) {
            // rollback transaction
            $this->rollback($allow_commit, $e);
        }
        // commit and response
        return $this->response($allow_commit, $response);
    }

    /**
     * 
     * @param array params[
     * account_commission_id,
     * amount,
     * updated_by
     * ]
     * @param boolean allow_commit
     * @return array
     */
    public function increaseBalanceWaiting(array $params, $allow_commit = false)
    {
        $response = null;
        $this->beginTransaction($allow_commit);
        try {
            $query = "UPDATE account_commissions 
                SET balance_waiting = balance_waiting + :amount, 
                    updated_by = :updated_by, 
                    updated_at = :updated_at
                WHERE id = :id 
                AND status = :status";
            $update = AccountCommission::updateRawQuery($query, [
                'id' => $params['account_commission_id'],
                'amount' => $params['amount'],
                'status' => AccountCommission::STATUS_ACTIVE,
                'updated_by' => $params['updated_by'],
                'updated_at' => date('Y-m-d H:i:s')
            ]);
            if (!$update) {
                $this->error('Có lỗi khi cập nhật số dư tài khoản hoa hồng');
            }
        } catch (\Exception $e) {
            // rollback transaction
            $this->rollback($allow_commit, $e);
        }
        // commit and response
        return $this->response($allow_commit, $response);
    }

    /**
     * 
     * @param array params[
     * account_commission_id,
     * updated_by,
     * ]
     * @param allow_commit
     */
    public function lock(array $params, $allow_commit = false)
    {
        $this->beginTransaction($allow_commit);
        try {
            $account = AccountCommission::find($params['account_commission_id']);
            if ($account) {
                if ($account->status == AccountCommission::STATUS_ACTIVE) {
                    $account->status = AccountCommission::STATUS_LOCK;
                    $account->updated_by = $params['updated_by'];
                    if (!$account->save()) {
                        $this->error('Có lỗi khi khóa tài khoản hoa hồng');
                    }
                } else {
                    $this->error('Tài khoản hoa hồng không hợp lệ');
                }
            } else {
                $this->error('Tài khoản hoa hồng không tồn tại');
            }
        } catch (\Exception $e) {
            // rollback transaction
            $this->rollback($allow_commit, $e);
        }
        // commit and response
        return $this->response($allow_commit);
    }

}
