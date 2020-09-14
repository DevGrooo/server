<?php

namespace App\Services\Transactions;

use App\Events\CancelTradingSessionEvent;
use App\Events\EndTradingSessionEvent;
use App\Models\TradingFrequency;
use App\Models\TradingSession;
use Exception;
use Illuminate\Support\Carbon;

/**
 * @author MSI
 * @version 1.0
 * @created 18-Aug-2020 3:39:58 PM
 */
class TradingSessionTransaction extends Transaction
{
	/**
	 *
	 * @param params
	 * @param allow_commit
	 */
	public function cancel(array $params, $allow_commit = false)
	{
		$response = null;
        $this->beginTransaction($allow_commit);
        try {
			$model = TradingSession::where('id', $params['trading_session_id'])
                ->where('status', TradingFrequency::STATUS_ACTIVE)
				->first();
			if ($model) {
			    if($model::STATUS_ACTIVE){
				$model->status = TradingSession::STATUS_CANCEL;
				$model->updated_by = $params['updated_by'];
				$cancel = $model->save();
                if(!$cancel){
                    $this->error('Có lỗi khi hủy phiên giao dịch');
                }
//				if ($model->save()) {
//					event(new CancelTradingSessionEvent($model));
				} else {
                    $this->error('Phiên giao dịch không được hỗ trợ');
				}
			} else {
				$this->error('Phiên giao dịch không tồn tại');
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
	 * previous_trading_session_id,
	 * trading_frequency_id,
	 * created_by
	 * ]
	 * @param allow_commit
	 */
	public function create(array $params, $allow_commit = false)
	{
		$response = null;
        $this->beginTransaction($allow_commit);
        try {
			$trading_frequency = TradingFrequency::where('id', $params['trading_frequency_id'])
				->where('status', TradingFrequency::STATUS_ACTIVE)->first();
			if (!$trading_frequency) {
				$this->error('Tần xuất giao dịch không tồn tại');
			}
			$time = Carbon::now();
			$previous_trading_session = TradingSession::find($params['previous_trading_session_id']);
			if ($previous_trading_session) {
				$time = Carbon::createFromFormat('d/m/Y H:i:s', $previous_trading_session->end_at);
			}
			$start_at = $trading_frequency->getStartAt($time);
			$model = TradingSession::create([
				'previous_trading_session_id' => $params['previous_trading_session_id'],
				'fund_company_id' => $trading_frequency->fund_company_id,
				'trading_frequency_id' => $trading_frequency->id,
				'code' => $start_at->format('dmY'),
				'start_at' => $start_at->toDateTimeString(),
				'end_at' => $trading_frequency->getEndAt($start_at)->toDateTimeString(),
				'limit_order_at' => $trading_frequency->getLimitOrderAt($start_at)->toDateTimeString(),
				'status' => TradingSession::STATUS_ACTIVE,
				'created_by' => $params['created_by'],
			]);
			if ($model) {
				$response['trading_session_id'] = $model->id;
			} else {
				$this->error('Có lỗi khi tạo phiên giao dịch');
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
	 * trading_session_id,
	 * updated_by
	 * ]
	 * @param allow_commit
	 */
	public function updateStatusEnd(array $params, $allow_commit = false)
	{
		$response = null;
        $this->beginTransaction($allow_commit);
        try {
			$model = TradingSession::where('id', $params['trading_session_id'])
				->where('end_at', '<=', Carbon::now())
				->where('status', TradingSession::STATUS_ACTIVE)
				->first();
			if ($model) {
				$model->status = TradingSession::STATUS_END;
				$model->updated_by = $params['updated_by'];
				if ($model->save()) {
					event(new EndTradingSessionEvent($model));
				} else {
					$this->error('Có lỗi khi cập nhật phiên giao dịch');
				}
			} else {
				$this->error('Phiên giao dịch không tồn tại');
			}
        } catch (\Exception $e) {
            // rollback transaction
            $this->rollback($allow_commit, $e);
        }
        //commit and response
        return $this->response($allow_commit, $response);
	}

	/**
	 * @param array $params[
	 * trading_session_id,
	 * updated_by
	 * ]
	 * @param boolean $allow_commit
	 * @throws Exception
	 * @return array
	 */
	public function updateStatusTimeUp(array $params, $allow_commit = false)
	{
		$response = null;
        $this->beginTransaction($allow_commit);
        try {
			$model = TradingSession::where('id', $params['trading_session_id'])
				->where('limit_order_at', '<=', Carbon::now())
				->where('status', TradingSession::STATUS_ACTIVE)
				->first();
			if ($model) {				
				$model->status = TradingSession::STATUS_TIME_UP;
				$model->updated_by = $params['updated_by'];
				if ($model->save()) {					
					$trading_frequency = TradingFrequency::where('id', $model->trading_frequency_id)
						->where('status', TradingFrequency::STATUS_ACTIVE)->first();
					if ($trading_frequency) {						
						// create next trading session
						$result = $this->create([
							'previous_trading_session_id' => $model->id,
							'trading_frequency_id' => $model->trading_frequency_id,
							'created_by' => $params['updated_by']
						]);
						$response['next_trading_session_id'] = $result['trading_session_id'];
					}
				} else {
					$this->error('Có lỗi khi cập nhật phiên giao dịch');
				}
			} else {
				$this->error('Phiên giao dịch không tồn tại');
			}
        } catch (\Exception $e) {
            // rollback transaction
            $this->rollback($allow_commit, $e);
        }
        //commit and response
        return $this->response($allow_commit, $response);
	}

}
