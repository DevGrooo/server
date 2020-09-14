<?php
namespace App\Services\Transactions;

use App\Models\FundDistributorProduct;
use App\Models\FundDistributorProductBalance;
use App\Models\FundProduct;
use App\Models\TradingSession;

/**
 * @author MSI
 * @version 1.0
 * @created 13-Jul-2020 2:51:46 PM
 */
class FundDistributorProductBalanceTransaction extends Transaction
{
	/**
	 * 
	 * @param array $params[
	 * trading_session_id,
	 * fund_distributor_product_id,
	 * created_by 
	 * ]
	 * @param allow_commit
	 */
	public function create(array $params, $allow_commit = false)
	{
		$response = null;
        $this->beginTransaction($allow_commit);
        try {
			$fund_distributor_product = FundDistributorProduct::find($params['fund_distributor_product_id']);
			if (!$fund_distributor_product) {
				$this->error('Sản phẩm quỹ của đại lý phân phối không tồn tại');
			}
			$fund_product = FundProduct::find($fund_distributor_product->fund_product_id);
			if (!$fund_product) {
				$this->error('Sản phẩm quỹ không tồn tại');
			}
			$trading_session = TradingSession::where('id', $params['trading_session_id'])
				->where('status', TradingSession::STATUS_END)->first();
			if (!$trading_session) {
				$this->error('Phiên giao dịch không hợp lệ');
			}
			$balance = FundDistributorProductBalance::getBalanceAfterEndTradingSession($trading_session, $fund_distributor_product);
			$model = FundDistributorProductBalance::create([
				'fund_distributor_id' => $fund_distributor_product->fund_distributor_id,
				'fund_product_id' => $fund_distributor_product->fund_product_id,
				'fund_distributor_product_id' => $fund_distributor_product->id,
				'trading_session_id' => $trading_session->id,
				'balance' => $balance,
				'currency' => $fund_product->code,
				'created_by' => $params['created_by'],
			]);
			if ($model) {
				$response['fund_distributor_product_balance_id'] = $model->id;
				$response['balance'] = $balance;
			} else {
				$this->error('Có lỗi khi cập nhật số dư chứng chỉ quỹ của đại lý phân phối');
			}
        } catch (\Exception $e) {
            // rollback transaction
            $this->rollback($allow_commit, $e);
        }
        //commit and response
        return $this->response($allow_commit, $response);
	}

}
