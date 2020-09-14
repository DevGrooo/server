<?php

namespace App\Services\Transactions;

use App\Models\Cashin;
use App\Models\InvestorBankAccount;
use App\Models\TradingOrder;
use App\Models\TradingOrderCollate;
use App\Models\TradingOrderCollateLine;
use App\Models\TransactionSystem;
use Carbon\Carbon;

/**
 * @author MSI
 * @version 1.0
 * @created 07-Sep-2020 8:54:37 AM
 */
class TradingOrderCollateTransaction extends Transaction
{
	/**
	 * 
	 * @param array $params[]
	 * @param allow_commit
	 */
	public function cancel(array $params, $allow_commit = false)
	{
		$response = null;
		$this->beginTransaction($allow_commit);
		try {
			$model = TradingOrderCollate::where('id', $params['trading_order_collate_id'])
				->where('status', TradingOrderCollate::STATUS_NOT_ENOUGHT)
				->first();
			if ($model) {
				$model->status = TradingOrderCollate::STATUS_CANCEL;
				$model->updated_by = $params['updated_by'];
				if ($model->save()) {
					$trading_order_collate_lines = TradingOrderCollateLine::where('trading_order_collate_id', $model->id)
						->where('status', TradingOrderCollateLine::STATUS_NEW)
						->get();
					if ($trading_order_collate_lines) {
						$transaction = new TradingOrderCollateLineTransaction();
						foreach ($trading_order_collate_lines as $trading_order_collate_line) {
							$transaction->cancel([
								'trading_order_collate_line_id' => $trading_order_collate_line->id,
								'updated_by' => $params['updated_by']
							]);
						}
						if ($model->trading_order->trading_session->isAllowOrder()) {
							(new TradingOrderTransaction)->collateBuy([
								'investor_id' => $model->trading_order->investor_id,
								'fund_product_id' => $model->trading_order->receive_fund_product_id,
								'trading_session_id' => $model->trading_order->trading_session_id,
								'currency' => $model->currency,
								'updated_by' => $params['updated_by']
							]);
						} else {
							$transaction = new CashinTransaction();
							foreach ($trading_order_collate_lines as $trading_order_collate_line) {
								$transaction->perform([
									'cashin_id' => $trading_order_collate_line->cashin_id, 
									'perform_at' => Carbon::now()->toDateTimeString(), 
									'updated_by' => $params['updated_by']
								]);
							}
						}						
					} else {
						$this->error('Dữ liệu không hợp lệ');
					}
				} else {
					$this->error('Có lỗi khi cập nhật đối chiếu tiền - lệnh');
				}
			} else {
				$this->error('Đối chiếu tiền - lệnh không hợp lệ');
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
	 * @param array $params[trading_order_id, cashin_ids, cashin_amounts, overpayments, currency, created_by]
	 * @param allow_commit
	 */
	public function create(array $params, $allow_commit = false)
	{
		$response = null;
		$this->beginTransaction($allow_commit);
		try {
			$trading_order = TradingOrder::where('id', $params['trading_order_id'])
				->where('exec_type', TradingOrder::EXEC_TYPE_BUY)
				->where('send_currency', $params['currency'])
				->where('status', TradingOrder::STATUS_NEW)
				->first();
			if (!$trading_order) {
				$this->error('Lệnh giao dịch không hợp lệ');
			}
			$cashins = Cashin::whereIn('id', $params['cashin_ids'])
				->where('currency', $params['currency'])
				->whereIn('status', [Cashin::STATUS_PAID, Cashin::STATUS_PARTIALLY_PERFORM])
				->get();
			if (!$cashins || count($cashins) != count($params['cashin_ids'])) {
				$this->error('Phiếu thu không hợp lệ');
			}
			$total_cashin_amount = 0;
			foreach ($params['cashin_amounts'] as $cashin_amount) {
				$total_cashin_amount+= $cashin_amount;
			}
			$total_overpayment = 0;
			foreach ($params['overpayments'] as $overpayment) {
				$total_overpayment+= $overpayment;
			}
			$model = TradingOrderCollate::create([
				'trading_order_id' => $trading_order->id,
				'trading_order_amount' => $trading_order->send_amount,
				'total_cashin_amount' => $total_cashin_amount,
				'investor_id' => $trading_order->investor_id,
				'investor_account_system_id' => $trading_order->send_account_system_id,
				'fund_product_id' => $trading_order->receive_fund_product_id,
				'trading_session_id' => $trading_order->trading_session_id,
				'overpayment' => $total_overpayment,
				'currency' => $params['currency'],
				'comparison_result' => TradingOrderCollate::COMPARISON_NOT_MATCH,
				'status' => TradingOrderCollate::STATUS_NOT_ENOUGHT,
				'created' => $params['created_by']
			]);
			if ($model) {
				$response['trading_order_collate_id'] = $model->id;
				(new TradingOrderTransaction())->waitCollate([
					'trading_order_id' => $trading_order->id, 
					'updated_by' => $params['created_by'],
				]);
				$index = 0;
				$transaction = new TradingOrderCollateLineTransaction();
				foreach ($cashins as $cashin) {
					if ($cashin_amount->amount_not_perform < $params['cashin_amounts'][$index]) {
						$this->error('Tham số đầu vào không hợp lệ');
					}
					$transaction->create([
						'trading_order_collate_id' => $model->id,
						'trading_order_id' => $trading_order->id, 
						'cashin_id' => $cashin->id, 
						'cashin_amount' => $params['cashin_amounts'][$index],
						'overpayment' => $params['overpayments'][$index],
						'currency' => $params['currency'],
						'bank_trans_note' => $cashin->bank_trans_note, 
						'created_by' => $params['created_by']
					]);
					$index++;
				}
			} else {
				$this->error('Có lỗi khi thêm đối chiếu tiền - lệnh');
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
	 * @param array $params[trading_order_collate_id, updated_by]
	 * @param allow_commit
	 */
	public function verify(array $params, $allow_commit = false)
	{
		$response = null;
		$this->beginTransaction($allow_commit);
		try {
			$model = TradingOrderCollate::where('id', $params['trading_order_collate_id'])
				->where('status', TradingOrderCollate::STATUS_NOT_ENOUGHT)				
				->first();
			if ($model) {
				$balance = $model->trading_order_amount - $model->total_cashin_amount;
				if ($balance > 0) {
					try {
						// freezing balance account system of investor
						(new AccountSystemTransaction)->increaseBalanceFreezing([
							'account_system_id' => $model->investor_account_system_id, 
							'amount' => $balance, 
							'updated_by' => $params['updated_by']
						]);
					} catch (\Exception $_e) {
						$balance = 0;
					}
				}
				// if enought then verify
				if (($model->trading_order_amount - $model->total_cashin_amount + $balance) >= 0) {
					$model->balance = $balance;
					$model->comparison_result = TradingOrderCollate::COMPARISON_MATCH;
					$model->status = $model->overpayment > 0 ? TradingOrderCollate::STATUS_VERIFY : TradingOrderCollate::STATUS_SUCCESS;
					$model->updated_by = $params['updated_by'];
					if ($model->save()) {
						if ($balance > 0) {
							// unfreezing balance account system of investor
							(new AccountSystemTransaction)->decreaseBalanceFreezing([
								'account_system_id' => $model->investor_account_system_id, 
								'amount' => $balance, 
								'updated_by' => $params['updated_by']
							]);
						}
						$trading_order_collate_lines = TradingOrderCollateLine::where('trading_order_collate_id', $model->id)
							->where('status', TradingOrderCollateLine::STATUS_NEW)
							->get();
						if ($trading_order_collate_lines) {
							$transaction = new TradingOrderCollateLineTransaction();
							foreach ($trading_order_collate_lines as $trading_order_collate_line) {
								$transaction->verify([
									'trading_order_collate_line_id' => $trading_order_collate_line->id,
									'updated_by' => $params['updated_by']
								]);
							}
							(new TradingOrderTransaction)->verifyBuy([
								'trading_order_id' => $model->trading_order_id, 
								'updated_by' => $params['updated_by']
							]);
						} else {
							$this->error('Dữ liệu không hợp lệ');
						}
					} else {
						$this->error('Có lỗi khi cập nhật đối chiếu tiền - lệnh');
					}
				}				
			} else {
				$this->error('Đối chiếu tiền - lệnh không hợp lệ');
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
	 * @param array $params[trading_order_collate_id, updated_by]
	 * @param allow_commit
	 */
	public function updateCashins(array $params, $allow_commit = false)
	{
		$response = null;
		$this->beginTransaction($allow_commit);
		try {
			$model = TradingOrderCollate::where('id', $params['trading_order_collate_id'])
				->where('status', TradingOrderCollate::STATUS_NOT_ENOUGHT)				
				->first();
			if ($model) {
				$cashins = Cashin::where('investor_id', $model->investor_id)
					->where('fund_product_id', $model->fund_product_id)
					->where('currency', $model->currency)
					->whereIn('status', [Cashin::STATUS_PAID, Cashin::STATUS_PARTIALLY_PERFORM])
					->where('amount_available', '>', 0)
					->orderBy('id', 'ASC')
					->get();
				if ($cashins) {
					$amount = $model->trading_order_amount - $model->total_cashin_amount;
					foreach ($cashins as $cashin) {
						if ($cashin->amount_available >= $amount) {
							$model->total_cashin_amount+= $amount;
							(new TradingOrderCollateLineTransaction)->create([
								'trading_order_collate_id' => $model->id,
								'trading_order_id' => $model->trading_order_id, 
								'cashin_id' => $cashin->id, 
								'cashin_amount' => $amount, 
								'overpayment' => $cashin->amount_available - $amount, 
								'currency' => $model->currency, 
								'bank_trans_note' => $cashin->bank_trans_note, 
								'created_by' => $params['updated_by']
							]);
							break;
						} else {
							$model->total_cashin_amount+= $cashin->amount_available;
							(new TradingOrderCollateLineTransaction)->create([
								'trading_order_collate_id' => $model->id,
								'trading_order_id' => $model->trading_order_id, 
								'cashin_id' => $cashin->id, 
								'cashin_amount' => $cashin->amount_available, 
								'overpayment' => 0, 
								'currency' => $model->currency, 
								'bank_trans_note' => $cashin->bank_trans_note, 
								'created_by' => $params['updated_by']
							]);
							$amount-= $cashin->amount_available;
						}
					}
					if (!$model->save()) {
						$this->error('Có lỗi khi cập nhật đối chiếu tiền - lệnh');
					}
				}			
			} else {
				$this->error('Đối chiếu tiền - lệnh không hợp lệ');
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
	 * @param array $params[trading_order_collate_id, overpayment, updated_by]
	 * @param allow_commit
	 */
	public function decreaseOverpayment(array $params, $allow_commit = false)
	{
		$response = null;
		$this->beginTransaction($allow_commit);
		try {
			$query = "UPDATE trading_order_collates 
				SET overpayment = overpayment - :overpayment, 
					status = IF(overpayment > 0, :status_verify, :status_success)
					updated_by = :updated_by, 
					updated_at = :updated_at
				WHERE id = :id 
				AND overpayment >= :overpayment";
			$update = Cashin::updateRawQuery($query, [
				'id' => $params['trading_order_collate_id'],
				'overpayment' => $params['overpayment'],
				'status_verify' => TradingOrderCollate::STATUS_VERIFY,
				'status_success' => TradingOrderCollate::STATUS_SUCCESS,
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
		// commit and response
		return $this->response($allow_commit, $response);
	}

	/**
	 * 
	 * @param array $params[trading_order_collate_id, updated_by]
	 * @param allow_commit
	 */
	public function waitConfirm(array $params, $allow_commit = false)
	{
		$response = null;
		$this->beginTransaction($allow_commit);
		try {
			$model = TradingOrderCollate::where('id', $params['trading_order_collate_id'])
				->where('status', TradingOrderCollate::STATUS_VERIFY)				
				->first();
			if ($model) {
				$model->status = TradingOrderCollate::STATUS_WAIT_CONFIRM;
				$model->updated_by = $params['updated_by'];
				if ($model->save()) {
					$lines = TradingOrderCollateLine::where('trading_order_collate_id', $model->id)
						->where('status', TradingOrderCollateLine::STATUS_VERIFY)
						->where('overpayment', '>', 0)
						->get();
					if ($lines) {
						foreach($lines as $line) {
							(new CashinTransaction)->increaseAmountFreezing([
								'cashin_id' => $line->cashin_id, 
								'amount' => $line->overpayment, 
								'updated_by' => $params['updated_by']
							]);
						}
					}					
				} else {
					$this->error('Có lỗi khi cập nhật đối chiếu tiền - lệnh');
				}
			} else {
				$this->error('Đối chiếu tiền - lệnh không hợp lệ');
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
	 * @param array $params[trading_order_collate_id, keep_for_next, updated_by]
	 * @param allow_commit
	 */
	public function updateKeepForNext(array $params, $allow_commit = false)
	{
		$response = null;
		$this->beginTransaction($allow_commit);
		try {
			$model = TradingOrderCollate::where('id', $params['trading_order_collate_id'])
				->where('status', TradingOrderCollate::STATUS_WAIT_CONFIRM)				
				->first();
			if ($model) {
				$model->status = TradingOrderCollate::STATUS_SUCCESS;
				$model->keep_for_next = $params['keep_for_next'];
				$model->updated_by = $params['updated_by'];
				if ($model->save()) {
					$lines = TradingOrderCollateLine::where('trading_order_collate_id', $model->id)
						->where('status', TradingOrderCollateLine::STATUS_VERIFY)
						->where('overpayment', '>', 0)
						->get();
					if ($lines) {
						$investor_bank_account = InvestorBankAccount::where('investor_id', $model->investor_id)
							->where('is_default', InvestorBankAccount::DEFAULT)
							->where('status', InvestorBankAccount::STATUS_ACTIVE)->first();
						foreach($lines as $line) {
							(new CashinTransaction)->decreaseAmountFreezing([
								'cashin_id' => $line->cashin_id, 
								'amount' => $line->overpayment, 
								'updated_by' => $params['updated_by']
							]);
							$result = (new CashinTransaction)->performAmount([
								'cashin_id' => $line->cashin_id, 
								'amount' => $line->overpayment, 
								'perform_at' => Carbon::now()->toDateTimeString(),
								'updated_by' => $params['updated_by']
							]);
							if ($params['keep_for_next'] == TradingOrderCollate::NOT_KEEP_FOR_NEXT && $investor_bank_account) {
								(new TransactionSystemTransaction)->createWithdrawAndVerify([
									'ref_id' => $line->cashin_id, 
									'ref_type' => TransactionSystem::REF_TYPE_CASHIN, 
									'send_account_system_id' => $model->investor_account_system_id,
									'amount' => $line->overpayment, 
									'currency' => $line->currency, 
									'refer_transaction_system_id' => $result['transaction_system_id'], 
									'bank_id' => $investor_bank_account->bank_id,
									'account_holder' => $investor_bank_account->account_holder,
									'account_number' => $investor_bank_account->account_number,
									'branch' => $investor_bank_account->branch,
									'created_by' => $params['updated_by'],
								]);
							}
						}
					}
				} else {
					$this->error('Có lỗi khi cập nhật đối chiếu tiền - lệnh');
				}
			} else {
				$this->error('Đối chiếu tiền - lệnh không hợp lệ');
			}
		} catch (\Exception $e) {
			// rollback transaction
			$this->rollback($allow_commit, $e);
		}
		// commit and response
		return $this->response($allow_commit, $response);
	}
}
