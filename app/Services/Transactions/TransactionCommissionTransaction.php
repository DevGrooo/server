<?php
namespace App\Services\Transactions;

use App\Models\AccountCommission;
use App\Models\FundDistributorProduct;
use App\Models\SettingCommission;
use App\Models\TradingOrder;
use App\Models\TransactionCommission;
use App\Models\TransactionCommissionReference;

/**
 * @author MSI
 * @version 1.0
 * @created 13-Jul-2020 2:52:39 PM
 */
class TransactionCommissionTransaction extends Transaction
{

	/**
	 * 
	 * @param params
	 * @param allow_commit
	 */
	public function cancel(array $params, $allow_commit = false)
	{
	}

	/**
	 * 
	 * @param array $params[
	 * fund_distributor_product_id,
	 * trading_session_id,
	 * balance,
	 * day_holding,
	 * created_by
	 * ]
	 * @param allow_commit
	 */
	public function createForFundHolding(array $params, $allow_commit = false)
	{
		$response = null;
        $this->beginTransaction($allow_commit);
        try {
			$fund_distributor_product = FundDistributorProduct::where('id', $params['fund_distributor_product_id'])->first();
			if ($fund_distributor_product) {
				$receive_account_commission = AccountCommission::where('ref_id', $fund_distributor_product->fund_distributor_id)
					->where('ref_type', 'fund_distributor')->first();
				if ($receive_account_commission) {
					$setting_commission_info = SettingCommission::getForTypeMaintance($fund_distributor_product, $params['balance'], $params['day_holding']);
					if ($setting_commission_info) {
						$model = TransactionCommission::create([
							'type' => TransactionCommission::TYPE_MAINTANCE,
							'ref_id' => $fund_distributor_product->fund_distributor_id,
							'ref_type' => 'fund_distributor',
							'fund_company_id' => $fund_distributor_product->fund_company_id,
							'fund_certificate_id' => $fund_distributor_product->fund_certificate_id,
							'fund_product_id' => $fund_distributor_product->fund_product_id,
							'trading_session_id' => $params['trading_session_id'],							
							'setting_commission_id' => $setting_commission_info['setting_commission_id'],
							'send_account_commission_id' => AccountCommission::MASTER_ID,
							'receive_account_commission_id' => $receive_account_commission->id,
							'amount' => $setting_commission_info['commission_amount'],
							'currency' => $setting_commission_info['currency'],
							'status' => TransactionCommission::STATUS_NEW,
							'created_by' => $params['created_by'],
						]);
						if ($model) {
							$response['transaction_commission_id'] = $model->id;
						} else {
							$this->error('Có lỗi khi thêm giao dịch hoa hồng');
						}
					}
				} else {
					$this->error('Không tồn tại tài khoản hoa hồng');
				}							
			} else {
				$this->error('Lệnh mua không tồn tại hoặc không hợp lệ');
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
	 * created_by
	 * ]
	 * @param allow_commit
	 */
	public function createAndVerifyForFundHolding(array $params, $allow_commit = false)
	{
		$response = null;
        $this->beginTransaction($allow_commit);
        try {
			$response = $this->createForFundHolding($params);
			$this->verify([
				'transaction_commission_id' => $response['transaction_commission_id'], 
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
	 * @param array $params[
	 * trading_order_id,
	 * created_by
	 * ]
	 * @param allow_commit
	 */
	public function createAndVerifyForOrderBuy(array $params, $allow_commit = false)
	{
		$response = null;
        $this->beginTransaction($allow_commit);
        try {
			$response = $this->createForOrderBuy($params);
			$this->verify([
				'transaction_commission_id' => $response['transaction_commission_id'], 
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
	 * @param array $params[
	 * trading_order_id,
	 * created_by
	 * ]
	 * @param allow_commit
	 */
	public function createForOrderBuy(array $params, $allow_commit = false)
	{
		$response = null;
        $this->beginTransaction($allow_commit);
        try {
			$trading_order = TradingOrder::where('id', $params['trading_order_id'])
				->where('status', TradingOrder::STATUS_PERFORM)->first();
			if ($trading_order) {
				$receive_account_commission = AccountCommission::where('ref_id', $trading_order->fund_distributor_id)
					->where('ref_type', 'fund_distributor')->first();
				if ($receive_account_commission) {
					$setting_commission_info = SettingCommission::getForTypeBuy($trading_order);
					if ($setting_commission_info) {
						$model = TransactionCommission::create([
							'type' => TransactionCommission::TYPE_BUY,
							'ref_id' => $trading_order->fund_distributor_id,
							'ref_type' => 'fund_distributor',
							'fund_company_id' => $trading_order->fund_company_id,
							'fund_certificate_id' => $trading_order->receive_fund_certificate_id,
							'fund_product_id' => $trading_order->receive_fund_product_id,
							'trading_order_id' => $trading_order->id,
							'setting_commission_id' => $setting_commission_info['setting_commission_id'],
							'send_account_commission_id' => AccountCommission::MASTER_ID,
							'receive_account_commission_id' => $receive_account_commission->id,
							'amount' => $setting_commission_info['commission_amount'],
							'currency' => $setting_commission_info['currency'],
							'status' => TransactionCommission::STATUS_NEW,
							'created_by' => $params['created_by'],
						]);
						if ($model) {
							$response['transaction_commission_id'] = $model->id;
						} else {
							$this->error('Có lỗi khi thêm giao dịch hoa hồng');
						}
					}
				} else {
					$this->error('Không tồn tại tài khoản hoa hồng');
				}							
			} else {
				$this->error('Lệnh mua không tồn tại hoặc không hợp lệ');
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
	public function createMultiForFundHoldingAndVerify(array $params, $allow_commit = false)
	{
	}

	/**
	 * 
	 * @param params
	 * @param allow_commit
	 */
	public function perform(array $params, $allow_commit = false)
	{
	}

	/**
	 * 
	 * @param array $params[
	 * transaction_commission_id,
	 * updated_by
	 * ]
	 * @param allow_commit
	 */
	public function verify(array $params, $allow_commit = false)
	{
		$response = null;
        $this->beginTransaction($allow_commit);
        try {
			$model = TransactionCommission::where('id', $params['transaction_commission_id'])
				->where('status', TransactionCommission::STATUS_NEW)->first();
			if ($model) {
				$model->status = TransactionCommission::STATUS_VERIFY;
				$model->updated_by = $params['updated_by'];
				$model->verified_by = $params['updated_by'];
				if ($model->save()) {
					// send account
					(new AccountCommissionTransaction())->increaseBalanceFreezing([
						'account_commission_id' => $model->send_account_commission_id, 
						'amount' => $model->amount, 
						'updated_by' => $params['updated_by'],
					]);
					// receive account
					(new AccountCommissionTransaction())->increaseBalanceWaiting([
						'account_commission_id' => $model->receive_account_commission_id, 
						'amount' => $model->amount, 
						'updated_by' => $params['updated_by'],
					]);
				} else {
					$this->error('Có lỗi khi xác nhận giao dịch hoa hồng');
				}
			} else {
				$this->error('Giao dịch ghi nhận hoa hồng không hợp lệ');
			}
        } catch (\Exception $e) {
            // rollback transaction
            $this->rollback($allow_commit, $e);
        }
        //commit and response
        return $this->response($allow_commit, $response);
	}

}
