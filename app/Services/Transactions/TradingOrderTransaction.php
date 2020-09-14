<?php

namespace App\Services\Transactions;

use App\Events\PerformTradingOrderEvent;
use App\Events\VerifyTradingOrderEvent;
use App\Models\AccountSystem;
use App\Models\Cashin;
use App\Models\FundDistributorBankAccount;
use App\Models\FundProduct;
use App\Models\Investor;
use App\Models\InvestorBankAccount;
use App\Models\InvestorFundProduct;
use App\Models\InvestorSip;
use App\Models\TradingOrder;
use App\Models\TradingOrderCollate;
use App\Models\TradingOrderFeeBuy;
use App\Models\TradingOrderFeeSell;
use App\Models\TradingSession;
use App\Models\TransactionSystem;

/**
 * @author MSI
 * @version 1.0
 * @created 08-Jul-2020 11:48:15 AM
 */
class TradingOrderTransaction extends Transaction
{
	/**
	 *
	 * @param array $params[
	 * investor_id,
	 * fund_product_id,
	 * trading_session_id,
	 * currency,
	 * updated_by
	 * ]
	 * @param allow_commit
	 */
	public function collateBuy(array $params,  $allow_commit = false)
	{
		$response = null;
        $this->beginTransaction($allow_commit);
        try {
			$collate_ids = [];
			$trading_order_collates = TradingOrderCollate::where('investor_id', $params['investor_id'])
				->where('fund_product_id', $params['fund_product_id'])
				->where('trading_session_id', $params['trading_session_id'])
				->where('currency', $params['currency'])
				->where('status', TradingOrderCollate::STATUS_NOT_ENOUGHT)
				->orderBy('id', 'ASC')
				->get();
			if ($trading_order_collates) {
				foreach ($trading_order_collates as $trading_order_collate) {
					$collate_ids[] = $trading_order_collate->id;
					(new TradingOrderCollateTransaction)->updateCashins([
						'trading_order_collate_id' => $trading_order_collate->id,
						'updated_by' => $params['updated_by'],
					]);
				}
			}
			// get list cashins
			$cashins = Cashin::where('investor_id', $params['investor_id'])
				->where('fund_product_id', $params['fund_product_id'])
				->where('currency', $params['currency'])
				->whereIn('status', [Cashin::STATUS_PAID, Cashin::STATUS_PARTIALLY_PERFORM])
				->where('amount_available', '>', 0)
				->orderBy('id', 'ASC')
				->get();
			if ($cashins) {
				// get list buy orders
				$trading_orders = TradingOrder::where('investor_id', $params['investor_id'])
					->where('receive_fund_product_id', $params['fund_product_id'])
					->where('trading_session_id', $params['trading_session_id'])
					->where('exec_type', TradingOrder::EXEC_TYPE_BUY)
					->where('send_currency', $params['currency'])
					->where('status', TradingOrder::STATUS_NEW)
					->orderBy('id', 'ASC')
					->get();
				if ($trading_orders) {
					// create collate
					$mapping = $this->_mappingTradingOrdersAndCashins($trading_orders, $cashins, $params['currency'], $params['updated_by']);
					$transaction = new TradingOrderCollateTransaction();
					foreach ($mapping as $params) {
						$result = $transaction->create($params);
						$collate_ids[] = $result['trading_order_collate_id'];
					}
				}
			}
			if (!empty($collate_ids)) {
				// verify collate
				foreach ($collate_ids as $collate_id) {
					$transaction->verify([
						'trading_order_collate_id' => $collate_id,
						'updated_by' => $params['updated_by'],
					]);
				}
			}			
        } catch (\Exception $e) {
            // rollback transaction
            $this->rollback($allow_commit, $e);
        }
        //commit and response
        return $this->response($allow_commit, $response);
	}

	private function _mappingTradingOrdersAndCashins($trading_orders, $cashins, $currency, $created_by)
	{
		$result = array();
		$amounts = array();
		foreach ($cashins as $cashin) {
			$amounts[$cashin->id] = $cashin->amount_available;
		}
		$total_trading_order = count($trading_orders);
		$index = 1;
		foreach ($trading_orders as $trading_order) {
			$trading_order_amount = $trading_order->send_amount;
			$cashin_ids = array();
			$cashin_amounts = array();
			$overpayments = array();
			foreach ($amounts as $cashin_id => $amount) {
				if ($amount > 0) {
					$cashin_ids[] = $cashin_id;
					if ($amount > $trading_order_amount) {
						$cashin_amounts[] = $trading_order_amount;
						$amounts[$cashin_id]-= $trading_order_amount;
						if ($total_trading_order == $index) {
							$overpayments[] = $amount - $trading_order_amount;
						} else {
							$overpayments[] = 0;
						}
						$trading_order_amount = 0;
						break;
					} else {
						$cashin_amounts[] = $amount;
						$amounts[$cashin_id] = 0;
						$overpayments[] = 0;
						$trading_order_amount-= $amount;
					}
				}
			}			
			if (!empty($cashin_ids)) {
				$result[] = array(
					'trading_order_id' => $trading_order->id, 
					'cashin_ids' => $cashin_ids, 
					'cashin_amounts' => $cashin_amounts,
					'overpayments' => $overpayments,
					'currency' => $currency,
					'created_by' => $created_by
				);
			} else {
				break;
			}
			$index++;
		}		
		return $result;
	}

	/**
	 *
	 * @param array $params[
	 * trading_order_id,
	 * reason_cancel,
	 * updated_by
	 * ]
	 * @param allow_commit
	 */
	public function cancelBuy(array $params,  $allow_commit = false)
	{
		$response = null;
        $this->beginTransaction($allow_commit);
        try {
			$model = TradingOrder::where('id', $params['trading_order_id'])
				->where('exec_type', TradingOrder::EXEC_TYPE_BUY)
				->whereIn('status', [TradingOrder::STATUS_NEW, TradingOrder::STATUS_WAIT_COLLATE, TradingOrder::STATUS_VERIFY])
				->where('vsd_status', TradingOrder::VSD_STATUS_NOT_IMPORT)
				->first();
			if ($model) {
				$status = $model->status;
				$model->status = TradingOrder::STATUS_CANCEL;
				$model->reason_cancel = $params['reason_cancel'];
				$model->updated_by = $params['updated_by'];
				if ($model->save()) {
					if ($status == TradingOrder::STATUS_VERIFY) {
						(new TransactionSystemTransaction())->cancel([
							'transaction_system_id' => $model->transaction_system_id,
							'updated_by' => $params['updated_by'],
						]);
					}
				} else {
					$this->error('Có lỗi khi hủy giao dịch');
				}
			} else {
				$this->error('Lệnh giao dịch không tồn tại');
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
	public function cancelExchange(array $params,  $allow_commit = false)
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
	 * @param array $params[
	 * trading_order_id,
	 * reason_cancel,
	 * updated_by
	 * ]
	 * @param allow_commit
	 */
	public function cancelSell(array $params,  $allow_commit = false)
	{
		$response = null;
        $this->beginTransaction($allow_commit);
        try {
            $model = TradingOrder::where('id', $params['trading_order_id'])
				->where('exec_type', TradingOrder::EXEC_TYPE_SELL)
				->whereIn('status', [TradingOrder::STATUS_NEW, TradingOrder::STATUS_WAIT_COLLATE, TradingOrder::STATUS_VERIFY])
				->where('vsd_status', TradingOrder::VSD_STATUS_NOT_IMPORT)
				->first();
			if ($model) {
				$status = $model->status;
				$model->status = TradingOrder::STATUS_CANCEL;
				$model->reason_cancel = $params['reason_cancel'];
				$model->updated_by = $params['updated_by'];
				if ($model->save()) {
					if ($status == TradingOrder::STATUS_VERIFY) {
						(new InvestorFundProductTransaction())->decreaseBalanceFreezing([
							'investor_fund_product_id' => $model->send_investor_fund_product_id,
							'amount' => $model->send_amount,
							'updated_by' => $params['updated_by']
						]);
					}
				} else {
					$this->error('Có lỗi khi hủy giao dịch');
				}
			} else {
				$this->error('Lệnh giao dịch không tồn tại');
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
	 * receive_fund_product_id,	 
	 * trading_account_number,
	 * investor_sip_id,
	 * send_amount,
	 * send_currency,
	 * created_by
	 * ]
	 * @param allow_commit
	 */
	public function createBuy(array $params,  $allow_commit = false)
	{
		$response = null;
        $this->beginTransaction($allow_commit);
        try {
			$investor = Investor::where('trading_account_number', $params['trading_account_number'])->first();
			if (!$investor) {
				$this->error('Nhà đầu tư không tồn tại');
			} elseif (!$investor->isActive()) {
				$this->error('Tài khoản nhà đầu tư chưa kích hoạt');
			}
			$fund_product = FundProduct::where('id', $params['receive_fund_product_id'])
				->where('status', FundProduct::STATUS_ACTIVE)->first();
			if (!$fund_product) {
				$this->error('Sản phẩm quỹ không tồn tại hoặc không hợp lệ');
			}
			$investor_fund_product = InvestorFundProduct::where('investor_id', $params['investor_id'])
				->where('fund_product_id', $params['receive_fund_product_id'])
				->where('status', InvestorFundProduct::STATUS_ACTIVE)->first();
			if (!$investor_fund_product) {
				$this->error('Tài khoản quỹ không tồn tại hoặc đang bị khóa');
			}
			$send_account_system = AccountSystem::getForInvestor($investor->id, $fund_product->fund_product_type_id, $params['send_currency']);
			if (!$send_account_system) {
				$this->error('Tài khoản chuyển tiền không tồn tại');
			}
			$trading_sesssion = TradingSession::getForTradingOrder($fund_product->fund_company_id, $params['investor_id'], $params['receive_fund_product_id'], date('Y-m-d H:i:s'));
			if (!$trading_sesssion) {
				$this->error('Phiên giao dịch không tồn tại');
			}
			if (intval(@$params['investor_sip_id']) > 0) {
				$deal_type = TradingOrder::DEAL_TYPE_BUY_SIP;
				$investor_sip = InvestorSip::where('id', $params['investor_sip_id'])->where('status', InvestorSip::STATUS_ACTIVE);
				if (!$investor_sip) {
					$this->error('SIP không tồn tại hoặc không hoạt động');
				}
			} else {
				$deal_type = TradingOrder::DEAL_TYPE_BUY_NORMAL;
			}
			$fund_distributor_bank_account = FundDistributorBankAccount::where('fund_distributor_id', $investor->fund_distributor_id)	
				->where('fund_product_id', $fund_product->id)
				->where('status', FundDistributorBankAccount::STATUS_ACTIVE)
				->first();
			// create trading order
			$model = TradingOrder::create([
				'fund_company_id' => $investor->fund_company_id,
				'fund_distributor_id' => $investor->fund_distributor_id,
				'fund_distributor_bank_account_id' => $fund_distributor_bank_account->id,
				'investor_id' => $investor->id,
				'investor_id_type' => $investor->id_type,
				'investor_id_number' => $investor->id_number,
				'trading_session_id' => $trading_sesssion->id,
				'trading_frequency_id' => $trading_sesssion->trading_frequency_id,
				'trading_account_number' => $investor->trading_account_number,
				'investor_sip_id' => intval(@$params['investor_sip_id']),
				'deal_type' => $deal_type,
				'exec_type' => TradingOrder::EXEC_TYPE_BUY,
				'send_account_system_id' => $send_account_system->id,
				'send_amount' => $params['send_amount'],
				'send_currency' => $params['send_currency'],
				'receive_fund_certificate_id' => $fund_product->fund_certificate_id,
				'receive_fund_product_id' => $fund_product->id,
				'receive_fund_product_type_id' => $fund_product->fund_product_type_id,
				'receive_investor_fund_product_id' => $investor_fund_product->id,
				'receive_currency' => $investor_fund_product->currency,
				'fee' => 0,
				'fee_send' => 0,
				'fee_receive' => 0,
				'vsd_status' => 1,
				'status' => TradingOrder::STATUS_NEW,
				'vsd_status' => TradingOrder::VSD_STATUS_NOT_IMPORT,
				'created_by' => $params['created_by'],
			]);
//			dd($model);
			if ($model) {
				$response['trading_order_id'] = $model->id;
				$response['send_account_system_id'] = $send_account_system->id;
			} else {
				$this->error('Có lỗi khi tạo lệnh mua chứng chỉ quỹ');
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
	 * send_fund_product_id,
	 * send_amount,
	 * receive_currency,
	 * trading_account_number,	 
	 * created_by
	 * ]
	 * @param allow_commit
	 */
	public function createSellAndVerify(array $params,  $allow_commit= false)
	{
		$response = null;
        $this->beginTransaction($allow_commit);
        try {
			$response = $this->createSell($params);
			$this->verifySell([
				'trading_order_id' => $response['trading_order_id'],
				'updated_by' => $params['created_by'],
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
	 * receive_fund_product_id,
	 * trading_account_number,
	 * investor_sip_id,	 
	 * send_amount,
	 * send_currency,
	 * created_by
	 * ]
	 * @param allow_commit
	 */
	public function createBuyAndVerify(array $params,  $allow_commit = false)
	{
		$response = null;
        $this->beginTransaction($allow_commit);
        try {
			$response = $this->createBuy($params);
			$this->verifyBuy([
				'trading_order_id' => $response['trading_order_id'],
				'updated_by' => $params['created_by'],
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
	 * @param params
	 * @param allow_commit
	 */
	public function createExchange(array $params,  $allow_commit = false)
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
	 * send_fund_product_id,
	 * send_amount,
	 * receive_currency,
	 * trading_account_number,
	 * created_by
	 * ]
	 * @param boolean allow_commit
	 */
	public function createSell(array $params,  $allow_commit = false)
	{
		$response = null;
        $this->beginTransaction($allow_commit);
        try {
			$investor = Investor::where('trading_account_number', $params['trading_account_number'])->first();
			if (!$investor) {
				$this->error('Nhà đầu tư không tồn tại');
			} elseif (!$investor->isActive()) {
				$this->error('Nhà đầu tư đang bị khóa');
			}
			$fund_product = FundProduct::where('id', $params['send_fund_product_id'])
				->where('status', FundProduct::STATUS_ACTIVE)->first();
			if (!$fund_product) {
				$this->error('Sản phẩm quỹ không tồn tại hoặc không hợp lệ');
			}
			$investor_fund_product = InvestorFundProduct::where('investor_id', $params['investor_id'])
				->where('fund_product_id', $params['send_fund_product_id'])
				->where('status', InvestorFundProduct::STATUS_ACTIVE)->first();
			if (!$investor_fund_product) {
				$this->error('Tài khoản quỹ không tồn tại hoặc đang bị khóa');
			} elseif ($investor_fund_product->balance_available < $params['send_amount']) {
				$this->error('Số chứng chỉ quỹ không đủ để bán');
			}
			$receive_account_system = AccountSystem::getForInvestor($investor->id, $fund_product->fund_product_type_id, $params['receive_currency']);
			if (!$receive_account_system) {
				$this->error('Tài khoản nhận tiền không tồn tại');
			}
			$trading_sesssion = TradingSession::getForTradingOrder($fund_product->fund_company_id, $params['investor_id'], $params['send_fund_product_id'], date('Y-m-d H:i:s'));
			if (!$trading_sesssion) {
				$this->error('Phiên giao dịch không tồn tại');
			}
			$fund_distributor_bank_account = FundDistributorBankAccount::where('fund_distributor_id', $investor->fund_distributor_id)	
				->where('fund_product_id', $fund_product->id)
				->where('status', FundDistributorBankAccount::STATUS_ACTIVE)
				->first();
			$deal_type = TradingOrder::DEAL_TYPE_SELL_NORMAL;
			// create trading order
			$model = TradingOrder::create([
				'fund_company_id' => $investor->fund_company_id,
				'fund_distributor_id' => $investor->fund_distributor_id,
				'fund_distributor_bank_account_id' => $fund_distributor_bank_account->id,
				'investor_id' => $investor->id,
				'investor_id_type' => $investor->id_type,
				'investor_id_number' => $investor->id_number,
				'trading_session_id' => $trading_sesssion->id,
				'trading_frequency_id' => $trading_sesssion->trading_frequency_id,
				'trading_account_number' => $params['trading_account_number'],
				'investor_sip_id' => 0,
				'deal_type' => $deal_type,
				'exec_type' => TradingOrder::EXEC_TYPE_SELL,
				'send_fund_certificate_id' => $fund_product->fund_certificate_id,
				'send_fund_product_id' => $fund_product->id,
				'send_fund_product_type_id' => $fund_product->fund_product_type_id,
				'send_investor_fund_product_id' => $investor_fund_product->id,
				'send_amount' => $params['send_amount'],
				'send_currency' => $investor_fund_product->currency,
				'receive_account_system_id' => $receive_account_system->id,
				'receive_currency' => $params['receive_currency'],
				'fee' => 0,
				'fee_send' => 0,
				'fee_receive' => 0,
				'status' => TradingOrder::STATUS_NEW,
				'vsd_status' => TradingOrder::VSD_STATUS_NOT_IMPORT,
				'created_by' => $params['created_by'],
			]);
			if ($model) {
				$response['trading_order_id'] = $model->id;
			} else {
				$this->error('Có lỗi khi tạo lệnh mua chứng chỉ quỹ');
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
	 * trading_order_id,
	 * receive_match_amount,
	 * send_match_amount,
	 * tax,
	 * nav,
	 * total_nav,
	 * vsd_trading_id,
	 * vsd_time_received
	 * updated_by,
	 * ]
	 * @param boolean allow_commit
	 */
	public function performBuy(array $params,  $allow_commit = false)
	{
		$response = null;
        $this->beginTransaction($allow_commit);
        try {
			$model = TradingOrder::where('id', $params['trading_order_id'])->where('exec_type', TradingOrder::EXEC_TYPE_BUY)
				->where('status', TradingOrder::STATUS_VERIFY)->first();
			if ($model) {
				$model->receive_match_amount = $params['receive_match_amount'];
				$model->send_match_amount = $params['send_match_amount'];
				$model->tax = $params['tax'];
				$model->nav = $params['nav'];
				$model->total_nav = $params['total_nav'];
				$model->vsd_trading_id = $params['vsd_trading_id'];
				$model->vsd_time_received = $params['vsd_time_received'];
				$model->fee = 0;
				$model->fee_send = 0;
				$model->fee_receive = 0;
				$model->status = TradingOrder::STATUS_PERFORM;
				$model->updated_by = $params['updated_by'];
				if ($model->save()) {
					// increase balance fund product
					(new InvestorFundProductTransaction())->increaseBalance([
						'investor_fund_product_id' => $model->receive_investor_fund_product_id,
						'amount' => $params['receive_match_amount'],
						'updated_by' => $params['updated_by'],
					]);
					// create fund product buy history
					(new FundProductBuyHistoryTransaction)->create([
						'investor_fund_product_id' => $model->receive_investor_fund_product_id,
						'trading_order_id' => $model->id,
					]);
					if ($model->send_amount > $params['send_match_amount']) {
						// update match amount
						(new TransactionSystemTransaction())->updateSendMatchAmount([
							'transaction_system_id' => $model->transaction_system_id,
							'send_match_amount' => $params['send_match_amount'],
							'updated_by' => $params['updated_by'],
						]);
					}
					// perform transaction
					(new TransactionSystemTransaction())->perform([
						'transaction_system_id' => $model->transaction_system_id,
						'perform_at' => date('Y-m-d H:i:s'),
						'perform_by' => $params['updated_by'],
					]);
					// create cashout request
					if ($model->send_amount > $params['send_match_amount']) {
						$result = (new TransactionSystemTransaction())->createWithdrawAndVerify([
							'ref_id' => $model->id,
							'ref_type' => TransactionSystem::REF_TYPE_TRADING_ORDER,
							'send_account_system_id' => $model->send_account_system_id,
							'receive_account_system_id' => AccountSystem::MASTER_ID,
							'amount' => ($model->send_amount - $params['send_match_amount']),
							'currency' => $model->send_currency,
							'created_by' => $params['updated_by'],
						]);
						$response['withdraw_transaction_system_id'] = $result['withdraw_transaction_system_id'];
						$response['cashout_request_id'] = $result['cashout_request_id'];
					}
					event(new PerformTradingOrderEvent($model));
				} else {
					$this->error('Có lỗi khi cập nhật lệnh giao dịch');
				}
			} else {
				$this->error('Lệnh giao dịch không tồn tại hoặc không hợp lệ');
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
	public function performExchange(array $params,  $allow_commit = false)
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
	 * @param array $params[
	 * trading_order_id,
	 * receive_match_amount,
	 * send_match_amount,
	 * tax,
	 * nav,
	 * total_nav,
	 * vsd_trading_id,
	 * vsd_time_received,
	 * ]
	 * @param allow_commit
	 */
	public function performSell(array $params,  $allow_commit = false)
	{
		$response = null;
        $this->beginTransaction($allow_commit);
        try {
			$model = TradingOrder::where('id', $params['trading_order_id'])->where('exec_type', TradingOrder::EXEC_TYPE_SELL)
				->where('status', TradingOrder::STATUS_VERIFY)->first();
			if ($model) {
				$model->receive_match_amount = $params['receive_match_amount'];
				$model->send_match_amount = $params['send_match_amount'];
				$model->tax = $params['tax'];
				$model->nav = $params['nav'];
				$model->total_nav = $params['total_nav'];
				$model->vsd_trading_id = $params['vsd_trading_id'];
				$model->vsd_time_received = $params['vsd_time_received'];
				$model->fee = 0;
				$model->fee_send = 0;
				$model->fee_receive = 0;
				$model->status = TradingOrder::STATUS_PERFORM;
				$model->updated_by = $params['updated_by'];
				if ($model->save()) {
					// decrease balance freezing fund product
					(new InvestorFundProductTransaction())->decreaseBalanceFreezing([
						'investor_fund_product_id' => $model->send_investor_fund_product_id,
						'amount' => $params['send_amount'],
						'updated_by' => $params['updated_by'],
					]);
					// decrease balance fund product
					(new InvestorFundProductTransaction())->decreaseBalance([
						'investor_fund_product_id' => $model->send_investor_fund_product_id,
						'amount' => $params['send_match_amount'],
						'updated_by' => $params['updated_by'],
					]);
					// create trading order lines
					$result = (new TradingOrderLineTransaction())->createByTradingOrderSell([
						'trading_order_id' => $model->id,
					]);
					// update fee
					$model->fee = $result['total_fee'];
					if (!$model->save()) {
						$this->error('Có lỗi khi cập nhật phí');
					}
					// create transfer transaction to receive_account
					$actual_amount = $model->receive_match_amount - $params['tax'] - $model->fee - $model->fee_receive;
					$result = (new TransactionSystemTransaction())->createTransferAndPerform([
						'ref_id' => $model->id,
						'ref_type' => TransactionSystem::REF_TYPE_TRADING_ORDER,
						'send_account_system_id' => AccountSystem::MASTER_ID,
						'receive_account_system_id' => $model->receive_account_system_id,
						'amount' => $actual_amount,
						'currency' => $model->receive_currency,
						'created_by' => $params['updated_by']
					]);
					$response['transfer_transaction_system_id'] = $result['transaction_system_id'];
					// update transaction_sytem_id
					$this->updateTransactionSystem([
						'trading_order_id' => $model->id,
						'transaction_system_id' => $response['transfer_transaction_system_id'],
						'updated_by' => $params['updated_by'],
					]);
					// get investor bank account default
					$investor_bank_account = InvestorBankAccount::where('investor_id', $model->investor_id)
						->where('is_default', InvestorBankAccount::DEFAULT)
						->where('status', InvestorBankAccount::STATUS_ACTIVE)->first();
					if ($investor_bank_account) {
						// create withdraw transaction from receive_account
						(new TransactionSystemTransaction())->createWithdrawAndVerify([
							'ref_id' => $model->id,
							'ref_type' => TransactionSystem::REF_TYPE_TRADING_ORDER,
							'send_account_system_id' => $model->receive_account_system_id,
							'amount' => $model->receive_match_amount,
							'currency' => $model->receive_currency,
							'refer_transaction_system_id' => $response['transfer_transaction_system_id'],
							'bank_id' => $investor_bank_account->bank_id,
							'account_holder' => $investor_bank_account->account_holder,
							'account_number' => $investor_bank_account->account_number,
							'branch' => $investor_bank_account->branch,
							'created_by' => $params['updated_by'],
						]);
					}
					event(new PerformTradingOrderEvent($model));
				} else {
					$this->error('Có lỗi khi cập nhật lệnh giao dịch');
				}
			} else {
				$this->error('Lệnh giao dịch không tồn tại hoặc không hợp lệ');
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
	 * @param array $params[
	 * trading_order_id,
	 * transaction_system_id,
	 * updated_by
	 * ]
	 * @param allow_commit
	 */
	public function updateTransactionSystem(array $params,  $allow_commit = false)
	{
		$response = null;
        $this->beginTransaction($allow_commit);
        try {
			$model = TradingOrder::find($params['trading_order_id']);
			if ($model) {
				$model->transaction_system_id = $params['transaction_system_id'];
				$model->updated_by = $params['updated_by'];
				if (!$model->save()) {
					$this->error('Có lỗi khi cập nhật lệnh giao dịch');
				}
			} else {
				$this->error('Lệnh giao dịch không tồn tại');
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
	public function updateVSDStatus(array $params,  $allow_commit = false)
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
	 * trading_order_id,
	 * updated_by,
	 * ]
	 * @param boolean allow_commit
	 */
	public function verifyBuy(array $params,  $allow_commit = false)
	{
		$response = null;
        $this->beginTransaction($allow_commit);
        try {
			$model = TradingOrder::where('id', $params['trading_order_id'])->where('exec_type', TradingOrder::EXEC_TYPE_BUY)
				->where('status', TradingOrder::STATUS_NEW)->first();
			if ($model) {
				$model->status = TradingOrder::STATUS_VERIFY;
				$model->updated_by = $params['updated_by'];
				if ($model->save()) {
					$send_account_system = AccountSystem::getForInvestor($model->investor_id, $model->receive_fund_product_type_id, $model->send_currency);
					$result = (new TransactionSystemTransaction())->createTransferAndVerify([
						'ref_id' => $model->id,
						'ref_type' => TransactionSystem::REF_TYPE_TRADING_ORDER,
						'send_account_system_id' => $send_account_system->id,
						'receive_account_system_id' => AccountSystem::MASTER_ID,
						'amount' => $model->send_amount,
						'currency' => $model->send_currency,
						'created_by' => $params['updated_by'],
					]);
					$response['transaction_system_id'] = $result['id'];
					$this->updateTransactionSystem([
						'trading_order_id' => $model->id,
						'transaction_system_id' => $response['transaction_system_id'],
						'updated_by' => $params['updated_by'],
					]);
					event(new VerifyTradingOrderEvent($model));
				} else {
					$this->error('Có lỗi khi cập nhật lệnh giao dịch');
				}
			} else {
				$this->error('Lệnh giao dịch không tồn tại hoặc không hợp lệ');
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
	public function verifyExchange(array $params,  $allow_commit = false)
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
	 * @param array $params[
	 * trading_order_id,
	 *
	 * updated_by
	 * ]
	 * @param allow_commit
	 */
	public function verifySell(array $params,  $allow_commit = false)
	{
		$response = null;
        $this->beginTransaction($allow_commit);
        try {
			$model = TradingOrder::where('id', $params['trading_order_id'])->where('exec_type', TradingOrder::EXEC_TYPE_SELL)
				->where('status', TradingOrder::STATUS_NEW)->first();
			if ($model) {
				$model->status = TradingOrder::STATUS_VERIFY;
				$model->updated_by = $params['updated_by'];
				if ($model->save()) {
					(new InvestorFundProductTransaction())->increaseBalanceFreezing([
						'investor_fund_product_id' => $model->send_investor_fund_product_id,
						'amount' => $model->send_amount,
						'updated_by'=> $params['updated_by']
					]);
					event(new VerifyTradingOrderEvent($model));
				} else {
					$this->error('Có lỗi khi cập nhật lệnh giao dịch');
				}
			} else {
				$this->error('Lệnh giao dịch không tồn tại hoặc không hợp lệ');
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
	 * @param array $params[
	 * trading_order_id,
	 * updated_by
	 * ]
	 * @param allow_commit
	 */
	public function waitCollate(array $params,  $allow_commit = false)
	{
		$response = null;
        $this->beginTransaction($allow_commit);
        try {
			$model = TradingOrder::where('id', $params['trading_order_id'])
				->where('exec_type', TradingOrder::EXEC_TYPE_BUY)
				->where('status', TradingOrder::STATUS_NEW)
				->first();
			if ($model) {
				$model->status = TradingOrder::STATUS_WAIT_COLLATE;
				$model->updated_by = $params['updated_by'];
				if (!$model->save()) {
					$this->error('Có lỗi khi cập nhật lệnh giao dịch');
				}
			} else {
				$this->error('Lệnh giao dịch không tồn tại hoặc không hợp lệ');
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
	 * receive_fund_product_id,	 
	 * trading_account_number,
	 * investor_sip_id,
	 * send_amount,
	 * send_currency,
	 * created_by
	 * ]
	 * @param allow_commit
	 */
	public function processBuy(array $params,  $allow_commit = false)
	{
		$response = null;
        $this->beginTransaction($allow_commit);
        try {
			$response = $this->createBuy($params);
			$trading_order = TradingOrder::find($response['trading_order_id']);
			$send_account_system = AccountSystem::find($trading_order->send_account_system_id);
			if ($send_account_system) {
				if ($send_account_system->balance_available >= $params['send_amount']) {
					$this->verifyBuy([
						'trading_order_id' => $response['trading_order_id'], 
						'updated_by' => $params['created_by'],
					]);
				} else {
					$this->collateBuy([
						'investor_id' => $trading_order->investor_id, 
						'fund_product_id' => $trading_order->receive_fund_product_id, 
						'trading_session_id' => $trading_order->trading_session_id, 
						'currency' => $trading_order->send_currency,
						'updated_by' => $params['created_by'],
					]);
				}
			} else {
				$this->error('Lỗi hệ thống');
			}
        } catch (\Exception $e) {
            // rollback transaction
            $this->rollback($allow_commit, $e);
        }
        //commit and response
        return $this->response($allow_commit, $response);
	}
}
