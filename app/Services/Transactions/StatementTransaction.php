<?php

namespace App\Services\Transactions;

use App\Imports\StatementImport;
use App\Jobs\ProcessStatementJob;
use App\Models\FundDistributor;
use App\Models\Statement;
use App\Models\StatementLine;
use App\Models\SupervisingBank;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;

/**
 * @author MSI
 * @version 1.0
 * @created 19-Aug-2020 5:54:52 PM
 */
class StatementTransaction extends Transaction
{

	/**
	 * 
	 * @param array $params[
	 * fund_distributor_id,
	 * supervising_bank_id,
	 * file_name,
	 * file_path	 
	 * created_by
	 * ]
	 * @param allow_commit
	 */
	public function create(array $params, $allow_commit = false)
	{
		$response = null;
        $this->beginTransaction($allow_commit);
        try {
			$fund_distributor = FundDistributor::where('status', FundDistributor::STATUS_ACTIVE)
				->where('id', $params['fund_distributor_id'])->first();
			if (!$fund_distributor) {
				$this->error('Đại lý phân phối không tồn tại');
			}
			$supervising_bank = SupervisingBank::where('status', SupervisingBank::STATUS_ACTIVE)
				->where('id', $params['supervising_bank_id'])->first();
			if (!$supervising_bank) {
				$this->error('Ngân hàng giám sát không tồn tại');
			}
			$model = Statement::create([
				'fund_company_id' => $fund_distributor->fund_company_id,
				'fund_distributor_id' => $fund_distributor->id,
				'supervising_bank_id' => $supervising_bank->id,
				'file_name' => $params['file_name'],
				'file_path' => $params['file_path'],
				'total_line' => 0,
				'processed_line' => 0,
				'status' => Statement::STATUS_NEW,
				'created_by' => $params['created_by'],
			]);
			if ($model) {
				$response['statement_id'] = $model->id;
				$import = new StatementImport($model->id, $fund_distributor->fund_company_id, $fund_distributor->id, $supervising_bank->id);
				Excel::import($import, $params['file_path']);
				if ($import->getTotalLine() > 0) {
					$model->total_line = $import->getTotalLine();
					if ($model->save()) {
						// add job process
						$job = (new ProcessStatementJob($model))->onConnection(config('queue.default'))->onQueue('imports');
						dispatch($job);
					} else {
						$this->error('Có lỗi khi thêm sao kê vào hệ thống');
					}					
				} else {
					$this->error('Sao kê không có giao dịch chuyển tiền vào tài khoản quỹ cần xử lý');
				}				
			} else {
				$this->error('Có lỗi khi thêm sao kê ngân hàng');
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
	 * statement_id,
	 * updated_by
	 * ]
	 * @param allow_commit
	 */
	public function increaseProcessedLine(array $params, $allow_commit = false)
	{
		$response = null;
        $this->beginTransaction($allow_commit);
        try {
			$query = "UPDATE statements 
						SET processed_line = (SELECT COUNT(id) FROM statement_lines WHERE statement_id = statements.id AND status IN (:line_status_error, :line_status_success)), 
						status = IF(total_line > processed_line, :status_processing, :status_processed),
						updated_at = :now 
						WHERE id = :id 
						AND status != :status_processed 
						AND total_line > processed_line ";
			$update = Statement::updateRawQuery($query, [
				'id' => $params['statement_id'],
				'status_processing' => Statement::STATUS_PROCESSING,
				'status_processed' => Statement::STATUS_PROCESSED,
				'line_status_error' => StatementLine::STATUS_ERROR,
				'line_status_success' => StatementLine::STATUS_SUCCESS,
				'now' => Carbon::now()->toDateTimeString()
			]);
			if (!$update) {
				$this->error('Có lỗi khi cập nhật số dòng xử lý');
			}
        } catch (\Exception $e) {
            // rollback transaction
            $this->rollback($allow_commit, $e);
        }
        //commit and response
        return $this->response($allow_commit, $response);
	}
}
