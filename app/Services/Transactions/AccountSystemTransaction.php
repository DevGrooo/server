<?php

namespace App\Services\Transactions;

use App\Models\AccountSystem;
/**
 * @author MSI
 * @version 1.0
 * @created 06-Jul-2020 1:30:23 PM
 */
class AccountSystemTransaction extends Transaction
{

	/**
	 * 
	 * @param array params[
	 * ref_type,
	 * fund_product_type_id,
	 * ref_id,
	 * status,
	 * created_by
	 * ]
	 * @param boolean allow_commit
	 * @return array [account_system_id]
	 */
	public function create(array $params, $allow_commit = false)
	{
		$response = null;
        $this->beginTransaction($allow_commit);
        try {
            $model = AccountSystem::create([
				'fund_product_type_id' => $params['fund_product_type_id'],
				'ref_type' => $params['ref_type'],
	 			'ref_id' => $params['ref_id'],	 			
				'balance' => 0,
				'balance_freezing' => 0,
				'balance_available' => 0, 
				'currency' => config('global.currency'),
				'status' => $params['status'],
	 			'created_by' => $params['created_by'],
			]);
			if ($model) {
				$response['account_system_id'] = $model->id;
			} else {
				$this->error('Có lỗi khi tạo tài khoản giao dịch hệ thống');
			}			
        } catch (\Exception $e) {
            // rollback transaction
            $this->rollback($allow_commit, $e);
        }
        //commit and response
        return $this->response($allow_commit, $response);
	}

	/**
	 * 
	 * @param array params[
	 * account_system_id,
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
            $query = "UPDATE account_systems 
				SET balance_freezing = balance_freezing - :amount, 
					balance_available = balance_available + :amount, 
					updated_by = :updated_by, 
					updated_at = :updated_at
				WHERE id = :id 
				AND status = :status 
				AND balance_freezing >= :amount";
			$update = AccountSystem::updateRawQuery($query, [
				'id' => $params['account_system_id'],
				'amount' => $params['amount'],
				'status' => AccountSystem::STATUS_ACTIVE,
				'updated_by' => $params['updated_by'],
				'updated_at' => date('Y-m-d H:i:s')
			]);
			if (!$update) {
				$this->error('Có lỗi khi cập nhật số dư tài khoản hệ thống');
			}
        } catch (\Exception $e) {
            // rollback transaction
            $this->rollback($allow_commit, $e);
        }
        //commit and response
        return $this->response($allow_commit, $response);
	}

	/**
	 * 
	 * @param array params[
	 * account_system_id,
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
            $query = "UPDATE account_systems 
				SET balance = balance - :amount, 
					balance_available = balance_available - :amount, 
					updated_by = :updated_by, 
					updated_at = :updated_at
				WHERE id = :id 
				AND status = :status 
				AND balance >= :amount 
				AND balance_available >= :amount";
			$update = AccountSystem::updateRawQuery($query, [
				'id' => $params['account_system_id'],
				'amount' => $params['amount'],
				'status' => AccountSystem::STATUS_ACTIVE,
				'updated_by' => $params['updated_by'],
				'updated_at' => date('Y-m-d H:i:s')
			]);
			if (!$update) {
				$this->error('Có lỗi khi cập nhật số dư tài khoản hệ thống');
			}
        } catch (\Exception $e) {
            // rollback transaction
            $this->rollback($allow_commit, $e);
        }
        //commit and response
        return $this->response($allow_commit, $response);
	}

	/**
	 * 
	 * @param array params[
	 * account_system_id,
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
            $query = "UPDATE account_systems 
				SET balance = balance + :amount, 
					balance_available = balance_available + :amount, 
					updated_by = :updated_by, 
					updated_at = :updated_at
				WHERE id = :id 
				AND status = :status";
			$update = AccountSystem::updateRawQuery($query, [
				'id' => $params['account_system_id'],
				'amount' => $params['amount'],
				'status' => AccountSystem::STATUS_ACTIVE,
				'updated_by' => $params['updated_by'],
				'updated_at' => date('Y-m-d H:i:s')
			]);
			if (!$update) {
				$this->error('Có lỗi khi cập nhật số dư tài khoản hệ thống');
			}
        } catch (\Exception $e) {
            // rollback transaction
            $this->rollback($allow_commit, $e);
        }
        //commit and response
        return $this->response($allow_commit, $response);
	}

	/**
	 * 
	 * @param array params[
	 * account_system_id,
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
            $query = "UPDATE account_systems 
				SET balance_freezing = balance_freezing + :amount, 
					balance_available = balance_available - :amount, 
					updated_by = :updated_by, 
					updated_at = :updated_at
				WHERE id = :id 
				AND status = :status 
				AND balance_available >= :amount";
			$update = AccountSystem::updateRawQuery($query, [
				'id' => $params['account_system_id'],
				'amount' => $params['amount'],
				'status' => AccountSystem::STATUS_ACTIVE,
				'updated_by' => $params['updated_by'],
				'updated_at' => date('Y-m-d H:i:s')
			]);
			if (!$update) {
				$this->error('Có lỗi khi cập nhật số dư tài khoản hệ thống');
			}
        } catch (\Exception $e) {
            // rollback transaction
            $this->rollback($allow_commit, $e);
        }
        //commit and response
        return $this->response($allow_commit, $response);
	}

	/**
	 * 
	 * @@param array params[
	 * account_system_id,
	 * updated_by
	 * ]
	 * @param allow_commit
	 */
	public function active(array $params, $allow_commit = false)
	{
		$response = null;
        $this->beginTransaction($allow_commit);
        try {
            $model = AccountSystem::find($params['account_system_id']);
			if ($model) {
				if ($model->status == AccountSystem::STATUS_LOCK) {
					$model->status = AccountSystem::STATUS_ACTIVE;
					$model->updated_by = $params['updated_by'];
					if (!$model->save()) {
						$this->error('Có lỗi khi kích hoạt tài khoản giao dịch hệ thống');
					}
				} else {
					$this->error('Tài khoản giao dịch hệ thống không hợp lệ');
				}				
			} else {
				$this->error('Tài khoản giao dịch hệ thống không tồn tại');
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
	 * @@param array params[
	 * account_system_id,
	 * updated_by
	 * ]
	 * @param allow_commit
	 */
	public function lock(array $params, $allow_commit = false)
	{
		$response = null;
        $this->beginTransaction($allow_commit);
        try {
            $model = AccountSystem::find($params['account_system_id']);
			if ($model) {
				if ($model->status == AccountSystem::STATUS_ACTIVE) {
					$model->status = AccountSystem::STATUS_LOCK;
					$model->updated_by = $params['updated_by'];
					if (!$model->save()) {
						$this->error('Có lỗi khi khóa tài khoản giao dịch hệ thống');
					}
				} else {
					$this->error('Tài khoản giao dịch hệ thống không hợp lệ');
				}				
			} else {
				$this->error('Tài khoản giao dịch hệ thống không tồn tại');
			}
        } catch (\Exception $e) {
            // rollback transaction
            $this->rollback($allow_commit, $e);
        }
        // commit and response
        return $this->response($allow_commit, $response);
	}
}
