<?php

namespace App\Services\Transactions;

use App\Models\StatementLine;
use Carbon\Carbon;

/**
 * @author MSI
 * @version 1.0
 * @created 19-Aug-2020 5:54:53 PM
 */
class StatementLineTransaction extends Transaction
{

	/**
	 * 
	 * @param array $params[
	 * statement_id,
	 * fund_company_id,
	 * fund_distributor_id,
	 * supervising_bank_id,
	 * data,
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
			$model = StatementLine::create([
				'statement_id' => $params['statement_id'],
				'fund_company_id' => $params['fund_company_id'],
				'fund_distributor_id' => $params['fund_distributor_id'],
				'supervising_bank_id' => $params['supervising_bank_id'],
				'data' => $params['data'],
				'bank_trans_note' => $params['bank_trans_note'],
				'status' => StatementLine::STATUS_NEW,
				'created_by' => $params['created_by'],
			]);
			if ($model) {
				$response['statement_line_id'] = $model->id;
			} else {
				$this->error('Có lỗi khi thêm chi tiết sao kê ngân hàng');
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
	 * statement_line_id,
	 * data_result,
	 * warnings,
	 * errors,
	 * updated_by
	 * ]
	 * @param allow_commit
	 */
	public function updateError(array $params, $allow_commit = false)
	{
		$response = null;
        $this->beginTransaction($allow_commit);
        try {
			$model = StatementLine::find($params['statement_line_id']);
			if ($model) {
				$query = "UPDATE statement_lines 
							SET status = :status_error, 
							data_result = :data_result, 
							warnings = :warnings, 
							errors = :errors, 
							updated_by = :updated_by 
							WHERE id = :id 
							AND status = :status_processing ";
				$update = StatementLine::updateRawQuery($query, [
					'status_processing' => StatementLine::STATUS_PROCESSING,
					'status_error' => StatementLine::STATUS_ERROR,
					'id' => $params['statement_line_id'],
					'data_result' => json_encode($params['data_result']),					
					'warnings' => json_encode($params['warnings']),
					'errors' => json_encode($params['errors']),
					'updated_by' => $params['updated_by'],
				]);
				if (!$update) {
					$this->error('Có lỗi khi cập nhật trạng thái');
				}
				(new StatementTransaction())->increaseProcessedLine([
					'statement_id' => $model->statement_id, 
					'updated_by' => $params['updated_by'],
				]);
			} else {
				$this->error('Chi tiết sao kê ngân hàng không tồn tại');
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
	 * statement_line_id,
	 * updated_by
	 * ]
	 * @param allow_commit
	 */
	public function updateProcessing(array $params, $allow_commit = false)
	{
		$response = null;
        $this->beginTransaction($allow_commit);
        try {
			$query = "UPDATE statement_lines 
						SET status = :status_processing, 
						updated_by = :updated_by,
						updated_at = :now
						WHERE id = :id 
						AND status = :status_new OR (status = :status_processing AND updated_at < :timeout) ";
			$update = StatementLine::updateRawQuery($query, [
				'status_processing' => StatementLine::STATUS_PROCESSING,
				'status_new' => StatementLine::STATUS_NEW,
				'id' => $params['statement_line_id'],
				'updated_by' => $params['updated_by'],
				'now' => Carbon::now()->toDateTimeString(),
				'timeout' => Carbon::now()->subMinutes(5)->toDateTimeString()
			]);
			if (!$update) {
				$this->error('Có lỗi khi cập nhật trạng thái');
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
	 * statement_line_id,
	 * data_result,	 
	 * warnings,
	 * updated_by
	 * ]
	 * @param allow_commit
	 */
	public function updateSuccess(array $params, $allow_commit = false)
	{
		$response = null;
        $this->beginTransaction($allow_commit);
        try {
			$model = StatementLine::find($params['statement_line_id']);
			if ($model) {
				$query = "UPDATE statement_lines 
							SET status = :status_success, 
							investor_id = :investor_id,
							fund_product_id = :fund_product_id,
							data_result = :data_result, 
							warnings = :warnings,
							bank_paid_at = :bank_paid_at,				
							updated_by = :updated_by 
							WHERE id = :id 
							AND status = :status_processing ";
				$update = StatementLine::updateRawQuery($query, [
					'status_processing' => StatementLine::STATUS_PROCESSING,
					'status_success' => StatementLine::STATUS_SUCCESS,
					'id' => $params['statement_line_id'],
					'investor_id' => $params['data_result']['investor_id'],
					'fund_product_id' => $params['data_result']['fund_product_id'],
					'data_result' => json_encode($params['data_result']),
					'bank_paid_at' => $params['data_result']['bank_paid_at'],
					'warnings' => json_encode($params['warnings']),			
					'updated_by' => $params['updated_by'],
				]);
				if (!$update) {
					$this->error('Có lỗi khi cập nhật trạng thái');
				}
				(new StatementTransaction())->increaseProcessedLine([
					'statement_id' => $model->statement_id, 
					'updated_by' => $params['updated_by'],
				]);
				$result = (new CashinTransaction())->createAndPaid([
					'fund_distributor_bank_account_id' => $params['data_result']['fund_distributor_bank_account_id'], 
					'investor_id' => $params['data_result']['investor_id'],
					'statement_id' => $model->statement_id,
					'amount' => $params['data_result']['amount'], 
					'currency' => $params['data_result']['currency'], 
					'fee' => $params['data_result']['fee'], 
					'receipt' => $params['data_result']['cashin_receipt'], 
					'bank_paid_at' => $params['data_result']['bank_paid_at'],
					'bank_trans_note' => $params['data_result']['bank_trans_note'],
					'paid_at' => Carbon::now()->toDateTimeString(),
					'created_by' => $params['updated_by'],
				]);
				$model->cashin_id = $result['cashin_id'];
				$model->cashin_receipt = $params['data_result']['cashin_receipt'];
				$model->save();
			} else {
				$this->error('Chi tiết sao kê ngân hàng không tồn tại');
			}
        } catch (\Exception $e) {
            // rollback transaction
            $this->rollback($allow_commit, $e);
        }
        //commit and response
        return $this->response($allow_commit, $response);
	}

}
