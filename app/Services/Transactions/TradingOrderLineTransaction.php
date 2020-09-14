<?php

namespace App\Services\Transactions;

use App\Models\FundProductBuyHistory;
use App\Models\TradingOrder;
use App\Models\TradingOrderFeeSell;
use App\Models\TradingOrderLine;

/**
 * @author MSI
 * @version 1.0
 * @created 08-Jul-2020 11:48:22 AM
 */
class TradingOrderLineTransaction extends Transaction
{

	/**
	 * 
	 * @param array $params[
	 * trading_order_id,
	 * trading_order_buy_id,
	 * sell_amount,
	 * fee,
	 * ]
	 * @param allow_commit
	 */
	public function create(array $params, $allow_commit = false)
	{
		$response = null;
        $this->beginTransaction($allow_commit);
        try {
			$trading_order = TradingOrder::where('id', $params['trading_order_id'])				
				->where('exec_type', TradingOrder::EXEC_TYPE_SELL)
				->where('status', TradingOrder::STATUS_PERFORM)->first();
			if (!$trading_order) {
				$this->error('Giao dịch không tồn tại');
			}
			$model = TradingOrderLine::create([
				'trading_order_id' => $trading_order->id,
				'trading_order_buy_id' => $params['trading_order_buy_id'],
				'sell_amount' => $params['sell_amount'],
				'fee' => $params['fee'],
				'currency' => $trading_order->receive_currency
			]);
			if ($model) {
				$response['trading_order_line_id'] = $model->id;
				(new FundProductBuyHistoryTransaction)->updateSellAmount([
					'trading_order_buy_id' => $params['trading_order_buy_id'],
					'sell_amount' => $params['sell_amount'],
				]);
			} else {
				$this->error('Có lỗi khi thêm chi tiết lệnh bán');
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
	 * @param array $params[ trading_order_id ]
	 * @param allow_commit
	 */
	public function createByTradingOrderSell(array $params, $allow_commit = false)
	{
		$response = null;
        $this->beginTransaction($allow_commit);
        try {
			$trading_order = TradingOrder::where('id', $params['trading_order_id'])				
				->where('exec_type', TradingOrder::EXEC_TYPE_SELL)
				->where('status', TradingOrder::STATUS_PERFORM)->first();
			if (!$trading_order) {
				$this->error('Giao dịch không tồn tại');
			}
			$buy_histories = FundProductBuyHistory::where('investor_fund_product_id', $trading_order->send_investor_fund_product_id)
				->where('status', FundProductBuyHistory::STATUS_NOT_SOLD_OUT)->orderBy('created_at', 'ASC');
			if ($buy_histories) {
				$total_fee = 0;
				$total_sell_amount = $trading_order->send_amount;
				foreach ($buy_histories as $model) {
					if ($total_sell_amount > $model->current_amount) {
						$total_sell_amount = $total_sell_amount - $model->current_amount;
						$sell_amount = $model->current_amount;
					} else {
						$total_sell_amount = 0;
						$sell_amount = $model->current_amount - $total_sell_amount;
					}
					//---------------
					$holding_period = $model->getHoldingPeriod($trading_order->vsd_time_received);
					$fee = TradingOrderFeeSell::getFee($trading_order->vsd_time_received, $holding_period, $sell_amount);
					$total_fee += $fee;
					$this->create([
						'trading_order_id' => $trading_order->id, 
						'trading_order_buy_id' => $model->trading_order_id, 
						'sell_amount' => $sell_amount, 
						'fee' => $fee,
					]);
					if ($total_sell_amount == 0) {
						break;
					}
				}
				if ($total_sell_amount > 0) {
					$this->error('Lỗi dữ liệu');
				} else {
					$response['total_fee'] = $total_fee;
				}
			} else {
				$this->error('Lỗi dữ liệu');
			}
        } catch (\Exception $e) {
            // rollback transaction
            $this->rollback($allow_commit, $e);
        }
        //commit and response
        return $this->response($allow_commit, $response);
	}
}
