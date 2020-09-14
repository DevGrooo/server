<?php

namespace App\Services\Transactions;

use App\Models\TradingOrderCollateLine;
use Carbon\Carbon;

/**
 * @author MSI
 * @version 1.0
 * @created 07-Sep-2020 4:54:40 PM
 */
class TradingOrderCollateLineTransaction extends Transaction
{


	/**
	 * 
	 * @param array $params[trading_order_collate_line_id, updated_by]
	 * @param allow_commit
	 */
	public function cancel(array $params, $allow_commit = false)
	{
		$response = null;
        $this->beginTransaction($allow_commit);
        try {
			$model = TradingOrderCollateLine::where('id', $params['trading_order_collate_line_id'])
				->where('status', TradingOrderCollateLine::STATUS_NEW)
				->first();
			if ($model) {
				$model->status = TradingOrderCollateLine::STATUS_CANCEL;
				$model->updated_by = $params['updated_by'];
				if ($model->save()) {
					(new CashinTransaction)->decreaseAmountFreezing([
						'cashin_id' => $model->cashin_id,
						'amount' => $model->cashin_amount,
						'updated_by' => $params['updated_by'],
					]);
				} else {
					$this->error('Có lỗi khi hủy đối chiếu tiền - lệnh');
				}
			} else {
				$this->error('Đối chiếu tiền - lệnh không hợp lệ');
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
	 * trading_order_collate_id
	 * trading_order_id,
	 * cashin_id,
	 * cashin_amount,
	 * overpayment,
	 * currency,
	 * bank_trans_note,
	 * created_by
	 * ]
	 * @param allow_commit
	 */
	public function create(array $params, $allow_commit = false)
	{
		$response = null;
        $this->beginTransaction($allow_commit);
        try {
			$model = TradingOrderCollateLine::create([
				'trading_order_collate_id' => $params['trading_order_collate_id'],
				'trading_order_id' => $params['trading_order_id'],
				'cashin_id' => $params['cashin_id'],
				'cashin_amount' => $params['cashin_amount'],
				'overpayment' => $params['overpayment'],
				'currency' => $params['currency'],
				'bank_trans_note' => $params['bank_trans_note'],
				'status' => TradingOrderCollateLine::STATUS_NEW,
				'created_by' => $params['created_by'],
			]);
			if ($model) {
				$response['trading_order_collate_line_id'] = $model->id;
				$others = TradingOrderCollateLine::where('cashin_id', $params['cashin_id'])
					->where('overpayment', '>', 0)
					->where('status', TradingOrderCollateLine::STATUS_VERIFY)
					->get();
				if ($others) {
					foreach ($others as $other) {
						$this->removeOverpaymentOtherCollate([
							'trading_order_collate_line_id' => $other->id,
							'updated_by' => $params['created_by'],
						]);
					}
				}
				(new CashinTransaction)->increaseAmountFreezing([
					'cashin_id' => $params['cashin_id'],
					'amount' => $params['cashin_amount'],
					'updated_by' => $params['updated_by'],
				]);
			} else {
				$this->error('Có lỗi khi thêm đối chiếu tiền - lệnh');
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
	 * @param array $params[trading_order_collate_line_id, updated_by]
	 * @param allow_commit
	 */
	public function verify(array $params, $allow_commit = false)
	{
		$response = null;
        $this->beginTransaction($allow_commit);
        try {
			$model = TradingOrderCollateLine::where('id', $params['trading_order_collate_line_id'])
				->where('status', TradingOrderCollateLine::STATUS_NEW)
				->first();
			if ($model) {
				$model->status = $model->overpayment > 0 ? TradingOrderCollateLine::STATUS_VERIFY : TradingOrderCollateLine::STATUS_SUCCESS;
				$model->updated_by = $params['updated_by'];
				if ($model->save()) {
					(new CashinTransaction)->decreaseAmountFreezing([
						'cashin_id' => $model->cashin_id,
						'amount' => $model->cashin_amount,
						'updated_by' => $params['updated_by'],
					]);
					(new CashinTransaction)->performAmount([
						'cashin_id' => $model->cashin_id,
						'amount' => $model->cashin_amount,
						'perform_at' => Carbon::now()->toDateTimeString(),
						'updated_by' => $params['updated_by'],
					]);
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
	 * @param array $params[trading_order_collate_line_id, updated_by]
	 * @param allow_commit
	 */
	public function removeOverpaymentOtherCollate(array $params, $allow_commit = false)
	{
		$response = null;
        $this->beginTransaction($allow_commit);
        try {
			$model = TradingOrderCollateLine::where('id', $params['trading_order_collate_line_id'])
				->where('status', TradingOrderCollateLine::STATUS_VERIFY)
				->first();
			if ($model) {
				$overpayment = $model->overpayment;
				$model->overpayment = 0;
				$model->status = TradingOrderCollateLine::STATUS_SUCCESS;
				$model->updated_by = $params['updated_by'];
				if ($model->save()) {
					(new TradingOrderCollateTransaction)->decreaseOverpayment([
						'trading_order_collate_id' => $model->trading_order_collate_id,
						'overpayment' => $overpayment,
						'updated_by' => $params['updated_by'],
					]);
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
}
