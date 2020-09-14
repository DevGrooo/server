<?php

namespace App\Services\Transactions;

use App\Models\FileImport;
use App\Models\FileImportLine;
use App\Models\TradingOrder;
use App\Models\TransactionFund;

/**
 * @author MSI
 * @version 1.0
 * @created 31-Aug-2020 5:17:05 PM
 */
class TransactionFundTransaction extends Transaction
{
	/**
	 * 
	 * @param array $params[transaction_fund_id,updated_by]
	 * @param allow_commit
	 */
	public function cancel(array $params, $allow_commit = false)
	{
		$response = null;
        $this->beginTransaction($allow_commit);
        try {
			$model = TransactionFund::where('id', $params['transaction_fund_id'])
				->whereIn('status', [TransactionFund::STATUS_NEW, TransactionFund::STATUS_VERIFY])
				->first();
			if ($model) {
				$model->status = TransactionFund::STATUS_CANCEL;
				$model->updated_by = $params['updated_by'] ;
				if ($model->save()) {
					if ($model->exec_type == TransactionFund::EXEC_TYPE_BUY) {
						(new TradingOrderTransaction())->cancelBuy([
							'trading_order_id' => $model->trading_order_id, 
							'reason_cancel' => 'Không khớp được lệnh trên VSD', 
							'updated_by' => $params['updated_by']
						]);
					} elseif ($model->exec_type == TransactionFund::EXEC_TYPE_SELL) {
						(new TradingOrderTransaction())->cancelSell([
							'trading_order_id' => $model->trading_order_id, 
							'reason_cancel' => 'Không khớp được lệnh trên VSD', 
							'updated_by' => $params['updated_by']
						]);
					} else {
						$this->error('Loại giao dịch chưa được hỗ trợ');
					}					
				} else {
					$this->error('Có lỗi khi cập nhật giao dịch quỹ');
				}
			} else {
				$this->error('Giao dịch quỹ không hợp lệ');
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
	 * @param $params[
	 * file_import_id,
	 * file_import_line_id,
	 * trading_order_id,
	 * send_match_amount,
	 * receive_match_amount,
	 * fee,
	 * fee_send,
	 * fee_receive,
	 * tax,
	 * vsd_trading_id,
	 * vsd_time_received,
	 * nav,
	 * total_nav,
	 * created_date,
	 * created_by
	 * ]
	 * @param allow_commit
	 */
	public function create(array $params, $allow_commit = false)
	{
		$response = null;
        $this->beginTransaction($allow_commit);
        try {
			$trading_order = TradingOrder::where('id', $params['trading_order_id'])
				->where('status', TradingOrder::STATUS_VERIFY)
				->where('vsd_status', TradingOrder::VSD_STATUS_IMPORT)
				->first();
			if (!$trading_order) {
				$this->error('Lệnh giao dịch quỹ không hợp lệ');
			}
			$model = TransactionFund::create([
				'file_import_id' => $params['file_import_id'],
				'file_import_line_id' => $params['file_import_line_id'],
				'trading_order_id' => $trading_order->id,
				'fund_company_id' => $trading_order->fund_company_id,
				'fund_distributor_id' => $trading_order->fund_distributor_id,
				'fund_distributor_bank_account_id' => $trading_order->fund_distributor_bank_account_id,
				'investor_id' => $trading_order->investor_id,
				'investor_id_type' => $trading_order->investor_id_type,
				'investor_id_number' => $trading_order->investor_id_number,
				'trading_frequency_id' => $trading_order->trading_frequency_id,
				'trading_session_id' => $trading_order->trading_session_id,
				'trading_account_number' => $trading_order->trading_account_number,
				'investor_sip_id' => $trading_order->investor_sip_id,
				'deal_type' => $trading_order->deal_type,
				'exec_type' => $trading_order->exec_type,
				'send_fund_certificate_id' => $trading_order->send_fund_certificate_id,
				'send_fund_product_id' => $trading_order->send_fund_product_id,
				'send_fund_product_type_id' => $trading_order->send_fund_product_type_id,
				'send_investor_fund_product_id' => $trading_order->send_investor_fund_product_id,
				'send_amount' => $trading_order->send_amount,
				'send_match_amount' => $params['send_match_amount'],
				'send_currency' => $trading_order->send_currency,
				'receive_fund_certificate_id' => $trading_order->receive_fund_certificate_id,
				'receive_fund_product_id' => $trading_order->receive_fund_product_id,
				'receive_fund_product_type_id' => $trading_order->receive_fund_product_type_id,
				'receive_investor_fund_product_id' => $trading_order->receive_investor_fund_product_id,
				'receive_amount' => $trading_order->receive_amount,
				'receive_match_amount' => $params['receive_match_amount'],
				'receive_currency' => $trading_order->receive_currency,
				'fee' => $params['fee'],
				'fee_send' => $params['fee_send'],
				'fee_receive' => $params['fee_receive'],
				'tax' => $params['tax'],
				'vsd_trading_id' => $params['vsd_trading_id'],
				'vsd_time_received' => $params['vsd_time_received'],
				'nav' => $params['nav'],
				'total_nav' => $params['total_nav'],
				'created_date' => $params['created_date'],
				'status' => TransactionFund::STATUS_NEW,
				'created_by' => $params['created_by']
			]);
			if ($model) {
				$response['transaction_fund_id'] = $model->id;
			} else {
				$this->error('Có lỗi khi thêm giao dịch quỹ');
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
	 * @param array $params[transaction_fund_id,updated_by]
	 * @param allow_commit
	 */
	public function perform(array $params, $allow_commit = false)
	{
		$response = null;
        $this->beginTransaction($allow_commit);
        try {
			$model = TransactionFund::where('id', $params['transaction_fund_id'])
				->whereIn('status', [TransactionFund::STATUS_NEW, TransactionFund::STATUS_VERIFY])
				->first();
			if ($model) {
				$model->status = TransactionFund::STATUS_PERFORM;
				$model->updated_by = $params['updated_by'] ;
				if ($model->save()) {
					if ($model->exec_type == TransactionFund::EXEC_TYPE_BUY) {
						(new TradingOrderTransaction())->performBuy([
							'trading_order_id' => $model->trading_order_id, 
							'receive_match_amount' => $model->receive_match_amount, 
							'send_match_amount' => $model->send_match_amount, 
							'tax' => $model->tax, 
							'nav' => $model->nav, 
							'total_nav' => $model->total_nav, 
							'vsd_trading_id' => $model->vsd_trading_id, 
							'vsd_time_received' => $model->vsd_time_received,
							'updated_by' => $params['updated_by'],
						]);
					} elseif ($model->exec_type == TransactionFund::EXEC_TYPE_SELL) {
						(new TradingOrderTransaction())->performSell([
							'trading_order_id' => $model->trading_order_id, 
							'receive_match_amount' => $model->receive_match_amount, 
							'send_match_amount' => $model->send_match_amount, 
							'tax' => $model->tax, 
							'nav' => $model->nav, 
							'total_nav' => $model->total_nav, 
							'vsd_trading_id' => $model->vsd_trading_id, 
							'vsd_time_received' => $model->vsd_time_received,
							'updated_by' => $params['updated_by'],
						]);
					} else {
						$this->error('Loại giao dịch chưa được hỗ trợ');
					}					
				} else {
					$this->error('Có lỗi khi cập nhật giao dịch quỹ');
				}
			} else {
				$this->error('Giao dịch quỹ không hợp lệ');
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
	 * @param array $params[file_import_id,created_by]
	 * @param allow_commit
	 */
	public function import(array $params, $allow_commit = false)
	{
		$response = null;
        $this->beginTransaction($allow_commit);
        try {
			$file_import = FileImport::where('id', $params['file_import_id'])
				->where('type', FileImport::TYPE_TRANSACTION_FUND)
				->where('status', FileImport::STATUS_PROCESSED)
				->where('total_error', 0)
				->where('total_warning', 0)
				->first();
			if (!$file_import) {
				$this->error('File import không hợp lệ');
			}
			$file_import_lines = FileImportLine::where('file_import_id', $params['file_import_id'])->get();
			if (!$file_import_lines) {
				$this->error('Không có dữ liệu để import');
			}
			foreach ($file_import_lines as $file_import_line) {
				$this->_importByFileImportLine($file_import_line, $params['created_by']);
			}
        } catch (\Exception $e) {
            // rollback transaction
            $this->rollback($allow_commit, $e);
        }
        //commit and response
        return $this->response($allow_commit, $response);
	}

	private function _importByFileImportLine($file_import_line, $created_by)
	{
		$inputs = $file_import_line->data_result;
		$inputs['created_by'] = $created_by;
		$result = $this->create($inputs);
		if ($file_import_line->data_result['status'] == TransactionFund::STATUS_PERFORM) {
			$inputs = array(
				'transaction_fund_id' => $result['transaction_fund_id'],
				'updated_by' => $created_by
			);
			$this->perform($inputs);
		} elseif($file_import_line->data_result['status'] == TransactionFund::STATUS_CANCEL) {
			$inputs = array(
				'transaction_fund_id' => $result['transaction_fund_id'],
				'updated_by' => $created_by
			);
			$this->cancel($inputs);
		}
	}
}
