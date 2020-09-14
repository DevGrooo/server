<?php
namespace App\Services\Transactions;

use App\Models\AccountSystem;
use App\Models\Cashin;
use App\Models\CashoutRequest;
use App\Models\TradingOrder;
use App\Models\TransactionSystem;

/**
 * @author MSI
 * @version 1.0
 * @updated 08-Jul-2020 11:48:04 AM
 */
class TransactionSystemTransaction extends Transaction
{

	/**
	 * 
	 * @param array params[
	 * transaction_system_id,
	 * updated_by
	 * ]
	 * @param boolean allow_commit
	 */
	public function cancel(array $params,  $allow_commit = false)
	{
		$response = null;
        $this->beginTransaction($allow_commit);
        try {
			$model = TransactionSystem::where('id', $params['transaction_system_id'])
				->whereIN('status', [TransactionSystem::STATUS_VERIFY, TransactionSystem::STATUS_NEW])
				->first();
			if ($model) {
				$status = $model->status;
				$model->status = TransactionSystem::STATUS_CANCEL;
				$model->updated_by = $params['updated_by'];
				if ($model->save()) {
					if ($status == TransactionSystem::STATUS_VERIFY) {
						(new AccountSystemTransaction())->decreaseBalanceFreezing([
							'account_system_id' => $model->send_account_system_id, 
							'amount' => $model->amount, 
							'updated_by' => $params['updated_by'],
						]);
					}
				} else {
					$this->error('Có lỗi khi cập nhật giao dịch');	
				}
			} else {
				$this->error('Giao dịch không tồn tại hoặc không hợp lệ');
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
	 * transaction_system_id,
	 * send_match_amount,
	 * updated_by,
	 * ]
	 * @param boolean allow_commit
	 */
	public function updateSendMatchAmount(array $params,  $allow_commit = false)
	{
		$response = null;
        $this->beginTransaction($allow_commit);
        try {
			$model = TransactionSystem::where('id', $params['transaction_system_id'])->where('status', TransactionSystem::STATUS_VERIFY)->first();
			if ($model) {
				$model->send_match_amount = $params['send_match_amount'];
				$model->updated_by = $params['updated_by'];
				if ($model->save()) {
					// decrease balance freezing
					(new AccountSystemTransaction())->decreaseBalanceFreezing([
						'account_system_id' => $model->send_account_system_id, 
						'amount' => ($model->send_amount - $params['send_match_amount']), 
						'updated_by' => $params['updated_by'],
					]);
				} else {
					$this->error('Có lỗi khi cập nhật giao dịch');	
				}
			} else {
				$this->error('Giao dịch không tồn tại hoặc không hợp lệ');
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
	 * cashin_id,
	 * created_by,
	 * ]
	 * @param boolean allow_commit
	 */
	public function createDeposit(array $params,  $allow_commit = false)
	{
		$response = null;
        $this->beginTransaction($allow_commit);
        try {
			$cashin = Cashin::where('id', $params['cashin_id'])->where('status', Cashin::STATUS_PAID)->first();
			if ($cashin) {
				$model = TransactionSystem::create([
					'type' => TransactionSystem::TYPE_DEPOSIT,
					'ref_id' => $cashin->id,
					'ref_type' => TransactionSystem::REF_TYPE_CASHIN,
					'send_account_system_id' => AccountSystem::MASTER_ID,
					'receive_account_system_id' => $cashin->deposit_account_system_id,
					'amount' => $cashin->amount,
					'send_fee' => 0,
					'receive_fee' => 0,
					'currency' => $cashin->currency,
					'status' => TransactionSystem::STATUS_NEW,
					'created_by' => $params['created_by'],
					'updated_by' => $params['created_by'],
				]);
				if ($model) {
					$model->send_fee = TransactionSystem::getSendFee($model);
					$model->receive_fee = TransactionSystem::getReceiveFee($model);
					$model->updated_by = $params['created_by'];
					if ($model->save()) {
						$response['transaction_system_id'] = $model->id;
					} else {
						$this->error('Có lỗi khi cập nhật phí giao dịch');
					}
				} else {
					$this->error('Có lỗi khi tạo giao dịch');
				}
			} else {
				$this->error('Phiếu thu không tồn tại hoặc không hợp lệ');
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
	 * cashin_id,
	 * created_by
	 * ]
	 * @param boolean allow_commit
	 */
	public function createDepositAndPerform(array $params,  $allow_commit = false)
	{
		$response = null;
        $this->beginTransaction($allow_commit);
        try {
			$result = $this->createDeposit($params);
			$response['transaction_system_id'] = $result['transaction_system_id'];
			$this->verify([
				'transaction_system_id' => $response['transaction_system_id'], 
				'verify_at' => date('Y-m-d H:i:s'), 
				'verify_by' => $params['created_by']
			]);
			$this->perform([
				'transaction_system_id' => $response['transaction_system_id'], 
				'perform_at' => date('Y-m-d H:i:s'), 
				'perform_by' => $params['created_by']
			]);
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
	 * ref_id,
	 * ref_type,
	 * send_account_system_id,
	 * receive_account_system_id,
	 * amount,
	 * currency,
	 * created_by
	 * ]
	 * @param boolean allow_commit
	 */
	public function createTransfer(array $params,  $allow_commit = false)
	{
		$response = null;
        $this->beginTransaction($allow_commit);
        try {
			$model = TransactionSystem::create([
				'type' => TransactionSystem::TYPE_TRANSFER,
				'ref_id' => $params['ref_id'],
				'ref_type' => $params['ref_type'],
				'send_account_system_id' => $params['send_account_system_id'],
				'receive_account_system_id' => $params['receive_account_system_id'],
				'amount' => $params['amount'],
				'send_fee' => 0,
				'receive_fee' => 0,
				'currency' => $params['currency'],
				'status' => TransactionSystem::STATUS_NEW,
				'created_by' => $params['created_by'],
				'updated_by' => $params['created_by'],
			]);
			if ($model) {
				$model->send_fee = TransactionSystem::getSendFee($model);
				$model->receive_fee = TransactionSystem::getReceiveFee($model);
				$model->updated_by = $params['created_by'];
				if ($model->save()) {
					$response['transaction_system_id'] = $model->id;
				} else {
					$this->error('Có lỗi khi cập nhật phí giao dịch');
				}
			} else {
				$this->error('Có lỗi khi tạo giao dịch');
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
	 * ref_id,
	 * ref_type,
	 * send_account_system_id,
	 * receive_account_system_id,
	 * amount,
	 * currency,
	 * created_by
	 * ]
	 * @param boolean allow_commit
	 */
	public function createTransferAndPerform(array $params,  $allow_commit = false)
	{
		$response = null;
        $this->beginTransaction($allow_commit);
        try {
			$result = $this->createTransferAndVerify($params);
			$response['transaction_system_id'] = $result['transaction_system_id'];
			$this->perform([
				'transaction_system_id' => $response['transaction_system_id'],
				'perform_at' => date('Y-m-d H:i:s'),
				'perform_by' => $params['created_by'],
			]);
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
	 * ref_id,
	 * ref_type,
	 * send_account_system_id,
	 * receive_account_system_id,
	 * amount,
	 * currency,
	 * created_by
	 * ]
	 * @param boolean allow_commit
	 */
	public function createTransferAndVerify(array $params,  $allow_commit = false)
	{
		$response = null;
        $this->beginTransaction($allow_commit);
        try {
			$result = $this->createTransfer($params);
			$response['transaction_system_id'] = $result['transaction_system_id'];
			$this->verify([
				'transaction_system_id' => $response['transaction_system_id'],
				'verify_at' => date('Y-m-d H:i:s'),
				'verify_by' => $params['created_by'],
			]);
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
	 * ref_id,
	 * ref_type,
	 * send_account_system_id,	 
	 * amount,
	 * currency,
	 * refer_transaction_system_id,
	 * created_by
	 * ]
	 * @param boolean allow_commit
	 */
	public function createWithdraw(array $params,  $allow_commit = false)
	{
		$response = null;
        $this->beginTransaction($allow_commit);
        try {
			$model = TransactionSystem::create([
				'type' => TransactionSystem::TYPE_WITHDRAW,
				'ref_id' => $params['ref_id'],
				'ref_type' => $params['ref_type'],
				'send_account_system_id' => $params['send_account_system_id'],
				'receive_account_system_id' => AccountSystem::MASTER_ID,
				'amount' => $params['amount'],
				'send_fee' => 0,
				'receive_fee' => 0,
				'currency' => $params['currency'],
				'refer_transaction_system_id' => $params['refer_transaction_system_id'],
				'status' => TransactionSystem::STATUS_NEW,
				'created_by' => $params['created_by'],
				'updated_by' => $params['created_by'],
			]);
			if ($model) {
				$model->send_fee = TransactionSystem::getSendFee($model);
				$model->receive_fee = TransactionSystem::getReceiveFee($model);
				$model->updated_by = $params['created_by'];
				if ($model->save()) {
					$response['transaction_system_id'] = $model->id;
				} else {
					$this->error('Có lỗi khi cập nhật phí giao dịch');
				}
			} else {
				$this->error('Có lỗi khi tạo giao dịch');
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
	 * ref_id,
	 * ref_type,
	 * send_account_system_id,	 
	 * amount,
	 * currency,
	 * refer_transaction_system_id,
	 * bank_id,
	 * account_holder,
	 * account_number,
	 * branch,
	 * created_by
	 * ]
	 * @param boolean allow_commit
	 */
	public function createWithdrawAndVerify(array $params,  $allow_commit = false)
	{
		$response = null;
        $this->beginTransaction($allow_commit);
        try {
			$response = $this->createWithdraw($params);
			$this->verify([
				'transaction_system_id' => $response['transaction_system_id'], 
				'verify_at' => date('Y-m-d H:i:s'), 
				'verify_by' => $params['created_by'], 
				'bank_id' => $params['bank_id'], 
				'account_holder' => $params['account_holder'], 
				'account_number' => $params['account_number'], 
				'branch' => $params['branch']
			]);
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
	 * transaction_system_id,
	 * perform_at,
	 * perform_by
	 * ]
	 * @param boolean allow_commit
	 */
	public function perform(array $params,  $allow_commit = false)
	{
		$response = null;
        $this->beginTransaction($allow_commit);
        try {
			$model = TransactionSystem::where('id', $params['transaction_system_id'])->where('status', TransactionSystem::STATUS_VERIFY)->first();
			if ($model) {
				$model->status = TransactionSystem::STATUS_PERFORM;
				$model->performed_at = $params['perform_at'];
				$model->performed_by = $params['perform_by'];
				if ($model->save()) {
					$obj = new AccountSystemTransaction();
					// send account
					$obj->decreaseBalanceFreezing([
						'account_system_id' => $model->send_account_system_id,
						'amount' => $model->amount,
						'updated_by' => $params['perform_by'],
					]);
					$obj->decreaseBalance([
						'account_system_id' => $model->send_account_system_id,
						'amount' => $model->amount,
						'updated_by' => $params['perform_by'],
					]);
					// receive account
					$obj->increaseBalance([
						'account_system_id' => $model->receive_account_system_id,
						'amount' => $model->amount,
						'updated_by' => $params['perform_by'],
					]);
				} else {
					$this->error('Có lỗi khi phê chuẩn giao dịch');
				}				
			} else {
				$this->error('Giao dịch không tồn tại hoặc không hợp lệ');
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
	 * transaction_system_id,
	 * verify_at,
	 * verify_by,
	 * bank_id,
	 * account_holder,
	 * account_number,
	 * branch
	 * ]
	 * @param boolean allow_commit
	 */
	public function verify(array $params,  $allow_commit = false)
	{
		$response = null;
        $this->beginTransaction($allow_commit);
        try {
			$model = TransactionSystem::where('id', $params['transaction_system_id'])->where('status', TransactionSystem::STATUS_NEW)->first();
			if ($model) {
				$model->status = TransactionSystem::STATUS_VERIFY;
				$model->verified_at = $params['verify_at'];
				$model->verified_by = $params['verify_by'];
				if ($model->save()) {
					$obj = new AccountSystemTransaction();
					// send account
					$obj->increaseBalanceFreezing([
						'account_system_id' => $model->send_account_system_id,
						'amount' => $model->amount,
						'updated_by' => $params['verify_by'],
					]);
					// process with transaction withdaw
					if ($model->type == TransactionSystem::TYPE_WITHDRAW) {
						$type = $model->ref->exec_type == TradingOrder::EXEC_TYPE_BUY ? CashoutRequest::TYPE_ORDER_BUY : CashoutRequest::TYPE_ORDER_SELL;
						$result = (new CashoutRequestTransaction())->create([
							'type' => $type,
							'transaction_system_id' => $model->id,
							'amount' => $model->amount,
							'bank_id' => $params['bank_id'],
							'account_holder' => $params['account_holder'],
							'account_number' => $params['account_number'],
							'branch' => $params['branch'],
							'created_by' => $params['verify_by'],
						]);
						$response['cashout_request_id'] = $result['cashout_request_id'];
					}
				} else {
					$this->error('Có lỗi khi phê chuẩn giao dịch');
				}				
			} else {
				$this->error('Giao dịch không tồn tại hoặc không hợp lệ');
			}
        } catch (\Exception $e) {
            // rollback transaction
            $this->rollback($allow_commit, $e);
        }
        //commit and response
        return $this->response($allow_commit, $response);
	}

}
