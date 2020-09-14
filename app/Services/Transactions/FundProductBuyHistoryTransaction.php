<?php

namespace App\Services\Transactions;

use App\Models\FundProductBuyHistory;
use App\Models\InvestorFundProduct;
use App\Models\TradingOrder;

/**
 * @author MSI
 * @version 1.0
 * @created 08-Jul-2020 11:48:50 AM
 */
class FundProductBuyHistoryTransaction extends Transaction
{

	/**
	 * 
	 * @param array $params[
	 * investor_fund_product_id,
	 * trading_order_id,
	 * ]
	 * @param allow_commit
	 */
	public function create(array $params, $allow_commit = false)
	{
		$response = null;
        $this->beginTransaction($allow_commit);
        try {
			$investor_fund_product = InvestorFundProduct::find($params['investor_fund_product_id']);
			if (!$investor_fund_product) {
				$this->error('Sản phẩm nhà đầu tư không tồn tại');
			}
			$trading_order = TradingOrder::find($params['trading_order_id']);
			if (!$trading_order) {
				$this->error('Lệnh mua không tồn tại');
			} elseif ($trading_order->exec_type != TradingOrder::EXEC_TYPE_BUY) {
				$this->error('Lệnh mua không hợp lệ');
			}
			// add
			$model = FundProductBuyHistory::create([
				'investor_id' => $investor_fund_product->investor_id,
				'fund_distributor_id' => $investor_fund_product->fund_distributor_id,
				'fund_company_id' => $investor_fund_product->fund_company_id,
				'fund_certificate_id' => $investor_fund_product->fund_certificate_id,
				'fund_product_id' => $investor_fund_product->fund_product_id,
				'investor_fund_product_id' => $investor_fund_product->id,
				'trading_order_id' => $trading_order->id,
				'buy_amount' => $trading_order->receive_amount,
				'sell_amount' => 0,
				'current_amount' => $trading_order->receive_amount,
				'currency' => $trading_order->receive_currency,
				'status' => FundProductBuyHistory::STATUS_NOT_SOLD_OUT,
			]);
			if (!$model) {
				$this->error('Có lỗi khi xử lý');
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
	 * trading_order_buy_id,
	 * sell_amount,	 
	 * ]
	 * @param allow_commit
	 */
	public function updateSellAmount(array $params, $allow_commit = false)
	{
		$response = null;
        $this->beginTransaction($allow_commit);
        try {
			$model = FundProductBuyHistory::where('trading_order_id', $params['trading_order_buy_id'])
					->where('status', FundProductBuyHistory::STATUS_NOT_SOLD_OUT)
					->where('current_amount', '>=', $params['sell_amount'])->first();
			if ($model) {
				$query = "UPDATE fund_product_buy_history 
							SET 
							sell_amount = sell_amount + :sell_amount,
							current_amount = current_amount - :sell_amount,
							status = IF(current_amount > :sell_amount, :status_not_sold_out, :status_sold_out) 
							WHERE id = :id 
							AND status = :status_not_sold_out 
							AND current_amount >= :sell_amount ";
				$update = FundProductBuyHistory::updateRawQuery($query, [
					'id' => $model->id,
					'sell_amount' => $params['sell_amount'],
					'status_not_sold_out' => FundProductBuyHistory::STATUS_NOT_SOLD_OUT,
					'status_sold_out' => FundProductBuyHistory::STATUS_SOLD_OUT
				]);
				if (!$update) {
					$this->error('Có lỗi khi cập nhật số chứng chỉ quỹ đã bán');	
				}
			} else {
				$this->error('Dữ liệu không hợp lệ');
			}
        } catch (\Exception $e) {
            // rollback transaction
            $this->rollback($allow_commit, $e);
        }
        //commit and response
        return $this->response($allow_commit, $response);
	}
}
