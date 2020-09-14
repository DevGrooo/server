<?php

namespace App\Services\Transactions;

use App\Models\FileImportLine;
use Carbon\Carbon;

/**
 * @author MSI
 * @version 1.0
 * @created 19-Aug-2020 5:54:53 PM
 */
class FileImportLineTransaction extends Transaction
{

	/**
	 * 
	 * @param array $params[
	 * file_import_id,
	 * data,	 
	 * ]
	 * @param allow_commit
	 */
	public function create(array $params, $allow_commit = false)
	{
		$response = null;
        $this->beginTransaction($allow_commit);
        try {
			$model = FileImportLine::create([
				'file_import_id' => $params['file_import_id'],
				'data' => $params['data'],				
				'status' => FileImportLine::STATUS_NEW,
			]);
			if ($model) {
				$response['file_import_line_id'] = $model->id;
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
	 * file_import_line_id,
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
			$model = FileImportLine::find($params['file_import_line_id']);
			if ($model) {
				$query = "UPDATE file_import_lines 
							SET status = :status_error, 
							data_result = :data_result, 
							warnings = :warnings, 
							errors = :errors
							WHERE id = :id 
							AND status = :status_processing ";
				$update = FileImportLine::updateRawQuery($query, [
					'status_processing' => FileImportLine::STATUS_PROCESSING,
					'status_error' => FileImportLine::STATUS_ERROR,
					'id' => $params['file_import_line_id'],
					'data_result' => json_encode($params['data_result']),					
					'warnings' => json_encode($params['warnings']),
					'errors' => json_encode($params['errors']),
				]);
				if (!$update) {
					$this->error('Có lỗi khi cập nhật trạng thái');
				}
				(new FileImportTransaction())->increaseProcessedLine([
					'file_import_id' => $model->file_import_id, 
					'number_error' => count($params['errors']),
					'number_warning' => count($params['warnings']),
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
	 * file_import_line_id,	 
	 * ]
	 * @param allow_commit
	 */
	public function updateProcessing(array $params, $allow_commit = false)
	{
		$response = null;
        $this->beginTransaction($allow_commit);
        try {
			$query = "UPDATE file_import_lines 
						SET status = :status_processing, 
						updated_at = :now
						WHERE id = :id 
						AND status = :status_new OR (status = :status_processing AND updated_at < :timeout) ";
			$update = FileImportLine::updateRawQuery($query, [
				'status_processing' => FileImportLine::STATUS_PROCESSING,
				'status_new' => FileImportLine::STATUS_NEW,
				'id' => $params['file_import_line_id'],				
				'now' => Carbon::now()->toDateTimeString(),
				'timeout' => Carbon::now()->addMinutes(1)->toDateTimeString()
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
	 * file_import_line_id,
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
			$model = FileImportLine::find($params['file_import_line_id']);
			if ($model) {
				$query = "UPDATE file_import_lines 
							SET status = :status_success, 
							data_result = :data_result, 
							warnings = :warnings
							WHERE id = :id 
							AND status = :status_processing ";
				$update = FileImportLine::updateRawQuery($query, [
					'status_processing' => FileImportLine::STATUS_PROCESSING,
					'status_success' => FileImportLine::STATUS_SUCCESS,
					'id' => $params['file_import_line_id'],
					'data_result' => json_encode($params['data_result']),
					'warnings' => json_encode($params['warnings']),			
				]);
				if (!$update) {
					$this->error('Có lỗi khi cập nhật trạng thái');
				}
				(new FileImportTransaction())->increaseProcessedLine([
					'file_import_id' => $model->file_import_id, 
					'number_error' => 0,
					'number_warning' => count($params['warnings']),
					'updated_by' => $params['updated_by'],
				]);
				$import = $model->file_import->getClassImport();
				$import->callTransactionAfterFileImportLineSuccess($model);
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
