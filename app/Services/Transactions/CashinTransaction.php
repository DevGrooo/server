<?php

namespace App\Services\Transactions;

use App\Events\PerformCashinEvent;
use App\Jobs\PerformCashinJob;
use App\Models\AccountSystem;
use App\Models\Cashin;
use App\Models\FundDistributorBankAccount;
use App\Models\TradingSession;
use App\Models\TransactionSystem;
use Carbon\Carbon;

/**
 * @author MSI
 * @version 1.0
 * @created 08-Jul-2020 11:48:11 AM
 */
class CashinTransaction extends Transaction
{
	/**
	 * 
	 * @param array params[
	 * cashin_id,
	 * updated_by,
	 * ]
	 * @param boolean allow_commit
	 */
	public function cancel(array $params, $allow_commit = false)
	{
		$response = null;
        $this->beginTransaction($allow_commit);
        try {
            $model = Cashin::where('id', $params['cashin_id'])->where('status', Cashin::STATUS_NEW)->first();
			if ($model) {
				$update = $model->update([
					'status' => Cashin::STATUS_CANCEL,
					'updated_by' => $params['updated_by'],
				]);
				if ($update) {
					// delete old cashin receipt
					(new CashinReceiptTransaction())->deleteByCashin([
						'cashin_id' => $model->id,					
					]);
				} else {
					$this->error('Có lỗi khi cập nhật phiếu thu');
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
	 * @param array params [
	 * fund_distributor_bank_account_id,
	 * statement_id,
	 * amount,
	 * currency,
	 * created_by
	 * ]
	 * @param boolean allow_commit
	 */
	public function create(array $params, $allow_commit = false)
	{
		$response = null;
        $this->beginTransaction($allow_commit);
        try {
			$distributor_bank_account = FundDistributorBankAccount::find($params['fund_distributor_bank_account_id']);
			if ($distributor_bank_account) {
				$model = Cashin::create([
					'fund_company_id' => $distributor_bank_account->fund_company_id,
					'fund_certificate_id' => $distributor_bank_account->fund_certificate_id,
					'fund_distributor_id' => $distributor_bank_account->fund_distributor_id,
					'fund_distributor_bank_account_id' => $distributor_bank_account->id,					
					'fund_product_type_id' => $distributor_bank_account->fund_product_type_id,
					'fund_product_id' => $distributor_bank_account->fund_product_id,
					'supervising_bank_id' => $distributor_bank_account->supervising_bank_id,
					'deposit_account_system_id' => $distributor_bank_account->account_system_id,
					'statement_id' => $params['statement_id'],
					'amount' => $params['amount'],
					'amount_not_perform' => 0,
					'amount_available' => 0,
					'amount_freezing' => 0,
					'currency' => $params['currency'],
					'status' => Cashin::STATUS_NEW,
					'created_by' => $params['created_by'],
				]);
				if ($model) {
					$response['cashin_id'] = $model->id;
				} else {
					$this->error('Có lối khi tạo phiếu thu');
				}
			} else {
				$this->error('Tài khoản ngân hàng đại lý phân phối không tồn tại');
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
	 * fund_distributor_bank_account_id,
	 * investor_id,
	 * statement_id,
	 * amount,
	 * currency,
	 * fee,
	 * receipt,
	 * bank_paid_at,
	 * paid_at,
	 * created_by
	 * ]
	 * @param allow_commit
	 */
	public function createAndPaid(array $params, $allow_commit = false)
	{
		$response = null;
        $this->beginTransaction($allow_commit);
        try {			
			$response = $this->create([
				'fund_distributor_bank_account_id' => $params['fund_distributor_bank_account_id'],
				'statement_id' => $params['statement_id'],
				'amount' => $params['amount'], 
				'currency' => $params['currency'], 
				'created_by' => $params['created_by'],
			]);
			$result = $this->paid([
				'cashin_id' => $response['cashin_id'],
				'fee' => $params['fee'], 
				'receipt' => $params['receipt'], 
				'investor_id' => $params['investor_id'],
				'bank_paid_at' => $params['bank_paid_at'],
				'bank_trans_note' => $params['bank_trans_note'],
				'paid_at' => $params['paid_at'], 
				'updated_by' => $params['created_by']
			]);
			$response['deposit_transaction_system_id'] = $result['deposit_transaction_system_id'];
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
	 * receipt,	
	 * updated_by
	 * ]
	 * @param boolean allow_commit
	 */
	public function updateReceipt(array $params, $allow_commit = false)
	{
		$response = null;
        $this->beginTransaction($allow_commit);
        try {
            $model = Cashin::where('id', $params['cashin_id'])->where('status', Cashin::STATUS_NEW)->first();
			if ($model) {
				$update = $model->update([
					'receipt' => $params['receipt'],
					'updated_by' => $params['updated_by'],
				]);
				if ($update) {
					// delete old cashin receipt
					$result = (new CashinReceiptTransaction())->deleteByCashin([
						'cashin_id' => $model->id,					
					]);
					// add cashin receipt
					$result = (new CashinReceiptTransaction())->create([
						'cashin_id' => $model->id,
						'supervising_bank_id' => $model->supervising_bank_id,
						'receipt' => $params['receipt'],
					]);
					$response['cashin_receipt_id'] = $result['cashin_receipt_id'];
				} else {
					$this->error('Có lỗi khi cập nhật phiếu thu');
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
	 * fee,
	 * receipt,
	 * investor_id,
	 * bank_paid_at,
	 * bank_trans_note,
	 * paid_at,
	 * updated_by
	 * ]
	 * @param boolean allow_commit
	 */
	public function paid(array $params, $allow_commit = false)
	{
		$response = null;
        $this->beginTransaction($allow_commit);
        try {
            $model = Cashin::where('id', $params['cashin_id'])->where('status', Cashin::STATUS_NEW)->first();
			if ($model) {
				// update receipt
				$result = $this->updateReceipt([
					'cashin_id' => $model->id, 
					'receipt' => $params['receipt'],
					'updated_by' => $params['updated_by'],
				]);
				$response['cashin_receipt_id'] = $result['cashin_receipt_id'];
				// update status paid
				$update = $model->update([
					'fee' => $params['fee'],
					'bank_paid_at' => $params['bank_paid_at'],
					'bank_trans_note' => $params['bank_trans_note'],
					'status' => Cashin::STATUS_PAID,
					'paid_at' => $params['paid_at'],
					'paid_by' => $params['updated_by'],
					'updated_by' => $params['updated_by'],
				]);
				if ($update) {
					$this->increaseAmountNotPerform([
						'cashin_id' => $model->id, 
						'amount' => $model->amount, 
						'updated_by' => $params['updated_by'],
					]);
					// add deposit transaction system and perform
					$result = (new TransactionSystemTransaction())->createDepositAndPerform([
						'cashin_id' => $model->id, 
						'created_by' => $params['updated_by']
					]);
					$response['deposit_transaction_system_id'] = $result['transaction_system_id'];
					// if investor !== 0
					if (intval($params['investor_id']) > 0) {
						$result = $this->updateInvestor([
							'cashin_id' => $model->id, 
							'investor_id' => $params['investor_id'], 
							'updated_by' => $params['updated_by']
						]);
						$response['cashin_queue_id'] = $result['cashin_queue_id'];
					}
				} else {
					$this->error('Có lỗi khi cập nhật phiếu thu');	
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
	 * perform_at,
	 * updated_by
	 * ]
	 * @param allow_commit
	 */
	public function perform(array $params, $allow_commit = false)
	{
		$response = null;
        $this->beginTransaction($allow_commit);
        try {
            $model = Cashin::where('id', $params['cashin_id'])				
				->whereIn('status', [Cashin::STATUS_PAID, Cashin::STATUS_PARTIALLY_PERFORM])
				->first();
			if ($model) {
				$params['amount'] = $model->amount_available;
				$response = $this->performAmount($params);
			} else {
				$this->error('Phiếu thu không tồn tại');
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
	 * amount
	 * perform_at,
	 * updated_by
	 * ]
	 * @param allow_commit
	 */
	public function performAmount(array $params, $allow_commit = false)
	{
		$response = null;
        $this->beginTransaction($allow_commit);
        try {
			$model = Cashin::find('id', $params['cashin_id']);
			if ($model) {
				$this->decreaseAmountNotPerform($params);
				$result = (new TransactionSystemTransaction())->createTransferAndPerform([
					'ref_id' => $model->id,
					'ref_type' => TransactionSystem::REF_TYPE_CASHIN,
					'send_account_system_id' => $model->deposit_account_system_id,
					'receive_account_system_id' => $model->target_account_system_id,
					'amount' => $params['amount'],
					'currency' => $model->currency,
					'created_by' => $params['updated_by'],
				]);
				$response['transaction_system_id'] = $result['transaction_system_id'];
			} else {
				$this->error('Phiếu thu không tồn tại');
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
	 * investor_id,
	 * updated_by
	 * ]
	 * @param boolean allow_commit
	 * @return array
	 */
	public function updateInvestor(array $params, $allow_commit = false)
	{
		$response = null;
        $this->beginTransaction($allow_commit);
        try {
			$model = Cashin::where('id', $params['cashin_id'])->whereIn('status', [Cashin::STATUS_PAID])->first();
			if ($model) {
				$target_account_system = AccountSystem::getForInvestor($params['investor_id'], $model->fund_product_type_id, $model->currency);
				if ($target_account_system) {
					$update = $model->update([
						'investor_id' => $params['investor_id'],
						'target_account_system_id' => $target_account_system->id,
						'updated_by' => $params['updated_by'],
					]);
					if ($update) {
						// get trading_session
						$trading_session = TradingSession::getForTradingOrder($model->fund_company_id, $params['investor_id'], $model->fund_product_id, $model->paid_at);
						if ($trading_session && $trading_session->isAllowOrder()) {
							(new TradingOrderTransaction)->collateBuy([
								'investor_id' => $params['investor_id'], 
								'fund_product_id' => $model->fund_product_id, 
								'trading_session_id' => $trading_session->id, 
								'currency' => $model->currency, 
								'updated_by' => $params['updated_by']
							]);
						} else {
							$this->perform([
								'cashin_id' => $model->id, 
								'perform_at' => Carbon::now()->toDateTimeString(), 
								'updated_by' => $params['updated_by'],
							]);
						}						
					} else {
						$this->error('Có lỗi khi cập nhật phiếu thu');	
					}
				} else {
					$this->error('Nhà đầu tư không có tài khoản giao dịch hệ thống');
				}				
			} else {
				$this->error('Phiếu thu không tồn tại');
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
	 * amount,
	 * updated_by
	 * ]
	 * @param boolean allow_commit
	 * @return array
	 */
	public function decreaseAmountFreezing(array $params, $allow_commit = false)
	{
		$response = null;
        $this->beginTransaction($allow_commit);
        try {
            $query = "UPDATE cashins 
				SET amount_freezing = amount_freezing - :amount, 
					amount_available = amount_available + :amount, 
					updated_by = :updated_by, 
					updated_at = :updated_at
				WHERE id = :id 
				AND status IN (:status) 
				AND amount_freezing >= :amount";
			$update = Cashin::updateRawQuery($query, [
				'id' => $params['cashin_id'],
				'amount' => $params['amount'],
				'status' => [Cashin::STATUS_PARTIALLY_PERFORM, Cashin::STATUS_PAID],
				'updated_by' => $params['updated_by'],
				'updated_at' => Carbon::now()->toDateTimeString()
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
	 * cashin_id,
	 * amount,
	 * perform_at,
	 * updated_by
	 * ]
	 * @param boolean allow_commit
	 * @return array
	 */
	public function decreaseAmountNotPerform(array $params, $allow_commit = false)
	{
		$response = null;
        $this->beginTransaction($allow_commit);
        try {
            $query = "UPDATE cashins 
				SET amount_not_perform = amount_not_perform - :amount, 
					amount_available = amount_available - :amount, 
					status = IF(amount_not_perform = 0 , :perform_status, :partially_status) 
					perform_at = :perform_at
					updated_by = :updated_by, 
					updated_at = :updated_at
				WHERE id = :id 
				AND status IN (:status) 
				AND amount_not_perform >= :amount 
				AND amount_available >= :amount";
			$update = Cashin::updateRawQuery($query, [
				'id' => $params['cashin_id'],
				'amount' => $params['amount'],
				'status' => [Cashin::STATUS_PARTIALLY_PERFORM, Cashin::STATUS_PAID],
				'perform_status' => Cashin::STATUS_PERFORM,
				'partially_status' => Cashin::STATUS_PARTIALLY_PERFORM,
				'perform_at' => $params['perform_at'],
				'updated_by' => $params['updated_by'],
				'updated_at' => Carbon::now()->toDateTimeString()
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
	 * cashin_id,
	 * amount,
	 * updated_by
	 * ]
	 * @param boolean allow_commit
	 * @return array
	 */
	public function increaseAmountNotPerform(array $params, $allow_commit = false)
	{
		$response = null;
        $this->beginTransaction($allow_commit);
        try {
            $query = "UPDATE cashins 
				SET amount_not_perform = amount_not_perform + :amount, 
					amount_available = amount_available + :amount, 
					updated_by = :updated_by, 
					updated_at = :updated_at
				WHERE id = :id 
				AND status IN (:status)";
			$update = Cashin::updateRawQuery($query, [
				'id' => $params['cashin_id'],
				'amount' => $params['amount'],
				'status' => [Cashin::STATUS_PARTIALLY_PERFORM, Cashin::STATUS_PAID],
				'updated_by' => $params['updated_by'],
				'updated_at' => Carbon::now()->toDateTimeString()
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
	 * cashin_id,
	 * amount,
	 * updated_by
	 * ]
	 * @param boolean allow_commit
	 * @return array
	 */
	public function increaseAmountFreezing(array $params, $allow_commit = false)
	{
		$response = null;
        $this->beginTransaction($allow_commit);
        try {
            $query = "UPDATE cashins 
				SET amount_freezing = amount_freezing + :amount, 
					amount_available = amount_available - :amount, 
					updated_by = :updated_by, 
					updated_at = :updated_at
				WHERE id = :id 
				AND status IN (:status)
				AND amount_available >= :amount";
			$update = Cashin::updateRawQuery($query, [
				'id' => $params['cashin_id'],
				'amount' => $params['amount'],
				'status' => [Cashin::STATUS_PARTIALLY_PERFORM, Cashin::STATUS_PAID],
				'updated_by' => $params['updated_by'],
				'updated_at' => Carbon::now()->toDateTimeString()
			]);
			if (!$update) {
				$this->error('Có lỗi khi cập nhật phiếu thu');
			}
        } catch (\Exception $e) {
            // rollback transaction
            $this->rollback($allow_commit, $e);
        }
        //commit and response
        return $this->response($allow_commit, $response);
	}
}
