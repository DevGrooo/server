<?php

namespace App\Services\Transactions;

use App\Models\CashoutRequest;

/**
 * @author MSI
 * @version 1.0
 * @created 08-Jul-2020 11:48:17 AM
 */
class CashoutRequestTransaction extends Transaction
{

	/**
	 * 
	 * @param params
	 * @param allow_commit
	 */
	public function accept(array $params, $allow_commit = false)
	{
		$response = null;
        $this->beginTransaction($allow_commit);
        try {
			
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
	 * type,
	 * transaction_system_id,
	 * amount,
	 * bank_id,
	 * account_holder,
	 * account_number,
	 * branch,
	 * created_by
	 * ]
	 * @param boolean allow_commit
	 */
	public function create(array $params, $allow_commit = false)
	{
		$response = null;
        $this->beginTransaction($allow_commit);
        try {
			$model = CashoutRequest::create([
				'type' => $params['type'],
				'transaction_system_id' => $params['transaction_system_id'],
				'amount' => $params['amount'],
				'bank_id' => $params['bank_id'],
				'account_holder' => $params['account_holder'],
				'account_number' => $params['account_number'],
				'branch' => $params['branch'],
				'created_by' => $params['created_by'],
			]);
			if ($model) {
				$response['cashout_request_id'] = $model->id;
			} else {
				$this->error('Có lỗi khi yêu cầu rút tiền');
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
	 * @param params
	 * @param allow_commit
	 */
	public function perform(array $params, $allow_commit = false)
	{
		$response = null;
        $this->beginTransaction($allow_commit);
        try {
			
        } catch (\Exception $e) {
            // rollback transaction
            $this->rollback($allow_commit, $e);
        }
        //commit and response
        return $this->response($allow_commit, $response);
	}

	/**
	 * 
	 * @param params
	 * @param allow_commit
	 */
	public function reject(array $params, $allow_commit = false)
	{
		$response = null;
        $this->beginTransaction($allow_commit);
        try {
			
        } catch (\Exception $e) {
            // rollback transaction
            $this->rollback($allow_commit, $e);
        }
        //commit and response
        return $this->response($allow_commit, $response);
	}
}
